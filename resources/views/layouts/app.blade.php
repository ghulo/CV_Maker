<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Critical Performance Optimizations -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    
    <!-- Preload critical fonts with priority -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'" crossorigin>
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700;800;900&display=swap"></noscript>
    
    <!-- Font Awesome fallback - Load immediately for critical icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Resource hints for faster loading - Vite will handle these automatically -->
    
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

    <!-- Critical CSS and JS with optimal loading -->
    @vite('resources/css/master.css')
    @vite('resources/js/app.js')
    
    <!-- Theme Color -->
    <meta name="theme-color" content="#5B21B6">
    
    <!-- Critical inline styles to prevent FOUC and CLS -->
    <style>
        /* Critical above-the-fold styles */
        html {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-display: swap;
            scroll-behavior: smooth;
        }
        
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            line-height: 1.5;
            font-synthesis: none;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: #ffffff;
            color: #111827;
        }
        
        * {
            box-sizing: border-box;
        }
        
        /* Performance optimized loading styles */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            backdrop-filter: blur(10px);
            transition: opacity 0.3s ease-out;
        }
        
        .loading-content {
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e2e8f0;
            border-top: 4px solid #5B21B6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        .loading-text {
            color: #4B5563;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            font-size: 16px;
            margin: 0;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Prevent layout shift */
        img {
            height: auto;
            max-width: 100%;
        }
        
        /* Critical Vue app container */
        #vue-app {
            min-height: 100vh;
            width: 100%;
        }
        
        /* Prevent text flash */
        .font-inter {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
    </style>
</head>
<body>
    <div id="vue-app">
        <!-- Optimized loading indicator -->
        <div id="loading-indicator" class="loading-container">
            <div class="loading-content">
                <div class="spinner"></div>
                <p class="loading-text">Loading ATELIER...</p>
            </div>
        </div>
        <!-- Vue.js will mount here -->
    </div>
    
    <script>
        // Ultra-optimized critical path
        (function() {
            'use strict';
            
            // Theme initialization (non-blocking)
            const theme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
            
            // Defer non-critical operations for better performance
            
            // Performance monitoring (non-blocking)
            if ('performance' in window && 'getEntriesByType' in performance) {
                addEventListener('load', () => {
                    requestAnimationFrame(() => {
                        const navigation = performance.getEntriesByType('navigation')[0];
                        if (navigation) {
                            console.log('🚀 Performance metrics:', {
                                LCP: Math.round(navigation.loadEventEnd - navigation.loadEventStart),
                                FCP: Math.round(navigation.responseEnd - navigation.responseStart),
                                TTI: Math.round(navigation.domInteractive - navigation.responseStart)
                            });
                        }
                    });
                });
            }
        })();
    </script>
</body>
</html> 