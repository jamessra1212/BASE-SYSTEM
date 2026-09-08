<?php

namespace Database\Seeders;

use App\Core\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::firstOrCreate(['key' => 'app_name'], ['value' => 'Base Template']);
        Setting::firstOrCreate(['key' => 'app_version'], ['value' => '1.0.0']);
        Setting::firstOrCreate(['key' => 'app_logo'], ['value' => null]);
    }
}