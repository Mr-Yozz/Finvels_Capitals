<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function base()
    {

        return view('admin.home');
    }

    public function about()
    {
        return view('admin.about');
    }

    public function service()
    {
        return view('admin.service');
    }

    public function contact()
    {
        return view('admin.contact');
    }
}
