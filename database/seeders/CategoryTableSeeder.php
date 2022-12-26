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

        // Category::factory(5)->create();

        $categories = [
            [
                'name' => 'ورزشی',
                'slug' => 'sport'
            ],
            [
                'name' => 'خانوادگی',
                'slug' => 'family'
            ], [
                'name' => 'هنر',
                'slug' => 'art'
            ], [
                'name' => 'طبیعت',
                'slug' => 'Nature'
            ], [
                'name' => 'کتاب',
                'slug' => 'books'
            ],

        ];
        DB::table('categories')->insert($categories);
    }
}
