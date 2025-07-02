<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use App\Models\UserProfile;
use App\Models\UserActivity;
use Carbon\Carbon;
use ZipArchive;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('profile');
        
        // Merge profile data with user data
        $userData = $user->toArray();
        if ($user->profile) {
            $userData = array_merge($userData, $user->profile->toArray());
        }
        
        return response()->json([
            'success' => true,
            'user' => $userData,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
        ]);

        $user = $request->user();
        
        // Update user basic info
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        $user->save();

        // Update or create profile
        $profileData = $request->only(['phone', 'location', 'bio']);
        if (!empty(array_filter($profileData))) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                array_filter($profileData) // Only save non-null values
            );
        }

        // Log activity
        UserActivity::log(
            $user->id,
            UserActivity::TYPE_PROFILE_UPDATED,
            'Profile Updated',
            'You updated your profile information',
            [
                'fields_updated' => array_keys(array_filter($profileData)),
                'name_changed' => $request->has('name'),
                'email_changed' => $request->has('email'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user' => $user->load('profile'),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 422);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Log activity
        UserActivity::log(
            $user->id,
            UserActivity::TYPE_PASSWORD_UPDATED,
            'Password Updated',
            'You changed your account password'
        );

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully',
        ]);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();

        // Delete old avatar if exists
        if ($user->profile && $user->profile->avatar) {
            Storage::disk('public')->delete($user->profile->avatar);
        }

        // Store new avatar
        $avatarPath = $request->file('avatar')->store('avatars', 'public');

        // Update or create profile with avatar
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['avatar' => $avatarPath]
        );

        // Log activity
        UserActivity::log(
            $user->id,
            UserActivity::TYPE_AVATAR_UPDATED,
            'Avatar Updated',
            'You updated your profile picture',
            ['avatar_path' => $avatarPath]
        );

        return response()->json([
            'success' => true,
            'message' => 'Avatar updated successfully',
            'avatar_url' => Storage::url($avatarPath),
        ]);
    }

    public function getUserCvs(Request $request)
    {
        $user = $request->user();
        $cvs = $user->cvs()->orderBy('updated_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'cvs' => $cvs,
        ]);
    }

    public function getUserStats(Request $request)
    {
        $user = $request->user();
        
        $stats = [
            'cvs_created' => $user->cvs()->count(),
            'total_downloads' => $user->cvs()->sum('downloads'),
            'total_views' => $user->cvs()->sum('views'),
            'achievements' => $this->calculateAchievements($user),
            'member_since' => $user->created_at->format('Y-m-d'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    public function getUserActivity(Request $request)
    {
        $user = $request->user();
        
        // Get recent activities from database
        $activities = UserActivity::getForUser($user->id, 15);
        
        // If no activities exist, create an initial account created activity
        if ($activities->isEmpty()) {
            UserActivity::log(
                $user->id,
                UserActivity::TYPE_ACCOUNT_CREATED,
                'Account Created',
                'Welcome to CV Maker! Your account was created successfully.',
                ['registration_date' => $user->created_at]
            );
            
            // Fetch activities again
            $activities = UserActivity::getForUser($user->id, 15);
        }

        return response()->json([
            'success' => true,
            'activity' => $activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'description' => $activity->description,
                    'icon' => $activity->icon,
                    'type' => $activity->type,
                    'metadata' => $activity->metadata,
                    'created_at' => $activity->created_at->toISOString(),
                ];
            }),
        ]);
    }

    public function exportData(Request $request)
    {
        $user = $request->user()->load(['profile', 'cvs']);
        
        $exportData = [
            'user_info' => [
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'profile' => $user->profile ? $user->profile->toArray() : null,
            ],
            'cvs' => $user->cvs->toArray(),
            'exported_at' => now(),
        ];

        $fileName = 'profile-data-' . $user->id . '-' . now()->format('Y-m-d') . '.json';
        
        // Log activity
        UserActivity::log(
            $user->id,
            UserActivity::TYPE_EXPORT_DATA,
            'Data Export',
            'Exported profile and CV data',
            [
                'file_name' => $fileName,
                'cvs_count' => $user->cvs->count(),
                'export_size' => strlen(json_encode($exportData))
            ]
        );
        
        return response()->json($exportData)
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Content-Type', 'application/json');
    }

    public function downloadAllCvs(Request $request)
    {
        $user = $request->user();
        $cvs = $user->cvs;

        if ($cvs->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No CVs found to download',
            ], 404);
        }

        // Create a temporary zip file
        $zipFileName = 'all-cvs-' . $user->id . '-' . now()->format('Y-m-d') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        // Ensure temp directory exists
        if (!file_exists(dirname($zipPath))) {
            mkdir(dirname($zipPath), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach ($cvs as $cv) {
                $cvData = [
                    'title' => $cv->cv_title,
                    'personal_info' => [
                        'name' => $cv->emri . ' ' . $cv->mbiemri,
                        'email' => $cv->email,
                        'phone' => $cv->telefoni,
                        'address' => $cv->address,
                    ],
                    'summary' => $cv->summary,
                    'created_at' => $cv->created_at,
                    'updated_at' => $cv->updated_at,
                ];
                
                $cvFileName = 'cv-' . $cv->id . '-' . str_replace(' ', '-', $cv->cv_title) . '.json';
                $zip->addFromString($cvFileName, json_encode($cvData, JSON_PRETTY_PRINT));
            }
            $zip->close();
        }

        // Log activity
        UserActivity::log(
            $user->id,
            UserActivity::TYPE_BULK_DOWNLOAD,
            'Bulk Download',
            'Downloaded all CVs as ZIP file',
            [
                'cvs_count' => $cvs->count(),
                'file_name' => $zipFileName,
                'file_size' => filesize($zipPath)
            ]
        );

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function destroy(Request $request)
    {
        try {
            \Log::info('User delete request received', [
                'user_id' => $request->user()?->id,
                'request_headers' => $request->headers->all(),
                'request_ip' => $request->ip()
            ]);

            $user = $request->user();
            
            if (!$user) {
                \Log::error('No authenticated user found for delete request');
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                ], 401);
            }

            \Log::info('Starting user deletion process', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'has_profile' => !!$user->profile,
                'cvs_count' => $user->cvs()->count()
            ]);

            // Delete user's avatar if exists
            if ($user->profile && $user->profile->avatar) {
                \Log::info('Deleting user avatar', ['avatar_path' => $user->profile->avatar]);
                Storage::disk('public')->delete($user->profile->avatar);
            }
            
            // Delete all user tokens
            \Log::info('Deleting user tokens', ['tokens_count' => $user->tokens()->count()]);
            $user->tokens()->delete();
            
            // Delete user (this will cascade delete profile and CVs due to foreign key constraints)
            \Log::info('Deleting user record', ['user_id' => $user->id]);
            $user->delete();

            \Log::info('User deletion completed successfully', ['deleted_user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting user account', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $request->user()?->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function calculateAchievements($user)
    {
        $achievements = 0;
        $cvsCount = $user->cvs()->count();
        
        // Achievement criteria
        if ($cvsCount >= 1) $achievements++; // First CV
        if ($cvsCount >= 5) $achievements++; // CV Expert
        if ($user->profile && $user->profile->bio) $achievements++; // Profile Complete
        if ($user->cvs()->sum('downloads') >= 10) $achievements++; // Download Master
        
        return $achievements;
    }


}
