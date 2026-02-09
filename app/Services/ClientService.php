<?php

namespace App\Services;

use App\Helpers\ActivityLogger;
use App\Models\Client;
use App\Repositories\ClientRepository;
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

        // Send notification
        try {
            NotificationService::notifyClientAction($isUpdate ? 'updated' : 'created', $client, auth()->id());
        } catch (\Exception $e) {
            Log::error('Failed to send client notification: '.$e->getMessage());
        }

        // Log activity
        try {
            ActivityLogger::log('client.'.($isUpdate ? 'updated' : 'created'), $client,
                ($isUpdate ? 'Client updated (existing email found)' : 'Client created').': '.($client->company ?? $client->name));
        } catch (\Exception $e) {
            Log::error('Failed to log client activity: '.$e->getMessage());
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

        // Update client
        $this->repository->update($client, $data);
        $client->refresh();

        // Send notification
        try {
            NotificationService::notifyClientAction('updated', $client, auth()->id());
        } catch (\Exception $e) {
            Log::error('Failed to send client update notification: '.$e->getMessage());
        }

        // Log activity
        try {
            ActivityLogger::log('client.updated', $client, 'Client updated: '.($client->company ?? $client->name));
        } catch (\Exception $e) {
            Log::error('Failed to log client update activity: '.$e->getMessage());
        }

        return $client;
    }

    /**
     * Delete a client
     */
    public function deleteClient(Client $client): bool
    {
        // Send notification before deletion
        try {
            NotificationService::notifyClientAction('deleted', $client, auth()->id());
        } catch (\Exception $e) {
            Log::error('Failed to send client deletion notification: '.$e->getMessage());
        }

        // Log activity before deletion
        try {
            ActivityLogger::log('client.deleted', $client, 'Client deleted: '.($client->company ?? $client->name));
        } catch (\Exception $e) {
            Log::error('Failed to log client deletion activity: '.$e->getMessage());
        }

        // Delete client
        return $this->repository->delete($client);
    }

    /**
     * Get clients with filters and pagination
     *
     * @return array{clients: \Illuminate\Database\Eloquent\Collection, total: int, sortBy: string, sortDir: string}
     */
    public function getClients(array $filters, int $perPage = 20, int $page = 1): array
    {
        $query = Client::withCount(['booths', 'books']);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['company'])) {
            $query->where('company', 'like', "%{$filters['company']}%");
        }

        $sortBy = $filters['sort_by'] ?? 'company';
        $sortDir = $filters['sort_dir'] ?? 'asc';
        if (in_array($sortBy, ['company', 'name', 'position', 'phone_number'])) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('company', 'asc');
        }

        $total = $query->count();
        $clients = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

        return [
            'clients' => $clients,
            'total' => $total,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
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
