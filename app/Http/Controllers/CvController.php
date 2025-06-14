<?php

namespace App\Http\Controllers;

use App\Models\Cv; // Import the Cv model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Illuminate\Support\Facades\Session; // Import Session facade (for messages)
use Illuminate\Support\Facades\Log; // Import Log facade for error logging
use App\Services\CvService;
use App\Http\Requests\StoreCvRequest;
use App\Http\Requests\UpdateCvRequest;

class CvController extends Controller
{
    protected $cvService;

    public function __construct(CvService $cvService)
    {
        $this->cvService = $cvService;
    }

    /**
     * Display a listing of the user's CVs.
     */
    public function index()
    {
        $cvs = auth()->user()->cvs()->latest()->get();
        return view('cv.index', compact('cvs'));
    }

    /**
     * Show the template selection page
     */
    public function showTemplates()
    {
        $templates = [
            [
                'id' => 'classic',
                'name' => 'Klasik',
                'description' => 'Një dizajn i thjeshtë dhe profesional, i përshtatshëm për të gjitha industritë.',
                'preview' => asset('images/templates/classic-preview.jpg'),
                'category' => 'Standard'
            ],
            [
                'id' => 'modern',
                'name' => 'Modern',
                'description' => 'Një dizajn modern me hapësirë të gjerë dhe tipografi të pastër.',
                'preview' => asset('images/templates/modern-preview.jpg'),
                'category' => 'Modern'
            ],
            [
                'id' => 'creative',
                'name' => 'Kreativ',
                'description' => 'Për profesionistët kreativë që duan të dalin jashtë rrugës së zakonshme.',
                'preview' => asset('images/templates/creative-preview.jpg'),
                'category' => 'Kreativ'
            ]
        ];

        return view('templates.choose', [
            'templates' => $templates,
            'header_subtitle' => 'Zgjidhni një Model',
            'current_page' => 'templates.choose',
        ]);
    }

    /**
     * Show the form for creating a new CV.
     */
    public function create()
    {
        return view('cv.create');
    }

    /**
     * Store a newly created CV in storage.
     */
    public function store(StoreCvRequest $request)
    {
        $cv = $this->cvService->create($request->validated());
        
        return redirect()
            ->route('cv.preview', $cv)
            ->with('success', 'CV created successfully.');
    }

    /**
     * Show the form for editing the specified CV.
     */
    public function edit(Cv $cv)
    {
        $this->authorize('update', $cv);
        
        return view('cv.edit', compact('cv'));
    }

    /**
     * Update the specified CV in storage.
     */
    public function update(UpdateCvRequest $request, Cv $cv)
    {
        $this->authorize('update', $cv);
        
        $cv = $this->cvService->update($cv, $request->validated());
        
        return redirect()
            ->route('cv.preview', $cv)
            ->with('success', 'CV updated successfully.');
    }

    /**
     * Remove the specified CV from storage.
     */
    public function destroy(Cv $cv)
    {
        $this->authorize('delete', $cv);
        
        $this->cvService->delete($cv);
        
        return redirect()
            ->route('cv.index')
            ->with('success', 'CV deleted successfully.');
    }

    /**
     * Display the template selection page.
     */
    public function templates()
    {
        return redirect()->route('templates.index');
    }

    /**
     * Display the template choosing page.
     */
    public function chooseTemplate()
    {
        return view('cv.choose_template');
    }

    /**
     * Change the CV template.
     */
    public function changeTemplate(Request $request, $cv)
    {
        $request->validate([
            'template' => 'required|string|in:classic,modern,professional'
        ]);

        $cv = CV::findOrFail($cv);
        
        // Check if user owns the CV
        if ($cv->user_id !== auth()->id()) {
            return back()->with('error', 'You do not have permission to modify this CV.');
        }

        $cv->template = $request->template;
        $cv->save();

        return back()->with('success', 'CV template has been updated successfully.');
    }
}
    