<?php

namespace App\Services;

use App\Helpers\ActivityLogger;
use App\Models\Client;
use App\Repositories\ClientRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ClientService
{
    public function __construct(
        private ClientRepository $repository
    ) {}

    /**
     * Create a new client
     * Returns array with 'client' and 'isUpdate' flag
     */
    public function createClient(array $data): array
    {
        // Check if client with same email exists - if so, update it instead of creating new one
        $client = null;
        $isUpdate = false;

        if (! empty($data['email']) && $data['email'] !== null) {
            $existing = $this->repository->findByEmail($data['email']);
            if ($existing) {
                // Update existing client - overwrite with new data
                $this->repository->update($existing, $data);
                $client = $existing->refresh();
                $isUpdate = true;
            }
        }

        // If no existing client found, create new one
        if (! $client) {
            $client = $this->repository->create($data);
            $isUpdate = false;
        }

        $activity = null;
        try {
            $activity = ActivityLogger::log('client.'.($isUpdate ? 'updated' : 'created'), $client,
                ($isUpdate ? 'Client updated (existing email found)' : 'Client created').': '.($client->company ?? $client->name));
        } catch (\Exception $e) {
            Log::error('Failed to log client activity: '.$e->getMessage());
        }

        try {
            NotificationService::notifyClientAction($isUpdate ? 'updated' : 'created', $client, auth()->id(), $activity?->id);
        } catch (\Exception $e) {
            Log::error('Failed to send client notification: '.$e->getMessage());
        }

        return ['client' => $client, 'isUpdate' => $isUpdate];
    }

    /**
     * Update an existing client
     */
    public function updateClient(Client $client, array $data): Client
    {
        // Check email uniqueness if email is provided
        if (! empty($data['email']) && $data['email'] !== null) {
            if ($this->repository->emailExists($data['email'], $client->id)) {
                throw ValidationException::withMessages([
                    'email' => ['The email "'.$data['email'].'" has already been taken. Please use a different email or update the existing client.'],
                ]);
            }
        }

        $this->repository->update($client, $data);
        $client->refresh();

        $activity = null;
        try {
            $activity = ActivityLogger::log('client.updated', $client, 'Client updated: '.($client->company ?? $client->name));
        } catch (\Exception $e) {
            Log::error('Failed to log client update activity: '.$e->getMessage());
        }

        try {
            NotificationService::notifyClientAction('updated', $client, auth()->id(), $activity?->id);
        } catch (\Exception $e) {
            Log::error('Failed to send client update notification: '.$e->getMessage());
        }

        return $client;
    }

    /**
     * Delete a client
     */
    public function deleteClient(Client $client): bool
    {
        $activity = null;
        try {
            $activity = ActivityLogger::log('client.deleted', $client, 'Client deleted: '.($client->company ?? $client->name));
        } catch (\Exception $e) {
            Log::error('Failed to log client deletion activity: '.$e->getMessage());
        }

        try {
            NotificationService::notifyClientAction('deleted', $client, auth()->id(), $activity?->id);
        } catch (\Exception $e) {
            Log::error('Failed to send client deletion notification: '.$e->getMessage());
        }

        return $this->repository->delete($client);
    }

    /**
     * Text / scalar columns that support LIKE (or special handling) in list filters.
     *
     * @return list<string>
     */
    public static function clientListTextFilterColumns(): array
    {
        return [
            'name', 'company', 'company_name_khmer', 'position',
            'phone_number', 'phone_1',
            'email',
            'address', 'tax_id', 'website', 'notes',
        ];
    }

    /**
     * Columns available for ORDER BY (includes withCount aliases).
     *
     * @return list<string>
     */
    public static function clientListSortableColumns(): array
    {
        return array_values(array_unique(array_merge(
            ['id', 'sex'],
            self::clientListTextFilterColumns(),
            ['booths_count', 'books_count']
        )));
    }

    /**
     * Sanitize column filter input from the request (whitelist keys only).
     *
     * @return array<string, string|int>
     */
    public static function sanitizeClientListFilters(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        $out = [];
        $textCols = array_flip(self::clientListTextFilterColumns());

        foreach ($raw as $key => $val) {
            $key = is_string($key) ? $key : '';
            if ($key === '') {
                continue;
            }
            if (isset($textCols[$key])) {
                $s = trim((string) $val);
                if ($s !== '') {
                    $out[$key] = $s;
                }

                continue;
            }
            if ($key === 'sex' && $val !== '' && $val !== null) {
                $s = trim((string) $val);
                if ($s !== '' && ctype_digit($s)) {
                    $out['sex'] = (int) $s;
                }
            }
            if ($key === 'id' && $val !== '' && $val !== null) {
                $s = trim((string) $val);
                if ($s !== '' && ctype_digit($s)) {
                    $out['id'] = (int) $s;
                }
            }
            if (($key === 'booths_count' || $key === 'books_count') && $val !== '' && $val !== null && is_numeric($val)) {
                $out[$key] = max(0, (int) $val);
            }
        }

        return $out;
    }

    /**
     * Resolved sort column and direction after validation (for UI state).
     *
     * @return array{sortBy: string, sortDir: string}
     */
    public function resolveClientListSort(array $filters): array
    {
        return $this->normalizeClientListSort($filters);
    }

    private function normalizeClientListSort(array $filters): array
    {
        $sortBy = $filters['sort_by'] ?? 'company';
        $sortDir = strtolower((string) ($filters['sort_dir'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowed = self::clientListSortableColumns();
        if (! in_array($sortBy, $allowed, true)) {
            $sortBy = 'company';
        }

        return ['sortBy' => $sortBy, 'sortDir' => $sortDir];
    }

    private function likeTerm(string $value): string
    {
        $value = trim($value);

        return '%'.addcslashes($value, '%_\\').'%';
    }

    private function applyClientGlobalSearch(Builder $query, string $search): void
    {
        $search = trim($search);
        if ($search === '') {
            return;
        }

        $term = $this->likeTerm($search);

        $query->where(function ($q) use ($term, $search) {
            foreach (self::clientListTextFilterColumns() as $col) {
                $q->orWhere($col, 'like', $term);
            }
            if (ctype_digit($search)) {
                $id = (int) $search;
                $q->orWhere('id', $id)->orWhere('sex', $id);
            }
        });
    }

    /**
     * @param  array<string, string|int>  $filter
     */
    private function applyClientColumnFilters(Builder $query, array $filter): void
    {
        foreach (self::clientListTextFilterColumns() as $col) {
            if (empty($filter[$col]) || ! is_string($filter[$col])) {
                continue;
            }
            $query->where($col, 'like', $this->likeTerm($filter[$col]));
        }

        if (isset($filter['id']) && is_int($filter['id'])) {
            $query->where('id', $filter['id']);
        }

        if (isset($filter['sex']) && is_int($filter['sex'])) {
            $query->where('sex', $filter['sex']);
        }

        if (isset($filter['booths_count']) && is_int($filter['booths_count']) && $filter['booths_count'] > 0) {
            $query->has('booths', '>=', $filter['booths_count']);
        }

        if (isset($filter['books_count']) && is_int($filter['books_count']) && $filter['books_count'] > 0) {
            $query->has('books', '>=', $filter['books_count']);
        }
    }

    /**
     * Base query for client listing (filters + sort).
     */
    public function clientsListQuery(array $filters): Builder
    {
        $query = Client::withCount(['booths', 'books']);

        if (! empty($filters['search'])) {
            $this->applyClientGlobalSearch($query, (string) $filters['search']);
        }

        $columnFilter = $filters['filter'] ?? [];
        if (is_array($columnFilter) && $columnFilter !== []) {
            $this->applyClientColumnFilters($query, self::sanitizeClientListFilters($columnFilter));
        }

        $normalized = $this->normalizeClientListSort($filters);

        return $query->orderBy($normalized['sortBy'], $normalized['sortDir']);
    }

    /**
     * Paginated clients for the admin list (filters preserved via withQueryString in caller).
     */
    public function paginateClients(array $filters, int $perPage): LengthAwarePaginator
    {
        $perPage = max(5, min(100, $perPage));

        return $this->clientsListQuery($filters)->paginate($perPage)->withQueryString();
    }

    /**
     * Get clients with filters and pagination
     *
     * @return array{clients: \Illuminate\Database\Eloquent\Collection, total: int, sortBy: string, sortDir: string}
     */
    public function getClients(array $filters, int $perPage = 20, int $page = 1): array
    {
        $query = $this->clientsListQuery($filters);
        $normalized = $this->normalizeClientListSort($filters);
        $total = (clone $query)->count();
        $clients = (clone $query)->forPage($page, $perPage)->get();

        return [
            'clients' => $clients,
            'total' => $total,
            'sortBy' => $normalized['sortBy'],
            'sortDir' => $normalized['sortDir'],
        ];
    }

    /**
     * Get client statistics for dashboard KPIs
     *
     * @return array{total_clients: int, clients_with_bookings: int, clients_with_booths: int, total_bookings: int}
     */
    public function getClientStatistics(): array
    {
        $totalClients = Client::count();
        $clientsWithBookings = Client::has('books')->count();
        $clientsWithBooths = Client::has('booths')->count();
        $totalBookings = \App\Models\Book::count();

        return [
            'total_clients' => $totalClients,
            'clients_with_bookings' => $clientsWithBookings,
            'clients_with_booths' => $clientsWithBooths,
            'total_bookings' => $totalBookings,
        ];
    }

    /**
     * Get unique company names for filter dropdown
     *
     * @return array<int, string>
     */
    public function getUniqueCompanies(): array
    {
        return Client::whereNotNull('company')
            ->where('company', '!=', '')
            ->distinct()
            ->orderBy('company')
            ->pluck('company')
            ->toArray();
    }

    /**
     * Search clients
     */
    public function searchClients(string $query, ?int $clientId = null, int $limit = 150): array
    {
        // If specific ID is requested, return that client
        if ($clientId) {
            $client = $this->repository->find($clientId);
            if ($client) {
                $name = trim($client->name ?? '');
                $company = trim($client->company ?? '');

                // Only return if client is valid (has name or company)
                if (! empty($name) || ! empty($company)) {
                    return [[
                        'id' => $client->id,
                        'name' => $client->name ?? '',
                        'company' => $client->company ?? '',
                        'email' => $client->email ?? null,
                        'phone_number' => $client->phone_number ?? '',
                        'address' => $client->address ?? null,
                        'position' => $client->position ?? '',
                        'sex' => $client->sex ?? null,
                        'tax_id' => $client->tax_id ?? null,
                        'website' => $client->website ?? null,
                        'notes' => $client->notes ?? null,
                        'display_text' => ($client->company ?? $client->name ?? 'N/A').
                            ($client->email ? ' ('.$client->email.')' : '').
                            ($client->phone_number ? ' | '.$client->phone_number : ''),
                    ]];
                }
            }

            return [];
        }

        // Search clients
        $clients = $this->repository->search($query, $limit);

        return $clients->map(function ($client) {
            return [
                'id' => $client->id,
                'name' => $client->name ?? '',
                'company' => $client->company ?? '',
                'email' => $client->email ?? null,
                'phone_number' => $client->phone_number ?? '',
                'address' => $client->address ?? null,
                'position' => $client->position ?? '',
                'sex' => $client->sex ?? null,
                'tax_id' => $client->tax_id ?? null,
                'website' => $client->website ?? null,
                'notes' => $client->notes ?? null,
                'display_text' => ($client->company ?? $client->name ?? 'N/A').
                    ($client->email ? ' ('.$client->email.')' : '').
                    ($client->phone_number ? ' | '.$client->phone_number : ''),
            ];
        })->toArray();
    }
}
