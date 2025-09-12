<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('browse', function () {
    return view('browse');
})->name('browse');

Route::get('create', function () {
    return view('create');
})->name('create');

Route::get('about', function () {
    return view('about');
})->name('about');

Route::get('faq', function () {
    return view('faq');
})->name('faq');

Route::middleware('guest')->group(function () {
    Route::get('signin', function () {
        return view('auth.signin');
    })->name('auth.signin');

    Route::get('join', function () {
        return view('auth.join');
    })->name('auth.join');
});