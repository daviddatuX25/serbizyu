<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * Display the FAQ page
     */
    public function index(): View
    {
        return view('faq');
    }
}
