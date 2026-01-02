<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booth;
use App\Models\Client;
use App\Models\Book;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index()
    {
        $stats = [
            'total_booths' => Booth::count(),
            'available_booths' => Booth::where('status', Booth::STATUS_AVAILABLE)->count(),
            'reserved_booths' => Booth::where('status', Booth::STATUS_RESERVED)->count(),
            'confirmed_booths' => Booth::where('status', Booth::STATUS_CONFIRMED)->count(),
            'paid_booths' => Booth::where('status', Booth::STATUS_PAID)->count(),
            'total_clients' => Client::count(),
            'total_users' => User::count(),
            'total_bookings' => Book::count(),
        ];

        $recentBookings = Book::with(['client', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.index', compact('stats', 'recentBookings'));
    }
}
