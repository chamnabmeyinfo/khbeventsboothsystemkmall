<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function __construct(
        private ClientService $clientService
    ) {}

    public function index(Request $request)
    {
        // If AJAX request for lazy loading
        if (($request->ajax() || $request->wantsJson() || $request->hasHeader('X-Requested-With')) && $request->has('page')) {
            return $this->lazyLoad($request);
        }

        $filters = [
            'search' => $request->input('search'),
            'company' => $request->input('company'),
            'sort_by' => $request->get('sort_by', 'company'),
            'sort_dir' => $request->get('sort_dir', 'asc'),
        ];

        $result = $this->clientService->getClients($filters, 20, 1);
        $clients = $result['clients'];
        $total = $result['total'];
        $sortBy = $result['sortBy'];
        $sortDir = $result['sortDir'];

        $stats = $this->clientService->getClientStatistics();
        $companies = $this->clientService->getUniqueCompanies();

        return view('clients.index', compact('clients', 'total', 'sortBy', 'sortDir', 'stats', 'companies'));
    }

    /**
     * Lazy load clients (AJAX endpoint)
     */
    public function lazyLoad(Request $request)
    {
        // Use exact same query structure as index method
        $query = Client::withCount(['booths', 'books']);

        // Search functionality (exact same as index)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        }

        // Filter by company (exact same as index)
        if ($request->filled('company')) {
            $query->where('company', 'like', "%{$request->company}%");
        }

        // Sort functionality (exact same as index)
        $sortBy = $request->get('sort_by', 'company');
        $sortDir = $request->get('sort_dir', 'asc');

        if (in_array($sortBy, ['company', 'name', 'position', 'phone_number'])) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('company', 'asc');
        }

        // Use same ordering and limit as initial load
        $page = $request->input('page', 1);
        $perPage = 20; // Same as initial load limit(20)
        $offset = ($page - 1) * $perPage;

        // Get total before pagination
        $total = $query->count();

        // Use exact same ordering as index method
        $clients = $query->offset($offset)->limit($perPage)->get();
        $hasMore = ($offset + $clients->count()) < $total;

        $html = '';
        foreach ($clients as $client) {
            // Ensure relationships are loaded (same as initial load)
            if (! $client->relationLoaded('booths')) {
                $client->load('booths');
            }
            if (! $client->relationLoaded('books')) {
                $client->load('books');
            }

            // Table row HTML - partial will calculate everything internally to match main view exactly
            $html .= view('clients.partials.table-row', compact('client'))->render();
        }

        return response()->json([
            'success' => true,
            'html' => $html,
            'hasMore' => $hasMore,
            'total' => $total,
            'loaded' => $offset + $clients->count(),
            'page' => $page,
            'perPage' => $perPage,
        ], 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(CreateClientRequest $request)
    {
        try {
            $validated = $request->validated();
            $result = $this->clientService->createClient($validated);
            $client = $result['client'];
            $isUpdate = $result['isUpdate'];

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
                    ],
                ], 200);
            }

            return redirect()->route('clients.index')
                ->with('success', $isUpdate ? 'Client updated successfully (existing email found).' : 'Client created successfully.');

        } catch (\Exception $e) {
            \Log::error('Client creation failed: '.$e->getMessage());

            // Return JSON if request expects JSON
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to create client: '.$e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create client. Please try again.']);
        }
    }

    public function show(Client $client)
    {
        // Load valid relationships only (books.booths is not a real relationship)
        $client->load(['booths', 'books.user']);

        // Manually load booths for each book since boothid is stored as JSON
        $client->books->each(function ($book) {
            $boothIds = json_decode($book->boothid, true) ?? [];
            if (! empty($boothIds)) {
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
        $recentBookings->each(function ($book) {
            $boothIds = json_decode($book->boothid, true) ?? [];
            if (! empty($boothIds)) {
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

    public function update(UpdateClientRequest $request, Client $client)
    {
        try {
            $validated = $request->validated();
            $this->clientService->updateClient($client, $validated);

            return redirect()->route('clients.index')
                ->with('success', 'Client updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return JSON if request expects JSON
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $e->errors(),
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors($e->errors());

        } catch (\Exception $e) {
            \Log::error('Client update failed: '.$e->getMessage());

            // Return JSON if request expects JSON
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update client: '.$e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update client. Please try again.']);
        }
    }

    public function destroy(Client $client)
    {
        try {
            $this->clientService->deleteClient($client);

            return redirect()->route('clients.index')
                ->with('success', 'Client deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Client deletion failed: '.$e->getMessage());

            return back()
                ->withErrors(['error' => 'Failed to delete client: '.$e->getMessage()]);
        }
    }

    /**
     * Search clients for AJAX requests (used in booking modal)
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('q', '');
            $clientId = $request->input('id', null);

            $results = $this->clientService->searchClients($query, $clientId);

            return response()->json($results);
        } catch (\Exception $e) {
            \Log::error('Client search error: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Search failed: '.$e->getMessage()], 500);
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
            $position = $validated['position'] ?? ($validated['x'].'% '.$validated['y'].'%');

            // Check if cover_position column exists, if not store in settings
            if (\Illuminate\Support\Facades\Schema::hasColumn('client', 'cover_position')) {
                $client->cover_position = $position;
                $client->save();
            } else {
                // Store in settings table as fallback
                \App\Models\Setting::setValue(
                    'client_'.$id.'_cover_position',
                    $position,
                    'string',
                    'Cover image position for client '.$id
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Cover position updated successfully.',
                'position' => $position,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating cover position: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating cover position: '.$e->getMessage(),
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
                        'duplicates' => [],
                    ];

                    foreach ($clientsToDelete as $clientToDelete) {
                        if (! $dryRun) {
                            // Merge data before deleting
                            $this->mergeClientData($keepClient, $clientToDelete);
                            $clientToDelete->delete();
                            $totalDeleted++;
                        }

                        $groupDetails['duplicates'][] = [
                            'id' => $clientToDelete->id,
                            'name' => $clientToDelete->name,
                        ];
                    }

                    if (! $dryRun) {
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
                        'would_delete' => $totalDeleted,
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Removed {$totalDeleted} duplicate clients across {$totalMerged} groups",
                'summary' => [
                    'duplicate_groups_merged' => $totalMerged,
                    'clients_deleted' => $totalDeleted,
                ],
                'details' => $details,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error removing duplicate clients: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error removing duplicates: '.$e->getMessage(),
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
            'address', 'tax_id', 'website', 'notes',
        ];

        $updated = false;
        foreach ($fieldsToMerge as $field) {
            // If kept client field is empty/null and duplicate has a value, use duplicate's value
            if (empty($keepClient->$field) && ! empty($duplicateClient->$field)) {
                $keepClient->$field = $duplicateClient->$field;
                $updated = true;
            }
        }

        if ($updated) {
            $keepClient->save();
        }
    }
}
