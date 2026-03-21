<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Booth;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkOperationController extends Controller
{
    /**
     * Bulk update booths
     */
    public function bulkUpdateBooths(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:booth,id',
            'field' => 'required|in:status,category_id,client_id',
            'value' => 'required',
        ]);

        $ids = $request->input('ids');
        $field = $request->input('field');
        $value = $request->input('value');

        try {
            DB::beginTransaction();

            $updated = Booth::whereIn('id', $ids)->update([$field => $value]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updated} booth(s)",
                'count' => $updated,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk delete booths
     */
    public function bulkDeleteBooths(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:booth,id',
        ]);

        try {
            DB::beginTransaction();

            $deleted = Booth::whereIn('id', $request->input('ids'))->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deleted} booth(s)",
                'count' => $deleted,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk update clients
     */
    public function bulkUpdateClients(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:client,id',
            'field' => 'required|in:position',
            'value' => 'required',
        ]);

        $ids = $request->input('ids');
        $field = $request->input('field');
        $value = $request->input('value');

        try {
            DB::beginTransaction();

            $updated = Client::whereIn('id', $ids)->update([$field => $value]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updated} client(s)",
                'count' => $updated,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk delete clients
     */
    public function bulkDeleteClients(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:client,id',
        ]);

        try {
            DB::beginTransaction();

            $deleted = Client::whereIn('id', $request->input('ids'))->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deleted} client(s)",
                'count' => $deleted,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Merge selected clients into the record with the lowest ID (fills empty fields, reassigns booths/bookings).
     */
    public function bulkMergeClients(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:2',
            'ids.*' => 'integer|exists:client,id',
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));
        sort($ids);
        $keepId = $ids[0];
        $mergeIds = array_slice($ids, 1);

        try {
            DB::beginTransaction();

            $keep = Client::findOrFail($keepId);

            foreach ($mergeIds as $mergeId) {
                $duplicate = Client::findOrFail($mergeId);
                $this->mergeClientScalarFields($keep, $duplicate);
                Booth::where('client_id', $duplicate->id)->update(['client_id' => $keep->id]);
                Book::where('clientid', $duplicate->id)->update(['clientid' => $keep->id]);
                $duplicate->delete();
                $keep->refresh();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Merged '.count($mergeIds).' client(s) into #'.$keepId.'.',
                'kept_id' => $keepId,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * @see ClientController::mergeClientData
     */
    private function mergeClientScalarFields(Client $keepClient, Client $duplicateClient): void
    {
        $fieldsToMerge = [
            'name', 'sex', 'position', 'company', 'company_name_khmer',
            'phone_number', 'phone_1',
            'email',
            'address', 'tax_id', 'website', 'notes',
        ];

        $updated = false;
        foreach ($fieldsToMerge as $field) {
            if (empty($keepClient->$field) && ! empty($duplicateClient->$field)) {
                $keepClient->$field = $duplicateClient->$field;
                $updated = true;
            }
        }

        if ($updated) {
            $keepClient->save();
        }
    }
}
