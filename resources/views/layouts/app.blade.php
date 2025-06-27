<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="ATELIER - Professional Resume Atelier. Where careers are crafted with AI precision and artistic excellence. Create stunning resumes in minutes.">
    <meta name="keywords" content="atelier, resume builder, ai resume, professional cv, career crafting, job application, resume templates">
    <meta name="author" content="ATELIER Team">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="{{ config('app.name', 'ATELIER') }} - Professional Resume Atelier">
    <meta property="og:description" content="Where careers are crafted with AI precision and artistic excellence. Create professional resumes in minutes.">
    <meta property="og:image" content="{{ asset('images/og-image.png') }}">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/') }}">
    <meta property="twitter:title" content="{{ config('app.name', 'ATELIER') }} - Professional Resume Atelier">
    <meta property="twitter:description" content="Where careers are crafted with AI precision and artistic excellence. Create professional resumes in minutes.">
    <meta property="twitter:image" content="{{ asset('images/twitter-card.png') }}">
    
    <title>{{ config('app.name', 'ATELIER') }} - Professional Resume Atelier</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles and Scripts -->
    @vite('resources/css/master.css')
    @vite('resources/js/app.js')
    
    <!-- Theme Color -->
    <meta name="theme-color" content="#5B21B6">
    
    <!-- Performance Hints -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
</head>
<body>
    <div id="vue-app">
        <!-- Loading indicator -->
        <div id="loading-indicator" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #fff; display: flex; justify-content: center; align-items: center; z-index: 9999;">
            <div style="text-align: center;">
                <div class="spinner" style="width: 50px; height: 50px; border: 5px solid #f3f3f3; border-top: 5px solid #5B21B6; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
                <p style="color: #4B5563; font-family: 'Inter', sans-serif;">Loading ATELIER...</p>
            </div>
        </div>
        <!-- Vue.js will mount here -->
    </div>
    
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Preload critical fonts */
        @font-face {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 400;
            font-display: swap;
        }
    </style>
    
    <script>
        // Theme initialization
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</body>
</html> 