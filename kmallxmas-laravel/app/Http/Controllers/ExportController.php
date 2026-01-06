<?php

namespace App\Http\Controllers;

use App\Models\Booth;
use App\Models\Client;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExportController extends Controller
{
    /**
     * Export booths to CSV
     */
    public function exportBooths(Request $request)
    {
        $booths = Booth::with(['client', 'category', 'user'])
            ->orderBy('booth_number')
            ->get();

        $filename = 'booths_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($booths) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Booth Number', 'Status', 'Client', 'Category', 'Price', 'User', 'Created']);
            
            // Data rows
            foreach ($booths as $booth) {
                fputcsv($file, [
                    $booth->booth_number,
                    $booth->getStatusLabel(),
                    $booth->client ? $booth->client->company : 'N/A',
                    $booth->category ? $booth->category->name : 'N/A',
                    number_format($booth->price, 2),
                    $booth->user ? $booth->user->username : 'N/A',
                    $booth->create_time ?? 'N/A'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export clients to CSV
     */
    public function exportClients(Request $request)
    {
        $clients = Client::with('booths')->orderBy('company')->get();

        $filename = 'clients_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($clients) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['ID', 'Company', 'Name', 'Position', 'Phone', 'Booths Count']);
            
            foreach ($clients as $client) {
                fputcsv($file, [
                    $client->id,
                    $client->company ?? 'N/A',
                    $client->name,
                    $client->position ?? 'N/A',
                    $client->phone_number ?? 'N/A',
                    $client->booths->count()
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export bookings to CSV
     */
    public function exportBookings(Request $request)
    {
        $books = Book::with(['client', 'user'])->latest('date_book')->get();

        $filename = 'bookings_export_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($books) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['ID', 'Date', 'Client', 'Booths', 'User', 'Type']);
            
            foreach ($books as $book) {
                $boothIds = json_decode($book->boothid, true) ?? [];
                fputcsv($file, [
                    $book->id,
                    $book->date_book->format('Y-m-d H:i:s'),
                    $book->client ? $book->client->company : 'N/A',
                    count($boothIds),
                    $book->user ? $book->user->username : 'N/A',
                    $book->type
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
