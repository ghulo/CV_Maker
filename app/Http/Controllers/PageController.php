<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Show the about page.
     */
    public function about()
    {
        return view('about');
    }

    /**
     * Show the contact page.
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Show the FAQ page.
     */
    public function faq()
    {
        return view('faq');
    }

    /**
     * Show the privacy policy page.
     */
    public function privacy()
    {
        return view('privacy-policy');
    }

    /**
     * Show the terms of service page.
     */
    public function terms()
    {
        return view('terms');
    }

    /**
     * Show the CV tips page.
     */
    public function tips()
    {
        // You might want to fetch tips from the database
        $tips = [
            [
                'title' => 'Përshtatni CV-në për çdo pozicion',
                'description' => 'Modifikoni CV-në tuaj për të përshtatur aftësitë dhe përvojën tuaj me kërkesat specifike të pozicionit.',
                'icon' => 'fa-bullseye'
            ],
            [
                'title' => 'Përdorni fjalë kyçe',
                'description' => 'Përfshini fjalë kyçe nga përshkrimi i punës për të kaluar sistemet e filtrimit të CV-ve.',
                'icon' => 'fa-key'
            ],
            [
                'title' => 'Theksoni arritjet',
                'description' => 'Përdorni numra dhe statistika për të demonstruar arritjet tuaja konkrete.',
                'icon' => 'fa-trophy'
            ],
            [
                'title' => 'Mbajeni të shkurtër',
                'description' => 'CV-ja juaj duhet të jetë e qartë dhe koncize, zakonisht jo më shumë se 2 faqe.',
                'icon' => 'fa-file-alt'
            ],
            [
                'title' => 'Kontrolloni gabimet',
                'description' => 'Sigurohuni që CV-ja juaj nuk ka gabime drejtshkrimore ose gramatikore.',
                'icon' => 'fa-check-double'
            ],
            [
                'title' => 'Formati profesional',
                'description' => 'Përdorni një format të qëndrueshëm dhe profesional me fonte të lexueshme.',
                'icon' => 'fa-paint-brush'
            ]
        ];

        return view('cv.tips', compact('tips'));
    }
} 