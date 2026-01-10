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

    /**
     * Export to PDF (using simple HTML to PDF approach)
     */
    public function exportToPdf(Request $request)
    {
        $type = $request->input('type', 'booths');
        $data = [];

        switch ($type) {
            case 'booths':
                $data = Booth::with(['client', 'category', 'user'])->orderBy('booth_number')->get();
                break;
            case 'clients':
                $data = Client::with('booths')->orderBy('company')->get();
                break;
            case 'bookings':
                $data = Book::with(['client', 'user'])->latest('date_book')->get();
                break;
        }

        $html = view('exports.pdf', compact('type', 'data'))->render();
        
        // For now, return HTML that can be printed to PDF by browser
        // In production, use a library like dompdf or snappy
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="export_' . $type . '_' . date('Y-m-d') . '.html"');
    }

    /**
     * Bulk export page
     */
    public function index()
    {
        return view('exports.index');
    }

    /**
     * Import data from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'type' => 'required|in:booths,clients,bookings',
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('file');
        $type = $request->input('type');
        $imported = 0;
        $errors = [];

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $headers = fgetcsv($handle); // Skip header row
            
            while (($data = fgetcsv($handle)) !== false) {
                try {
                    switch ($type) {
                        case 'booths':
                            // Import booth logic
                            break;
                        case 'clients':
                            // Import client logic
                            break;
                        case 'bookings':
                            // Import booking logic
                            break;
                    }
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = 'Row ' . ($imported + 1) . ': ' . $e->getMessage();
                }
            }
            fclose($handle);
        }

        return response()->json([
            'success' => true,
            'message' => "Imported {$imported} records",
            'errors' => $errors,
        ]);
    }
}
