<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booth;
use App\Models\Book;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceController extends Controller
{
    /**
     * Display financial dashboard
     */
    public function dashboard(Request $request)
    {
        // Date range filter
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Floor plan filter
        $floorPlanId = $request->input('floor_plan_id');
        
        // KPIs
        $stats = $this->getFinancialStats($startDate, $endDate, $floorPlanId);
        
        // Charts data
        $revenueByZone = $this->getRevenueByZone($floorPlanId);
        $revenueByCategory = $this->getRevenueByCategory($floorPlanId);
        $paymentTrends = $this->getPaymentTrends($startDate, $endDate, $floorPlanId);
        $topClients = $this->getTopClients(10, $floorPlanId);
        
        // Get floor plans for filter
        $floorPlans = \App\Models\FloorPlan::where('is_active', true)->get();
        
        return view('finance.dashboard', compact(
            'stats',
            'revenueByZone',
            'revenueByCategory',
            'paymentTrends',
            'topClients',
            'floorPlans',
            'startDate',
            'endDate',
            'floorPlanId'
        ));
    }
    
    /**
     * Get financial statistics
     */
    private function getFinancialStats($startDate, $endDate, $floorPlanId = null)
    {
        $query = Booth::query();
        
        if ($floorPlanId) {
            $query->where('floor_plan_id', $floorPlanId);
        }
        
        // Total booths and revenue potential
        $totalBooths = (clone $query)->count();
        $totalRevenue = (clone $query)->sum('price');
        
        // Booked booths and collected revenue
        $bookedBooths = (clone $query)->whereNotNull('bookid')->count();
        $collectedRevenue = (clone $query)
            ->where('status', 5) // Paid status
            ->sum('price');
        
        // Deposit and balance stats
        $totalDeposits = (clone $query)->sum('deposit_paid');
        $totalBalance = (clone $query)->sum('balance_paid');
        
        // Pending payments
        $pendingDeposits = (clone $query)
            ->whereNotNull('bookid')
            ->where(function($q) {
                $q->where('deposit_amount', '>', DB::raw('deposit_paid'))
                  ->orWhere('deposit_paid', 0);
            })
            ->sum(DB::raw('(deposit_amount - deposit_paid)'));
        
        $pendingBalance = (clone $query)
            ->whereNotNull('bookid')
            ->where('balance_due', '>', DB::raw('balance_paid'))
            ->sum(DB::raw('(balance_due - balance_paid)'));
        
        // Overdue payments
        $overduePayments = (clone $query)
            ->whereNotNull('bookid')
            ->where('payment_due_date', '<', Carbon::now())
            ->where('payment_status', '!=', 'paid')
            ->count();
        
        // Occupancy rate
        $occupancyRate = $totalBooths > 0 ? ($bookedBooths / $totalBooths) * 100 : 0;
        
        // Collection rate
        $collectionRate = $totalRevenue > 0 ? ($collectedRevenue / $totalRevenue) * 100 : 0;
        
        return [
            'total_booths' => $totalBooths,
            'total_revenue' => $totalRevenue,
            'booked_booths' => $bookedBooths,
            'collected_revenue' => $collectedRevenue,
            'total_deposits' => $totalDeposits,
            'total_balance' => $totalBalance,
            'pending_deposits' => $pendingDeposits,
            'pending_balance' => $pendingBalance,
            'total_pending' => $pendingDeposits + $pendingBalance,
            'overdue_payments' => $overduePayments,
            'occupancy_rate' => round($occupancyRate, 1),
            'collection_rate' => round($collectionRate, 1),
            'available_booths' => $totalBooths - $bookedBooths,
        ];
    }
    
    /**
     * Get revenue by zone
     */
    private function getRevenueByZone($floorPlanId = null)
    {
        $query = Booth::select(
            DB::raw('LEFT(booth_number, 1) as zone'),
            DB::raw('SUM(price) as total_revenue'),
            DB::raw('COUNT(*) as booth_count'),
            DB::raw('SUM(CASE WHEN status = 5 THEN price ELSE 0 END) as collected')
        )->groupBy('zone');
        
        if ($floorPlanId) {
            $query->where('floor_plan_id', $floorPlanId);
        }
        
        return $query->get();
    }
    
    /**
     * Get revenue by category
     */
    private function getRevenueByCategory($floorPlanId = null)
    {
        $query = Booth::select(
            'categories.name',
            DB::raw('SUM(booth.price) as total_revenue'),
            DB::raw('COUNT(booth.id) as booth_count')
        )
        ->leftJoin('categories', 'booth.category_id', '=', 'categories.id')
        ->groupBy('categories.id', 'categories.name');
        
        if ($floorPlanId) {
            $query->where('booth.floor_plan_id', $floorPlanId);
        }
        
        return $query->get();
    }
    
    /**
     * Get payment trends over time
     */
    private function getPaymentTrends($startDate, $endDate, $floorPlanId = null)
    {
        $query = Booth::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as bookings'),
            DB::raw('SUM(price) as revenue')
        )
        ->whereNotNull('bookid')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date');
        
        if ($floorPlanId) {
            $query->where('floor_plan_id', $floorPlanId);
        }
        
        return $query->get();
    }
    
    /**
     * Get top clients by revenue
     */
    private function getTopClients($limit = 10, $floorPlanId = null)
    {
        $query = Booth::select(
            'client.id',
            'client.company',
            'client.name',
            DB::raw('COUNT(booth.id) as booth_count'),
            DB::raw('SUM(booth.price) as total_spent'),
            DB::raw('SUM(booth.deposit_paid + booth.balance_paid) as total_paid')
        )
        ->join('client', 'booth.client_id', '=', 'client.id')
        ->whereNotNull('booth.bookid')
        ->groupBy('client.id', 'client.company', 'client.name')
        ->orderByDesc('total_spent')
        ->limit($limit);
        
        if ($floorPlanId) {
            $query->where('booth.floor_plan_id', $floorPlanId);
        }
        
        return $query->get();
    }
}
