<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TermsOfUse extends Controller
{
    public function index()
    {
        return view('terms-of-use');
    }
}
