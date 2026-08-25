<?php

namespace Database\Seeders;

use App\Models\Reviewer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use MoonShine\Laravel\Models\MoonshineUser;
use MoonShine\Laravel\Models\MoonshineUserRole;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Редактор — доступ в MoonShine
        MoonshineUser::firstOrCreate(
            ['email' => 'editor@kjmph.kz'],
            [
                'name' => 'Редактор',
                'password' => Hash::make('secret123'),
                'moonshine_user_role_id' => MoonshineUserRole::DEFAULT_ROLE_ID,
            ]
        );

        // Автор — публичный кабинет
        User::firstOrCreate(
            ['email' => 'author@kjmph.kz'],
            [
                'name' => 'Тестовый Автор',
                'organisation' => 'Тестовый университет',
                'country' => 'KZ',
                'password' => Hash::make('secret123'),
            ]
        );

        // Рецензент — вход через токен (пароль не используется)
        Reviewer::firstOrCreate(
            ['email' => 'reviewer@kjmph.kz'],
            [
                'name' => 'Тестовый Рецензент',
                'organisation' => 'Тестовый институт',
                'specialization' => 'Медицина',
                'is_active' => true,
            ]
        );
    }
}
