<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            CurrencySeeder::class,
            FeeConfigurationSeeder::class,
            InterestRateSeeder::class,
            SettingSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
