<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CV;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get statistics
        $totalCvs = CV::where('user_id', $user->id)->count();
        $totalViews = CV::where('user_id', $user->id)->sum('views');
        $totalDownloads = CV::where('user_id', $user->id)->sum('downloads');
        
        // Get recent CVs
        $recentCvs = CV::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        return view('dashboard', compact('totalCvs', 'totalViews', 'totalDownloads', 'recentCvs'));
    }
} 