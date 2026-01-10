<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Client;
use App\Models\Booth;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    /**
     * Display a listing of bookings
     */
    public function index(Request $request)
    {
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
        
        $books = $query->latest('date_book')->paginate(20)->withQueryString();
        
        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new booking
     */
    public function create()
    {
        $clients = Client::orderBy('company')->get();
        $booths = Booth::whereIn('status', [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN])
            ->orderBy('booth_number')
            ->get();
        $categories = Category::where('status', 1)->orderBy('name')->get();
        
        return view('books.create', compact('clients', 'booths', 'categories'));
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
            'date_book' => 'required|date',
            'type' => 'nullable|integer',
        ]);

        // Check if all booths are available
        $booths = Booth::whereIn('id', $validated['booth_ids'])
            ->whereNotIn('status', [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN])
            ->get();

        if ($booths->count() > 0) {
            return back()->withErrors([
                'booth_ids' => 'Some selected booths are not available.'
            ])->withInput();
        }

        $book = Book::create([
            'clientid' => $validated['clientid'],
            'boothid' => json_encode($validated['booth_ids']),
            'date_book' => $validated['date_book'],
            'userid' => auth()->id(),
            'type' => $validated['type'] ?? 1,
        ]);

        // Update booths status
        Booth::whereIn('id', $validated['booth_ids'])->update([
            'status' => Booth::STATUS_RESERVED,
            'client_id' => $validated['clientid'],
            'userid' => auth()->id(),
            'bookid' => $book->id,
        ]);

        return redirect()->route('books.index')
            ->with('success', 'Booking created successfully.');
    }

    /**
     * Display the specified booking
     */
    public function show(Book $book)
    {
        $book->load(['client', 'user']);
        $boothIds = json_decode($book->boothid, true) ?? [];
        $booths = !empty($boothIds) ? Booth::whereIn('id', $boothIds)->get() : collect([]);
        return view('books.show', compact('book', 'booths'));
    }

    /**
     * Remove the specified booking
     */
    public function destroy(Book $book)
    {
        try {
            DB::beginTransaction();

            // Release booths (set status to available)
            $boothIds = json_decode($book->boothid, true) ?? [];
            if (!empty($boothIds)) {
                Booth::whereIn('id', $boothIds)->update([
                    'status' => Booth::STATUS_AVAILABLE,
                    'client_id' => null,
                    'userid' => null,
                    'bookid' => null,
                ]);
            }

            // Delete the booking
            $book->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
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
        
        // Validate required fields
        $requiredFields = ['book', 'inputCpnName', 'inputPosition', 'inputName', 'inputPhone', 'booth'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                if ($data['book'] != 3) { // Allow book=3 without all fields
                    return response()->json([
                        'status' => 403,
                        'message' => 'Please Check Data Before Submit'
                    ], 403);
                }
            }
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
        
        // Check if all booths are available
        foreach ($data['booth'] as $boothId) {
            $booth = Booth::find($boothId);
            if (!$booth || ($booth->status != Booth::STATUS_AVAILABLE && $booth->status != Booth::STATUS_HIDDEN)) {
                return response()->json([
                    'status' => 403,
                    'message' => 'Booth ' . ($booth ? $booth->booth_number : $boothId) . ' is not available.'
                ], 403);
            }
        }
        
        $userid = auth()->id();
        $clientID = 0;
        
        // Create client if information provided
        if (!empty($data['inputCpnName']) && !empty($data['inputPosition']) 
            && !empty($data['inputName']) && !empty($data['inputPhone'])) {
            $client = Client::create([
                'company' => $data['inputCpnName'],
                'position' => $data['inputPosition'],
                'name' => $data['inputName'],
                'phone_number' => $data['inputPhone'],
            ]);
            $clientID = $client->id;
        }
        
        // Create booking
        $book = Book::create([
            'userid' => $userid,
            'type' => $data['book'] ?? 3,
            'clientid' => $clientID,
            'boothid' => json_encode($data['booth']),
            'date_book' => now(),
        ]);
        
        $bookID = $book->id;
        
        // Update booths
        foreach ($data['booth'] as $boothId) {
            $booth = Booth::find($boothId);
            if ($booth && ($booth->status == Booth::STATUS_AVAILABLE || $booth->status == Booth::STATUS_HIDDEN)) {
                $booth->update([
                    'status' => $data['book'] ?? 3,
                    'client_id' => $clientID,
                    'userid' => $userid,
                    'bookid' => $bookID,
                    'booth_type_id' => $data['inputBoothType'] ?? null,
                    'asset_id' => $data['inputAsset'] ?? null,
                    'category_id' => $data['inputCategory'] ?? null,
                    'sub_category_id' => $data['inputSubCategory'] ?? null,
                ]);
            }
        }
        
        return response()->json([
            'status' => 200,
            'message' => 'Successful.'
        ]);
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
        
        $userid = auth()->id();
        
        // Find existing book
        $book = Book::where('clientid', $data['companyID'])->first();
        
        if (!$book) {
            return response()->json([
                'status' => 403,
                'message' => 'Booking not found'
            ], 403);
        }
        
        $getBoothDB = json_decode($book->boothid, true) ?? [];
        $getBoothRqs = $data['booth'];
        
        // Update booths
        foreach ($getBoothRqs as $boothId) {
            $booth = Booth::find($boothId);
            if ($booth && ($booth->status == Booth::STATUS_AVAILABLE || $booth->status == Booth::STATUS_HIDDEN)) {
                $booth->update([
                    'status' => $data['book'],
                    'client_id' => $data['companyID'],
                    'userid' => $userid,
                    'booth_type_id' => $data['inputBoothType'],
                    'asset_id' => $data['inputAsset'],
                    'category_id' => $data['inputCategory'],
                    'sub_category_id' => $data['inputSubCategory'],
                ]);
            }
            // Add to booth array
            if (!in_array($boothId, $getBoothDB)) {
                $getBoothDB[] = $boothId;
            }
        }
        
        // Update book
        $book->boothid = json_encode($getBoothDB);
        $book->save();
        
        return response()->json([
            'status' => 200,
            'message' => 'Successful.'
        ]);
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
