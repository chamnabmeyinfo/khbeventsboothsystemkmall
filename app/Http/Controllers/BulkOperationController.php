<?php

namespace App\Http\Controllers;

use App\Models\Booth;
use App\Models\Client;
use App\Models\Book;
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
                'message' => 'Error: ' . $e->getMessage(),
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
                'message' => 'Error: ' . $e->getMessage(),
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
                'message' => 'Error: ' . $e->getMessage(),
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
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
