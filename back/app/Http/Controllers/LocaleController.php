<?php

namespace App\Http\Controllers;

use App\Locale;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function index()
    {
    	$locale = Locale::all();

    	return response()->json($locale);
    }
}
