<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::withCount(['booths', 'books']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }
        
        // Filter by company
        if ($request->filled('company')) {
            $query->where('company', 'like', "%{$request->company}%");
        }
        
        // Sort functionality
        $sortBy = $request->get('sort_by', 'company');
        $sortDir = $request->get('sort_dir', 'asc');
        
        if (in_array($sortBy, ['company', 'name', 'position', 'phone_number'])) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('company', 'asc');
        }
        
        $clients = $query->paginate(20)->withQueryString();
        
        // Get statistics
        $stats = [
            'total_clients' => Client::count(),
            'clients_with_bookings' => Client::has('books')->count(),
            'clients_with_booths' => Client::has('booths')->count(),
            'total_bookings' => \App\Models\Book::count(),
        ];
        
        // Get unique companies for filter
        $companies = Client::whereNotNull('company')
            ->where('company', '!=', '')
            ->distinct()
            ->orderBy('company')
            ->pluck('company');
        
        return view('clients.index', compact('clients', 'sortBy', 'sortDir', 'stats', 'companies'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:45',
            'sex' => 'nullable|integer|in:1,2,3',
            'position' => 'nullable|string|max:191',
            'company' => 'required|string|max:191',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:191|unique:client,email',
            'address' => 'required|string',
            'tax_id' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
        ]);

        $client = Client::create($validated);

        // Return JSON if request expects JSON (for AJAX/modal requests)
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Client created successfully.',
                'client' => [
                    'id' => $client->id,
                    'name' => $client->name,
                    'company' => $client->company,
                    'email' => $client->email,
                    'phone_number' => $client->phone_number,
                    'address' => $client->address,
                    'position' => $client->position,
                ]
            ], 200);
        }

        return redirect()->route('clients.index')
            ->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        $client->load(['booths', 'books.user', 'books.booths']);
        
        // Calculate statistics
        $stats = [
            'total_booths' => $client->booths->count(),
            'total_bookings' => $client->books->count(),
            'paid_booths' => $client->booths->where('status', \App\Models\Booth::STATUS_PAID)->count(),
            'confirmed_booths' => $client->booths->where('status', \App\Models\Booth::STATUS_CONFIRMED)->count(),
            'reserved_booths' => $client->booths->where('status', \App\Models\Booth::STATUS_RESERVED)->count(),
        ];
        
        // Calculate total revenue from paid booths
        $totalRevenue = $client->booths->where('status', \App\Models\Booth::STATUS_PAID)->sum('price');
        $stats['total_revenue'] = $totalRevenue;
        
        // Get recent bookings
        $recentBookings = $client->books()->with('user')->latest('date_book')->take(10)->get();
        
        // Return JSON if request expects JSON (for API calls)
        if (request()->expectsJson() || request()->wantsJson()) {
            return response()->json([
                'id' => $client->id,
                'name' => $client->name,
                'sex' => $client->sex,
                'company' => $client->company,
                'position' => $client->position,
                'phone_number' => $client->phone_number,
                'email' => $client->email,
                'address' => $client->address,
                'tax_id' => $client->tax_id,
                'website' => $client->website,
                'notes' => $client->notes,
            ]);
        }
        
        return view('clients.show', compact('client', 'stats', 'recentBookings'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:45',
            'sex' => 'nullable|integer|in:1,2,3',
            'position' => 'nullable|string|max:191',
            'company' => 'required|string|max:191',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:191|unique:client,email,' . $client->id,
            'address' => 'required|string',
            'tax_id' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    /**
     * Search clients for AJAX requests (used in booking modal)
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        if (empty($query)) {
            return response()->json([]);
        }
        
        $clients = Client::where('name', 'like', "%{$query}%")
            ->orWhere('company', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone_number', 'like', "%{$query}%")
            ->orderBy('company')
            ->limit(10)
            ->get();
        
        $results = $clients->map(function ($client) {
            return [
                'id' => $client->id,
                'name' => $client->name,
                'company' => $client->company,
                'email' => $client->email,
                'phone_number' => $client->phone_number,
                'address' => $client->address,
                'position' => $client->position,
                'sex' => $client->sex,
                'tax_id' => $client->tax_id,
                'website' => $client->website,
                'notes' => $client->notes,
                'display_text' => ($client->company ?? $client->name) . 
                    ($client->email ? ' (' . $client->email . ')' : '') . 
                    ($client->phone_number ? ' | ' . $client->phone_number : '')
            ];
        });
        
        return response()->json($results);
    }
}

