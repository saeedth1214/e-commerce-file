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
            [
                'parent_id' => 1,
                'name' => '  بک گراند',
                'slug' => 'Backgrounds  ',
            ],
            [
                'parent_id' => 1,
                'name' => ' پوستر',
                'slug' => 'Posters  ',
            ],
            [
                'parent_id' => 1,
                'name' => ' بنر',
                'slug' => 'Banners  ',
            ],
            [
                'parent_id' => 1,
                'name' => '  لوگو',
                'slug' => 'Logos  ',
            ],
            [
                'parent_id' => 1,
                'name' => '  کارت تبریک',
                'slug' => 'Greeting Card  ',
            ],

            [
                'parent_id' => 2,
                'name' => '  طبیعت',
                'slug' => 'Nature  ',
            ],
            [
                'parent_id' => 2,
                'name' => '  غذا و نوشیدنی',
                'slug' => 'Food & Drink ',
            ],

            [
                'parent_id' => 2,
                'name' => ' ورزشی',
                'slug' => 'Sport ',
            ],

            [
                'parent_id' => 2,
                'name' => ' صنعت و تکلولوژی',
                'slug' => 'Industry & technology ',
            ],

        ];
        DB::table('categories')->insert($categories);
    }
}
