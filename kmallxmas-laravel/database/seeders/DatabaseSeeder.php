<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asset;
use App\Models\BoothType;
use App\Models\Booth;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::where('username', 'admin')->first();
        if ($admin) {
            $admin->password = Hash::make('admin@123!');
            $admin->type = 1;
            $admin->status = 1;
            $admin->save();
        } else {
            User::create([
                'username' => 'admin',
                'password' => Hash::make('admin@123!'),
                'type' => 1,
                'status' => 1,
            ]);
        }

        // Create assets
        Asset::create(['name' => '10A', 'type' => 1, 'status' => 1]);
        Asset::create(['name' => '20A', 'type' => 1, 'status' => 1]);
        Asset::create(['name' => '30A', 'type' => 1, 'status' => 1]);

        // Create booth types
        BoothType::create(['name' => 'Space with booth', 'status' => 1]);
        BoothType::create(['name' => 'Space only', 'status' => 1]);

        // Create sample booths
        $boothNumbers = [
            'A01', 'A02', 'A03', 'A04', 'A05', 'A06', 'A07', 'A08', 'A09', 'A10',
            'A11', 'A12', 'A13', 'B01', 'B02', 'B03', 'B04', 'B05', 'B06', 'B07',
            'B08', 'B09', 'B10', 'B11', 'B12', 'B13', 'B14', 'B15', 'B16', 'B17',
            'B18', 'B19', 'B20', 'B21', 'B22', 'B23', 'B24', 'B25', 'B26', 'B27',
            'B28', 'B29', 'B30', 'B31', 'B32', 'B33', 'B34', 'B35', 'B36', 'B37',
            'B38', 'B39', 'B40', 'C01', 'C02', 'C03', 'C04', 'C05', 'C06', 'C07',
            'C08', 'C09', 'C10', 'C11', 'C12', 'C13', 'C14', 'C15', 'C16', 'C17',
            'C18', 'C19', 'C20', 'C21', 'C22', 'C23', 'C24', 'C25', 'C26', 'C27',
            'C28', 'D01', 'D02', 'D03', 'D04', 'D05', 'D06', 'D07', 'D08', 'D09',
            'D10', 'D11', 'D12', 'D13', 'D14', 'D15', 'D16', 'D17', 'D18', 'D19',
            'D20', 'D21', 'D22', 'D23', 'D24', 'D25', 'D26', 'D27', 'D28', 'D29',
            'D30', 'D31', 'D32', 'D33', 'D34', 'D35', 'D36', 'SP-01', 'SP-02', 'SP-03',
            'SP-04', 'SP-05', 'SP-06', 'SP-07', 'SP-08', 'SP-09', 'SP-10', 'SP-11',
            'SP-12', 'SP-13', 'SP-14', 'SP-15', 'SP-16', 'SP-17', 'SP-18', 'SP-19', 'SP-20'
        ];

        foreach ($boothNumbers as $number) {
            Booth::create([
                'booth_number' => $number,
                'type' => 2,
                'price' => 500,
                'status' => Booth::STATUS_AVAILABLE,
            ]);
        }
    }
}
