<?php

namespace App\Http\Controllers;

use App\Models\Booth;
use App\Models\Client;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Global search across all entities
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $type = $request->input('type', 'all'); // all, booths, clients, bookings, users

        if (empty($query)) {
            return response()->json(['results' => []]);
        }

        $results = [];

        if ($type === 'all' || $type === 'booths') {
            $booths = Booth::where('booth_number', 'like', "%{$query}%")
                ->orWhereHas('client', function($q) use ($query) {
                    $q->where('company', 'like', "%{$query}%")
                      ->orWhere('name', 'like', "%{$query}%");
                })
                ->with(['client', 'category'])
                ->limit(10)
                ->get();

            foreach ($booths as $booth) {
                $results[] = [
                    'type' => 'booth',
                    'id' => $booth->id,
                    'title' => 'Booth #' . $booth->booth_number,
                    'description' => ($booth->client ? $booth->client->company : 'No client') . ' - ' . $booth->getStatusLabel(),
                    'url' => route('booths.show', $booth),
                    'icon' => 'fas fa-cube',
                ];
            }
        }

        if ($type === 'all' || $type === 'clients') {
            $clients = Client::where('company', 'like', "%{$query}%")
                ->orWhere('name', 'like', "%{$query}%")
                ->orWhere('phone_number', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            foreach ($clients as $client) {
                $results[] = [
                    'type' => 'client',
                    'id' => $client->id,
                    'title' => $client->company ?? $client->name,
                    'description' => $client->name . ($client->phone_number ? ' - ' . $client->phone_number : ''),
                    'url' => route('clients.show', $client),
                    'icon' => 'fas fa-building',
                ];
            }
        }

        if ($type === 'all' || $type === 'bookings') {
            $bookings = Book::whereHas('client', function($q) use ($query) {
                    $q->where('company', 'like', "%{$query}%")
                      ->orWhere('name', 'like', "%{$query}%");
                })
                ->with('client')
                ->limit(10)
                ->get();

            foreach ($bookings as $booking) {
                $results[] = [
                    'type' => 'booking',
                    'id' => $booking->id,
                    'title' => 'Booking #' . $booking->id,
                    'description' => ($booking->client ? $booking->client->company : 'N/A') . ' - ' . $booking->date_book->format('Y-m-d'),
                    'url' => route('books.show', $booking),
                    'icon' => 'fas fa-calendar-check',
                ];
            }
        }

        if ($type === 'all' || $type === 'users') {
            $users = User::where('username', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            foreach ($users as $user) {
                $results[] = [
                    'type' => 'user',
                    'id' => $user->id,
                    'title' => $user->username,
                    'description' => $user->isAdmin() ? 'Admin' : 'Sale',
                    'url' => route('users.show', $user),
                    'icon' => 'fas fa-user',
                ];
            }
        }

        return response()->json(['results' => $results]);
    }
}
