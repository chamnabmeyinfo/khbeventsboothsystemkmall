<?php

namespace Database\Seeders;

use App\Models\AffiliateBenefit;
use App\Models\AffiliateClick;
use App\Models\Book;
use App\Models\Booth;
use App\Models\Client;
use App\Models\FloorPlan;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

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

        if (! $salesManagerRole || ! $salesStaffRole) {
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
                    'phone_number' => '+1-555-'.rand(100, 999).'-'.rand(1000, 9999),
                    'address' => rand(100, 9999).' Main Street, City, State '.rand(10000, 99999),
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
                    $boothNumber = 'B'.str_pad($i, 2, '0', STR_PAD_LEFT);
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
                // Only create if affiliate_clicks table exists
                if (\Illuminate\Support\Facades\Schema::hasTable('affiliate_clicks')) {
                    try {
                        $clickDate = $bookingDate->copy()->subDays(rand(1, 14)); // Click happened 1-14 days before booking
                        AffiliateClick::create([
                            'affiliate_user_id' => $affiliateUser->id,
                            'floor_plan_id' => $floorPlan->id,
                            'ref_code' => 'demo_ref_'.$booking->id,
                            'ip_address' => '192.168.1.'.rand(100, 255),
                            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                            'expires_at' => $clickDate->copy()->addDays(30),
                            'created_at' => $clickDate,
                            'updated_at' => $clickDate,
                        ]);
                    } catch (\Exception $e) {
                        $this->command->warn('Failed to create affiliate click: '.$e->getMessage());
                    }
                }
            }
        }

        $this->command->info("Created {$bookingsCreated} affiliate bookings");

        // Create some additional affiliate clicks without bookings (browsers who didn't book)
        // Only create if affiliate_clicks table exists
        if (\Illuminate\Support\Facades\Schema::hasTable('affiliate_clicks')) {
            $this->command->info('Creating additional affiliate clicks (non-converting)...');
            $clicksCreated = 0;
            for ($i = 0; $i < 30; $i++) {
                try {
                    $affiliateUser = $salesUsers[array_rand($salesUsers)];
                    $floorPlan = $floorPlans[array_rand($floorPlans)];
                    $clickDate = Carbon::now()->subDays(rand(1, 90));

                    AffiliateClick::create([
                        'affiliate_user_id' => $affiliateUser->id,
                        'floor_plan_id' => $floorPlan->id,
                        'ref_code' => 'demo_ref_browse_'.$i,
                        'ip_address' => '192.168.1.'.rand(100, 255),
                        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'expires_at' => $clickDate->copy()->addDays(30),
                        'created_at' => $clickDate,
                        'updated_at' => $clickDate,
                    ]);
                    $clicksCreated++;
                } catch (\Exception $e) {
                    $this->command->warn("Failed to create affiliate click #{$i}: ".$e->getMessage());
                }
            }
            $this->command->info("Created {$clicksCreated} additional affiliate clicks");
        } else {
            $this->command->warn('affiliate_clicks table does not exist. Skipping click creation.');
            $this->command->warn('Please run: php artisan migrate --force');
        }

        // Create Affiliate Benefits Demo Data
        $this->command->info('Creating affiliate benefits configurations...');
        $benefitsCreated = 0;

        // Check if affiliate_benefits table exists
        if (Schema::hasTable('affiliate_benefits')) {
            // 1. Standard Commission - 5% of all revenue
            $standardCommission = AffiliateBenefit::firstOrCreate(
                ['name' => 'Standard Commission'],
                [
                    'type' => AffiliateBenefit::TYPE_COMMISSION,
                    'calculation_method' => AffiliateBenefit::METHOD_PERCENTAGE,
                    'percentage' => 5.00,
                    'is_active' => true,
                    'priority' => 10,
                    'description' => 'Standard 5% commission on all affiliate bookings',
                    'created_by' => $adminRole ? User::where('username', 'admin')->first()?->id : null,
                ]
            );
            $benefitsCreated++;
            $this->command->info('Created: Standard Commission (5%)');

            // 2. Performance Bonus - Fixed $500 when reaching 10 bookings
            $performanceBonus = AffiliateBenefit::firstOrCreate(
                ['name' => 'Performance Bonus - 10 Bookings'],
                [
                    'type' => AffiliateBenefit::TYPE_BONUS,
                    'calculation_method' => AffiliateBenefit::METHOD_FIXED_AMOUNT,
                    'fixed_amount' => 500.00,
                    'target_bookings' => 10,
                    'is_active' => true,
                    'priority' => 20,
                    'description' => 'Bonus of $500 when sales person reaches 10 bookings',
                    'created_by' => $adminRole ? User::where('username', 'admin')->first()?->id : null,
                ]
            );
            $benefitsCreated++;
            $this->command->info('Created: Performance Bonus ($500 for 10 bookings)');

            // 3. Revenue Milestone Bonus - $1000 when reaching $50,000 revenue
            $revenueBonus = AffiliateBenefit::firstOrCreate(
                ['name' => 'Revenue Milestone Bonus'],
                [
                    'type' => AffiliateBenefit::TYPE_BONUS,
                    'calculation_method' => AffiliateBenefit::METHOD_FIXED_AMOUNT,
                    'fixed_amount' => 1000.00,
                    'target_revenue' => 50000.00,
                    'is_active' => true,
                    'priority' => 25,
                    'description' => 'Bonus of $1,000 when reaching $50,000 in revenue',
                    'created_by' => $adminRole ? User::where('username', 'admin')->first()?->id : null,
                ]
            );
            $benefitsCreated++;
            $this->command->info('Created: Revenue Milestone Bonus ($1,000 for $50k revenue)');

            // 4. Tiered Commission - Higher percentage for higher revenue
            $tieredCommission = AffiliateBenefit::firstOrCreate(
                ['name' => 'Tiered Commission Structure'],
                [
                    'type' => AffiliateBenefit::TYPE_COMMISSION,
                    'calculation_method' => AffiliateBenefit::METHOD_TIERED_PERCENTAGE,
                    'tier_structure' => [
                        ['min' => 0, 'max' => 10000, 'percentage' => 5],
                        ['min' => 10000, 'max' => 50000, 'percentage' => 7],
                        ['min' => 50000, 'max' => 100000, 'percentage' => 10],
                        ['min' => 100000, 'max' => 999999999, 'percentage' => 12],
                    ],
                    'is_active' => false, // Inactive by default, can be activated
                    'priority' => 5,
                    'description' => 'Tiered commission: 5% up to $10k, 7% up to $50k, 10% up to $100k, 12% above',
                    'created_by' => $adminRole ? User::where('username', 'admin')->first()?->id : null,
                ]
            );
            $benefitsCreated++;
            $this->command->info('Created: Tiered Commission Structure');

            // 5. Client Acquisition Incentive - $200 per new client
            $clientIncentive = AffiliateBenefit::firstOrCreate(
                ['name' => 'Client Acquisition Incentive'],
                [
                    'type' => AffiliateBenefit::TYPE_INCENTIVE,
                    'calculation_method' => AffiliateBenefit::METHOD_FIXED_AMOUNT,
                    'fixed_amount' => 200.00,
                    'target_clients' => 1, // Per client
                    'is_active' => true,
                    'priority' => 15,
                    'description' => '$200 incentive for each new unique client acquired',
                    'created_by' => $adminRole ? User::where('username', 'admin')->first()?->id : null,
                ]
            );
            $benefitsCreated++;
            $this->command->info('Created: Client Acquisition Incentive ($200 per client)');

            // 6. High-Value Booking Reward - Extra 2% for bookings over $5,000
            $highValueReward = AffiliateBenefit::firstOrCreate(
                ['name' => 'High-Value Booking Reward'],
                [
                    'type' => AffiliateBenefit::TYPE_REWARD,
                    'calculation_method' => AffiliateBenefit::METHOD_PERCENTAGE,
                    'percentage' => 2.00,
                    'min_revenue' => 5000.00,
                    'is_active' => true,
                    'priority' => 8,
                    'description' => 'Additional 2% reward for bookings over $5,000',
                    'created_by' => $adminRole ? User::where('username', 'admin')->first()?->id : null,
                ]
            );
            $benefitsCreated++;
            $this->command->info('Created: High-Value Booking Reward (2% for bookings > $5k)');

            // 7. Monthly Performance Bonus - $300 for 5+ bookings in a month
            $monthlyBonus = AffiliateBenefit::firstOrCreate(
                ['name' => 'Monthly Performance Bonus'],
                [
                    'type' => AffiliateBenefit::TYPE_BONUS,
                    'calculation_method' => AffiliateBenefit::METHOD_FIXED_AMOUNT,
                    'fixed_amount' => 300.00,
                    'target_bookings' => 5,
                    'is_active' => true,
                    'priority' => 12,
                    'description' => 'Monthly bonus of $300 for achieving 5+ bookings',
                    'start_date' => now()->startOfMonth(),
                    'end_date' => now()->endOfMonth(),
                    'created_by' => $adminRole ? User::where('username', 'admin')->first()?->id : null,
                ]
            );
            $benefitsCreated++;
            $this->command->info('Created: Monthly Performance Bonus ($300 for 5+ bookings/month)');

            $this->command->info("Created {$benefitsCreated} affiliate benefit configurations");
        } else {
            $this->command->warn('affiliate_benefits table does not exist. Skipping benefit creation.');
            $this->command->warn('Please run: php artisan migrate --force');
        }

        $this->command->info('Demo data created successfully!');
        $this->command->info('You can now test the affiliate system at: /affiliates');
        $this->command->info('Manage benefits at: /affiliates/benefits');
        $this->command->info('Sales team login credentials:');
        $this->command->info('  Username: Any sales username (e.g., sarah_manager, emily_sales)');
        $this->command->info('  Password: password123');
    }
}
