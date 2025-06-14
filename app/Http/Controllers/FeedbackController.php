<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    /**
     * Store a newly created feedback in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'feedback_subject' => 'required|string|max:255',
            'feedback_message' => 'required|string',
        ]);
        
        // Add submission timestamp
        $validatedData['submitted_at'] = now();

        try {
            // Create the feedback
            $feedback = new Feedback();
            $feedback->fill($validatedData);
            
            // Associate with user if logged in
            if (Auth::check()) {
                $feedback->user_id = Auth::id();
            }
            
            $feedback->save();

            return redirect()->back()->with('success_message', 'Faleminderit për mesazhin tuaj! Do t\'ju kontaktojmë së shpejti.');
        } catch (\Exception $e) {
            Log::error('Error saving feedback: ' . $e->getMessage());
            return redirect()->back()->with('error_message', 'Ndodhi një gabim. Ju lutemi provoni përsëri më vonë.')->withInput();
        }
    }
}
