<?php

use App\AttendDetail;
use Illuminate\Database\Seeder;

class AttendDetailTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = 100;
        factory(AttendDetail::class, $count)->create();
    }
}
