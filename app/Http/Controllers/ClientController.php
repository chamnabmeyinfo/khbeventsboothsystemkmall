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
            if ($value === null) return true;
            if ($value === '') return true;
            if (is_string($value) && trim($value) === '') return true;
            return false;
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
        
        // Build validation rules - all fields optional, use present() to conditionally validate
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
        
        // Add email and website fields - make them all nullable with no format validation initially
        // We'll validate format manually after basic validation passes
        $rules['email'] = 'nullable|string|max:191';
        $rules['email_1'] = 'nullable|string|max:191';
        $rules['email_2'] = 'nullable|string|max:191';
        $rules['website'] = 'nullable|string|max:255';
        
        try {
            $validated = $request->validate($rules);
            
            // Clean validated data - ensure null values are properly set
            foreach ($validated as $key => $value) {
                if ($isEmpty($value)) {
                    $validated[$key] = null;
                }
            }
            
            // Manual format validation for non-empty emails and URLs
            if (!empty($validated['email']) && !filter_var($validated['email'], FILTER_VALIDATE_EMAIL)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => ['The email must be a valid email address.']
                ]);
            }
            
            if (!empty($validated['email_1']) && !filter_var($validated['email_1'], FILTER_VALIDATE_EMAIL)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email_1' => ['The email 1 must be a valid email address.']
                ]);
            }
            
            if (!empty($validated['email_2']) && !filter_var($validated['email_2'], FILTER_VALIDATE_EMAIL)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email_2' => ['The email 2 must be a valid email address.']
                ]);
            }
            
            if (!empty($validated['website']) && !filter_var($validated['website'], FILTER_VALIDATE_URL)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'website' => ['The website must be a valid URL.']
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors for debugging
            \Log::error('Client creation validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'rules' => $rules,
                'cleaned_data' => $data
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

        // Check if client with same email exists - if so, update it instead of creating new one
        $client = null;
        $isUpdate = false;
        
        if (!empty($validated['email']) && $validated['email'] !== null) {
            $existing = Client::where('email', $validated['email'])->first();
            if ($existing) {
                // Update existing client - overwrite with new data
                $existing->update($validated);
                $client = $existing;
                $isUpdate = true;
            }
        }
        
        // If no existing client found, create new one
        if (!$client) {
            $client = Client::create($validated);
        }

        // Send notification about client action
        try {
            \App\Services\NotificationService::notifyClientAction($isUpdate ? 'updated' : 'created', $client, auth()->id());
        } catch (\Exception $e) {
            \Log::error('Failed to send client notification: ' . $e->getMessage());
        }

        // Return JSON if request expects JSON (for AJAX/modal requests)
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $isUpdate ? 'Client updated successfully (existing email found).' : 'Client created successfully.',
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
            ->with('success', $isUpdate ? 'Client updated successfully (existing email found).' : 'Client created successfully.');
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
        
        // Add email and website fields with conditional validation
        // Check if values exist before adding format validation
        $email = $request->input('email');
        $email1 = $request->input('email_1');
        $email2 = $request->input('email_2');
        $website = $request->input('website');
        
        // Add email and website fields - make them all nullable with no format validation initially
        // We'll validate format manually after basic validation passes
        $rules['email'] = 'nullable|string|max:191';
        $rules['email_1'] = 'nullable|string|max:191';
        $rules['email_2'] = 'nullable|string|max:191';
        $rules['website'] = 'nullable|string|max:255';
        
        try {
            $validated = $request->validate($rules);
            
            // Clean validated data - ensure null values are properly set
            foreach ($validated as $key => $value) {
                if ($isEmpty($value)) {
                    $validated[$key] = null;
                }
            }
            
            // Manual format validation for non-empty emails and URLs
            if (!empty($validated['email']) && !filter_var($validated['email'], FILTER_VALIDATE_EMAIL)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => ['The email must be a valid email address.']
                ]);
            }
            
            // Check email uniqueness if email is provided (excluding current client, only check if email is not null)
            if (!empty($validated['email']) && $validated['email'] !== null) {
                $existing = Client::where('email', $validated['email'])->where('id', '!=', $client->id)->first();
                if ($existing) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'email' => ['The email "' . $validated['email'] . '" has already been taken. Please use a different email or update the existing client.']
                    ]);
                }
            }
            
            if (!empty($validated['email_1']) && !filter_var($validated['email_1'], FILTER_VALIDATE_EMAIL)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email_1' => ['The email 1 must be a valid email address.']
                ]);
            }
            
            if (!empty($validated['email_2']) && !filter_var($validated['email_2'], FILTER_VALIDATE_EMAIL)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email_2' => ['The email 2 must be a valid email address.']
                ]);
            }
            
            if (!empty($validated['website']) && !filter_var($validated['website'], FILTER_VALIDATE_URL)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'website' => ['The website must be a valid URL.']
                ]);
            }
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

        // Send notification about client update
        try {
            \App\Services\NotificationService::notifyClientAction('updated', $client, auth()->id());
        } catch (\Exception $e) {
            \Log::error('Failed to send client update notification: ' . $e->getMessage());
        }

        return redirect()->route('clients.index')
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        // Send notification about client deletion before deleting
        try {
            \App\Services\NotificationService::notifyClientAction('deleted', $client, auth()->id());
        } catch (\Exception $e) {
            \Log::error('Failed to send client deletion notification: ' . $e->getMessage());
        }

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
            
            // Check which columns exist in the database
            $columns = \DB::select("SHOW COLUMNS FROM `client`");
            $columnNames = array_map(function($col) {
                return $col->Field;
            }, $columns);
            
            // Build query - if query is empty, load all clients (at least 150)
            if (empty($query)) {
                // Load all clients, ordered by company then name
                $clients = Client::orderByRaw('CASE WHEN company IS NULL OR company = "" THEN 1 ELSE 0 END')
                    ->orderBy('company')
                    ->orderBy('name')
                    ->limit(150)
                    ->get();
            } else {
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
                    ->limit(150) // Increased limit for search results too
                    ->get();
            }
            
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

    /**
     * Remove duplicate clients based on phone_2, email_1, or email_2
     */
    public function removeDuplicates(Request $request)
    {
        try {
            $field = $request->input('field', 'all'); // all, phone_2, email_1, email_2
            $dryRun = $request->input('dry_run', false);
            $keepOldest = $request->input('keep_oldest', false);

            $fieldsToCheck = [];
            if ($field === 'all') {
                $fieldsToCheck = ['phone_2', 'email_1', 'email_2'];
            } else {
                $fieldsToCheck = [$field];
            }

            $totalDeleted = 0;
            $totalMerged = 0;
            $details = [];

            foreach ($fieldsToCheck as $checkField) {
                // Find duplicates
                $duplicates = \DB::table('client')
                    ->select($checkField, \DB::raw('COUNT(*) as count'))
                    ->whereNotNull($checkField)
                    ->where($checkField, '!=', '')
                    ->groupBy($checkField)
                    ->having('count', '>', 1)
                    ->get();

                if ($duplicates->isEmpty()) {
                    continue;
                }

                foreach ($duplicates as $duplicate) {
                    $value = $duplicate->$checkField;
                    $count = $duplicate->count;

                    // Get all clients with this duplicate value
                    $clients = Client::where($checkField, $value)
                        ->orderBy('id', $keepOldest ? 'asc' : 'desc')
                        ->get();

                    // Keep the first one
                    $keepClient = $clients->first();
                    $clientsToDelete = $clients->skip(1);

                    $groupDetails = [
                        'field' => $checkField,
                        'value' => $value,
                        'keep_client_id' => $keepClient->id,
                        'keep_client_name' => $keepClient->name,
                        'duplicates' => []
                    ];

                    foreach ($clientsToDelete as $clientToDelete) {
                        if (!$dryRun) {
                            // Merge data before deleting
                            $this->mergeClientData($keepClient, $clientToDelete);
                            $clientToDelete->delete();
                            $totalDeleted++;
                        }
                        
                        $groupDetails['duplicates'][] = [
                            'id' => $clientToDelete->id,
                            'name' => $clientToDelete->name
                        ];
                    }

                    if (!$dryRun) {
                        $totalMerged++;
                    }
                    
                    $details[] = $groupDetails;
                }
            }

            if ($dryRun) {
                return response()->json([
                    'success' => true,
                    'dry_run' => true,
                    'message' => 'Preview mode - No changes made',
                    'details' => $details,
                    'summary' => [
                        'duplicate_groups' => count($details),
                        'would_delete' => $totalDeleted
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Removed {$totalDeleted} duplicate clients across {$totalMerged} groups",
                'summary' => [
                    'duplicate_groups_merged' => $totalMerged,
                    'clients_deleted' => $totalDeleted
                ],
                'details' => $details
            ]);

        } catch (\Exception $e) {
            \Log::error('Error removing duplicate clients: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error removing duplicates: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Merge data from duplicate client into the kept client
     */
    private function mergeClientData(Client $keepClient, Client $duplicateClient)
    {
        $fieldsToMerge = [
            'name', 'sex', 'position', 'company', 'company_name_khmer',
            'phone_number', 'phone_1', 'phone_2',
            'email', 'email_1', 'email_2',
            'address', 'tax_id', 'website', 'notes'
        ];

        $updated = false;
        foreach ($fieldsToMerge as $field) {
            // If kept client field is empty/null and duplicate has a value, use duplicate's value
            if (empty($keepClient->$field) && !empty($duplicateClient->$field)) {
                $keepClient->$field = $duplicateClient->$field;
                $updated = true;
            }
        }

        if ($updated) {
            $keepClient->save();
        }
    }
}

