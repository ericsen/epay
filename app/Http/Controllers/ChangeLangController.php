<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class ChangeLangController extends Controller
{
    //
    public function changeLang($locale = 'zh-TW')
    {

        if (!empty($locale)) {
            if (array_key_exists($locale, config('languages'))) {
                Session::put('locale', $locale);
            }
        }

        return redirect()->back();
    }
}
