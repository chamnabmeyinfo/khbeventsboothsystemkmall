<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Booth;
use App\Repositories\BookingRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Schema;

class BookService
{
    public function __construct(
        private BookingRepository $repository
    ) {}

    /**
     * Get bookings with filters and pagination
     */
    public function getBookings(array $filters = [], int $perPage = 20, int $page = 1): array
    {
        $with = ['client', 'user', 'floorPlan', 'statusSetting'];
        if (Schema::hasTable('events')) {
            $with[] = 'floorPlan.event';
        }

        $query = Book::with($with);

        // Restrict to own bookings when setting is enabled
        if ($this->restrictToOwnBookings()) {
            $query->where('userid', auth()->id());
        }

        // Apply filters
        $this->applyFilters($query, $filters);

        // Get total count before pagination
        $total = $query->count();

        // Get paginated results
        $books = $query->latest('date_book')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Batch-load booths for all books to avoid N+1
        $boothsByBookId = $this->loadBoothsForBooks($books);

        return [
            'books' => $books,
            'total' => $total,
            'boothsByBookId' => $boothsByBookId,
        ];
    }

    /**
     * Get grouped bookings
     */
    public function getGroupedBookings(array $filters = [], string $groupBy = 'none'): array
    {
        $result = $this->getBookings($filters, 1000, 1); // Get all for grouping
        $books = $result['books'];

        $groupedBooks = [];
        if ($groupBy === 'name' && $books->count() > 0) {
            foreach ($books as $book) {
                $groupKey = $book->client ? ($book->client->company ?? $book->client->name ?? 'Unknown') : 'Unknown';
                if (! isset($groupedBooks[$groupKey])) {
                    $groupedBooks[$groupKey] = [];
                }
                $groupedBooks[$groupKey][] = $book;
            }
        } elseif ($groupBy === 'date' && $books->count() > 0) {
            foreach ($books as $book) {
                $groupKey = $book->date_book->format('Y-m-d');
                if (! isset($groupedBooks[$groupKey])) {
                    $groupedBooks[$groupKey] = [];
                }
                $groupedBooks[$groupKey][] = $book;
            }
        }

        return [
            'books' => $books,
            'groupedBooks' => $groupedBooks,
            'boothsByBookId' => $result['boothsByBookId'],
        ];
    }

    /**
     * Get booths for a booking
     */
    public function getBoothsForBooking(Book $book): Collection
    {
        $boothIds = json_decode($book->boothid, true) ?? [];
        if (empty($boothIds)) {
            return collect([]);
        }

        return Booth::whereIn('id', $boothIds)
            ->with(['client', 'category', 'subCategory', 'asset', 'boothType', 'floorPlan'])
            ->get();
    }

