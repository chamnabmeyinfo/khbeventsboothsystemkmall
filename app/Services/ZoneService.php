<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Booth;
use App\Models\FloorPlan;
use App\Models\ZoneSetting;
use App\Repositories\BoothRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ZoneService
{
    public function __construct(
        private BoothRepository $boothRepository
    ) {}

    /**
     * Get zone settings for a specific zone and floor plan
     */
    public function getZoneSettings(string $zoneName, ?int $floorPlanId = null): array
    {
        $settings = ZoneSetting::getZoneDefaults($zoneName, $floorPlanId);

        return [
            'zone_name' => $zoneName,
            'floor_plan_id' => $floorPlanId,
            'settings' => $settings,
        ];
    }

    /**
     * Create booths in a zone
     */
    public function createBoothsInZone(
        string $zoneName,
        int $floorPlanId,
        array $options = []
    ): array {
        $floorPlan = FloorPlan::findOrFail($floorPlanId);

        // Get zone settings
        $zoneSettings = ZoneSetting::getZoneDefaults($zoneName, $floorPlanId);
        $zonePrice = $zoneSettings['price'] ?? 500;

        $createdBooths = [];
        $skippedBooths = [];
        $errors = [];

        // Determine mode: range or count
        if (isset($options['from']) && isset($options['to'])) {
            // Range mode
            $from = $options['from'];
            $to = $options['to'];
            $format = $options['format'] ?? 2;

            if ($from > $to) {
                throw ValidationException::withMessages([
                    'from' => ['"From" number must be less than or equal to "To" number'],
                ]);
            }

            $count = $to - $from + 1;
            if ($count > 100) {
                throw ValidationException::withMessages([
                    'range' => ['Maximum 100 booths can be created at once. Your range would create '.$count.' booths.'],
                ]);
            }

            // Create booths in range
            for ($i = $from; $i <= $to; $i++) {
                try {
                    $boothNumber = $zoneName.str_pad($i, $format, '0', STR_PAD_LEFT);

                    // Check if booth exists
                    if ($this->boothRepository->numberExists($boothNumber, null, $floorPlanId)) {
                        $skippedBooths[] = $boothNumber;

                        continue;
                    }

                    $booth = $this->createBoothInZone($boothNumber, $zoneName, $floorPlanId, $zoneSettings, $zonePrice);
                    $createdBooths[] = [
                        'id' => $booth->id,
                        'booth_number' => $booth->booth_number,
                        'status' => $booth->status,
                    ];
                } catch (\Exception $e) {
                    $errors[] = [
                        'booth_number' => $boothNumber ?? 'unknown',
                        'error' => $e->getMessage(),
                    ];
                }
            }
        } else {
            // Count mode
            $count = $options['count'] ?? 1;
            $customBoothNumber = $options['booth_number'] ?? null;

            for ($i = 0; $i < $count; $i++) {
                try {
                    $boothNumber = null;

                    if ($customBoothNumber && $i === 0) {
                        $boothNumber = $customBoothNumber;
                    } else {
                        $boothNumber = $this->generateNextBoothNumber($zoneName, $floorPlanId);
                    }

                    // Check if booth exists
                    if ($this->boothRepository->numberExists($boothNumber, null, $floorPlanId)) {
                        $skippedBooths[] = $boothNumber;
                        $boothNumber = $this->generateNextBoothNumber($zoneName, $floorPlanId);
                        if ($this->boothRepository->numberExists($boothNumber, null, $floorPlanId)) {
                            continue;
                        }
                    }

                    $booth = $this->createBoothInZone($boothNumber, $zoneName, $floorPlanId, $zoneSettings, $zonePrice);
                    $createdBooths[] = [
                        'id' => $booth->id,
                        'booth_number' => $booth->booth_number,
                        'status' => $booth->status,
                    ];
                } catch (\Exception $e) {
                    $errors[] = [
                        'index' => $i,
                        'error' => $e->getMessage(),
                    ];
                }
            }
        }

        // Save zone_about if provided
        if (isset($options['zone_about'])) {
            $existingSettings = ZoneSetting::getZoneDefaults($zoneName, $floorPlanId);
            $settingsToSave = array_merge($existingSettings, [
                'zone_about' => $options['zone_about'] ?: null,
            ]);
            ZoneSetting::saveZoneSettings($zoneName, $settingsToSave, $floorPlanId);
        }

        return [
            'created' => $createdBooths,
            'skipped' => $skippedBooths,
            'errors' => $errors,
            'floor_plan_id' => $floorPlanId,
            'floor_plan_name' => $floorPlan->name,
        ];
    }

    /**
     * Create a single booth in zone
     */
    private function createBoothInZone(
        string $boothNumber,
        string $zoneName,
        int $floorPlanId,
        array $zoneSettings,
        float $zonePrice
    ): Booth {
        $zoneAppearance = [
            'background_color' => $zoneSettings['background_color'] ?? null,
            'border_color' => $zoneSettings['border_color'] ?? null,
            'text_color' => $zoneSettings['text_color'] ?? null,
            'font_weight' => $zoneSettings['font_weight'] ?? null,
            'font_family' => $zoneSettings['font_family'] ?? null,
            'text_align' => $zoneSettings['text_align'] ?? null,
            'box_shadow' => $zoneSettings['box_shadow'] ?? null,
        ];

        return $this->boothRepository->create([
            'booth_number' => $boothNumber,
            'type' => 2,
            'price' => $zonePrice,
            'status' => Booth::STATUS_AVAILABLE,
            'floor_plan_id' => $floorPlanId,
            'background_color' => $zoneAppearance['background_color'],
            'border_color' => $zoneAppearance['border_color'],
            'text_color' => $zoneAppearance['text_color'],
            'font_weight' => $zoneAppearance['font_weight'],
            'font_family' => $zoneAppearance['font_family'],
            'text_align' => $zoneAppearance['text_align'],
            'box_shadow' => $zoneAppearance['box_shadow'],
        ]);
    }

    /**
     * Generate next available booth number for a zone
     */
    private function generateNextBoothNumber(string $zoneName, ?int $floorPlanId = null): string
    {
        $query = Booth::where('booth_number', 'LIKE', $zoneName.'%');

        if ($floorPlanId) {
            $query->where('floor_plan_id', $floorPlanId);
        }

        $zoneBooths = $query->get();

        if ($zoneBooths->isEmpty()) {
            return $zoneName.'01';
        }

        $maxNumber = 0;
        foreach ($zoneBooths as $booth) {
            $boothNumber = $booth->booth_number;
            if (preg_match('/^'.preg_quote($zoneName, '/').'[-_]?(\d+)/i', $boothNumber, $matches)) {
                $number = (int) $matches[1];
                if ($number > $maxNumber) {
                    $maxNumber = $number;
                }
            }
        }

        $nextNumber = $maxNumber + 1;

        return $zoneName.str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Delete booths from a zone
     */
    public function deleteBoothsInZone(
        string $zoneName,
        int $floorPlanId,
        string $mode,
        array $options = []
    ): array {
        $deletedBooths = [];
        $bookedBooths = [];
        $errors = [];
        $forceDelete = $options['force_delete_booked'] ?? false;

        if ($mode === 'all') {
            $zoneBooths = Booth::where('booth_number', 'LIKE', $zoneName.'%')
                ->where('floor_plan_id', $floorPlanId)
                ->get();

            foreach ($zoneBooths as $booth) {
                $result = $this->deleteBoothIfAllowed($booth, $forceDelete);
                if ($result['deleted']) {
                    $deletedBooths[] = $booth->booth_number;
                } elseif ($result['booked']) {
                    $bookedBooths[] = [
                        'booth_number' => $booth->booth_number,
                        'status' => $booth->getStatusLabel(),
                        'client' => $booth->client ? $booth->client->company : 'Unknown',
                    ];
                } else {
                    $errors[] = [
                        'booth_number' => $booth->booth_number,
                        'error' => $result['error'] ?? 'Unknown error',
                    ];
                }
            }
        } elseif ($mode === 'specific') {
            $boothIds = $options['booth_ids'] ?? [];

            foreach ($boothIds as $boothId) {
                try {
                    $booth = Booth::where('id', $boothId)
                        ->where('floor_plan_id', $floorPlanId)
                        ->firstOrFail();

                    $result = $this->deleteBoothIfAllowed($booth, $forceDelete);
                    if ($result['deleted']) {
                        $deletedBooths[] = $booth->booth_number;
                    } elseif ($result['booked']) {
                        $bookedBooths[] = [
                            'booth_number' => $booth->booth_number,
                            'status' => $booth->getStatusLabel(),
                            'client' => $booth->client ? $booth->client->company : 'Unknown',
                        ];
                    } else {
                        $errors[] = [
                            'booth_id' => $boothId,
                            'error' => $result['error'] ?? 'Unknown error',
                        ];
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'booth_id' => $boothId,
                        'error' => $e->getMessage(),
                    ];
                }
            }
        } elseif ($mode === 'range') {
            $from = $options['from'];
            $to = $options['to'];

            if ($from > $to) {
                throw ValidationException::withMessages([
                    'from' => ['"From" number must be less than or equal to "To" number'],
                ]);
            }

            for ($i = $from; $i <= $to; $i++) {
                for ($format = 2; $format <= 4; $format++) {
                    $boothNumber = $zoneName.str_pad($i, $format, '0', STR_PAD_LEFT);
                    $booth = Booth::where('booth_number', $boothNumber)
                        ->where('floor_plan_id', $floorPlanId)
                        ->first();

                    if ($booth) {
                        $result = $this->deleteBoothIfAllowed($booth, $forceDelete);
                        if ($result['deleted']) {
                            $deletedBooths[] = $boothNumber;
                        } elseif ($result['booked']) {
                            $bookedBooths[] = [
                                'booth_number' => $boothNumber,
                                'status' => $booth->getStatusLabel(),
                                'client' => $booth->client ? $booth->client->company : 'Unknown',
                            ];
                        } else {
                            $errors[] = [
                                'booth_number' => $boothNumber,
                                'error' => $result['error'] ?? 'Unknown error',
                            ];
                        }
                        break;
                    }
                }
            }
        }

        return [
            'deleted' => $deletedBooths,
            'booked_booths_skipped' => $bookedBooths,
            'errors' => $errors,
        ];
    }

    /**
     * Delete booth if allowed (handles booking cleanup)
     */
    private function deleteBoothIfAllowed(Booth $booth, bool $forceDelete): array
    {
        // Check if booth has active booking
        if ($booth->bookid && ! $forceDelete) {
            return ['deleted' => false, 'booked' => true];
        }

        try {
            // Handle bookings before deleting booth
            if ($booth->bookid) {
                $book = Book::find($booth->bookid);
                if ($book) {
                    $boothIds = json_decode($book->boothid, true) ?? [];
                    $boothIds = array_filter($boothIds, function ($id) use ($booth) {
                        return $id != $booth->id;
                    });

                    if (count($boothIds) > 0) {
                        $book->boothid = json_encode(array_values($boothIds));
                        $book->save();
                    } else {
                        $book->delete();
                    }
                }
            }

            $booth->delete();

            return ['deleted' => true, 'booked' => false];
        } catch (\Exception $e) {
            return ['deleted' => false, 'booked' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Save zone settings
     */
    public function saveZoneSettings(string $zoneName, array $settings, ?int $floorPlanId = null): void
    {
        ZoneSetting::saveZoneSettings($zoneName, $settings, $floorPlanId);

        // Log activity
        try {
            \App\Helpers\ActivityLogger::log('zone.settings_updated', null,
                'Zone settings updated: '.$zoneName.($floorPlanId ? ' (Floor Plan: '.$floorPlanId.')' : ''));
        } catch (\Exception $e) {
            Log::error('Failed to log zone settings update activity: '.$e->getMessage());
        }
    }
}
