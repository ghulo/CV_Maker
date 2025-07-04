<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormSubmission;
use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * Handle the contact form submission.
     */
    public function send(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            // Store the contact message in the database
            $contact = Contact::create($validated);

            // Send email notification
            Mail::to(config('mail.admin_email', 'admin@cvmaker.al'))->send(
                new ContactFormSubmission($contact)
            );

            // Return success response
            return back()->with('success', 'Mesazhi juaj u dërgua me sukses! Do t\'ju kontaktojmë së shpejti.');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Contact form submission failed: ' . $e->getMessage());

            // Return error response
            return back()
                ->withInput()
                ->with('error_message', 'Na vjen keq, por pati një problem me dërgimin e mesazhit tuaj. Ju lutemi provoni përsëri më vonë.');
        }
    }

    /**
     * Handle the contact form submission for API requests.
     */
    public function sendApi(Request $request)
    {
        // Validate the request
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        try {
            // Store the contact message in the database
            $contact = Contact::create($validated);

            // Send email notification (optional - comment out if no email setup)
            try {
                Mail::to(config('mail.admin_email', 'admin@cvmaker.al'))->send(
                    new ContactFormSubmission($contact)
                );
            } catch (\Exception $emailError) {
                // Log email error but don't fail the entire request
                \Log::warning('Contact form email failed: ' . $emailError->getMessage());
            }

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Mesazhi juaj u dërgua me sukses! Do t\'ju kontaktojmë së shpejti.',
                'contact_id' => $contact->id
            ], 201);

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Contact form submission failed: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Na vjen keq, por pati një problem me dërgimin e mesazhit tuaj. Ju lutemi provoni përsëri më vonë.'
            ], 500);
        }
    }
} 