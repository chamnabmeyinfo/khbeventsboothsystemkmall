<?php

namespace App\Http\Controllers;

use App\Helpers\AssetHelper;
use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Book;
use App\Models\Client;
use App\Services\ClientService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class ClientController extends Controller
{
    public function __construct(
        private ClientService $clientService
    ) {}

    public function index(Request $request)
    {
        $allowedPerPage = [10, 20, 50];
        $perPage = (int) $request->input('per_page', 20);
        if (! in_array($perPage, $allowedPerPage, true)) {
            $perPage = 20;
        }

        $filterInput = $request->input('filter', []);
        if (! is_array($filterInput)) {
            $filterInput = [];
        }
        if ($request->filled('company') && empty($filterInput['company'])) {
            $filterInput['company'] = $request->input('company');
        }

        $filters = [
            'search' => $request->input('search'),
            'filter' => $filterInput,
            'sort_by' => $request->get('sort_by', 'company'),
            'sort_dir' => $request->get('sort_dir', 'asc'),
        ];

        $clients = $this->clientService->paginateClients($filters, $perPage);
        $resolved = $this->clientService->resolveClientListSort($filters);
        $sortBy = $resolved['sortBy'];
        $sortDir = $resolved['sortDir'];

        $stats = $this->clientService->getClientStatistics();

        return view('clients.index', compact(
            'clients',
            'sortBy',
            'sortDir',
            'stats',
            'allowedPerPage',
            'perPage'
        ));
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

        // Get recent bookings with user + floor plan (for React profile UI)
        $recentBookings = $client->books()->with(['user', 'floorPlan'])->latest('date_book')->take(10)->get();

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

        $clientProfilePayload = $this->buildClientProfilePayload($client, $stats, $recentBookings);

        return view('clients.show', compact('client', 'stats', 'recentBookings', 'clientProfilePayload'));
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
     * Remove duplicate clients based on email, phone_number, or phone_1
     */
    public function removeDuplicates(Request $request)
    {
        try {
            $field = $request->input('field', 'all');
            $dryRun = $request->input('dry_run', false);
            $keepOldest = $request->input('keep_oldest', false);

            $allowed = ['email', 'phone_number', 'phone_1', 'all'];
            if (! in_array($field, $allowed, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid field. Use email, phone_number, phone_1, or all.',
                ], 422);
            }

            $fieldsToCheck = [];
            if ($field === 'all') {
                $fieldsToCheck = ['email', 'phone_number', 'phone_1'];
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
    private function safeRoute(string $name, array $parameters = []): string
    {
        if (! Route::has($name)) {
            return '#';
        }

        try {
            return route($name, $parameters);
        } catch (\Throwable $e) {
            return '#';
        }
    }

    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\Book>  $recentBookings
     */
    private function buildClientProfilePayload(Client $client, array $stats, $recentBookings): array
    {
        $user = Auth::user();

        $genderLabel = match ((int) ($client->sex ?? 0)) {
            1 => 'Male',
            2 => 'Female',
            default => 'N/A',
        };

        $coverPosition = $client->cover_position ?? null;
        if (! $coverPosition) {
            $coverPosition = \App\Models\Setting::getValue('client_'.$client->id.'_cover_position', null);
        }
        $coverPosition = $coverPosition ?? 'center center';

        $totalBooths = max(0, (int) ($stats['total_booths'] ?? 0));
        $confirmed = (int) ($stats['confirmed_booths'] ?? 0);
        $reserved = (int) ($stats['reserved_booths'] ?? 0);
        $paid = (int) ($stats['paid_booths'] ?? 0);
        $tb = max(1, $totalBooths);
        $boothsRingPct = $totalBooths > 0 ? (int) round(min(100, ($confirmed + $reserved) / $tb * 100)) : 0;
        $paidRingPct = $totalBooths > 0 ? (int) round(min(100, $paid / $tb * 100)) : 0;

        $hasBookingStatus = Schema::hasColumn('book', 'status');
        $cancelled = $hasBookingStatus
            ? (int) Book::where('clientid', $client->id)->where('status', Book::STATUS_CANCELLED)->count()
            : 0;

        $primaryTags = [];
        if (Schema::hasColumn('book', 'floor_plan_id')) {
            $primaryTags = Book::query()
                ->where('clientid', $client->id)
                ->whereNotNull('floor_plan_id')
                ->with('floorPlan')
                ->get()
                ->map(function ($book) {
                    $fp = $book->floorPlan;

                    return $fp ? ($fp->project_name ?: $fp->name) : null;
                })
                ->filter()
                ->unique()
                ->values()
                ->take(8)
                ->all();
        }

        $activityBooks = Book::query()
            ->where('clientid', $client->id)
            ->with('user')
            ->latest('date_book')
            ->take(3)
            ->get();

        $activity = [];
        foreach ($activityBooks as $book) {
            $by = $book->user ? ' by '.$book->user->username : '';
            $activity[] = [
                'variant' => 'log',
                'title' => 'Log: Booking #'.$book->id.$by,
                'date' => $book->date_book ? $book->date_book->format('M jS') : '',
            ];
        }

        $upcomingEvents = [];
        foreach ($recentBookings as $book) {
            $fp = $book->floorPlan;
            $name = $fp?->project_name ?: ($fp?->name ?: 'Booking #'.$book->id);
            $venue = $fp?->event_venue ?: ($fp?->event_location ?: '—');
            $dateStr = '—';
            if ($fp && $fp->event_start_date && $fp->event_end_date) {
                $dateStr = Carbon::parse($fp->event_start_date)->format('M jS').' - '.Carbon::parse($fp->event_end_date)->format('M jS');
            } elseif ($book->date_book) {
                $dateStr = $book->date_book->format('M jS, Y');
            }

            if ($hasBookingStatus) {
                $st = (int) ($book->status ?? 0);
                $tone = match ($st) {
                    Book::STATUS_CONFIRMED, Book::STATUS_PAID => 'green',
                    Book::STATUS_RESERVED => 'yellow',
                    Book::STATUS_CANCELLED => 'red',
                    default => 'gray',
                };
                $label = $book->status_label ?? 'Unknown';
            } else {
                $tone = 'gray';
                $label = 'Booking';
            }

            $upcomingEvents[] = [
                'name' => $name,
                'date' => $dateStr,
                'location' => $venue ?: '—',
                'statusLabel' => $label,
                'statusTone' => $tone,
            ];
        }

        return [
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'position' => $client->position ?? '',
                'company' => $client->company ?? '',
                'email' => $client->email ?? '',
                'phone' => trim((string) ($client->phone_number ?? '')) !== ''
                    ? $client->phone_number
                    : (string) ($client->phone_1 ?? ''),
                'address' => $client->address ?? '',
                'genderLabel' => $genderLabel,
                'avatarUrl' => AssetHelper::imageUrl($client->avatar ?? null),
                'coverUrl' => AssetHelper::imageUrl($client->cover_image ?? null),
                'coverPosition' => $coverPosition,
            ],
            'stats' => [
                'totalBooths' => $totalBooths,
                'totalBookings' => (int) ($stats['total_bookings'] ?? 0),
                'paidBooths' => $paid,
                'totalRevenue' => (float) ($stats['total_revenue'] ?? 0),
                'confirmed' => $confirmed,
                'reserved' => $reserved,
                'cancelled' => $cancelled,
                'boothsRingPct' => $boothsRingPct,
                'paidRingPct' => $paidRingPct,
            ],
            'primaryTags' => array_values($primaryTags),
            'activity' => $activity,
            'upcomingEvents' => $upcomingEvents,
            'routes' => [
                'dashboard' => $this->safeRoute('dashboard'),
                'clientsIndex' => $this->safeRoute('clients.index'),
                'clientEdit' => $this->safeRoute('clients.edit', ['client' => $client->id]),
                'clientBack' => $this->safeRoute('clients.index'),
                'booksIndex' => $this->safeRoute('books.index'),
                'boothsIndex' => $this->safeRoute('booths.index'),
                'floorPlansIndex' => $this->safeRoute('floor-plans.index'),
                'affiliatesIndex' => $this->safeRoute('affiliates.index'),
                'hrDashboard' => $this->safeRoute('hr.dashboard'),
                'financeDashboard' => $this->safeRoute('finance.dashboard'),
                'usersIndex' => $this->safeRoute('users.index'),
                'settingsIndex' => $this->safeRoute('settings.index'),
            ],
            'user' => [
                'initial' => strtoupper(mb_substr($user->username ?? 'U', 0, 1)),
                'username' => $user->username ?? 'User',
                'roleLabel' => $user->isAdmin() ? 'Administrator' : 'User',
            ],
        ];
    }

    private function mergeClientData(Client $keepClient, Client $duplicateClient)
    {
        $fieldsToMerge = [
            'name', 'sex', 'position', 'company', 'company_name_khmer',
            'phone_number', 'phone_1',
            'email',
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
