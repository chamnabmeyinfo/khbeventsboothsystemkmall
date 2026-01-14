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
        // Helper function to check if value is truly empty
        $isEmpty = function($value) {
            return $value === null || $value === '' || (is_string($value) && trim($value) === '');
        };
        
        // Filter out empty string values and convert to null
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (is_string($value) && trim($value) === '') {
                $data[$key] = null;
            }
        }
        // Replace request data with cleaned data
        $request->replace($data);
        
        // Build validation rules - all fields optional
        $rules = [
            'name' => 'nullable|string|max:45',
            'sex' => 'nullable|integer|in:1,2,3',
            'position' => 'nullable|string|max:191',
            'company' => 'nullable|string|max:191',
            'company_name_khmer' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'phone_1' => 'nullable|string|max:20',
            'phone_2' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ];
        
        // Email validation - only validate email format and uniqueness if value is provided and not empty
        $email = $request->input('email');
        if (!$isEmpty($email)) {
            $rules['email'] = 'nullable|email|max:191|unique:client,email';
        } else {
            $rules['email'] = 'nullable|string|max:191';
        }
        
        // Email 1 and 2 - only validate email format if value is provided and not empty
        $email1 = $request->input('email_1');
        $email2 = $request->input('email_2');
        $rules['email_1'] = !$isEmpty($email1) ? 'nullable|email|max:191' : 'nullable|string|max:191';
        $rules['email_2'] = !$isEmpty($email2) ? 'nullable|email|max:191' : 'nullable|string|max:191';
        
        // Website - only validate URL format if value is provided and not empty
        $website = $request->input('website');
        $rules['website'] = !$isEmpty($website) ? 'nullable|url|max:255' : 'nullable|string|max:255';
        
        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors for debugging
            \Log::error('Client creation validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'rules' => $rules
            ]);
            
            // Return JSON error response for AJAX requests
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

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
        // Load valid relationships only (books.booths is not a real relationship)
        $client->load(['booths', 'books.user']);
        
        // Manually load booths for each book since boothid is stored as JSON
        $client->books->each(function($book) {
            $boothIds = json_decode($book->boothid, true) ?? [];
            if (!empty($boothIds)) {
                $book->setRelation('booths', \App\Models\Booth::whereIn('id', $boothIds)->get());
            } else {
                $book->setRelation('booths', collect([]));
            }
        });
        
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
        
        // Get recent bookings with user relationship
        $recentBookings = $client->books()->with('user')->latest('date_book')->take(10)->get();
        
        // Manually load booths for recent bookings
        $recentBookings->each(function($book) {
            $boothIds = json_decode($book->boothid, true) ?? [];
            if (!empty($boothIds)) {
                $book->setRelation('booths', \App\Models\Booth::whereIn('id', $boothIds)->with('category')->get());
            } else {
                $book->setRelation('booths', collect([]));
            }
        });
        
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
        // Helper function to check if value is truly empty
        $isEmpty = function($value) {
            return $value === null || $value === '' || (is_string($value) && trim($value) === '');
        };
        
        // Filter out empty string values and convert to null
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (is_string($value) && trim($value) === '') {
                $data[$key] = null;
            }
        }
        // Replace request data with cleaned data
        $request->replace($data);
        
        // Build validation rules - all fields optional
        $rules = [
            'name' => 'nullable|string|max:45',
            'sex' => 'nullable|integer|in:1,2,3',
            'position' => 'nullable|string|max:191',
            'company' => 'nullable|string|max:191',
            'company_name_khmer' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'phone_1' => 'nullable|string|max:20',
            'phone_2' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ];
        
        // Email validation - only validate email format and uniqueness if value is provided and not empty
        $email = $request->input('email');
        if (!$isEmpty($email)) {
            $rules['email'] = 'nullable|email|max:191|unique:client,email,' . $client->id;
        } else {
            $rules['email'] = 'nullable|string|max:191';
        }
        
        // Email 1 and 2 - only validate email format if value is provided and not empty
        $email1 = $request->input('email_1');
        $email2 = $request->input('email_2');
        $rules['email_1'] = !$isEmpty($email1) ? 'nullable|email|max:191' : 'nullable|string|max:191';
        $rules['email_2'] = !$isEmpty($email2) ? 'nullable|email|max:191' : 'nullable|string|max:191';
        
        // Website - only validate URL format if value is provided and not empty
        $website = $request->input('website');
        $rules['website'] = !$isEmpty($website) ? 'nullable|url|max:255' : 'nullable|string|max:255';
        
        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return JSON error response for AJAX requests
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

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
        try {
            $query = $request->input('q', '');
            $clientId = $request->input('id', null);
            
            // If specific ID is requested, return that client
            if ($clientId) {
                $client = Client::find($clientId);
                if ($client) {
                    return response()->json([[
                        'id' => $client->id,
                        'name' => $client->name ?? '',
                        'company' => $client->company ?? '',
                        'email' => property_exists($client, 'email') ? ($client->email ?? null) : null,
                        'phone_number' => $client->phone_number ?? '',
                        'address' => property_exists($client, 'address') ? ($client->address ?? null) : null,
                        'position' => $client->position ?? '',
                        'sex' => $client->sex ?? null,
                        'tax_id' => property_exists($client, 'tax_id') ? ($client->tax_id ?? null) : null,
                        'website' => property_exists($client, 'website') ? ($client->website ?? null) : null,
                        'notes' => property_exists($client, 'notes') ? ($client->notes ?? null) : null,
                        'display_text' => ($client->company ?? $client->name ?? 'N/A') . 
                            (property_exists($client, 'email') && $client->email ? ' (' . $client->email . ')' : '') . 
                            ($client->phone_number ? ' | ' . $client->phone_number : '')
                    ]]);
                }
                return response()->json([]);
            }
            
            if (empty($query)) {
                return response()->json([]);
            }
            
            // Check which columns exist in the database
            $columns = \DB::select("SHOW COLUMNS FROM `client`");
            $columnNames = array_map(function($col) {
                return $col->Field;
            }, $columns);
            
            // Build search query with only existing columns
            $clients = Client::where(function($q) use ($query, $columnNames) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('company', 'like', "%{$query}%")
                      ->orWhere('phone_number', 'like', "%{$query}%");
                    
                    // Add email search only if column exists
                    if (in_array('email', $columnNames)) {
                        $q->orWhere('email', 'like', "%{$query}%");
                    }
                });
            
            // Order by company (handle NULLs) then by name
            $clients = $clients->orderByRaw('CASE WHEN company IS NULL OR company = "" THEN 1 ELSE 0 END')
                ->orderBy('company')
                ->orderBy('name')
                ->limit(20)
                ->get();
            
            $results = $clients->map(function ($client) use ($columnNames) {
                return [
                    'id' => $client->id,
                    'name' => $client->name ?? '',
                    'company' => $client->company ?? '',
                    'email' => in_array('email', $columnNames) ? ($client->email ?? null) : null,
                    'phone_number' => $client->phone_number ?? '',
                    'address' => in_array('address', $columnNames) ? ($client->address ?? null) : null,
                    'position' => $client->position ?? '',
                    'sex' => $client->sex ?? null,
                    'tax_id' => in_array('tax_id', $columnNames) ? ($client->tax_id ?? null) : null,
                    'website' => in_array('website', $columnNames) ? ($client->website ?? null) : null,
                    'notes' => in_array('notes', $columnNames) ? ($client->notes ?? null) : null,
                    'display_text' => ($client->company ?? $client->name ?? 'N/A') . 
                        (in_array('email', $columnNames) && isset($client->email) && $client->email ? ' (' . $client->email . ')' : '') . 
                        ($client->phone_number ? ' | ' . $client->phone_number : '')
                ];
            });
            
            return response()->json($results->values()->all());
        } catch (\Exception $e) {
            \Log::error('Client search error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Search failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update cover image position
     */
    public function updateCoverPosition(Request $request, $id)
    {
        try {
            $client = Client::findOrFail($id);
            
            $validated = $request->validate([
                'x' => 'required|numeric|min:0|max:100',
                'y' => 'required|numeric|min:0|max:100',
                'position' => 'nullable|string|max:50',
            ]);
            
            // Store position as "x% y%" format
            $position = $validated['position'] ?? ($validated['x'] . '% ' . $validated['y'] . '%');
            
            // Check if cover_position column exists, if not store in settings
            if (\Illuminate\Support\Facades\Schema::hasColumn('client', 'cover_position')) {
                $client->cover_position = $position;
                $client->save();
            } else {
                // Store in settings table as fallback
                \App\Models\Setting::setValue(
                    'client_' . $id . '_cover_position',
                    $position,
                    'string',
                    'Cover image position for client ' . $id
                );
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Cover position updated successfully.',
                'position' => $position
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating cover position: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating cover position: ' . $e->getMessage()
            ], 500);
        }
    }
}

