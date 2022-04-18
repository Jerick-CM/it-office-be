<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{

    public function run()
    {

        $users =  DB::table('users')->insert([
            'name' => "superuser 1",
            'email' => 'admin1@itoffice.com',
            'email_verified_at' => now(),
            'password' => bcrypt(env('MASTERPASSWORD1')),
            'remember_token' => Str::random(10),
            'is_admin' => 1,
        ]);

        \App\Models\UserDetails::factory(1)->create([
            'user_id' =>  DB::getPdo()->lastInsertId()
        ]);


        $users =  DB::table('users')->insert([
            'name' => "superuser 2",
            'email' => 'admin2@itoffice.com',
            'email_verified_at' => now(),
            'password' => bcrypt(env('MASTERPASSWORD2')),
            'remember_token' => Str::random(10),
            'is_admin' => 1,
        ]);

        \App\Models\UserDetails::factory(1)->create([
            'user_id' =>  DB::getPdo()->lastInsertId()
        ]);


        $users =  DB::table('users')->insert([
            'name' => "superuser 3",
            'email' => 'admin3@itoffice.com',
            'email_verified_at' => now(),
            'password' => bcrypt(env('MASTERPASSWORD3')),
            'remember_token' => Str::random(10),
            'is_admin' => 1,
        ]);

        \App\Models\UserDetails::factory(1)->create([
            'user_id' =>  DB::getPdo()->lastInsertId()
        ]);


        $users =  DB::table('users')->insert([
            'name' => "superuser 4",
            'email' => 'admin4@itoffice.com',
            'email_verified_at' => now(),
            'password' => bcrypt(env('MASTERPASSWORD4')),
            'remember_token' => Str::random(10),
            'is_admin' => 1,
        ]);

        \App\Models\UserDetails::factory(1)->create([
            'user_id' =>  DB::getPdo()->lastInsertId()
        ]);

        $users =  DB::table('users')->insert([
            'name' => "superuser 5",
            'email' => 'admin5@itoffice.com',
            'email_verified_at' => now(),
            'password' => bcrypt(env('MASTERPASSWORD5')),
            'remember_token' => Str::random(10),
            'is_admin' => 1,
        ]);

        \App\Models\UserDetails::factory(1)->create([
            'user_id' =>  DB::getPdo()->lastInsertId()
        ]);

        for ($x = 1; $x <= 9; $x++) {
            $faker_users = \App\Models\User::factory(1)->create();
            \App\Models\UserDetails::factory(1)->create([
                'user_id' => $faker_users[0]->id,
            ]);
        }
    }
}
