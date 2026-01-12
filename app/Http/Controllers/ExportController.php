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
        $skipped = 0;
        $errors = [];

        try {
            DB::beginTransaction();

            if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
                $headers = fgetcsv($handle); // Skip header row
                
                while (($data = fgetcsv($handle)) !== false) {
                    try {
                        // Skip empty rows
                        if (count($data) < 2 || empty(trim($data[0]))) {
                            continue;
                        }

                        switch ($type) {
                            case 'clients':
                                $result = $this->importClient($data, $headers);
                                if ($result['success']) {
                                    $imported++;
                                } else {
                                    $skipped++;
                                    if ($result['error']) {
                                        $errors[] = 'Row ' . ($imported + $skipped) . ': ' . $result['error'];
                                    }
                                }
                                break;
                                
                            case 'booths':
                                $result = $this->importBooth($data, $headers);
                                if ($result['success']) {
                                    $imported++;
                                } else {
                                    $skipped++;
                                    if ($result['error']) {
                                        $errors[] = 'Row ' . ($imported + $skipped) . ': ' . $result['error'];
                                    }
                                }
                                break;
                                
                            case 'bookings':
                                // Bookings import is complex and requires existing clients/booths
                                // Skipping for now - can be implemented if needed
                                $errors[] = 'Row ' . ($imported + $skipped) . ': Booking import not yet implemented';
                                $skipped++;
                                break;
                        }
                    } catch (\Exception $e) {
                        $skipped++;
                        $errors[] = 'Row ' . ($imported + $skipped) . ': ' . $e->getMessage();
                    }
                }
                fclose($handle);
            }

            DB::commit();

            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Import completed: {$imported} records imported" . ($skipped > 0 ? ", {$skipped} skipped" : ""),
                    'imported' => $imported,
                    'skipped' => $skipped,
                    'errors' => $errors,
                ]);
            }

            // Return redirect for regular form submissions
            return redirect()->route('export.index')
                ->with('success', "Import completed: {$imported} records imported" . ($skipped > 0 ? ", {$skipped} skipped" : ""))
                ->with('import_errors', $errors);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Return JSON for AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import failed: ' . $e->getMessage(),
                    'errors' => [$e->getMessage()],
                ], 500);
            }
            
            // Return redirect for regular form submissions
            return redirect()->route('export.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Import a single client from CSV row
     */
    private function importClient($data, $headers)
    {
        try {
            // Map headers to data (flexible column order)
            $headerMap = array_flip(array_map('strtolower', array_map('trim', $headers)));
            
            $name = isset($headerMap['name']) && isset($data[$headerMap['name']]) ? trim($data[$headerMap['name']]) : null;
            if (!$name) {
                return ['success' => false, 'error' => 'Name is required'];
            }

            // Check if client already exists (by name or phone)
            $phone = isset($headerMap['phone']) && isset($data[$headerMap['phone']]) ? trim($data[$headerMap['phone']]) : null;
            if ($phone) {
                $existing = Client::where('phone_number', $phone)->first();
                if ($existing) {
                    return ['success' => false, 'error' => 'Client with this phone already exists'];
                }
            }

            $client = Client::create([
                'name' => $name,
                'company' => isset($headerMap['company']) && isset($data[$headerMap['company']]) ? trim($data[$headerMap['company']]) : null,
                'position' => isset($headerMap['position']) && isset($data[$headerMap['position']]) ? trim($data[$headerMap['position']]) : null,
                'phone_number' => $phone,
                'sex' => isset($headerMap['sex']) && isset($data[$headerMap['sex']]) ? (int)$data[$headerMap['sex']] : null,
            ]);

            return ['success' => true, 'client' => $client];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Import a single booth from CSV row
     */
    private function importBooth($data, $headers)
    {
        try {
            $headerMap = array_flip(array_map('strtolower', array_map('trim', $headers)));
            
            $boothNumber = isset($headerMap['booth number']) && isset($data[$headerMap['booth number']]) 
                ? trim($data[$headerMap['booth number']]) 
                : (isset($headerMap['booth_number']) && isset($data[$headerMap['booth_number']]) 
                    ? trim($data[$headerMap['booth_number']]) 
                    : null);
            
            if (!$boothNumber) {
                return ['success' => false, 'error' => 'Booth number is required'];
            }

            // Check if booth already exists
            $existing = Booth::where('booth_number', $boothNumber)->first();
            if ($existing) {
                return ['success' => false, 'error' => "Booth {$boothNumber} already exists"];
            }

            $booth = Booth::create([
                'booth_number' => $boothNumber,
                'price' => isset($headerMap['price']) && isset($data[$headerMap['price']]) ? (float)$data[$headerMap['price']] : 0,
                'status' => isset($headerMap['status']) && isset($data[$headerMap['status']]) 
                    ? $this->parseBoothStatus($data[$headerMap['status']]) 
                    : Booth::STATUS_AVAILABLE,
                'type' => isset($headerMap['type']) && isset($data[$headerMap['type']]) ? (int)$data[$headerMap['type']] : 1,
            ]);

            return ['success' => true, 'booth' => $booth];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Parse booth status from string to constant
     */
    private function parseBoothStatus($status)
    {
        $status = strtolower(trim($status));
        
        if (in_array($status, ['available', 'free', 'open'])) {
            return Booth::STATUS_AVAILABLE;
        } elseif (in_array($status, ['reserved', 'booked'])) {
            return Booth::STATUS_RESERVED;
        } elseif (in_array($status, ['confirmed', 'confirm'])) {
            return Booth::STATUS_CONFIRMED;
        } elseif (in_array($status, ['paid', 'payment'])) {
            return Booth::STATUS_PAID;
        } elseif (in_array($status, ['hidden', 'hide'])) {
            return Booth::STATUS_HIDDEN;
        }
        
        return Booth::STATUS_AVAILABLE;
    }
}

