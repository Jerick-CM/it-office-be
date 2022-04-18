<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\Role::factory(10)->create();

          $now = Carbon::now();
          DB::table('roles')->insert([
            'name' => 'Admin',
            'created_at' =>  $now ,
            'updated_at' =>  $now ,
          ]);

          $now = Carbon::now();
          DB::table('roles')->insert([
            'name' => 'Registrar',
            'created_at' =>  $now ,
            'updated_at' =>  $now ,
          ]);

          $now = Carbon::now();
          DB::table('roles')->insert([
            'name' => 'Encoder',
            'created_at' =>  $now ,
            'updated_at' =>  $now ,
          ]);

          $now = Carbon::now();
          DB::table('roles')->insert([
            'name' => 'Member',
            'created_at' =>  $now ,
            'updated_at' =>  $now ,
          ]);


    }
}
