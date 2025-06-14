{{-- resources/views/templates/select.blade.php --}}
@extends('layouts.app')

@section('title', 'CV Maker - Zgjidh Modelin')

@section('content')
    <main class="main">
        <div class="template-selection-page-container page-container animate-in">
            <h2 class="reveal-on-scroll">Zgjidhni Modelin për CV-në Tuaj</h2>
            <p class="page-intro-text reveal-on-scroll" data-reveal-delay="100">Çdo model ofron një pamje unike. Klikoni mbi një model për të filluar plotësimin e të dhënave tuaja.</p>

            <div class="message-area">
                @if (session('error_message_template_choice'))<p class="message error" role="alert">{{ session('error_message_template_choice') }}</p>@endif
                @if (session('info_message'))<p class="message info" role="status">{{ session('info_message') }}</p>@endif
            </div>

            <div class="template-options-grid">
                {{-- Ensure the route 'cv.create' expects a 'template' parameter --}}
                <a href="{{ route('cv.create', ['template' => 'classic']) }}" class="template-option-card reveal-on-scroll" data-reveal-delay="150" data-template="classic">
                    <div class="template-thumbnail">
                        <div class="thumb-header">EMRI MBIEMRI</div>
                        <div class="thumb-line bg-muted"></div>
                        <div class="thumb-line bg-primary w-80"></div>
                        <div class="thumb-line bg-muted w-90"></div>
                        <div class="thumb-line bg-primary w-70"></div>
                        <div class="thumb-line bg-muted w-full"></div>
                    </div>
                    <span class="template-name">Modeli Klasik</span>
                    <p class="template-description">Një dizajn tradicional dhe i qartë, ideal për shumicën e profesioneve.</p>
                </a>

                <a href="{{ route('cv.create', ['template' => 'modern']) }}" class="template-option-card reveal-on-scroll" data-reveal-delay="200" data-template="modern">
                    <div class="template-thumbnail">
                        <div class="thumb-modern-layout">
                            <div class="thumb-sidebar">
                                <div class="thumb-avatar"></div>
                                <div class="thumb-line bg-primary w-70 mt-sm"></div>
                                <div class="thumb-line bg-muted w-90"></div>
                            </div>
                            <div class="thumb-main-content">
                                <div class="thumb-header-sm">EMRI MBIEMRI</div>
                                <div class="thumb-line bg-muted w-60"></div>
                                <div class="thumb-line bg-primary w-full mt-sm"></div>
                                <div class="thumb-line bg-muted w-80"></div>
                            </div>
                        </div>
                    </div>
                    <span class="template-name">Modeli Modern</span>
                    <p class="template-description">Një pamje bashkëkohore me fokus në dizajn dhe lexueshmëri.</p>
                </a>

                <a href="{{ route('cv.create', ['template' => 'professional']) }}" class="template-option-card reveal-on-scroll" data-reveal-delay="250" data-template="professional">
                    <div class="template-thumbnail">
                         <div class="thumb-header-alt">EMRI MBIEMRI</div>
                         <div class="thumb-line bg-primary w-50 mx-auto mb-sm"></div>
                         <div class="thumb-modern-layout">
                            <div class="thumb-sidebar-alt">
                                <div class="thumb-line bg-muted w-full"></div>
                                <div class="thumb-line bg-muted w-80"></div>
                            </div>
                            <div class="thumb-main-content-alt">
                                <div class="thumb-line bg-primary w-full"></div>
                                <div class="thumb-line bg-muted w-90"></div>
                                <div class="thumb-line bg-muted w-full"></div>
                            </div>
                        </div>
                    </div>
                    <span class="template-name">Modeli Profesional</span>
                    <p class="template-description">Elegant dhe i strukturuar, perfekt për role ekzekutive dhe korporative.</p>
                </a>
            </div>

            <div class="page-actions" style="margin-top: var(--space-xl);">
                @auth {{-- Show link to My CVs if logged in --}}
                    <a href="{{ route('my-cvs.index') }}" class="btn btn-secondary reveal-on-scroll" data-reveal-delay="300"><i class="fas fa-arrow-left"></i> Kthehu te CV-të e Mia</a>
                @else {{-- Show login/signup prompt if not logged in. Uses standard auth routes now. --}}
                     <p class="form-alternate-action reveal-on-scroll" data-reveal-delay="300">Keni një llogari? <a href="{{ route('login') }}">Kyçu</a> për të ruajtur dhe menaxhuar CV-të.</p>
                @endauth
            </div>
        </div>
    </main>
@endsection
