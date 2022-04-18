<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Roles_UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function run()
    {
        // \App\Models\Roles_Users::factory(10)->create();
        for ($x = 1; $x <= 5; $x++) {
            $now = Carbon::now();
            DB::table('role_user')->insert([
              'user_id' => $x,
              'role_id' => 1, //use 3 as encoder
              'created_at' =>  $now ,
              'updated_at' =>  $now ,
            ]);
        }

        for ($x = 6; $x <= 15; $x++) {
            $now = Carbon::now();
            DB::table('role_user')->insert([
              'user_id' => $x,
              'role_id' => 3, //use 3 as encoder
              'created_at' =>  $now ,
              'updated_at' =>  $now ,
            ]);
        }
    }

}
