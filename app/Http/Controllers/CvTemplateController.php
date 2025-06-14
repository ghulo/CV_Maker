<?php

namespace App\Http\Controllers;

use App\Models\CvTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CvTemplateController extends Controller
{
    /**
     * Display a listing of the templates.
     */
    public function index()
    {
        $templates = CvTemplate::where('is_active', true)->get();
        return view('templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        return view('templates.create');
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'style_config' => 'nullable|json',
            'color_schemes' => 'nullable|json',
            'thumbnail' => 'nullable|image|max:2048',
            'is_premium' => 'boolean',
        ]);

        // Generate slug from name
        $validated['slug'] = Str::slug($validated['name']);

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('templates', 'public');
        }

        CvTemplate::create($validated);

        return redirect()->route('templates.index')
            ->with('success', 'Template created successfully.');
    }

    /**
     * Display the specified template.
     */
    public function show(CvTemplate $template)
    {
        return view('templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(CvTemplate $template)
    {
        return view('templates.edit', compact('template'));
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, CvTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'style_config' => 'nullable|json',
            'color_schemes' => 'nullable|json',
            'thumbnail' => 'nullable|image|max:2048',
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Generate slug from name if name changed
        if ($template->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($template->thumbnail) {
                Storage::disk('public')->delete($template->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('templates', 'public');
        }

        $template->update($validated);

        return redirect()->route('templates.index')
            ->with('success', 'Template updated successfully.');
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(CvTemplate $template)
    {
        // Soft delete by marking as inactive instead of removing
        $template->update(['is_active' => false]);

        return redirect()->route('templates.index')
            ->with('success', 'Template removed successfully.');
    }

    /**
     * Preview the template
     */
    public function preview(CvTemplate $template)
    {
        return view('templates.preview', compact('template'));
    }

    /**
     * Display templates for selection
     */
    public function choose()
    {
        $templates = CvTemplate::where('is_active', true)->get();
        return view('templates.choose', compact('templates'));
    }
}
