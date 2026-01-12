<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Book;
use App\Models\Booth;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientPortalController extends Controller
{
    /**
     * Client login page
     */
    public function showLogin()
    {
        return view('client-portal.login');
    }

    /**
     * Client login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        // For now, use phone_number as identifier (simplified - in production, add proper authentication)
        $client = Client::where('phone_number', $request->email)
            ->orWhere('name', $request->email)
            ->first();

        if ($client) {
            // In production, verify password here
            session(['client_id' => $client->id]);
            return redirect()->route('client-portal.dashboard')
                ->with('success', 'Welcome back, ' . ($client->company ?? $client->name) . '!');
        }

        return back()->withErrors(['email' => 'Invalid credentials. Please contact administrator.']);
    }

    /**
     * Client dashboard
     */
    public function dashboard()
    {
        $clientId = session('client_id');
        if (!$clientId) {
            return redirect()->route('client-portal.login');
        }

        $client = Client::findOrFail($clientId);
        $bookings = Book::where('clientid', $clientId)->with('booths')->latest()->get();
        $payments = Payment::where('client_id', $clientId)->latest()->get();

        return view('client-portal.dashboard', compact('client', 'bookings', 'payments'));
    }

    /**
     * Client profile
     */
    public function profile()
    {
        $clientId = session('client_id');
        if (!$clientId) {
            return redirect()->route('client-portal.login');
        }

        $client = Client::findOrFail($clientId);
        return view('client-portal.profile', compact('client'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $clientId = session('client_id');
        $client = Client::findOrFail($clientId);

        $client->update($request->only(['name', 'company', 'position', 'phone_number']));

        return redirect()->route('client-portal.profile')
            ->with('success', 'Profile updated successfully');
    }

    /**
     * View booking details
     */
    public function booking($id)
    {
        $clientId = session('client_id');
        $booking = Book::where('clientid', $clientId)->findOrFail($id);
        
        return view('client-portal.booking', compact('booking'));
    }

    /**
     * Logout
     */
    public function logout()
    {
        session()->forget('client_id');
        return redirect()->route('client-portal.login');
    }
}

