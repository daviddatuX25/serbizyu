@props([
    'title' => config('app.name', 'Serbizyu'),
    'cssFiles' => [],   // accepts array or string
    'jsFiles' => [],    // accepts array or string
])

@php
    // Backward compatibility (if someone passes single string)
    $cssFiles = is_array($cssFiles) ? $cssFiles : [$cssFiles];
    $jsFiles = is_array($jsFiles) ? $jsFiles : [$jsFiles];

    // Default to app.css / app.js if none provided
    if (empty($cssFiles)) $cssFiles = ['app.css'];
    if (empty($jsFiles)) $jsFiles = ['app.js'];
@endphp

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

    <!-- Styles & Scripts -->
    @foreach ($cssFiles as $css)
        @vite("resources/css/{$css}")
    @endforeach

    @foreach ($jsFiles as $js)
        @vite("resources/js/{$js}")
    @endforeach
</head>

<body>
    <div class="min-h-screen">
        <x-navbar />

        @isset($header)
            <header class="shadow" style="background-color: var(--color-background);">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main>
            {{ $slot }}
        </main>

        @include('layouts.footer')
    </div>
    
</body>
</html>
