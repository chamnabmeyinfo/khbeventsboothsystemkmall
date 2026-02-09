<?php

namespace App\Repositories;

use App\Models\Booth;
use App\Models\FloorPlan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BoothRepository
{
    /**
     * Find a booth by ID
     */
    public function find(int $id): ?Booth
    {
        return Booth::find($id);
    }

    /**
     * Find a booth by ID with relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?Booth
    {
        $query = Booth::query();

        if (! empty($relations)) {
            $query->with($relations);
        } else {
            $query->with(['client', 'user', 'category', 'subCategory', 'asset', 'boothType', 'book', 'floorPlan']);
        }

        return $query->find($id);
    }

    /**
     * Create a new booth
     */
    public function create(array $data): Booth
    {
        return Booth::create($data);
    }

    /**
     * Update a booth
     */
    public function update(Booth $booth, array $data): bool
    {
        return $booth->update($data);
    }

    /**
     * Delete a booth
     */
    public function delete(Booth $booth): bool
    {
        return $booth->delete();
    }

    /**
     * Check if booth number exists in floor plan
     */
    public function numberExists(string $boothNumber, ?int $excludeId = null, ?int $floorPlanId = null): bool
    {
        $query = Booth::where('booth_number', $boothNumber);

        if ($floorPlanId !== null) {
            $query->where('floor_plan_id', $floorPlanId);
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get booths by floor plan
     */
    public function getByFloorPlan(int $floorPlanId, array $relations = []): Collection
    {
        $query = Booth::where('floor_plan_id', $floorPlanId);

        if (! empty($relations)) {
            $query->with($relations);
        }

        return $query->orderBy('booth_number')->get();
    }

    /**
     * Get booths by status
     */
    public function getByStatus(int $status, ?int $floorPlanId = null): Collection
    {
        $query = Booth::where('status', $status);

        if ($floorPlanId) {
            $query->where('floor_plan_id', $floorPlanId);
        }

        return $query->get();
    }

    /**
     * Get booths by user
     */
    public function getByUser(int $userId, array $relations = []): Collection
    {
        $query = Booth::where('userid', $userId);

        if (! empty($relations)) {
            $query->with($relations);
        }

        return $query->get();
    }

    /**
     * Get default floor plan
     */
    public function getDefaultFloorPlan(): ?FloorPlan
    {
        return FloorPlan::where('is_default', true)->first();
    }

    /**
     * Get booth statistics
     */
    public function getStatistics(?int $floorPlanId = null): object
    {
        $query = Booth::query();

        if ($floorPlanId) {
            $query->where('floor_plan_id', $floorPlanId);
        }

        return $query->selectRaw('
            COUNT(*) as total,
            SUM(CASE WHEN status IN ('.Booth::STATUS_AVAILABLE.', '.Booth::STATUS_HIDDEN.') THEN 1 ELSE 0 END) as available,
            SUM(CASE WHEN status = '.Booth::STATUS_RESERVED.' THEN 1 ELSE 0 END) as reserved,
            SUM(CASE WHEN status = '.Booth::STATUS_CONFIRMED.' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN status = '.Booth::STATUS_PAID.' THEN 1 ELSE 0 END) as paid
        ')->first();
    }

    /**
     * Get all booths with pagination
     */
    public function paginate(int $perPage = 20, array $filters = []): LengthAwarePaginator
    {
        $query = Booth::query();

        if (isset($filters['floor_plan_id'])) {
            $query->where('floor_plan_id', $filters['floor_plan_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['userid'])) {
            $query->where('userid', $filters['userid']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where('booth_number', 'like', "%{$search}%");
        }

        return $query->orderBy('booth_number')->paginate($perPage);
    }

    /**
     * Bulk delete booths
     */
    public function bulkDelete(array $boothIds): int
    {
        return Booth::whereIn('id', $boothIds)->delete();
    }

    /**
     * Bulk update booths
     */
    public function bulkUpdate(array $boothIds, array $data): int
    {
        return Booth::whereIn('id', $boothIds)->update($data);
    }
}
