<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Setting;
use App\DelayTime;

class ScheduleDelayTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:delayTimes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $monthString = $now->format("yy/m");

        $setting = Setting::find(1);
        $dTime = DelayTime::select("*")->where("month",$monthString)->first();
       
        if($dTime == null){
            $delayTime = new DelayTime([
                'month' => $monthString,
                'am' => $setting->am,
                'pm' => $setting->pm,
                'money' => $setting->money
            ]);
            $delayTime->save();
        }
       
    }
}
