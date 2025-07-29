<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{

    public function index()
{
    // Daily registrations (last 7 days)
    $daily = \App\Models\User::where('is_admin' , false)->selectRaw('DATE(created_at) as date, COUNT(*) as count')
        ->where('created_at', '>=', now()->subDays(7))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // Monthly registrations (last 6 months)
    $monthly = \App\Models\User::where('is_admin' , false)->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    // Yearly registrations (last 5 years)
    $yearly = \App\Models\User::where('is_admin' , false)->selectRaw('YEAR(created_at) as year, COUNT(*) as count')
        ->groupBy('year')
        ->orderBy('year')
        ->get();

    // Prepare data for Vue
    $stats = [
        'daily' => [
            'labels' => $daily->pluck('date'),
            'data' => $daily->pluck('count'),
        ],
        'monthly' => [
            'labels' => $monthly->pluck('month'),
            'data' => $monthly->pluck('count'),
        ],
        'yearly' => [
            'labels' => $yearly->pluck('year'),
            'data' => $yearly->pluck('count'),
        ],
    ];

    return Inertia::render('Admin/Dashboard', [
         'statsCount' => [
            'admins' => User::where('is_admin', true)->count(),
            'moderators' => User::role('User')->count(), 
            'posts' => Post::count(),
            'users' => User::where('is_admin' , false)->count(),
            ],
        'stats' => $stats,
    ]);
}


}
