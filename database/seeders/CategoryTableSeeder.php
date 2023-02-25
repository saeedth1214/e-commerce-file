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
        // DB::table('categories')->truncate();
        $categories = [
            [
                'parent_id' => null,
                'name' => 'طراحی',
                'slug' => 'designs'
            ],
            [
                'parent_id' => null,
                'name' => 'تصاویر',
                'slug' => 'photose'
            ],
            // [
            //     'parent_id' => 1,
            //     'name' => ' 2 بک گراند',
            //     'slug' => 'Backgrounds 2',
            // ],
            // [
            //     'parent_id' => 1,
            //     'name' => '2پوستر',
            //     'slug' => 'Posters 2',
            // ],
            // [
            //     'parent_id' => 1,
            //     'name' => '2بنر',
            //     'slug' => 'Banners 2',
            // ],
            // [
            //     'parent_id' => 1,
            //     'name' => ' 2لوگو',
            //     'slug' => 'Logos 2',
            // ],
            // [
            //     'parent_id' => 1,
            //     'name' => ' 2کارت تبریک',
            //     'slug' => 'Greeting Card 2',
            // ],

            // [
            //     'parent_id' => 2,
            //     'name' => ' 2طبیعت',
            //     'slug' => 'Nature 2',
            // ],
            // [
            //     'parent_id' => 2,
            //     'name' => ' 2غذا و نوشیدنی',
            //     'slug' => 'Food & Drink2',
            // ],

            // [
            //     'parent_id' => 2,
            //     'name' => '2ورزشی',
            //     'slug' => 'Sport2',
            // ],

            // [
            //     'parent_id' => 2,
            //     'name' => '2صنعت و تکلولوژی',
            //     'slug' => 'Industry & technology2',
            // ],

        ];
        DB::table('categories')->insert($categories);
    }
}
