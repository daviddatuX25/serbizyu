@props([
'title' => config('app.name', 'Serbizyu'),
'cssFile' => 'app.css',
'jsFile' => 'app.js'
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
 
        
        <title>{{ $title }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600|inter:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(["resources/css/{$cssFile}", "resources/js/{$jsFile}"])
        
    </head>
    <body>
        <div class="min-h-screen">
            <x-navbar />

            <!-- Page Heading -->
            @isset($header)
                <header class="shadow" style="background-color: var(--color-background);">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            @include('layouts.footer')
        </div>
    </body>
</html>