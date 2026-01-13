<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'role.permissions']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhereHas('role', function($roleQuery) use ($search) {
                      $roleQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by status
        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        // Filter by role (only if column exists)
        if ($request->filled('role_id') && \Illuminate\Support\Facades\Schema::hasColumn('user', 'role_id')) {
            if ($request->role_id == '0') {
                $query->whereNull('role_id');
            } else {
                $query->where('role_id', $request->role_id);
            }
        }
        
        $users = $query->orderBy('username')->paginate(20)->withQueryString();
        
        $roles = Role::where('is_active', true)->orderBy('name')->get();
        
        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::where('is_active', true)->orderBy('name')->get();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:45|unique:user,username',
            'password' => 'required|string|min:6|confirmed',
            'type' => 'required|integer|in:1,2',
            'status' => 'required|integer|in:0,1',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Return JSON if request expects JSON (for AJAX/modal requests)
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'type' => $user->type,
                    'status' => $user->status,
                    'role_id' => $user->role_id,
                ]
            ], 200);
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['role', 'role.permissions']);
        
        // Calculate affiliate/commission statistics
        $affiliateStats = $this->calculateAffiliateStats($user);
        
        return view('users.show', compact('user', 'affiliateStats'));
    }

    /**
     * Calculate affiliate statistics for a user
     */
    private function calculateAffiliateStats(User $user)
    {
        // Get affiliate bookings (bookings where this user is the affiliate)
        $affiliateBookings = \App\Models\Book::where('affiliate_user_id', $user->id)->get();
        
        // Calculate total revenue from affiliate bookings
        $totalRevenue = $affiliateBookings->sum(function($booking) {
            $booths = $booking->booths();
            return $booths->sum('price') ?? 0;
        });
        
        // Calculate statistics
        $stats = [
            'total_bookings' => $affiliateBookings->count(),
            'total_revenue' => $totalRevenue,
            'unique_clients' => $affiliateBookings->pluck('clientid')->unique()->count(),
            'unique_floor_plans' => $affiliateBookings->pluck('floor_plan_id')->unique()->count(),
            'avg_booking_value' => $affiliateBookings->count() > 0 ? round($totalRevenue / $affiliateBookings->count(), 2) : 0,
            'last_booking_at' => $affiliateBookings->max('date_book'),
            'first_booking_at' => $affiliateBookings->min('date_book'),
            'total_clicks' => \App\Models\AffiliateClick::where('affiliate_user_id', $user->id)->count(),
            'conversion_rate' => $affiliateBookings->count() > 0 && \App\Models\AffiliateClick::where('affiliate_user_id', $user->id)->count() > 0 
                ? round(($affiliateBookings->count() / \App\Models\AffiliateClick::where('affiliate_user_id', $user->id)->count()) * 100, 2) 
                : 0,
        ];
        
        // Get bookings by month (last 6 months)
        $bookingsByMonth = \App\Models\Book::where('affiliate_user_id', $user->id)
            ->where('date_book', '>=', now()->subMonths(6))
            ->select(DB::raw('DATE_FORMAT(date_book, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();
        
        $stats['bookings_by_month'] = $bookingsByMonth;
        
        // Get recent bookings (last 5)
        $stats['recent_bookings'] = $affiliateBookings->sortByDesc('date_book')->take(5);
        
        return $stats;
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $roles = Role::where('is_active', true)->orderBy('name')->get();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user
     * Matches Yii actionUpdate - only updates type and status
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'type' => 'required|integer|in:1,2',
            'status' => 'required|integer|in:0,1',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $user->update($validated);

        return redirect()->route('users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * Update user password
     * Matches Yii actionUpdate_pass
     */
    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.show', $user)
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Toggle user status (AJAX)
     * Matches Yii actionStatus
     */
    public function status($id, Request $request)
    {
        if (!$request->has('status')) {
            return response()->json([
                'status' => 400,
                'message' => 'Status parameter is required'
            ], 400);
        }

        $user = User::findOrFail($id);
        $status = $request->input('status');

        if ($status === 'true' || $status === true || $status === '1' || $status === 1) {
            $user->status = 1;
        } else {
            $user->status = 0;
        }

        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'User status updated successfully.'
        ]);
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}

