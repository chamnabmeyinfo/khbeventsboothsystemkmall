<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientRepository
{
    /**
     * Find a client by ID
     */
    public function find(int $id): ?Client
    {
        return Client::find($id);
    }

    /**
     * Find a client by ID with relationships
     */
    public function findWithRelations(int $id, array $relations = []): ?Client
    {
        $query = Client::query();

        if (! empty($relations)) {
            $query->with($relations);
        } else {
            $query->with(['booths', 'books.user']);
        }

        return $query->find($id);
    }

    /**
     * Create a new client
     */
    public function create(array $data): Client
    {
        return Client::create($data);
    }

    /**
     * Update a client
     */
    public function update(Client $client, array $data): bool
    {
        return $client->update($data);
    }

    /**
     * Delete a client
     */
    public function delete(Client $client): bool
    {
        return $client->delete();
    }

    /**
     * Find client by email
     */
    public function findByEmail(string $email): ?Client
    {
        return Client::where('email', $email)->first();
    }

    /**
     * Search clients
     */
    public function search(string $query, int $limit = 150): Collection
    {
        $clientsQuery = Client::query();

        // Filter to only include valid clients (must have at least name or company)
        $clientsQuery->where(function ($q) {
            $q->where(function ($subQ) {
                $subQ->whereNotNull('name')
                    ->where('name', '!=', '');
            })->orWhere(function ($subQ) {
                $subQ->whereNotNull('company')
                    ->where('company', '!=', '');
            });
        });

        if (! empty($query)) {
            $clientsQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('company', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%")
                    ->orWhere('phone_number', 'like', "%{$query}%");
            });
        }

        return $clientsQuery->orderByRaw('CASE WHEN company IS NULL OR company = "" THEN 1 ELSE 0 END')
            ->orderBy('company')
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * Get clients with pagination and filters
     */
    public function getWithFilters(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Client::withCount(['booths', 'books']);

        // Search functionality
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'company';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get clients by company
     */
    public function getByCompany(string $company): Collection
    {
        return Client::where('company', $company)->get();
    }

    /**
     * Check if email exists
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $query = Client::where('email', $email);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
