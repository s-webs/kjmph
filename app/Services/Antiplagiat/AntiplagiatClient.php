<?php

namespace App\Services\Antiplagiat;

use SoapClient;

class AntiplagiatClient
{
    protected SoapClient $client;

    public function __construct(
        string $login,
        string $password,
        string $companyName,
        string $apicorpAddress
    ) {
        $wsdl = "https://{$apicorpAddress}/apiCorp/{$companyName}?singleWsdl";

        $oldValue = libxml_disable_entity_loader(false);

        try {
            $this->client = new SoapClient($wsdl, [
                'trace'         => 1,
                'login'         => $login,
                'password'      => $password,
                'soap_version'  => SOAP_1_1,
                'features'      => SOAP_SINGLE_ELEMENT_ARRAYS,
            ]);
        } finally {
            libxml_disable_entity_loader($oldValue);
        }
    }

    public function getClient(): SoapClient
    {
        return $this->client;
    }
}
