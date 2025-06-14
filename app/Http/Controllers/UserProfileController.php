<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show()
    {
        $profile = auth()->user()->profile ?? auth()->user()->profile()->create();
        return view('profile.show', compact('profile'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $profile = auth()->user()->profile ?? auth()->user()->profile()->create();
        return view('profile.edit', compact('profile'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $profile = $user->profile ?? $user->profile()->create();

        $validated = $request->validate([
            'headline' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'avatar' => 'nullable|image|max:2048',
            'social_links' => 'nullable|array',
            'social_links.*' => 'nullable|url',
            'portfolio_links' => 'nullable|array',
            'portfolio_links.*' => 'nullable|url',
            'skills' => 'nullable|array',
            'skills.*' => 'string',
            'interests' => 'nullable|array',
            'interests.*' => 'string',
            'languages' => 'nullable|array',
            'languages.*' => 'string',
            'is_public' => 'boolean',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $profile->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Remove the user's avatar.
     */
    public function removeAvatar()
    {
        $profile = auth()->user()->profile;

        if ($profile && $profile->avatar) {
            Storage::disk('public')->delete($profile->avatar);
            $profile->update(['avatar' => null]);
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Avatar removed successfully.');
    }

    /**
     * Add a social link to the user's profile.
     */
    public function addSocialLink(Request $request)
    {
        $validated = $request->validate([
            'platform' => 'required|string',
            'url' => 'required|url',
        ]);

        $profile = auth()->user()->profile;
        $socialLinks = $profile->social_links ?? [];
        $socialLinks[$validated['platform']] = $validated['url'];
        
        $profile->update(['social_links' => $socialLinks]);

        return response()->json([
            'message' => 'Social link added successfully',
            'social_links' => $profile->social_links_formatted,
        ]);
    }

    /**
     * Remove a social link from the user's profile.
     */
    public function removeSocialLink(Request $request)
    {
        $validated = $request->validate([
            'platform' => 'required|string',
        ]);

        $profile = auth()->user()->profile;
        $socialLinks = $profile->social_links ?? [];
        unset($socialLinks[$validated['platform']]);
        
        $profile->update(['social_links' => $socialLinks]);

        return response()->json([
            'message' => 'Social link removed successfully',
            'social_links' => $profile->social_links_formatted,
        ]);
    }

    /**
     * Add a portfolio link to the user's profile.
     */
    public function addPortfolioLink(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string',
        ]);

        $profile = auth()->user()->profile;
        $portfolioLinks = $profile->portfolio_links ?? [];
        $portfolioLinks[] = $validated;
        
        $profile->update(['portfolio_links' => $portfolioLinks]);

        return response()->json([
            'message' => 'Portfolio link added successfully',
            'portfolio_links' => $profile->portfolio_links,
        ]);
    }

    /**
     * Remove a portfolio link from the user's profile.
     */
    public function removePortfolioLink(Request $request)
    {
        $validated = $request->validate([
            'index' => 'required|integer|min:0',
        ]);

        $profile = auth()->user()->profile;
        $portfolioLinks = $profile->portfolio_links ?? [];
        
        if (isset($portfolioLinks[$validated['index']])) {
            array_splice($portfolioLinks, $validated['index'], 1);
            $profile->update(['portfolio_links' => $portfolioLinks]);
        }

        return response()->json([
            'message' => 'Portfolio link removed successfully',
            'portfolio_links' => $profile->portfolio_links,
        ]);
    }
}
