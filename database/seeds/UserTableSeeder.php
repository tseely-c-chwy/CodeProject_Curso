<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //\CodeProject\Entities\Project::truncate();
        factory(\CodeProject\Entities\User::class, 10)->create();
        factory(\CodeProject\Entities\User::class)->create([
            'name' => 'Thiago Ricardo Seely',
            'email' => 'tseely@pet360.com',
            'password' => bcrypt('admin'),
            'remember_token' => str_random(10), 
        ]);
    }
}