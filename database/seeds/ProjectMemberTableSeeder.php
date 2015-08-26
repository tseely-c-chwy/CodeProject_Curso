<?php

use Illuminate\Database\Seeder;

class ProjectMemberTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //\CodeProject\Entities\Project::truncate();
        factory(\CodeProject\Entities\ProjectMember::class, 20)->create();
    }
}