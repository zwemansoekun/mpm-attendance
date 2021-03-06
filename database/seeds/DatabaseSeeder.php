<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Setting::class , 1)->create();
        factory(App\User::class , 1)->create();
        // $this->call(AttendDetailTableSeeder::class);    
        $this->call(DefaultSettingSeeder::class);      
    }
}
