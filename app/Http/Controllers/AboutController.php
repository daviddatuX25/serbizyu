<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class AboutController extends Controller
{
    public function index()
    {
        $aboutContent = File::get(base_path('app/about.txt'));
        $sections = collect(preg_split('/(?=^#\s)/m', $aboutContent, -1, PREG_SPLIT_NO_EMPTY));
        $about = $sections->mapWithKeys(function ($section) {
            $lines = array_filter(explode("\n", trim($section)));
            $title = trim(array_shift($lines), '# ');
            $content = implode("\n", $lines);

            return [$title => $content];
        });

        return view('about', ['about' => $about]);
    }
}
