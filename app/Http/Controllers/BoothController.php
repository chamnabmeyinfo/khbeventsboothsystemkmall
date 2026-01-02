<?php

namespace App\Http\Controllers;

use App\Models\Booth;
use App\Models\Client;
use App\Models\Category;
use App\Models\Asset;
use App\Models\BoothType;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BoothController extends Controller
{
    /**
     * Display a listing of booths
     */
    public function index(Request $request)
    {
        $query = Booth::with(['client', 'category', 'asset', 'boothType'])
            ->orderBy('booth_number', 'asc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('company')) {
            $query->whereHas('client', function($q) use ($request) {
                $q->where('company', 'like', '%' . $request->company . '%');
            });
        }

        $booths = $query->get();
        $categories = Category::where('status', 1)->orderBy('name')->get();
        $assets = Asset::where('status', 1)->orderBy('name')->get();
        $boothTypes = BoothType::where('status', 1)->orderBy('name')->get();

        // Get reserved booths for mapping
        $reservedBooths = Booth::where('status', Booth::STATUS_RESERVED)
            ->with('client')
            ->get()
            ->groupBy(function($booth) {
                return $booth->client ? $booth->client->company : 'No Company';
            });

        return view('booths.index', compact('booths', 'categories', 'assets', 'boothTypes', 'reservedBooths'));
    }

    /**
     * Show the form for creating a new booth
     */
    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $assets = Asset::where('status', 1)->get();
        $boothTypes = BoothType::where('status', 1)->get();
        
        return view('booths.create', compact('categories', 'assets', 'boothTypes'));
    }

    /**
     * Store a newly created booth
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booth_number' => 'required|string|max:45|unique:booths,booth_number',
            'type' => 'required|integer',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'asset_id' => 'nullable|exists:assets,id',
            'booth_type_id' => 'nullable|exists:booth_types,id',
        ]);

        Booth::create($validated);

        return redirect()->route('booths.index')
            ->with('success', 'Booth created successfully.');
    }

    /**
     * Display the specified booth
     */
    public function show(Booth $booth)
    {
        $booth->load(['client', 'user', 'category', 'asset', 'boothType']);
        return view('booths.show', compact('booth'));
    }

    /**
     * Show the form for editing the specified booth
     */
    public function edit(Booth $booth)
    {
        $categories = Category::where('status', 1)->get();
        $assets = Asset::where('status', 1)->get();
        $boothTypes = BoothType::where('status', 1)->get();
        $clients = Client::orderBy('company')->get();

        return view('booths.edit', compact('booth', 'categories', 'assets', 'boothTypes', 'clients'));
    }

    /**
     * Update the specified booth
     */
    public function update(Request $request, Booth $booth)
    {
        $validated = $request->validate([
            'booth_number' => 'required|string|max:45|unique:booths,booth_number,' . $booth->id,
            'type' => 'required|integer',
            'price' => 'required|numeric|min:0',
            'status' => 'required|integer',
            'client_id' => 'nullable|exists:clients,id',
            'category_id' => 'nullable|exists:categories,id',
            'asset_id' => 'nullable|exists:assets,id',
            'booth_type_id' => 'nullable|exists:booth_types,id',
        ]);

        $booth->update($validated);

        return redirect()->route('booths.index')
            ->with('success', 'Booth updated successfully.');
    }

    /**
     * Remove the specified booth
     */
    public function destroy(Booth $booth)
    {
        if ($booth->status !== Booth::STATUS_AVAILABLE) {
            return back()->with('error', 'Cannot delete a booth that is not available.');
        }

        $booth->delete();

        return redirect()->route('booths.index')
            ->with('success', 'Booth deleted successfully.');
    }

    /**
     * Confirm reservation
     */
    public function confirmReservation($id)
    {
        $booth = Booth::findOrFail($id);
        
        if ($booth->status === Booth::STATUS_RESERVED || auth()->user()->isAdmin()) {
            $booth->update(['status' => Booth::STATUS_CONFIRMED]);
            
            return response()->json([
                'status' => 200,
                'message' => 'Reservation confirmed successfully.'
            ]);
        }

        return response()->json([
            'status' => 403,
            'message' => 'Unauthorized action.'
        ], 403);
    }

    /**
     * Clear reservation
     */
    public function clearReservation($id)
    {
        $booth = Booth::findOrFail($id);
        
        if ($booth->user_id === auth()->id() || auth()->user()->isAdmin()) {
            if ($booth->status === Booth::STATUS_RESERVED) {
                // Remove from book if exists
                if ($booth->book_id) {
                    $book = Book::find($booth->book_id);
                    if ($book) {
                        $boothIds = $book->booth_ids ?? [];
                        $boothIds = array_diff($boothIds, [$booth->id]);
                        
                        if (empty($boothIds)) {
                            $book->delete();
                        } else {
                            $book->update(['booth_ids' => array_values($boothIds)]);
                        }
                    }
                }

                $booth->update([
                    'status' => Booth::STATUS_AVAILABLE,
                    'client_id' => 0,
                    'user_id' => null,
                    'book_id' => 0,
                    'category_id' => null,
                    'sub_category_id' => null,
                    'asset_id' => null,
                    'booth_type_id' => null,
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => 'Reservation cleared successfully.'
                ]);
            }
        }

        return response()->json([
            'status' => 403,
            'message' => 'Unauthorized action.'
        ], 403);
    }

    /**
     * Mark booth as paid
     */
    public function markPaid($id)
    {
        $booth = Booth::findOrFail($id);
        
        if (($booth->user_id === auth()->id() || auth()->user()->isAdmin()) 
            && $booth->status === Booth::STATUS_CONFIRMED) {
            $booth->update(['status' => Booth::STATUS_PAID]);
            
            return response()->json([
                'status' => 200,
                'message' => 'Booth marked as paid successfully.'
            ]);
        }

        return response()->json([
            'status' => 403,
            'message' => 'Unauthorized action.'
        ], 403);
    }

    /**
     * Get user's booths
     */
    public function myBooths()
    {
        $booths = Booth::where('user_id', auth()->id())
            ->with(['client', 'category', 'asset'])
            ->orderBy('booth_number')
            ->get();

        return view('booths.my-booths', compact('booths'));
    }
}