    /**
     * Delete multiple bookings
     */
    public function deleteAll(array $bookIds, bool $forceDeletePaid = false): array
    {
        $deleted = [];
        $skipped = [];
        $errors = [];

        foreach ($bookIds as $bookId) {
            try {
                $book = Book::find($bookId);
                if (! $book) {
                    $errors[] = ['book_id' => $bookId, 'error' => 'Booking not found'];

                    continue;
                }

                // Check if any booths are paid
                $boothIds = json_decode($book->boothid, true) ?? [];

                if (! empty($boothIds)) {
                    $paidBooths = Booth::whereIn('id', $boothIds)
                        ->where('status', Booth::STATUS_PAID)
                        ->get();

                    if ($paidBooths->count() > 0 && ! $forceDeletePaid) {
                        $skipped[] = [
                            'book_id' => $bookId,
                            'reason' => 'Contains paid booths',
                            'paid_booths' => $paidBooths->pluck('booth_number')->toArray(),
                        ];

                        continue;
                    }

                    // Release booths
                    Booth::whereIn('id', $boothIds)
                        ->where('bookid', $bookId)
                        ->update([
                            'status' => Booth::STATUS_AVAILABLE,
                            'client_id' => null,
                            'userid' => null,
                            'bookid' => null,
                        ]);
                }

                // Delete booking
                $book->delete();
                $deleted[] = $bookId;
            } catch (\Exception $e) {
                $errors[] = ['book_id' => $bookId, 'error' => $e->getMessage()];
            }
        }

        return [
            'deleted' => $deleted,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, array $filters): void
    {
        // Search functionality
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%");
            });
        }

        // Date filter
        if (! empty($filters['date_from'])) {
            $query->whereDate('date_book', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->whereDate('date_book', '<=', $filters['date_to']);
        }

        // Type filter
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Floor plan filter
        if (! empty($filters['floor_plan_id'])) {
            $query->where('floor_plan_id', $filters['floor_plan_id']);
        }

        // Status filter
        if (isset($filters['status'])) {
            $query->where('status', (int) $filters['status']);
        }

        // Amount range filter
        if (isset($filters['amount_min']) && is_numeric($filters['amount_min'])) {
            $query->where('total_amount', '>=', (float) $filters['amount_min']);
        }
        if (isset($filters['amount_max']) && is_numeric($filters['amount_max'])) {
            $query->where('total_amount', '<=', (float) $filters['amount_max']);
        }

        // Min booth count filter
        if (isset($filters['booth_count_min']) && is_numeric($filters['booth_count_min']) && (int) $filters['booth_count_min'] > 0) {
            $driver = $query->getConnection()->getDriverName();
            $minBooths = (int) $filters['booth_count_min'];
            if ($driver === 'mysql') {
                $query->whereRaw('JSON_LENGTH(COALESCE(boothid, \'[]\')) >= ?', [$minBooths]);
            } elseif ($driver === 'sqlite') {
                $query->whereRaw('json_array_length(COALESCE(boothid, \'[]\')) >= ?', [$minBooths]);
            }
        }

        // Date range filter
        if (! empty($filters['date_range']) && $filters['date_range'] !== 'all') {
            $now = now();
            switch ($filters['date_range']) {
                case 'today':
                    $query->whereDate('date_book', $now->toDateString());
                    break;
                case '3days':
                    $query->whereDate('date_book', '>=', $now->copy()->subDays(3)->toDateString());
                    break;
                case '7days':
                    $query->whereDate('date_book', '>=', $now->copy()->subDays(7)->toDateString());
                    break;
                case '14days':
                    $query->whereDate('date_book', '>=', $now->copy()->subDays(14)->toDateString());
                    break;
                case 'more':
                    $query->whereDate('date_book', '<', $now->copy()->subDays(14)->toDateString());
                    break;
            }
        }
    }

    /**
     * Load booths for multiple books efficiently (batch loading)
     */
    private function loadBoothsForBooks(Collection $books): array
    {
        $boothsByBookId = [];

        // Collect all booth IDs from all books
        $allBoothIds = [];
        foreach ($books as $book) {
            $boothIds = json_decode($book->boothid, true) ?? [];
            $allBoothIds = array_merge($allBoothIds, $boothIds);
        }

        // Batch load all booths in one query
        if (! empty($allBoothIds)) {
            $allBooths = Booth::whereIn('id', array_unique($allBoothIds))
                ->with(['client', 'category', 'subCategory', 'asset', 'boothType', 'floorPlan'])
                ->get()
                ->keyBy('id');

            // Group booths by book ID
            foreach ($books as $book) {
                $boothIds = json_decode($book->boothid, true) ?? [];
                $boothsByBookId[$book->id] = collect($boothIds)
                    ->map(function ($boothId) use ($allBooths) {
                        return $allBooths->get($boothId);
                    })
                    ->filter()
                    ->values();
            }
        }

        return $boothsByBookId;
    }

    /**
     * Get booths for booking modal
     */
    public function getBoothsForBookingModal(?int $floorPlanId = null): array
    {
        $boothsQuery = Booth::whereIn('status', [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN]);

        if ($floorPlanId) {
            $boothsQuery->where('floor_plan_id', $floorPlanId);
        }

        $booths = $boothsQuery->orderBy('booth_number')->get();

        // Group booths by first letter (A-Z) for tab view
        $boothsByLetter = [];

        foreach ($booths as $booth) {
            $firstChar = strtoupper(substr(trim($booth->booth_number), 0, 1));
            if (! ctype_alpha($firstChar)) {
                $firstChar = '#';
            }

            if (! isset($boothsByLetter[$firstChar])) {
                $boothsByLetter[$firstChar] = collect();
            }

            $boothsByLetter[$firstChar]->push($booth);
        }

        ksort($boothsByLetter);

        if (isset($boothsByLetter['#'])) {
            $numbersGroup = $boothsByLetter['#'];
            unset($boothsByLetter['#']);
            $boothsByLetter['#'] = $numbersGroup;
        }

        $boothsByCategory = [];
        foreach ($boothsByLetter as $letter => $boothCollection) {
            $boothsByCategory[$letter] = [
                'category' => (object) ['id' => $letter, 'name' => $letter, 'avatar' => null],
                'booths' => $boothCollection,
            ];
        }

        return [
            'booths' => $booths,
            'boothsByCategory' => $boothsByCategory,
        ];
    }

    /**
     * Check if user should be restricted to own bookings
     */
    private function restrictToOwnBookings(): bool
    {
        if (auth()->user()->isAdmin()) {
            return false;
        }

        try {
            $setting = \App\Models\Setting::where('key', 'restrict_bookings_to_own')->first();

            return $setting && $setting->value == '1';
        } catch (\Exception $e) {
            return false;
        }
    }
}
