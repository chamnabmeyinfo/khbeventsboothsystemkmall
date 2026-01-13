<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\FloorPlan;
use App\Models\Booth;
use App\Models\Book;
use App\Models\AffiliateClick;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AffiliateDemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating affiliate demo data...');

        // Get or create roles (run RolesAndPermissionsSeeder first if roles don't exist)
        $salesManagerRole = Role::where('slug', 'sales-manager')->first();
        $salesStaffRole = Role::where('slug', 'sales-staff')->first();
        $adminRole = Role::where('slug', 'administrator')->first();

        if (!$salesManagerRole || !$salesStaffRole) {
            $this->command->warn('Roles not found. Please run: php artisan db:seed --class=RolesAndPermissionsSeeder');
            $this->command->warn('Continuing without role assignments...');
        }

        // Create Sales Team Users
        $this->command->info('Creating sales team users...');
        $salesUsers = [];

        $salesManagers = [
            ['username' => 'sarah_manager', 'name' => 'Sarah Johnson'],
            ['username' => 'mike_manager', 'name' => 'Mike Chen'],
        ];

        $salesStaff = [
            ['username' => 'emily_sales', 'name' => 'Emily Davis'],
            ['username' => 'john_sales', 'name' => 'John Smith'],
            ['username' => 'lisa_sales', 'name' => 'Lisa Anderson'],
            ['username' => 'david_sales', 'name' => 'David Brown'],
        ];

        // Create Sales Managers
        foreach ($salesManagers as $manager) {
            $user = User::firstOrCreate(
                ['username' => $manager['username']],
                [
                    'password' => Hash::make('password123'),
                    'type' => 1,
                    'status' => 1,
                    'role_id' => $salesManagerRole ? $salesManagerRole->id : null,
                    'create_time' => now(),
                    'update_time' => now(),
                ]
            );
            $salesUsers[] = $user;
            $this->command->info("Created sales manager: {$manager['username']}");
        }

        // Create Sales Staff
        foreach ($salesStaff as $staff) {
            $user = User::firstOrCreate(
                ['username' => $staff['username']],
                [
                    'password' => Hash::make('password123'),
                    'type' => 1,
                    'status' => 1,
                    'role_id' => $salesStaffRole ? $salesStaffRole->id : null,
                    'create_time' => now(),
                    'update_time' => now(),
                ]
            );
            $salesUsers[] = $user;
            $this->command->info("Created sales staff: {$staff['username']}");
        }

        // Create Sample Clients
        $this->command->info('Creating sample clients...');
        $clients = [];
        $clientNames = [
            ['name' => 'Tech Solutions Inc', 'company' => 'Tech Solutions Inc', 'email' => 'contact@techsolutions.com'],
            ['name' => 'John Williams', 'company' => 'Williams Marketing', 'email' => 'john@williamsmarketing.com'],
            ['name' => 'Sarah Martinez', 'company' => 'Martinez Events', 'email' => 'sarah@martinezevents.com'],
            ['name' => 'Robert Taylor', 'company' => 'Taylor Industries', 'email' => 'robert@taylorind.com'],
            ['name' => 'Jennifer Lee', 'company' => 'Lee & Associates', 'email' => 'jennifer@leeassoc.com'],
            ['name' => 'Michael Brown', 'company' => 'Brown Enterprises', 'email' => 'michael@brownent.com'],
            ['name' => 'Amanda White', 'company' => 'White Group', 'email' => 'amanda@whitegroup.com'],
            ['name' => 'Christopher Green', 'company' => 'Green Solutions', 'email' => 'chris@greensolutions.com'],
            ['name' => 'Jessica Wilson', 'company' => 'Wilson Corp', 'email' => 'jessica@wilsoncorp.com'],
            ['name' => 'Daniel Moore', 'company' => 'Moore Industries', 'email' => 'daniel@mooreind.com'],
        ];

        foreach ($clientNames as $clientData) {
            $client = Client::firstOrCreate(
                ['email' => $clientData['email']],
                [
                    'name' => $clientData['name'],
                    'company' => $clientData['company'],
                    'phone_number' => '+1-555-' . rand(100, 999) . '-' . rand(1000, 9999),
                    'address' => rand(100, 9999) . ' Main Street, City, State ' . rand(10000, 99999),
                ]
            );
            $clients[] = $client;
        }

        // Get or create Floor Plans
        $this->command->info('Creating/retrieving floor plans...');
        $floorPlans = [];

        $floorPlanData = [
            ['name' => 'Summer Expo 2026', 'description' => 'Annual summer exhibition event'],
            ['name' => 'Tech Conference 2026', 'description' => 'Technology innovation conference'],
            ['name' => 'Business Summit 2026', 'description' => 'Business networking summit'],
        ];

        foreach ($floorPlanData as $fpData) {
            $floorPlan = FloorPlan::firstOrCreate(
                ['name' => $fpData['name']],
                [
                    'description' => $fpData['description'],
                    'canvas_width' => 1200,
                    'canvas_height' => 800,
                    'is_active' => true,
                    'is_default' => false,
                ]
            );
            $floorPlans[] = $floorPlan;
        }

        // Create booths for floor plans if they don't exist
        $this->command->info('Creating booths for floor plans...');
        foreach ($floorPlans as $floorPlan) {
            $existingBooths = Booth::where('floor_plan_id', $floorPlan->id)->count();
            if ($existingBooths < 20) {
                for ($i = 1; $i <= 20; $i++) {
                    $boothNumber = 'B' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    Booth::firstOrCreate(
                        [
                            'floor_plan_id' => $floorPlan->id,
                            'booth_number' => $boothNumber,
                        ],
                        [
                            'type' => 2,
                            'price' => rand(500, 5000), // Random price between $500-$5000
                            'status' => Booth::STATUS_AVAILABLE,
                            'position_x' => rand(50, 1000),
                            'position_y' => rand(50, 700),
                            'width' => 80,
                            'height' => 50,
                        ]
                    );
                }
            }
        }

        // Create Affiliate Bookings
        $this->command->info('Creating affiliate bookings...');
        $bookingDates = [];
        
        // Generate bookings over the last 6 months
        for ($i = 0; $i < 6; $i++) {
            $date = Carbon::now()->subMonths($i);
            $daysInMonth = $date->daysInMonth;
            $bookingsThisMonth = rand(3, 8);
            
            for ($j = 0; $j < $bookingsThisMonth; $j++) {
                $bookingDates[] = $date->copy()->day(rand(1, $daysInMonth));
            }
        }

        $bookingsCreated = 0;
        foreach ($bookingDates as $bookingDate) {
            // Randomly select affiliate user, client, and floor plan
            $affiliateUser = $salesUsers[array_rand($salesUsers)];
            $client = $clients[array_rand($clients)];
            $floorPlan = $floorPlans[array_rand($floorPlans)];

            // Get available booths for this floor plan
            $availableBooths = Booth::where('floor_plan_id', $floorPlan->id)
                ->where('status', Booth::STATUS_AVAILABLE)
                ->limit(rand(1, 4)) // Book 1-4 booths
                ->get();

            if ($availableBooths->count() > 0) {
                $boothIds = $availableBooths->pluck('id')->toArray();
                $totalPrice = $availableBooths->sum('price');

                // Create booking
                $booking = Book::create([
                    'floor_plan_id' => $floorPlan->id,
                    'clientid' => $client->id,
                    'boothid' => json_encode($boothIds),
                    'date_book' => $bookingDate->format('Y-m-d H:i:s'),
                    'userid' => $affiliateUser->id, // The user who processed the booking
                    'affiliate_user_id' => $affiliateUser->id, // The affiliate who generated the link
                    'type' => 1,
                ]);

                // Update booth status to confirmed
                Booth::whereIn('id', $boothIds)->update([
                    'status' => Booth::STATUS_CONFIRMED,
                    'client_id' => $client->id,
                    'bookid' => $booking->id,
                ]);

                $bookingsCreated++;
                
                // Create affiliate clicks (simulate link clicks before booking)
                $clickDate = $bookingDate->copy()->subDays(rand(1, 14)); // Click happened 1-14 days before booking
                AffiliateClick::create([
                    'affiliate_user_id' => $affiliateUser->id,
                    'floor_plan_id' => $floorPlan->id,
                    'ref_code' => 'demo_ref_' . $booking->id,
                    'ip_address' => '192.168.1.' . rand(100, 255),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'expires_at' => $clickDate->copy()->addDays(30),
                    'created_at' => $clickDate,
                    'updated_at' => $clickDate,
                ]);
            }
        }

        $this->command->info("Created {$bookingsCreated} affiliate bookings");

        // Create some additional affiliate clicks without bookings (browsers who didn't book)
        $this->command->info('Creating additional affiliate clicks (non-converting)...');
        for ($i = 0; $i < 30; $i++) {
            $affiliateUser = $salesUsers[array_rand($salesUsers)];
            $floorPlan = $floorPlans[array_rand($floorPlans)];
            $clickDate = Carbon::now()->subDays(rand(1, 90));

            AffiliateClick::create([
                'affiliate_user_id' => $affiliateUser->id,
                'floor_plan_id' => $floorPlan->id,
                'ref_code' => 'demo_ref_browse_' . $i,
                'ip_address' => '192.168.1.' . rand(100, 255),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'expires_at' => $clickDate->copy()->addDays(30),
                'created_at' => $clickDate,
                'updated_at' => $clickDate,
            ]);
        }

        $this->command->info('Demo data created successfully!');
        $this->command->info('You can now test the affiliate system at: /affiliates');
        $this->command->info('Sales team login credentials:');
        $this->command->info('  Username: Any sales username (e.g., sarah_manager, emily_sales)');
        $this->command->info('  Password: password123');
    }
}
