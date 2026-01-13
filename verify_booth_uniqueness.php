<?php
/**
 * Verification Script: Check Booth Number Uniqueness Per Floor Plan
 * 
 * This script verifies that:
 * 1. Each booth number is unique within its floor plan
 * 2. The database constraint is properly set
 * 3. No duplicates exist in the database
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔍 Verifying Booth Number Uniqueness Per Floor Plan...\n\n";

// Check 1: Verify database constraint exists
echo "1️⃣ Checking database constraint...\n";
$connection = Schema::getConnection();
$database = $connection->getDatabaseName();
$constraintExists = $connection->select(
    "SELECT COUNT(*) as count FROM information_schema.statistics 
     WHERE table_schema = ? 
     AND table_name = 'booth' 
     AND index_name = 'booth_number_floor_plan_unique'",
    [$database]
);

if (!empty($constraintExists) && $constraintExists[0]->count > 0) {
    echo "   ✅ Composite unique constraint 'booth_number_floor_plan_unique' exists\n";
} else {
    echo "   ❌ Composite unique constraint 'booth_number_floor_plan_unique' NOT FOUND!\n";
    echo "   ⚠️  Run migration: php artisan migrate\n";
}

// Check 2: Find any duplicate booth numbers within the same floor plan
echo "\n2️⃣ Checking for duplicate booth numbers per floor plan...\n";
$duplicates = DB::table('booth')
    ->select('booth_number', 'floor_plan_id', DB::raw('COUNT(*) as count'))
    ->whereNotNull('floor_plan_id')
    ->groupBy('booth_number', 'floor_plan_id')
    ->having('count', '>', 1)
    ->get();

if ($duplicates->isEmpty()) {
    echo "   ✅ No duplicates found! All booth numbers are unique within their floor plans.\n";
} else {
    echo "   ❌ Found " . $duplicates->count() . " duplicate(s):\n";
    foreach ($duplicates as $dup) {
        echo "      - Booth '{$dup->booth_number}' in Floor Plan ID {$dup->floor_plan_id} appears {$dup->count} times\n";
    }
}

// Check 3: Check booths without floor_plan_id
echo "\n3️⃣ Checking booths without floor_plan_id...\n";
$boothsWithoutFloorPlan = DB::table('booth')
    ->whereNull('floor_plan_id')
    ->count();

if ($boothsWithoutFloorPlan > 0) {
    echo "   ⚠️  Found {$boothsWithoutFloorPlan} booth(s) without floor_plan_id\n";
    echo "   ℹ️  These booths use global uniqueness (no floor plan restriction)\n";
    
    // Check for duplicates among booths without floor_plan_id
    $globalDuplicates = DB::table('booth')
        ->select('booth_number', DB::raw('COUNT(*) as count'))
        ->whereNull('floor_plan_id')
        ->groupBy('booth_number')
        ->having('count', '>', 1)
        ->get();
    
    if ($globalDuplicates->isEmpty()) {
        echo "   ✅ No duplicates among booths without floor_plan_id\n";
    } else {
        echo "   ❌ Found duplicates among booths without floor_plan_id:\n";
        foreach ($globalDuplicates as $dup) {
            echo "      - Booth '{$dup->booth_number}' appears {$dup->count} times\n";
        }
    }
} else {
    echo "   ✅ All booths have floor_plan_id assigned\n";
}

// Check 4: Summary statistics
echo "\n4️⃣ Summary Statistics:\n";
$totalBooths = DB::table('booth')->count();
$totalFloorPlans = DB::table('floor_plans')->count();
$boothsWithFloorPlan = DB::table('booth')->whereNotNull('floor_plan_id')->count();

echo "   📊 Total booths: {$totalBooths}\n";
echo "   📊 Booths with floor_plan_id: {$boothsWithFloorPlan}\n";
echo "   📊 Total floor plans: {$totalFloorPlans}\n";

// Check 5: Sample booth numbers per floor plan
echo "\n5️⃣ Sample booth numbers by floor plan:\n";
$floorPlans = DB::table('floor_plans')->get();
foreach ($floorPlans->take(5) as $floorPlan) {
    $boothCount = DB::table('booth')
        ->where('floor_plan_id', $floorPlan->id)
        ->count();
    
    $sampleBooths = DB::table('booth')
        ->where('floor_plan_id', $floorPlan->id)
        ->select('booth_number')
        ->orderBy('booth_number')
        ->limit(5)
        ->pluck('booth_number')
        ->toArray();
    
    echo "   📋 {$floorPlan->name} (ID: {$floorPlan->id}): {$boothCount} booths\n";
    if (!empty($sampleBooths)) {
        echo "      Sample: " . implode(', ', $sampleBooths) . "\n";
    }
}

echo "\n✅ Verification complete!\n";
echo "\n💡 Key Points:\n";
echo "   - Booth numbers MUST be unique within each floor plan\n";
echo "   - Same booth number CAN exist in different floor plans\n";
echo "   - Database constraint prevents duplicates at the database level\n";
echo "   - Model validation prevents duplicates at the application level\n";
