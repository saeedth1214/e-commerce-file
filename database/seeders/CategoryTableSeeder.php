<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'طراحی',
                'slug' => 'designs'
            ],
            [
                'name' => 'تصاویر',
                'slug' => 'photose'
            ],

        ];
        DB::table('categories')->insert($categories);
    }
}
