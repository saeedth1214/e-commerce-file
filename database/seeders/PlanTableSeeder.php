<?php

namespace Database\Seeders;

use App\Enums\PlanTypeEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plans')->truncate();
        $plans = [
            [
                'title' => ' دانلود روزانه 3 طرح - برنزی',
                'description' => '',
                'amount' => 160000,
                'rebate' => 0,
                'percentage' => true,
                'daily_download_limit_count' => 3,
                'daily_free_download_limit_count' => 20,
                'type' => PlanTypeEnum::MONTHLY,
                'created_at' => now(),
            ],
            [
                'title' => ' دانلود روزانه 3 طرح - نقره ای',
                'description' => '',
                'amount' => 360000,
                'rebate' => 0,
                'percentage' => true,
                'daily_download_limit_count' => 3,
                'daily_free_download_limit_count' => 30,
                'type' => PlanTypeEnum::QUARTELY,
                'created_at' => now(),

            ],
            [
                'title' => ' دانلود روزانه 3 طرح - طلایی',
                'description' => '',
                'amount' => 560000,
                'rebate' => 0,
                'percentage' => true,
                'daily_download_limit_count' => 3,
                'daily_free_download_limit_count' => 40,
                'type' => PlanTypeEnum::BIANNUAL,
                'created_at' => now(),

            ],

            [
                'title' => ' دانلود روزانه 10 طرح - برنزی',
                'description' => '',
                'amount' => 220000,
                'rebate' => 0,
                'percentage' => true,
                'daily_download_limit_count' => 10,
                'daily_free_download_limit_count' => 20,
                'type' => PlanTypeEnum::MONTHLY,
                'created_at' => now(),

            ],
            [
                'title' => ' دانلود روزانه 10 طرح - نقره ای',
                'description' => '',
                'amount' => 420000,
                'rebate' => 0,
                'percentage' => true,
                'daily_download_limit_count' => 10,
                'daily_free_download_limit_count' => 30,
                'type' => PlanTypeEnum::QUARTELY,
                'created_at' => now(),

            ],
            [
                'title' => ' دانلود روزانه 10 طرح - طلایی',
                'description' => '',
                'amount' => 620000,
                'rebate' => 0,
                'percentage' => true,
                'daily_download_limit_count' => 10,
                'daily_free_download_limit_count' => 40,
                'type' => PlanTypeEnum::BIANNUAL,
                'created_at' => now(),

            ],

            [
                'title' => ' دانلود روزانه 20 طرح - برنزی',
                'description' => '',
                'amount' => 320000,
                'rebate' => 0,
                'percentage' => true,
                'daily_download_limit_count' => 20,
                'daily_free_download_limit_count' => 20,
                'type' => PlanTypeEnum::MONTHLY,
                'created_at' => now(),

            ],
            [
                'title' => ' دانلود روزانه 20 طرح - نقره ای',
                'description' => '',
                'amount' => 520000,
                'rebate' => 0,
                'percentage' => true,
                'daily_download_limit_count' => 20,
                'daily_free_download_limit_count' => 30,
                'type' => PlanTypeEnum::QUARTELY,
                'created_at' => now(),

            ],
            [
                'title' => ' دانلود روزانه 20 طرح - طلایی',
                'description' => '',
                'amount' => 720000,
                'rebate' => 0,
                'percentage' => true,
                'daily_download_limit_count' => 20,
                'daily_free_download_limit_count' => 40,
                'type' => PlanTypeEnum::BIANNUAL,
                'created_at' => now(),

            ],
        ];
        DB::table('plans')->insert($plans);
    }
}
