<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Credit Analyst Demo User (Approver role)
        User::updateOrCreate(
            ['email' => 'analyst@cmd.co.id'],
            [
                'name' => 'Hendra Gunawan',
                'role' => 'analyst',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Marketing Officer Demo User (Maker role)
        User::updateOrCreate(
            ['email' => 'marketing@cmd.co.id'],
            [
                'name' => 'Rahmat Hidayat',
                'role' => 'marketing',
                'password' => Hash::make('password'),
            ]
        );

        $this->call(LoanApplicationSeeder::class);
    }
}
