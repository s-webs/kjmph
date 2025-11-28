<?php

namespace App\Services\Antiplagiat;

use Illuminate\Http\UploadedFile;
use RuntimeException;
use SoapClient;

class AntiplagiatService
{
    protected SoapClient $soap;

    public function __construct(
        protected AntiplagiatClient $client,
        protected string $companyName, // можно прокинуть из config('antiplagiat.company_name')
    )
    {
        $this->soap = $client->getClient();
    }

    /*
     |--------------------------------------------------------------------------
     | 1. Подготовка документа
     |--------------------------------------------------------------------------
     */

    /**
     * Подготовка данных документа из пути к файлу.
     */
    public function prepareDocumentFromPath(
        string $filepath,
        string $externalUserId,
        array  $authorData = []
    ): array
    {
        if (!is_file($filepath) || !is_readable($filepath)) {
            throw new RuntimeException("File not found or not readable: {$filepath}");
        }

        $content = file_get_contents($filepath);
        $filename = basename($filepath);

        return $this->prepareDocumentFromRaw(
            $content,
            $filename,
            $externalUserId,
            $authorData
        );
    }

    /**
     * Подготовка данных документа из UploadedFile (Laravel).
     */
    public function prepareDocumentFromUploadedFile(
        UploadedFile $file,
        string       $externalUserId,
        array        $authorData = []
    ): array
    {
        $content = file_get_contents($file->getRealPath());
        $filename = $file->getClientOriginalName();

        return $this->prepareDocumentFromRaw(
            $content,
            $filename,
            $externalUserId,
            $authorData
        );
    }

    /**
     * Базовый метод подготовки данных документа.
     * Возвращает массив:
     * [
     *   'data'       => [...],
     *   'attributes' => [...],
     * ]
     */
    public function prepareDocumentFromRaw(
        string $binaryContent,
        string $filename,
        string $externalUserId,
        array  $authorData = []
    ): array
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $data = [
            'Data' => $binaryContent,
            'FileName' => $filename,
            'FileType' => $extension ? '.' . $extension : null,
            'ExternalUserID' => $externalUserId,
        ];

        $authorSurname = $authorData['surname'] ?? 'Иванов';
        $authorOtherName = $authorData['otherNames'] ?? 'Иван Иванович';
        $authorCustomId = $authorData['customId'] ?? 'original';

        $attributes = [
            'DocumentDescription' => [
                'Authors' => [
                    'AuthorName' => [
                        [
                            'OtherNames' => $authorOtherName,
                            'Surname' => $authorSurname,
                            'PersonIDs' => [
                                'CustomID' => $authorCustomId,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return [
            'data' => $data,
            'attributes' => $attributes,
        ];
    }

    /*
     |--------------------------------------------------------------------------
     | 2. Загрузка документа
     |--------------------------------------------------------------------------
     */

    /**
     * Загружает документ и возвращает docId.
     */
    public function uploadDocument(array $payload): string
    {
        $uploadResult = $this->soap->UploadDocument($payload);

        if (
            empty($uploadResult->UploadDocumentResult->Uploaded) ||
            empty($uploadResult->UploadDocumentResult->Uploaded[0]->Id)
        ) {
            throw new RuntimeException('Не удалось получить Id загруженного документа');
        }

        return $uploadResult->UploadDocumentResult->Uploaded[0]->Id;
    }

    /*
     |--------------------------------------------------------------------------
     | 3. Запуск проверки
     |--------------------------------------------------------------------------
     */

    /**
     * Отправить документ на проверку.
     *
     * @param string $docId
     * @param array|null $checkServicesList список модулей поиска или null (все подключённые)
     */
    public function startCheck(string $docId, ?array $checkServicesList = null): void
    {
        $params = ['docId' => $docId];

        if ($checkServicesList !== null) {
            $params['checkServicesList'] = $checkServicesList;
        }

        $this->soap->CheckDocument($params);
    }

    /*
     |--------------------------------------------------------------------------
     | 4. Статус и ожидание окончания проверки
     |--------------------------------------------------------------------------
     */

    /**
     * Получить текущий статус проверки.
     */
    public function getCheckStatus(string $docId): object
    {
        return $this->soap->GetCheckStatus(['docId' => $docId]);
    }

    /**
     * Ждать завершения проверки.
     *
     * Возвращает финальный объект статуса.
     */
    public function waitForCheckCompletion(
        string $docId,
        int    $maxAttempts = 60
    ): object
    {
        $attempt = 0;
        $status = $this->getCheckStatus($docId);

        while (
            $status->GetCheckStatusResult->Status === 'InProgress' &&
            $attempt < $maxAttempts
        ) {
            $waitSeconds = max(
                1,
                (int)round($status->GetCheckStatusResult->EstimatedWaitTime * 0.1)
            );

            sleep($waitSeconds);
            $status = $this->getCheckStatus($docId);
            $attempt++;
        }

        if ($status->GetCheckStatusResult->Status === 'Failed') {
            $details = $status->GetCheckStatusResult->FailDetails ?? 'Unknown error';
            throw new RuntimeException('Ошибка при проверке документа: ' . $details);
        }

        return $status;
    }

    /*
     |--------------------------------------------------------------------------
     | 5. Получение отчётов
     |--------------------------------------------------------------------------
     */

    /**
     * Краткий отчёт.
     */
    public function getShortReport(string $docId)
    {
        $report = $this->soap->GetReportView(['docId' => $docId]);

        return $report->GetReportViewResult ?? null;
    }

    /**
     * Полный отчёт.
     *
     * $options можно переопределить, но по умолчанию включено всё.
     */
    public function getFullReport(string $docId, array $options = [])
    {
        $options = array_replace([
            'FullReport' => true,
            'NeedText' => true,
            'NeedStats' => true,
            'NeedAttributes' => true,
        ], $options);

        $report = $this->soap->GetReportView([
            'docId' => $docId,
            'options' => $options,
        ]);

        return $report->GetReportViewResult ?? null;
    }

    /*
     |--------------------------------------------------------------------------
     | 6. Удобный «полный сценарий» одной функцией (по желанию)
     |--------------------------------------------------------------------------
     */

    /**
     * Полный сценарий:
     *  - подготовка документа
     *  - загрузка
     *  - запуск проверки
     *  - ожидание
     *  - краткий и полный отчёт
     */
    public function runFullCheckFromPath(
        string $filepath,
        string $externalUserId,
        array  $authorData = [],
        ?array $checkServicesList = null
    ): array
    {
        $payload = $this->prepareDocumentFromPath(
            $filepath,
            $externalUserId,
            $authorData
        );

        $docId = $this->uploadDocument($payload);

        $this->startCheck($docId, $checkServicesList);

        $status = $this->waitForCheckCompletion($docId);

        $shortReport = $this->getShortReport($docId);
        $fullReport = $this->getFullReport($docId);

        return [
            'doc_id' => $docId,
            'status' => $status->GetCheckStatusResult->Status,
            'short_report' => $shortReport,
            'full_report' => $fullReport,
        ];
    }
}
