<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PerizinanController extends Controller
{
    public function index()
    {
        return view('admin.pages.perizinan.index');
    }

    public function detail()
    {
        return view('admin.pages.perizinan.detail');
    }
}
