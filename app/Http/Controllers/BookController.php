<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Client;
use App\Models\Booth;
use App\Models\Category;
use App\Models\FloorPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class BookController extends Controller
{
    /**
     * Display a listing of bookings
     */
    public function index(Request $request)
    {
        // If AJAX request for lazy loading (check for page parameter or X-Requested-With header)
        if (($request->ajax() || $request->wantsJson() || $request->hasHeader('X-Requested-With')) && $request->has('page')) {
            return $this->lazyLoad($request);
        }

        $query = Book::with(['client', 'user']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('client', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            })->orWhereHas('user', function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%");
            });
        }
        
        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('date_book', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_book', '<=', $request->date_to);
        }
        
        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Get initial 20 records for lazy loading
        $books = $query->latest('date_book')->limit(20)->get();
        $total = $query->count();
        
        return view('books.index', compact('books', 'total'));
    }

    /**
     * Lazy load bookings (AJAX endpoint)
     */
    public function lazyLoad(Request $request)
    {
        // Use exact same query structure as index method
        $query = Book::with(['client', 'user']);
        
        // Search functionality (exact same as index)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('client', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%");
            })->orWhereHas('user', function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%");
            });
        }
        
        // Date filter (exact same as index)
        if ($request->filled('date_from')) {
            $query->whereDate('date_book', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_book', '<=', $request->date_to);
        }
        
        // Type filter (exact same as index)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Use same ordering and limit as initial load
        $page = $request->input('page', 1);
        $perPage = 20; // Same as initial load limit(20)
        $offset = ($page - 1) * $perPage;
        
        // Get total before pagination
        $total = $query->count();
        
        // Use exact same ordering as index method
        $books = $query->latest('date_book')->offset($offset)->limit($perPage)->get();
        $hasMore = ($offset + $books->count()) < $total;
        
        $view = $request->input('view', 'table'); // 'table' or 'card'
        $html = '';
        
        foreach ($books as $book) {
            // Ensure relationships are loaded (same as initial load)
            if (!$book->relationLoaded('client')) {
                $book->load('client');
            }
            if (!$book->relationLoaded('user')) {
                $book->load('user');
            }
            
            // Calculate booth count (same logic as main view)
            $boothIds = json_decode($book->boothid, true) ?? [];
            $boothCount = count($boothIds);
            
            // Determine type badge and class (same logic as main view)
            $typeClass = 'regular';
            $typeBadge = 'badge-modern-primary';
            if ($book->type == 2) {
                $typeClass = 'special';
                $typeBadge = 'badge-modern-warning';
            } elseif ($book->type == 3) {
                $typeClass = 'temporary';
                $typeBadge = 'badge-modern-danger';
            }
            
            if ($view === 'table') {
                // Compact card HTML for table view (now using card/icon view)
                $boothIds = json_decode($book->boothid, true) ?? [];
                $boothCount = count($boothIds);
                $typeClass = 'regular';
                $typeBadge = 'badge-modern-primary';
                if ($book->type == 2) {
                    $typeClass = 'special';
                    $typeBadge = 'badge-modern-warning';
                } elseif ($book->type == 3) {
                    $typeClass = 'temporary';
                    $typeBadge = 'badge-modern-danger';
                }
                
                try {
                    $statusSetting = $book->statusSetting ?? \App\Models\BookingStatusSetting::getByCode($book->status ?? 1);
                    $statusColor = $statusSetting ? $statusSetting->status_color : '#6c757d';
                    $statusTextColor = $statusSetting && $statusSetting->text_color ? $statusSetting->text_color : '#ffffff';
                    $statusName = $statusSetting ? $statusSetting->status_name : 'Pending';
                } catch (\Exception $e) {
                    $statusColor = '#6c757d';
                    $statusTextColor = '#ffffff';
                    $statusName = 'Pending';
                }
                
                $totalAmount = $book->total_amount ?? \App\Models\Booth::whereIn('id', $boothIds)->sum('price');
                $paidAmount = $book->paid_amount ?? 0;
                $balanceAmount = $book->balance_amount ?? ($totalAmount - $paidAmount);
                
                $html .= view('books.partials.compact-card', compact('book', 'boothCount', 'typeClass', 'typeBadge', 'statusColor', 'statusTextColor', 'statusName', 'totalAmount', 'balanceAmount'))->render();
            } else {
                // Card HTML
                $html .= view('books.partials.card-item', compact('book', 'boothCount', 'typeBadge', 'typeClass'))->render();
            }
        }
        
        return response()->json([
            'success' => true,
            'html' => $html,
            'hasMore' => $hasMore,
            'total' => $total,
            'loaded' => $offset + $books->count(),
            'page' => $page,
            'perPage' => $perPage
        ], 200, [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Show the form for creating a new booking
     */
    public function create(Request $request)
    {
        $clients = Client::orderBy('company')->get();
        
        // Get floor plan filter (from query param)
        $floorPlanId = $request->input('floor_plan_id');
        
        // Get all floor plans for selector
        $floorPlans = \App\Models\FloorPlan::where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('name', 'asc')
            ->get();
        
        // Filter booths by floor plan if specified
        $boothsQuery = Booth::whereIn('status', [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN]);
        
        if ($floorPlanId) {
            $boothsQuery->where('floor_plan_id', $floorPlanId);
        }
        
        $booths = $boothsQuery->orderBy('booth_number')->get();
        
        $categories = Category::where('status', 1)->orderBy('name')->get();
        
        // Group booths by first letter (A-Z) for tab view
        $boothsByLetter = [];
        
        foreach ($booths as $booth) {
            // Get first character of booth number (uppercase)
            $firstChar = strtoupper(substr(trim($booth->booth_number), 0, 1));
            
            // If first character is not a letter, put it in "#" group
            if (!ctype_alpha($firstChar)) {
                $firstChar = '#';
            }
            
            if (!isset($boothsByLetter[$firstChar])) {
                $boothsByLetter[$firstChar] = collect();
            }
            
            $boothsByLetter[$firstChar]->push($booth);
        }
        
        // Sort by letter (A-Z, then #)
        ksort($boothsByLetter);
        
        // Move # to the end if it exists
        if (isset($boothsByLetter['#'])) {
            $numbersGroup = $boothsByLetter['#'];
            unset($boothsByLetter['#']);
            $boothsByLetter['#'] = $numbersGroup;
        }
        
        // Convert to format expected by view
        $boothsByCategory = [];
        foreach ($boothsByLetter as $letter => $boothCollection) {
            $boothsByCategory[$letter] = [
                'category' => (object)['id' => $letter, 'name' => $letter, 'avatar' => null],
                'booths' => $boothCollection
            ];
        }
        
        // Get current floor plan if specified
        $currentFloorPlan = $floorPlanId ? \App\Models\FloorPlan::find($floorPlanId) : null;
        
        // Detect device type and serve appropriate view
        $device = \App\Helpers\DeviceDetector::detect($request);
        $viewName = \App\Helpers\DeviceDetector::getViewName('books.create', $request);
        
        return view($viewName, compact('clients', 'booths', 'categories', 'floorPlans', 'floorPlanId', 'currentFloorPlan', 'device', 'boothsByCategory'));
    }

    /**
     * Get booths for modal (AJAX endpoint)
     */
    public function getBooths(Request $request)
    {
        $floorPlanId = $request->input('floor_plan_id');
        
        // Filter booths by floor plan if specified
        $boothsQuery = Booth::whereIn('status', [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN]);
        
        if ($floorPlanId) {
            $boothsQuery->where('floor_plan_id', $floorPlanId);
        }
        
        $booths = $boothsQuery->orderBy('booth_number')->get();
        
        $html = '';
        if ($booths->count() > 0) {
            foreach ($booths as $booth) {
                $html .= '<div class="col-md-6 mb-2">';
                $html .= '<div class="booth-option-modal border rounded p-2" data-booth-id="' . $booth->id . '" data-price="' . ($booth->price ?? 0) . '" style="cursor: pointer; transition: all 0.2s; background: white;">';
                $html .= '<label class="mb-0 w-100" style="cursor: pointer;">';
                $html .= '<input type="checkbox" name="booth_ids[]" value="' . $booth->id . '" class="modal-booth-checkbox" onchange="modalUpdateSelection()">';
                $html .= '<strong class="text-primary">' . e($booth->booth_number) . '</strong>';
                $html .= '<span class="badge badge-' . ($booth->getStatusColor() ?? 'secondary') . ' ml-2" style="font-size: 0.75rem;">' . e($booth->getStatusLabel() ?? 'Available') . '</span>';
                if ($booth->category) {
                    $html .= '<br><small class="text-muted ml-4" style="font-size: 0.8125rem;"><i class="fas fa-folder"></i> ' . e($booth->category->name) . '</small>';
                }
                $html .= '<div class="mt-1 text-right"><strong class="text-success" style="font-size: 0.875rem;">$' . number_format($booth->price ?? 0, 2) . '</strong></div>';
                $html .= '</label></div></div>';
            }
        } else {
            $html = '<div class="col-12"><div class="alert alert-warning"><i class="fas fa-exclamation-triangle mr-2"></i>No available booths found.</div></div>';
        }
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    /**
     * Store a newly created booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'clientid' => 'required|exists:client,id',
            'booth_ids' => 'required|array|min:1',
            'booth_ids.*' => 'exists:booth,id',
            'date_book' => 'nullable|date',
            'type' => 'nullable|integer|in:1,2,3',
        ]);
        
        // Auto-set date_book to current date/time if not provided
        if (empty($validated['date_book'])) {
            $validated['date_book'] = now();
        }

        try {
            DB::beginTransaction();

            // Check if all booths are available
            $unavailableBooths = Booth::whereIn('id', $validated['booth_ids'])
                ->whereNotIn('status', [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN])
                ->get();

            if ($unavailableBooths->count() > 0) {
                DB::rollBack();
                $boothNumbers = $unavailableBooths->pluck('booth_number')->implode(', ');
                return back()->withErrors([
                    'booth_ids' => 'Some selected booths are not available: ' . $boothNumbers
                ])->withInput();
            }

            // Verify all booths exist
            $boothsCount = Booth::whereIn('id', $validated['booth_ids'])->count();
            if ($boothsCount !== count($validated['booth_ids'])) {
                DB::rollBack();
                return back()->withErrors([
                    'booth_ids' => 'One or more selected booths do not exist.'
                ])->withInput();
            }

            // Get floor plan and event from first booth (all booths should be from same floor plan)
            $booths = Booth::whereIn('id', $validated['booth_ids'])->get();
            
            // Verify all booths are from the same floor plan
            $floorPlanIds = $booths->pluck('floor_plan_id')->unique()->filter();
            if ($floorPlanIds->count() > 1) {
                DB::rollBack();
                return back()->withErrors([
                    'booth_ids' => 'All booths must be from the same floor plan.'
                ])->withInput();
            }
            
            $firstBooth = $booths->first();
            $floorPlanId = $firstBooth ? $firstBooth->floor_plan_id : null;
            $eventId = null;
            
            if ($floorPlanId) {
                $floorPlan = FloorPlan::find($floorPlanId);
                $eventId = $floorPlan ? $floorPlan->event_id : null;
            }
            
            // Map booking type to booth status
            // Booking types: 1=Regular=RESERVED, 2=Special=CONFIRMED, 3=Temporary=RESERVED
            $bookingType = $validated['type'] ?? 1;
            $boothStatus = ($bookingType == 2) ? Booth::STATUS_CONFIRMED : Booth::STATUS_RESERVED;
            
            // Get affiliate user ID from session (if customer came from affiliate link)
            $affiliateUserId = null;
            if (session()->has('affiliate_user_id') && session('affiliate_floor_plan_id') == $floorPlanId) {
                $affiliateUserId = session('affiliate_user_id');
                // Check if affiliate session is still valid (not expired)
                if (session()->has('affiliate_expires_at') && now()->lt(session('affiliate_expires_at'))) {
                    $affiliateUserId = session('affiliate_user_id');
                } else {
                    $affiliateUserId = null; // Session expired
                }
            }
            
            // Calculate total amount from booths
            $booths = Booth::whereIn('id', $validated['booth_ids'])->get();
            $totalAmount = $booths->sum('price');
            
            // Get default booking status (only if status column exists)
            $bookingStatus = null;
            if (Schema::hasColumn('book', 'status')) {
                try {
                    $defaultStatus = \App\Models\BookingStatusSetting::getDefault();
                    $bookingStatus = $defaultStatus ? $defaultStatus->status_code : Book::STATUS_PENDING;
                } catch (\Exception $e) {
                    $bookingStatus = Book::STATUS_PENDING;
                }
            }
            
            // Build booking data array
            $bookingData = [
                'event_id' => $eventId,
                'floor_plan_id' => $floorPlanId,
                'clientid' => $validated['clientid'],
                'boothid' => json_encode($validated['booth_ids']),
                'date_book' => $validated['date_book'],
                'userid' => auth()->user()->id,
                'type' => $bookingType,
            ];
            
            // Add optional fields only if columns exist
            if ($affiliateUserId && Schema::hasColumn('book', 'affiliate_user_id')) {
                $bookingData['affiliate_user_id'] = $affiliateUserId;
            }
            
            if ($bookingStatus !== null && Schema::hasColumn('book', 'status')) {
                $bookingData['status'] = $bookingStatus;
            }
            
            if (Schema::hasColumn('book', 'total_amount')) {
                $bookingData['total_amount'] = $totalAmount;
            }
            
            if (Schema::hasColumn('book', 'paid_amount')) {
                $bookingData['paid_amount'] = 0;
            }
            
            if (Schema::hasColumn('book', 'balance_amount')) {
                $bookingData['balance_amount'] = $totalAmount;
            }
            
            // Create booking with project/floor plan tracking
            $book = Book::create($bookingData);

            // Update booths status - use lockForUpdate to prevent race conditions
            $updated = Booth::whereIn('id', $validated['booth_ids'])
                ->whereIn('status', [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN])
                ->lockForUpdate()
                ->update([
                    'status' => $boothStatus,
                    'client_id' => $validated['clientid'],
                    'userid' => auth()->user()->id,
                    'bookid' => $book->id,
                ]);

            // Verify all booths were updated
            if ($updated !== count($validated['booth_ids'])) {
                DB::rollBack();
                return back()->withErrors([
                    'booth_ids' => 'Some booths became unavailable during booking. Please try again.'
                ])->withInput();
            }

            DB::commit();

            // Send notification about booking creation
            try {
                \App\Services\NotificationService::notifyBookingAction('created', $book, $book->userid);
            } catch (\Exception $e) {
                \Log::error('Failed to send booking creation notification: ' . $e->getMessage());
            }

            // Return JSON response for AJAX requests
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking created successfully.',
                    'booking' => [
                        'id' => $book->id,
                        'client' => $book->client ? ($book->client->company ?? $book->client->name) : 'N/A',
                    ]
                ]);
            }

            return redirect()->route('books.index')
                ->with('success', 'Booking created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Booking creation failed: ' . $e->getMessage(), [
                'user_id' => auth()->user()->id ?? null,
                'client_id' => $validated['clientid'] ?? null,
                'booth_ids' => $validated['booth_ids'] ?? [],
            ]);
            
            // Return JSON response for AJAX requests
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating booking: ' . $e->getMessage(),
                    'errors' => ['error' => [$e->getMessage()]]
                ], 422);
            }
            
            return back()->withErrors([
                'error' => 'Error creating booking: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Display the specified booking
     */
    public function show(Book $book)
    {
        $book->load(['client', 'user', 'payments', 'statusSetting']);
        
        // Calculate amounts if not set
        if (!$book->total_amount) {
            $book->total_amount = $book->calculateTotalAmount();
            $book->save();
        }
        if (!$book->paid_amount) {
            $book->paid_amount = $book->calculatePaidAmount();
            $book->balance_amount = $book->total_amount - $book->paid_amount;
            $book->save();
        }
        
        $boothIds = json_decode($book->boothid, true) ?? [];
        $booths = !empty($boothIds) ? Booth::whereIn('id', $boothIds)->get() : collect([]);
        
        // Get all payments for this booking
        $payments = $book->payments()->with('user')->latest('paid_at')->get();
        
        // Get booking status settings
        try {
            $statusSettings = \App\Models\BookingStatusSetting::getActiveStatuses();
        } catch (\Exception $e) {
            // Fallback if table doesn't exist or is empty
            $statusSettings = collect([]);
        }
        
        return view('books.show', compact('book', 'booths', 'payments', 'statusSettings'));
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, Book $book)
    {
        $request->validate([
            'status' => 'required|integer|exists:booking_status_settings,status_code',
        ]);

        $oldStatus = $book->status;
        $book->status = $request->status;
        $book->save();

        // Create timeline entry
        try {
            $boothIds = json_decode($book->boothid, true) ?? [];
            foreach ($boothIds as $boothId) {
                \App\Models\BookingTimeline::create([
                    'booking_id' => $book->id,
                    'booth_id' => $boothId,
                    'action' => 'status_changed',
                    'old_status' => $oldStatus,
                    'new_status' => $request->status,
                    'user_id' => auth()->id(),
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create timeline entry: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully',
            'status' => $book->status,
            'status_label' => $book->status_label,
        ]);
    }

    /**
     * Show the form for editing the specified booking
     */
    public function edit(Book $book)
    {
        $book->load(['client', 'user']);
        $boothIds = json_decode($book->boothid, true) ?? [];
        $currentBooths = !empty($boothIds) ? Booth::whereIn('id', $boothIds)->get() : collect([]);
        
        $clients = Client::orderBy('company')->get();
        $allBooths = Booth::orderBy('booth_number')->get();
        $categories = Category::where('status', 1)->orderBy('name')->get();
        
        return view('books.edit', compact('book', 'clients', 'allBooths', 'currentBooths', 'boothIds', 'categories'));
    }

    /**
     * Update the specified booking
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'clientid' => 'required|exists:client,id',
            'booth_ids' => 'required|array|min:1',
            'booth_ids.*' => 'exists:booth,id',
            'date_book' => 'nullable|date',
            'type' => 'nullable|integer|in:1,2,3',
            'status' => 'nullable|integer',
            'payment_due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
        
        // Auto-set date_book to current date/time if not provided
        if (empty($validated['date_book'])) {
            $validated['date_book'] = $book->date_book ?? now();
        }

        try {
            DB::beginTransaction();

            // Get current booth IDs
            $currentBoothIds = json_decode($book->boothid, true) ?? [];
            $newBoothIds = $validated['booth_ids'];
            
            // Find booths to release (in current but not in new)
            $boothsToRelease = array_diff($currentBoothIds, $newBoothIds);
            
            // Find booths to reserve (in new but not in current)
            $boothsToReserve = array_diff($newBoothIds, $currentBoothIds);
            
            // Check if new booths are available
            // Note: $boothsToReserve only contains booths NOT in current booking, so we just check availability
            if (!empty($boothsToReserve)) {
                $unavailableBooths = Booth::whereIn('id', $boothsToReserve)
                    ->whereNotIn('status', [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN])
                    ->get();
                
                if ($unavailableBooths->count() > 0) {
                    DB::rollBack();
                    return back()->withErrors([
                        'booth_ids' => 'Some selected booths are not available: ' . $unavailableBooths->pluck('booth_number')->implode(', ')
                    ])->withInput();
                }
            }
            
            // Release old booths - but NOT if they are PAID
            if (!empty($boothsToRelease)) {
                $boothsToReleaseModels = Booth::whereIn('id', $boothsToRelease)->get();
                $paidBooths = [];
                
                foreach ($boothsToReleaseModels as $booth) {
                    if ($booth->status === Booth::STATUS_PAID) {
                        $paidBooths[] = $booth->booth_number;
                    } else {
                        // Only release non-paid booths
                        $booth->update([
                            'status' => Booth::STATUS_AVAILABLE,
                            'client_id' => null,
                            'userid' => null,
                            'bookid' => null,
                        ]);
                    }
                }
                
                if (!empty($paidBooths)) {
                    DB::rollBack();
                    return back()->withErrors([
                        'booth_ids' => 'Cannot remove paid booths from booking: ' . implode(', ', $paidBooths) . '. Please refund payment first.'
                    ])->withInput();
                }
            }
            
            // Reserve new booths - use lock to prevent race conditions
            if (!empty($boothsToReserve)) {
                $updated = Booth::whereIn('id', $boothsToReserve)
                    ->whereIn('status', [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN])
                    ->lockForUpdate()
                    ->update([
                        'status' => Booth::STATUS_RESERVED,
                        'client_id' => $validated['clientid'],
                        'userid' => auth()->user()->id,
                        'bookid' => $book->id,
                    ]);
                
                // Verify all booths were updated
                if ($updated !== count($boothsToReserve)) {
                    DB::rollBack();
                    return back()->withErrors([
                        'booth_ids' => 'Some booths became unavailable during update. Please try again.'
                    ])->withInput();
                }
            }
            
            // Update existing booths with new client if client changed
            $boothsToKeep = array_intersect($currentBoothIds, $newBoothIds);
            if (!empty($boothsToKeep) && $book->clientid != $validated['clientid']) {
                Booth::whereIn('id', $boothsToKeep)->update([
                    'client_id' => $validated['clientid'],
                ]);
            }
            
            // Build update data array
            $updateData = [
                'clientid' => $validated['clientid'],
                'boothid' => json_encode($newBoothIds),
                'date_book' => $validated['date_book'] ?? $book->date_book ?? now(),
                'type' => $validated['type'] ?? $book->type,
            ];
            
            // Add optional fields only if columns exist
            if (Schema::hasColumn('book', 'status')) {
                $updateData['status'] = $validated['status'] ?? $book->status ?? Book::STATUS_PENDING;
            }
            
            if (Schema::hasColumn('book', 'payment_due_date')) {
                $updateData['payment_due_date'] = $validated['payment_due_date'] ?? $book->payment_due_date;
            }
            
            if (Schema::hasColumn('book', 'notes')) {
                $updateData['notes'] = $validated['notes'] ?? $book->notes;
            }
            
            // Update booking
            $book->update($updateData);
            
            // Recalculate amounts after booth changes
            $book->updatePaymentAmounts();

            DB::commit();

            // Send notification about booking update
            try {
                \App\Services\NotificationService::notifyBookingAction('updated', $book, $book->userid);
            } catch (\Exception $e) {
                \Log::error('Failed to send booking update notification: ' . $e->getMessage());
            }

            return redirect()->route('books.show', $book)
                ->with('success', 'Booking updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Error updating booking: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Remove the specified booking
     */
    public function destroy(Book $book)
    {
        try {
            DB::beginTransaction();

            // Check if booking has payment
            $payment = \App\Models\Payment::where('booking_id', $book->id)
                ->where('status', \App\Models\Payment::STATUS_COMPLETED)
                ->first();
            
            if ($payment) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete booking with completed payment. Please refund payment first.'
                ], 400);
            }

            // Release booths (set status to available) - but NOT if they are PAID
            $boothIds = json_decode($book->boothid, true) ?? [];
            if (!empty($boothIds)) {
                $booths = Booth::whereIn('id', $boothIds)->get();
                $paidBooths = [];
                
                foreach ($booths as $booth) {
                    if ($booth->status === Booth::STATUS_PAID) {
                        $paidBooths[] = $booth->booth_number;
                        // Keep paid booths as-is, just remove booking reference
                        $booth->update([
                            'bookid' => null,
                            // Keep status, client_id, userid for paid booths
                        ]);
                    } else {
                        // Release non-paid booths
                        $booth->update([
                            'status' => Booth::STATUS_AVAILABLE,
                            'client_id' => null,
                            'userid' => null,
                            'bookid' => null,
                        ]);
                    }
                }
                
                if (!empty($paidBooths)) {
                    // Log warning but allow deletion
                    \Log::warning('Booking deleted with paid booths', [
                        'booking_id' => $book->id,
                        'paid_booths' => $paidBooths,
                    ]);
                }
            }

            // Store booking info before deletion for notification
            $bookingId = $book->id;
            $bookingUserId = $book->userid;
            $bookingClientId = $book->clientid;
            
            // Delete the booking
            $book->delete();

            // Send notification about booking deletion
            try {
                // Create a temporary booking object for notification
                $tempBook = new Book();
                $tempBook->id = $bookingId;
                $tempBook->userid = $bookingUserId;
                $tempBook->clientid = $bookingClientId;
                \App\Services\NotificationService::notifyBookingAction('deleted', $tempBook, $bookingUserId);
            } catch (\Exception $e) {
                \Log::error('Failed to send booking deletion notification: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Booking deletion failed: ' . $e->getMessage(), [
                'booking_id' => $book->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all booking records (requires password verification)
     */
    public function deleteAll(Request $request)
    {
        // Only allow admin users
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.'
            ], 403);
        }

        $request->validate([
            'password' => 'required|string',
        ]);

        // Verify password
        $user = auth()->user();
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password. Please try again.'
            ], 401);
        }

        try {
            DB::beginTransaction();

            $totalCount = Book::count();
            
            if ($totalCount === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'No booking records to delete.'
                ], 400);
            }

            // Get all bookings to release booths
            $books = Book::all();
            $boothIdsToRelease = [];

            foreach ($books as $book) {
                $boothIds = json_decode($book->boothid, true) ?? [];
                $boothIdsToRelease = array_merge($boothIdsToRelease, $boothIds);
            }

            // Release all booths (set status to available) - but NOT if they are PAID
            if (!empty($boothIdsToRelease)) {
                $booths = Booth::whereIn('id', array_unique($boothIdsToRelease))->get();
                $paidBooths = [];

                foreach ($booths as $booth) {
                    if ($booth->status === Booth::STATUS_PAID) {
                        $paidBooths[] = $booth->booth_number;
                        // Keep paid booths as-is, just remove booking reference
                        $booth->update([
                            'bookid' => null,
                        ]);
                    } else {
                        // Release non-paid booths
                        $booth->update([
                            'status' => Booth::STATUS_AVAILABLE,
                            'client_id' => null,
                            'userid' => null,
                            'bookid' => null,
                        ]);
                    }
                }

                if (!empty($paidBooths)) {
                    \Log::warning('All bookings deleted with paid booths', [
                        'paid_booths' => $paidBooths,
                        'deleted_by' => auth()->user()->id ?? null,
                    ]);
                }
            }

            // Delete all bookings
            Book::query()->delete();

            DB::commit();

            \Log::info('All booking records deleted', [
                'total_deleted' => $totalCount,
                'deleted_by' => auth()->user()->id ?? null,
                'deleted_by_username' => auth()->user()->username ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => "All {$totalCount} booking record(s) deleted successfully."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Delete all bookings failed: ' . $e->getMessage(), [
                'deleted_by' => auth()->user()->id ?? null,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error deleting records: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Booking action - Creates a new booking with client creation
     * This matches the Yii actionBooking logic
     */
    public function booking(Request $request)
    {
        $data = $request->input('data');
        
        if (!isset($data)) {
            return response()->json([
                'status' => 403,
                'message' => 'Please Check Data Before Submit'
            ], 403);
        }
        
        // Replace @rp4and with & (Yii code does this)
        $data = str_replace('@rp4and', '&', $data);
        $data = json_decode($data, true);
        
        if (!is_array($data)) {
            return response()->json([
                'status' => 403,
                'message' => 'Invalid data format'
            ], 403);
        }
        
        // Validate required fields - ALL client information is now required for successful booking
        $requiredFields = [
            'book', 
            'inputCpnName',      // Company name
            'inputName',         // Client name
            'inputPhone',        // Phone number
            'inputEmail',        // Email (NEW - required)
            'inputAddress',      // Address (NEW - required)
            'booth'              // Booth IDs
        ];
        
        // Always require all fields for any booking type (no exceptions)
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Please fill in all required client information fields (Name, Company, Phone, Email, Address)'
                ], 403);
            }
        }
        
        // Validate email format
        if (isset($data['inputEmail']) && !filter_var($data['inputEmail'], FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'status' => 403,
                'message' => 'Please enter a valid email address'
            ], 403);
        }
        
        // Check category limits
        if (isset($data['inputCategory']) && !empty($data['inputCategory'])) {
            if ($this->isLimitCategory($data['inputCategory'], count($data['booth']), 1)) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Limit Category Contact Admin'
                ], 403);
            }
        }
        
        if (isset($data['inputSubCategory']) && !empty($data['inputSubCategory'])) {
            if ($this->isLimitCategory($data['inputSubCategory'], count($data['booth']), 2)) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Limit Sub Category Contact Admin'
                ], 403);
            }
        }
        
        try {
            DB::beginTransaction();
            
            // Check if all booths are available (with lock to prevent race conditions)
            $unavailableBooths = Booth::whereIn('id', $data['booth'])
                ->whereNotIn('status', [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN])
                ->lockForUpdate()
                ->get();
            
            if ($unavailableBooths->count() > 0) {
                DB::rollBack();
                $boothNumbers = $unavailableBooths->pluck('booth_number')->implode(', ');
                return response()->json([
                    'status' => 403,
                    'message' => 'Booth(s) not available: ' . $boothNumbers
                ], 403);
            }
            
            // Verify all booths exist
            $boothsCount = Booth::whereIn('id', $data['booth'])->count();
            if ($boothsCount !== count($data['booth'])) {
                DB::rollBack();
                return response()->json([
                    'status' => 403,
                    'message' => 'One or more selected booths do not exist.'
                ], 403);
            }
            
            // Verify all booths are from the same floor plan
            $booths = Booth::whereIn('id', $data['booth'])->get();
            $floorPlanIds = $booths->pluck('floor_plan_id')->unique()->filter();
            if ($floorPlanIds->count() > 1) {
                DB::rollBack();
                return response()->json([
                    'status' => 403,
                    'message' => 'All booths must be from the same floor plan.'
                ], 403);
            }
            
            $userid = auth()->user()->id;
            $clientID = 0;
            
            // Create client with ALL required information (all fields are now required)
            $clientData = [
                'company' => $data['inputCpnName'],
                'name' => $data['inputName'],
                'phone_number' => $data['inputPhone'],
                'email' => $data['inputEmail'],
                'address' => $data['inputAddress'],
                'position' => $data['inputPosition'] ?? null,
                'sex' => isset($data['inputSex']) && !empty($data['inputSex']) ? (int)$data['inputSex'] : null,
                'tax_id' => $data['inputTaxId'] ?? null,
                'website' => $data['inputWebsite'] ?? null,
                'notes' => $data['inputNotes'] ?? null,
            ];
            
            // Check if client already exists by email or phone (avoid duplicates)
            $existingClient = Client::where('email', $clientData['email'])
                ->orWhere('phone_number', $clientData['phone_number'])
                ->first();
            
            if ($existingClient) {
                // Update existing client with latest information
                $existingClient->update($clientData);
                $clientID = $existingClient->id;
            } else {
                // Create new client
                $client = Client::create($clientData);
                $clientID = $client->id;
            }
            
            // Map booking type to status (1=Regular=RESERVED, 2=Special=CONFIRMED, 3=Temporary=RESERVED)
            $bookingType = $data['book'] ?? 3;
            $boothStatus = ($bookingType == 2) ? Booth::STATUS_CONFIRMED : Booth::STATUS_RESERVED;
            
            // Get floor plan and event from first booth (all booths should be from same floor plan)
            $booths = Booth::whereIn('id', $data['booth'])->get();
            
            // Verify all booths are from the same floor plan
            $floorPlanIds = $booths->pluck('floor_plan_id')->unique()->filter();
            if ($floorPlanIds->count() > 1) {
                DB::rollBack();
                return response()->json([
                    'status' => 403,
                    'message' => 'All booths must be from the same floor plan.'
                ], 403);
            }
            
            $firstBooth = $booths->first();
            $floorPlanId = $firstBooth ? $firstBooth->floor_plan_id : null;
            $eventId = null;
            
            if ($floorPlanId) {
                $floorPlan = FloorPlan::find($floorPlanId);
                $eventId = $floorPlan ? $floorPlan->event_id : null;
            }
            
            // Get affiliate user ID from session (if customer came from affiliate link)
            $affiliateUserId = null;
            if (session()->has('affiliate_user_id') && session('affiliate_floor_plan_id') == $floorPlanId) {
                // Check if affiliate session is still valid (not expired)
                if (session()->has('affiliate_expires_at') && now()->lt(session('affiliate_expires_at'))) {
                    $affiliateUserId = session('affiliate_user_id');
                } else {
                    $affiliateUserId = null; // Session expired
                }
            }
            
            // Create booking with project/floor plan tracking
            // Calculate total amount from booths
            $totalAmount = $booths->sum('price');
            
            // Get default booking status (only if status column exists)
            $bookingStatus = null;
            if (Schema::hasColumn('book', 'status')) {
                try {
                    $defaultStatus = \App\Models\BookingStatusSetting::getDefault();
                    $bookingStatus = $defaultStatus ? $defaultStatus->status_code : Book::STATUS_PENDING;
                } catch (\Exception $e) {
                    $bookingStatus = Book::STATUS_PENDING;
                }
            }
            
            // Build booking data array
            $bookingData = [
                'event_id' => $eventId,
                'floor_plan_id' => $floorPlanId,
                'userid' => $userid,
                'type' => $bookingType,
                'clientid' => $clientID,
                'boothid' => json_encode($data['booth']),
                'date_book' => now(),
            ];
            
            // Add optional fields only if columns exist
            if ($affiliateUserId && Schema::hasColumn('book', 'affiliate_user_id')) {
                $bookingData['affiliate_user_id'] = $affiliateUserId;
            }
            
            if ($bookingStatus !== null && Schema::hasColumn('book', 'status')) {
                $bookingData['status'] = $bookingStatus;
            }
            
            if (Schema::hasColumn('book', 'total_amount')) {
                $bookingData['total_amount'] = $totalAmount;
            }
            
            if (Schema::hasColumn('book', 'paid_amount')) {
                $bookingData['paid_amount'] = 0;
            }
            
            if (Schema::hasColumn('book', 'balance_amount')) {
                $bookingData['balance_amount'] = $totalAmount;
            }
            
            $book = Book::create($bookingData);
            
            $bookID = $book->id;
            
            // Update booths with lock to prevent race conditions
            $updated = Booth::whereIn('id', $data['booth'])
                ->whereIn('status', [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN])
                ->lockForUpdate()
                ->update([
                    'status' => $boothStatus,
                    'client_id' => $clientID,
                    'userid' => $userid,
                    'bookid' => $bookID,
                    'booth_type_id' => $data['inputBoothType'] ?? null,
                    'asset_id' => $data['inputAsset'] ?? null,
                    'category_id' => $data['inputCategory'] ?? null,
                    'sub_category_id' => $data['inputSubCategory'] ?? null,
                ]);
            
            // Verify all booths were updated
            if ($updated !== count($data['booth'])) {
                DB::rollBack();
                return response()->json([
                    'status' => 403,
                    'message' => 'Some booths became unavailable during booking. Please try again.'
                ], 403);
            }
            
            DB::commit();
            
            return response()->json([
                'status' => 200,
                'message' => 'Successful.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Booking API creation failed: ' . $e->getMessage(), [
                'user_id' => auth()->user()->id ?? null,
                'data' => $data,
            ]);
            return response()->json([
                'status' => 500,
                'message' => 'Error creating booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update booking action - Updates an existing booking
     * This matches the Yii actionUpbooking logic
     */
    public function upbooking(Request $request)
    {
        $data = $request->input('data');
        
        if (!isset($data)) {
            return response()->json([
                'status' => 403,
                'message' => 'Please Check Data Before Submit'
            ], 403);
        }
        
        // Replace @rp4and with & (Yii code does this)
        $data = str_replace('@rp4and', '&', $data);
        $data = json_decode($data, true);
        
        if (!is_array($data)) {
            return response()->json([
                'status' => 403,
                'message' => 'Invalid data format'
            ], 403);
        }
        
        // Validate required fields
        $requiredFields = ['companyID', 'book', 'inputCpnName', 'inputPosition', 'inputName', 'inputPhone', 
                          'booth', 'inputBoothType', 'inputAsset', 'inputCategory', 'inputSubCategory'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Please Check Data Before Submit'
                ], 403);
            }
        }
        
        // Check category limits
        if ($this->isLimitCategory($data['inputCategory'], count($data['booth']), 1)) {
            return response()->json([
                'status' => 403,
                'message' => 'Limit Category Contact Admin'
            ], 403);
        }
        
        if ($this->isLimitCategory($data['inputSubCategory'], count($data['booth']), 2)) {
            return response()->json([
                'status' => 403,
                'message' => 'Limit Sub Category Contact Admin'
            ], 403);
        }
        
        try {
            DB::beginTransaction();
            
            $userid = auth()->user()->id;
            
            // Find existing book
            $book = Book::where('clientid', $data['companyID'])->first();
            
            if (!$book) {
                DB::rollBack();
                return response()->json([
                    'status' => 403,
                    'message' => 'Booking not found'
                ], 403);
            }
            
            $getBoothDB = json_decode($book->boothid, true) ?? [];
            $getBoothRqs = $data['booth'];
            
            // Map booking type to status
            $bookingType = $data['book'] ?? 3;
            $boothStatus = ($bookingType == 2) ? Booth::STATUS_CONFIRMED : Booth::STATUS_RESERVED;
            
            // Find booths to release (in current but not in new)
            $boothsToRelease = array_diff($getBoothDB, $getBoothRqs);
            
            // Find booths to reserve (in new but not in current)
            $boothsToReserve = array_diff($getBoothRqs, $getBoothDB);
            
            // Check if new booths are available (with lock to prevent race conditions)
            if (!empty($boothsToReserve)) {
                $unavailableBooths = Booth::whereIn('id', $boothsToReserve)
                    ->whereNotIn('status', [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN])
                    ->where(function($query) use ($book) {
                        $query->where('bookid', '!=', $book->id)
                              ->orWhereNull('bookid');
                    })
                    ->lockForUpdate()
                    ->get();
                
                if ($unavailableBooths->count() > 0) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 403,
                        'message' => 'Some booths are not available: ' . $unavailableBooths->pluck('booth_number')->implode(', ')
                    ], 403);
                }
            }
            
            // Release old booths - but NOT if they are PAID
            if (!empty($boothsToRelease)) {
                $boothsToReleaseModels = Booth::whereIn('id', $boothsToRelease)->lockForUpdate()->get();
                $paidBooths = [];
                
                foreach ($boothsToReleaseModels as $booth) {
                    if ($booth->status === Booth::STATUS_PAID) {
                        $paidBooths[] = $booth->booth_number;
                        // Keep paid booths as-is, just remove booking reference
                        $booth->update([
                            'bookid' => null,
                        ]);
                    } else {
                        // Only release non-paid booths
                        $booth->update([
                            'status' => Booth::STATUS_AVAILABLE,
                            'client_id' => null,
                            'userid' => null,
                            'bookid' => null,
                        ]);
                    }
                }
                
                if (!empty($paidBooths)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 403,
                        'message' => 'Cannot remove paid booths from booking: ' . implode(', ', $paidBooths) . '. Please refund payment first.'
                    ], 403);
                }
            }
            
            // Reserve new booths - use lock to prevent race conditions
            if (!empty($boothsToReserve)) {
                $updated = Booth::whereIn('id', $boothsToReserve)
                    ->whereIn('status', [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN])
                    ->lockForUpdate()
                    ->update([
                        'status' => $boothStatus,
                        'client_id' => $data['companyID'],
                        'userid' => $userid,
                        'bookid' => $book->id,
                        'booth_type_id' => $data['inputBoothType'] ?? null,
                        'asset_id' => $data['inputAsset'] ?? null,
                        'category_id' => $data['inputCategory'] ?? null,
                        'sub_category_id' => $data['inputSubCategory'] ?? null,
                    ]);
                
                // Verify all booths were updated
                if ($updated !== count($boothsToReserve)) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 403,
                        'message' => 'Some booths became unavailable during update. Please try again.'
                    ], 403);
                }
            }
            
            // Update existing booths with new client if client changed
            $boothsToKeep = array_intersect($getBoothDB, $getBoothRqs);
            if (!empty($boothsToKeep) && $book->clientid != $data['companyID']) {
                Booth::whereIn('id', $boothsToKeep)
                    ->where('status', '!=', Booth::STATUS_PAID) // Don't update paid booths
                    ->update([
                        'client_id' => $data['companyID'],
                        'booth_type_id' => $data['inputBoothType'] ?? null,
                        'asset_id' => $data['inputAsset'] ?? null,
                        'category_id' => $data['inputCategory'] ?? null,
                        'sub_category_id' => $data['inputSubCategory'] ?? null,
                    ]);
            }
            
            // Update existing booths status if booking type changed (but not if PAID)
            if (!empty($boothsToKeep)) {
                Booth::whereIn('id', $boothsToKeep)
                    ->where('status', '!=', Booth::STATUS_PAID)
                    ->update([
                        'status' => $boothStatus,
                    ]);
            }
            
            // Update book
            $book->boothid = json_encode($getBoothRqs);
            $book->type = $bookingType;
            $book->save();
            
            DB::commit();
            
            return response()->json([
                'status' => 200,
                'message' => 'Successful.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Booking API update failed: ' . $e->getMessage(), [
                'user_id' => auth()->user()->id ?? null,
                'data' => $data,
            ]);
            return response()->json([
                'status' => 500,
                'message' => 'Error updating booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get client info for a booth (info action)
     */
    public function info($id)
    {
        $booth = Booth::findOrFail($id);
        $client = $booth->client;
        
        if (!$client) {
            return response()->json([]);
        }
        
        return response()->json([
            'id' => $client->id,
            'name' => $client->name,
            'company' => $client->company,
            'position' => $client->position,
            'phone_number' => $client->phone_number,
        ]);
    }

    /**
     * Check if category limit is exceeded
     * This matches the Yii isLimitCat method
     * 
     * @param int $categoryId Category ID
     * @param int $boothCount Number of booths being added
     * @param int $type 1 for category, 2 for sub-category
     * @return bool True if limit exceeded
     */
    private function isLimitCategory($categoryId, $boothCount, $type)
    {
        $category = Category::find($categoryId);
        
        if (!$category || !$category->limit) {
            return false; // No limit set
        }
        
        if ($type == 2) {
            // Sub-category limit check
            $countBoothSub = Booth::where('sub_category_id', $categoryId)->count() + $boothCount;
            return $countBoothSub > $category->limit;
        } else {
            // Category limit check
            $countBoothCat = Booth::where('category_id', $categoryId)->count() + $boothCount;
            return $countBoothCat > $category->limit;
        }
    }
}

