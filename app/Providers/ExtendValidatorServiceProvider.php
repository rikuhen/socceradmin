<?php

namespace HappyFeet\Providers;

use Illuminate\Support\ServiceProvider;
use Tavo\ValidadorEc;
use Validator;
use DB;

class ExtendValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('valid_dni',function($attribute, $value, $parameters, $validator) {

            $validEc = new ValidadorEc();
            if ($validEc->validarCedula($value)) {
                return true;
            }

            return false;
        });


         Validator::extendImplicit('is_used', function ($attribute, $value, $parameters, $validator) {
            $data = DB::table($parameters[0])->where($parameters[1],$value)->first();
            
            if (!$data) {
                return true;
            }

            if ($data->deleted_at && $data->deleted_at != null) {
                return true;
            }

            return false;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
