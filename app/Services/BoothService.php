<?php

namespace App\Services;

use App\Helpers\ActivityLogger;
use App\Models\Booth;
use App\Repositories\BoothRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BoothService
{
    public function __construct(
        private BoothRepository $repository
    ) {}

    /**
     * Create a new booth
     */
    public function createBooth(array $data, ?UploadedFile $image = null): Booth
    {
        // Handle default floor plan if not specified
        if (empty($data['floor_plan_id'])) {
            $defaultFloorPlan = $this->repository->getDefaultFloorPlan();
            if ($defaultFloorPlan) {
                $data['floor_plan_id'] = $defaultFloorPlan->id;
            }
        }

        // Check for duplicate booth number
        if ($this->repository->numberExists($data['booth_number'], null, $data['floor_plan_id'] ?? null)) {
            $errorMessage = ! empty($data['floor_plan_id'])
                ? 'This booth number already exists in this floor plan. Please choose a different number.'
                : 'This booth number already exists. Please choose a different number.';

            throw ValidationException::withMessages([
                'booth_number' => [$errorMessage],
            ]);
        }

        // Handle image upload
        if ($image) {
            $data['booth_image'] = $this->handleImageUpload($image);
        }

        // Create booth
        $booth = $this->repository->create($data);

        // Log activity first so we can link notification
        $activity = null;
        try {
            $activity = ActivityLogger::log('booth.created', $booth, 'Booth created: '.$booth->booth_number);
        } catch (\Exception $e) {
            Log::error('Failed to log booth creation activity: '.$e->getMessage());
        }

        try {
            NotificationService::notifyBoothAction('created', $booth, $booth->userid ?? null, $activity?->id);
        } catch (\Exception $e) {
            Log::error('Failed to send booth creation notification: '.$e->getMessage());
        }

        return $booth;
    }

    /**
     * Update an existing booth
     */
    public function updateBooth(Booth $booth, array $data, ?UploadedFile $image = null): Booth
    {
        $floorPlanId = $data['floor_plan_id'] ?? $booth->floor_plan_id;

        // Check for duplicate booth number (excluding current booth)
        if (isset($data['booth_number']) && $this->repository->numberExists(
            $data['booth_number'],
            $booth->id,
            $floorPlanId
        )) {
            $errorMessage = $floorPlanId
                ? 'This booth number already exists in this floor plan. Please choose a different number.'
                : 'This booth number already exists. Please choose a different number.';

            throw ValidationException::withMessages([
                'booth_number' => [$errorMessage],
            ]);
        }

        // Handle default floor plan if not specified
        if (empty($data['floor_plan_id']) && ! $booth->floor_plan_id) {
            $defaultFloorPlan = $this->repository->getDefaultFloorPlan();
            if ($defaultFloorPlan) {
                $data['floor_plan_id'] = $defaultFloorPlan->id;
            }
        } elseif (empty($data['floor_plan_id'])) {
            // Keep current floor plan if not specified
            $data['floor_plan_id'] = $booth->floor_plan_id;
        }

        // Handle image upload
        if ($image) {
            // Delete old image if exists
            if ($booth->booth_image && file_exists(public_path($booth->booth_image))) {
                File::delete(public_path($booth->booth_image));
            }
            $data['booth_image'] = $this->handleImageUpload($image, $booth->id);
        }

        // Track status change
        $oldStatus = $booth->status;
        $newStatus = $data['status'] ?? $booth->status;

        // Update booth
        $this->repository->update($booth, $data);
        $booth->refresh();

        $activity = null;
        try {
            $activity = ActivityLogger::log('booth.updated', $booth, 'Booth updated: '.$booth->booth_number);
        } catch (\Exception $e) {
            Log::error('Failed to log booth update activity: '.$e->getMessage());
        }

        try {
            if ($oldStatus != $newStatus) {
                NotificationService::notifyBoothStatusChange($booth, $oldStatus, $newStatus, $activity?->id);
            } else {
                NotificationService::notifyBoothAction('updated', $booth, $booth->userid ?? null, $activity?->id);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send booth update notification: '.$e->getMessage());
        }

        return $booth;
    }

    /**
     * Delete a booth
     */
    public function deleteBooth(Booth $booth): bool
    {
        // Check if booth has active booking
        if ($booth->bookid) {
            throw ValidationException::withMessages([
                'booth' => ['Cannot delete booth with active booking. Please cancel the booking first.'],
            ]);
        }

        $boothNumber = $booth->booth_number;
        $boothUserId = $booth->userid;

        if ($booth->booth_image && file_exists(public_path($booth->booth_image))) {
            File::delete(public_path($booth->booth_image));
        }

        $activity = null;
        try {
            $activity = ActivityLogger::log('booth.deleted', $booth, 'Booth deleted: '.$booth->booth_number);
        } catch (\Exception $e) {
            Log::error('Failed to log booth deletion activity: '.$e->getMessage());
        }

        try {
            $tempBooth = new Booth;
            $tempBooth->booth_number = $boothNumber;
            $tempBooth->userid = $boothUserId;
            NotificationService::notifyBoothAction('deleted', $tempBooth, $boothUserId, $activity?->id);
        } catch (\Exception $e) {
            Log::error('Failed to send booth deletion notification: '.$e->getMessage());
        }

        return $this->repository->delete($booth);
    }

    /**
     * Handle image upload
     */
    private function handleImageUpload(UploadedFile $image, ?int $boothId = null): string
    {
        $imageName = $boothId
            ? 'booth_'.$boothId.'_'.time().'.'.$image->getClientOriginalExtension()
            : 'booth_'.time().'_'.uniqid().'.'.$image->getClientOriginalExtension();

        $imagePath = 'images/booths';
        $fullPath = public_path($imagePath);

        // Create directory if it doesn't exist
        if (! file_exists($fullPath)) {
            File::makeDirectory($fullPath, 0755, true);
        }

        // Move uploaded file
        $image->move($fullPath, $imageName);

        return $imagePath.'/'.$imageName;
    }

    /**
     * Get booth statistics
     */
    public function getStatistics(?int $floorPlanId = null): object
    {
        return $this->repository->getStatistics($floorPlanId);
    }

    /**
     * Check if booth number is duplicate
     */
    public function checkDuplicate(string $boothNumber, ?int $excludeId = null, ?int $floorPlanId = null): bool
    {
        return $this->repository->numberExists($boothNumber, $excludeId, $floorPlanId);
    }

    /**
     * Confirm reservation (change status from RESERVED to CONFIRMED)
     */
    public function confirmReservation(Booth $booth, int $userId, bool $isAdmin = false): void
    {
        // Check authorization
        if ($booth->userid !== $userId && ! $isAdmin) {
            throw ValidationException::withMessages([
                'booth' => ['You do not have permission to confirm this reservation.'],
            ]);
        }

        // Only confirm if status is RESERVED or user is admin
        if ($booth->status !== Booth::STATUS_RESERVED && ! $isAdmin) {
            throw ValidationException::withMessages([
                'booth' => ['These actions are available for registered booths only.'],
            ]);
        }

        DB::beginTransaction();

        try {
            $oldStatus = $booth->status;
            $this->repository->update($booth, ['status' => Booth::STATUS_CONFIRMED]);
            $booth->refresh();

            // Send notification
            try {
                NotificationService::notifyBoothStatusChange($booth, $oldStatus, Booth::STATUS_CONFIRMED);
            } catch (\Exception $e) {
                Log::error('Failed to send status change notification: '.$e->getMessage());
            }

            // Log activity
            try {
                ActivityLogger::log('booth.status_changed', $booth,
                    'Booth reservation confirmed: '.$booth->booth_number);
            } catch (\Exception $e) {
                Log::error('Failed to log booth status change activity: '.$e->getMessage());
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Clear reservation (change status from RESERVED to AVAILABLE)
     */
    public function clearReservation(Booth $booth, int $userId): void
    {
        // Only allow if user owns the booth
        if ($booth->userid !== $userId) {
            throw ValidationException::withMessages([
                'booth' => ['You do not have permission to clear this reservation.'],
            ]);
        }

        // Only clear if status is RESERVED
        if ($booth->status !== Booth::STATUS_RESERVED) {
            throw ValidationException::withMessages([
                'booth' => ['Please Check Data Before Submit'],
            ]);
        }

        DB::beginTransaction();

        try {
            // Remove from book table
            if ($booth->bookid) {
                $book = \App\Models\Book::find($booth->bookid);
                if ($book) {
                    $bookBooths = json_decode($book->boothid, true) ?? [];
                    $bookBooths = array_values(array_diff($bookBooths, [$booth->id]));

                    if (count($bookBooths) > 0) {
                        $book->boothid = json_encode($bookBooths);
                        $book->save();
                    } else {
                        $book->delete();
                    }
                }
            }

            // Reset booth to available
            $this->repository->update($booth, [
                'status' => Booth::STATUS_AVAILABLE,
                'client_id' => null,
                'userid' => null,
                'bookid' => null,
                'category_id' => null,
                'sub_category_id' => null,
                'asset_id' => null,
                'booth_type_id' => null,
            ]);

            // Log activity
            try {
                ActivityLogger::log('booth.reservation_cleared', $booth,
                    'Booth reservation cleared: '.$booth->booth_number);
            } catch (\Exception $e) {
                Log::error('Failed to log booth reservation clear activity: '.$e->getMessage());
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Mark booth as paid (change status from CONFIRMED to PAID)
     */
    public function markPaid(Booth $booth, int $userId, bool $isAdmin = false): void
    {
        // Check authorization
        if ($booth->userid !== $userId && ! $isAdmin) {
            throw ValidationException::withMessages([
                'booth' => ['You do not have permission to mark this booth as paid.'],
            ]);
        }

        // Only mark as paid if status is CONFIRMED or user is admin
        if ($booth->status !== Booth::STATUS_CONFIRMED && ! $isAdmin) {
            throw ValidationException::withMessages([
                'booth' => ['These actions are available for registered booths only.'],
            ]);
        }

        DB::beginTransaction();

        try {
            $oldStatus = $booth->status;
            $this->repository->update($booth, ['status' => Booth::STATUS_PAID]);
            $booth->refresh();

            // Send notification
            try {
                NotificationService::notifyPaymentReceived($booth, $booth->price ?? 0);
                NotificationService::notifyBoothStatusChange($booth, $oldStatus, Booth::STATUS_PAID);
            } catch (\Exception $e) {
                Log::error('Failed to send payment notification: '.$e->getMessage());
            }

            // Log activity
            try {
                ActivityLogger::log('booth.marked_paid', $booth,
                    'Booth marked as paid: '.$booth->booth_number);
            } catch (\Exception $e) {
                Log::error('Failed to log booth payment activity: '.$e->getMessage());
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove booth from booking
     */
    public function removeBoothFromBooking(Booth $booth, int $userId, bool $isAdmin = false): void
    {
        // Check authorization: booth must not be available, and user must own it or be admin
        if ($booth->status === Booth::STATUS_AVAILABLE) {
            throw ValidationException::withMessages([
                'booth' => ['Please Check Data Before Submit'],
            ]);
        }

        if ($booth->userid !== $userId && ! $isAdmin) {
            throw ValidationException::withMessages([
                'booth' => ['You do not have permission to remove this booth from booking.'],
            ]);
        }

        DB::beginTransaction();

        try {
            // Remove from book table
            if ($booth->bookid) {
                $book = \App\Models\Book::find($booth->bookid);
                if ($book) {
                    $bookBooths = json_decode($book->boothid, true) ?? [];
                    $bookBooths = array_values(array_diff($bookBooths, [$booth->id]));

                    if (count($bookBooths) > 0) {
                        $book->boothid = json_encode($bookBooths);
                        $book->save();
                    } else {
                        $book->delete();
                    }
                }
            }

            // Reset booth to available
            $this->repository->update($booth, [
                'status' => Booth::STATUS_AVAILABLE,
                'client_id' => null,
                'userid' => null,
                'bookid' => null,
                'category_id' => null,
                'sub_category_id' => null,
                'asset_id' => null,
                'booth_type_id' => null,
            ]);

            // Log activity
            try {
                ActivityLogger::log('booth.removed_from_booking', $booth,
                    'Booth removed from booking: '.$booth->booth_number);
            } catch (\Exception $e) {
                Log::error('Failed to log booth removal activity: '.$e->getMessage());
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update booth position and styling
     */
    public function updatePosition(Booth $booth, array $data): Booth
    {
        // Prepare position data with proper type casting
        $updateData = [];

        if (isset($data['position_x'])) {
            $updateData['position_x'] = $data['position_x'] !== null ? (float) $data['position_x'] : null;
        }
        if (isset($data['position_y'])) {
            $updateData['position_y'] = $data['position_y'] !== null ? (float) $data['position_y'] : null;
        }
        if (isset($data['width'])) {
            $updateData['width'] = $data['width'] !== null ? (float) $data['width'] : null;
        }
        if (isset($data['height'])) {
            $updateData['height'] = $data['height'] !== null ? (float) $data['height'] : null;
        }
        if (isset($data['rotation'])) {
            $updateData['rotation'] = $data['rotation'] !== null ? (float) $data['rotation'] : 0;
        }
        if (isset($data['z_index'])) {
            $updateData['z_index'] = $data['z_index'] !== null ? (int) $data['z_index'] : 10;
        }
        if (isset($data['font_size'])) {
            $updateData['font_size'] = $data['font_size'] !== null ? (int) $data['font_size'] : 14;
        }
        if (isset($data['border_width'])) {
            $updateData['border_width'] = $data['border_width'] !== null ? (int) $data['border_width'] : 2;
        }
        if (isset($data['border_radius'])) {
            $updateData['border_radius'] = $data['border_radius'] !== null ? (int) $data['border_radius'] : 6;
        }
        if (isset($data['opacity'])) {
            $updateData['opacity'] = $data['opacity'] !== null ? (float) $data['opacity'] : 1.00;
        }

        // Appearance properties
        $appearanceFields = [
            'background_color', 'border_color', 'text_color', 'font_weight',
            'font_family', 'text_align', 'box_shadow', 'price',
        ];

        foreach ($appearanceFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        $this->repository->update($booth, $updateData);
        $booth->refresh();

        return $booth;
    }

    /**
     * Bulk update booth positions
     */
    public function bulkUpdatePositions(array $boothsData): array
    {
        $saved = 0;
        $errors = [];

        foreach ($boothsData as $boothData) {
            try {
                $booth = $this->repository->find($boothData['id']);
                if (! $booth) {
                    $errors[] = "Booth ID {$boothData['id']} not found.";

                    continue;
                }

                $this->updatePosition($booth, $boothData);
                $saved++;
            } catch (\Exception $e) {
                $errors[] = "Booth ID {$boothData['id']}: ".$e->getMessage();
                Log::error('Error updating booth position in bulk: '.$e->getMessage(), [
                    'booth_id' => $boothData['id'] ?? null,
                ]);
            }
        }

        return [
            'saved' => $saved,
            'errors' => $errors,
        ];
    }

    /**
     * Update external view status (toggle between available and hidden)
     */
    public function updateExternalView(Booth $booth): Booth
    {
        $newStatus = $booth->status === Booth::STATUS_AVAILABLE
            ? Booth::STATUS_HIDDEN
            : Booth::STATUS_AVAILABLE;

        $oldStatus = $booth->status;
        $this->repository->update($booth, ['status' => $newStatus]);
        $booth->refresh();

        // Send notification if status changed
        try {
            NotificationService::notifyBoothStatusChange($booth, $oldStatus, $newStatus);
        } catch (\Exception $e) {
            Log::error('Failed to send external view status change notification: '.$e->getMessage());
        }

        return $booth;
    }
}
