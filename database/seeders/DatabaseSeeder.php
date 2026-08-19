<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@otec.cl'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Admin123456!'),
            ]
        );

        $this->call(HolidaySeeder::class);
        $this->call(SurveyQuestionSeeder::class);
        $this->call(DiplomaTemplateSeeder::class);
        $this->call(ExecutionChecklistItemSeeder::class);
    }
}
