<?php

namespace Database\Seeders;

use App\Enums\UserRoleEnum;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->firstOrCreate([
            'email' => 'm.h.soltani1214@gmail.com',
        ], [
            'mobile' => '9302474269',
            'password' => '$argon2id$v=19$m=1024,t=2,p=2$YlF0Ty5jRmVQTzlOeVRUbw$UNqkBGues+AwXqr+ZnwEoWRIp8LLllCMcdrdlgqQF3A', //password
            'mobile_verified_at' => now(),
            'email_verified_at' => now(),            
            'first_name' => 'saeed',
            'last_name' => 'soltani',
            'role'=>UserRoleEnum::ADMIN
        ]);
    }
}
