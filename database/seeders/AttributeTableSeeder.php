<?php

namespace Database\Seeders;

use App\Enums\AttributeTypeEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('attributes')->truncate();
        $attributes = [
            [
                "slug" => "Aspect Ratio",
                "name" => "نسبت تصویر",
                "type" => AttributeTypeEnum::ASPECT
            ],
            [
                "slug" => "Screen Size",
                "name" => "ابعاد تصویر",
                "type" => AttributeTypeEnum::SCREEN
            ],
            [
                "slug" => "Format",
                "name" => "فرمت",
                "type" => AttributeTypeEnum::FORMAT
            ],
            [
                "slug" => "Resolution",
                "name" => "رزولوشن",
                "type" => AttributeTypeEnum::RESOLUTION

            ],
            [
                "slug" => "File Size",
                "name" => "حجم فایل",
                "type" => AttributeTypeEnum::SIZE
            ],
        ];
        DB::table('attributes')->insert($attributes);
    }
}
