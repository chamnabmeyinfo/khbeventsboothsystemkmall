<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Client;
use App\Models\Booth;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with(['client', 'user'])->latest()->paginate(20);
        return view('books.index', compact('books'));
    }

    public function create()
    {
        $clients = Client::orderBy('company')->get();
        $booths = Booth::where('status', Booth::STATUS_AVAILABLE)->orderBy('booth_number')->get();
        return view('books.create', compact('clients', 'booths'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'booth_ids' => 'required|array|min:1',
            'booth_ids.*' => 'exists:booths,id',
            'date_book' => 'required|date',
            'type' => 'nullable|integer',
        ]);

        // Check if all booths are available
        $booths = Booth::whereIn('id', $validated['booth_ids'])
            ->where('status', '!=', Booth::STATUS_AVAILABLE)
            ->get();

        if ($booths->count() > 0) {
            return back()->withErrors([
                'booth_ids' => 'Some selected booths are not available.'
            ])->withInput();
        }

        $book = Book::create([
            'client_id' => $validated['client_id'],
            'booth_ids' => $validated['booth_ids'],
            'date_book' => $validated['date_book'],
            'user_id' => auth()->id(),
            'type' => $validated['type'] ?? 1,
        ]);

        // Update booths status
        Booth::whereIn('id', $validated['booth_ids'])->update([
            'status' => Booth::STATUS_RESERVED,
            'client_id' => $validated['client_id'],
            'user_id' => auth()->id(),
            'book_id' => $book->id,
        ]);

        return redirect()->route('books.index')
            ->with('success', 'Booking created successfully.');
    }

    public function show(Book $book)
    {
        $book->load(['client', 'user']);
        return view('books.show', compact('book'));
    }
}
