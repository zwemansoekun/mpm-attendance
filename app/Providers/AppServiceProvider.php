<?php

namespace App\Providers;

use App\DefaultSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // DB::listen(function ($query) {
        //     var_dump($query->sql);
        // });
        JsonResource::withoutWrapping();
        config([
            'global' => DefaultSetting::all([
                'money','ssb_max'
            ])
            // ->keyBy('money') // key every setting by its name
            // ->transform(function ($setting) {
            //      return $setting->money; // return only the value
            // })
            // ->toArray() // make it an array
        ]);
          
    }
}
