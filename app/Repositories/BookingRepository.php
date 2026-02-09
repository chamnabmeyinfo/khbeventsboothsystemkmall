<?php

namespace App\Repositories;

use App\Models\Book;
use App\Models\Booth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingRepository
{
    /**
     * Find a booking by ID
     */
    public function find(int $id): ?Book
    {
        return Book::find($id);
    }

    /**
     * Find a booking by ID with relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?Book
    {
        $query = Book::query();

        if (! empty($relations)) {
            $query->with($relations);
        } else {
            $query->with(['client', 'user', 'floorPlan', 'statusSetting']);
        }

        return $query->find($id);
    }

    /**
     * Create a new booking
     */
    public function create(array $data): Book
    {
        return Book::create($data);
    }

    /**
     * Update a booking
     */
    public function update(Book $booking, array $data): bool
    {
        return $booking->update($data);
    }

    /**
     * Delete a booking
     */
    public function delete(Book $booking): bool
    {
        return $booking->delete();
    }

    /**
     * Get bookings with filters
     */
    public function getWithFilters(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Book::with(['client', 'user', 'floorPlan', 'statusSetting']);

        // Restrict to own bookings if setting enabled
        if (isset($filters['user_id'])) {
            $query->where('userid', $filters['user_id']);
        }

        // Search functionality
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            })->orWhereHas('user', function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%");
            });
        }

        // Date filter
        if (isset($filters['date_from'])) {
            $query->whereDate('date_book', '>=', $filters['date_from']);
        }
        if (isset($filters['date_to'])) {
            $query->whereDate('date_book', '<=', $filters['date_to']);
        }

        // Type filter
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // Floor plan filter
        if (isset($filters['floor_plan_id'])) {
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
        if (isset($filters['date_range']) && $filters['date_range'] !== 'all') {
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

        return $query->latest('date_book')->paginate($perPage);
    }

    /**
     * Get bookings by user
     */
    public function getByUser(int $userId, array $relations = []): Collection
    {
        $query = Book::where('userid', $userId);

        if (! empty($relations)) {
            $query->with($relations);
        }

        return $query->latest('date_book')->get();
    }

    /**
     * Get bookings by client
     */
    public function getByClient(int $clientId, array $relations = []): Collection
    {
        $query = Book::where('clientid', $clientId);

        if (! empty($relations)) {
            $query->with($relations);
        }

        return $query->latest('date_book')->get();
    }

    /**
     * Get bookings by status
     */
    public function getByStatus(int $status, ?int $floorPlanId = null): Collection
    {
        $query = Book::where('status', $status);

        if ($floorPlanId) {
            $query->where('floor_plan_id', $floorPlanId);
        }

        return $query->get();
    }

    /**
     * Check if booths are available
     */
    public function checkBoothsAvailability(array $boothIds): array
    {
        $unavailableBooths = Booth::whereIn('id', $boothIds)
            ->whereNotIn('status', [Booth::STATUS_AVAILABLE, Booth::STATUS_HIDDEN])
            ->get();

        return [
            'available' => $unavailableBooths->isEmpty(),
            'unavailable_booths' => $unavailableBooths,
        ];
    }

    /**
     * Verify all booths exist
     */
    public function verifyBoothsExist(array $boothIds): bool
    {
        $boothsCount = Booth::whereIn('id', $boothIds)->count();

        return $boothsCount === count($boothIds);
    }

    /**
     * Get booths for booking
     */
    public function getBoothsForBooking(array $boothIds): Collection
    {
        return Booth::whereIn('id', $boothIds)->get();
    }

    /**
     * Calculate total amount from booths
     */
    public function calculateTotalAmount(array $boothIds): float
    {
        return Booth::whereIn('id', $boothIds)->sum('price');
    }

    /**
     * Bulk delete bookings
     */
    public function bulkDelete(array $bookingIds): int
    {
        return Book::whereIn('id', $bookingIds)->delete();
    }
}
