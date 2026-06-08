<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        $this->call([
            AdminSeeder::class,
            OutletSeeder::class,
            PackageSeeder::class,
            TechnicianSeeder::class,
            CustomerSeeder::class,
            PromoSeeder::class,
            BookingSeeder::class,
        ]);

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->command->info('✅ Database seeding selesai!');
    }
}

/* ─────────────────────────────────────────────────────── */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->truncate();
        $admins = [
            ['name' => 'Super Admin',        'email' => 'superadmin@vehiclewash.id', 'role' => 'super_admin'],
            ['name' => 'Admin Operasional',  'email' => 'ops@vehiclewash.id',        'role' => 'admin_operasional'],
            ['name' => 'Admin Outlet Pusat', 'email' => 'outlet@vehiclewash.id',     'role' => 'admin_outlet'],
            ['name' => 'Admin Keuangan',     'email' => 'finance@vehiclewash.id',    'role' => 'admin_keuangan'],
        ];
        foreach ($admins as $a) {
            DB::table('users')->insert([
                ...$a,
                'password'   => Hash::make('password123'),
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('  ✔ Admins: ' . count($admins));
    }
}

/* ─────────────────────────────────────────────────────── */
class OutletSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('outlets')->truncate();
        $outlets = [
            ['name' => 'Outlet Pusat — Jakarta Selatan', 'address' => 'Jl. Sudirman No.45, Jakarta Selatan', 'phone' => '021-1234-5678', 'latitude' => -6.2088, 'longitude' => 106.8456, 'capacity_per_hour' => 5, 'open_time' => '07:00:00', 'close_time' => '20:00:00', 'status' => 'active', 'rating' => 4.8],
            ['name' => 'Outlet Bekasi Barat',            'address' => 'Jl. Ahmad Yani No.12, Bekasi Barat',  'phone' => '021-8765-4321', 'latitude' => -6.2349, 'longitude' => 106.9923, 'capacity_per_hour' => 3, 'open_time' => '08:00:00', 'close_time' => '19:00:00', 'status' => 'active', 'rating' => 4.6],
            ['name' => 'Outlet Tangerang City',          'address' => 'Jl. Merdeka No.78, Tangerang',        'phone' => '021-5555-7890', 'latitude' => -6.1702, 'longitude' => 106.6403, 'capacity_per_hour' => 4, 'open_time' => '07:00:00', 'close_time' => '20:00:00', 'status' => 'active', 'rating' => 4.7],
            ['name' => 'Outlet Depok Sawangan',          'address' => 'Jl. Sawangan Raya No.22, Depok',      'phone' => '021-7777-1234', 'latitude' => -6.3728, 'longitude' => 106.7832, 'capacity_per_hour' => 3, 'open_time' => '08:00:00', 'close_time' => '18:00:00', 'status' => 'active', 'rating' => 4.5],
            ['name' => 'Outlet Bogor Tengah',            'address' => 'Jl. Pajajaran No.55, Bogor',          'phone' => '0251-333-444',  'latitude' => -6.5971, 'longitude' => 106.8060, 'capacity_per_hour' => 2, 'open_time' => '08:00:00', 'close_time' => '17:00:00', 'status' => 'maintenance', 'rating' => 4.4],
            ['name' => 'Outlet Jakarta Timur',           'address' => 'Jl. Raya Bogor No.99, Jakarta Timur', 'phone' => '021-9876-5432', 'latitude' => -6.2251, 'longitude' => 106.9004, 'capacity_per_hour' => 4, 'open_time' => '07:00:00', 'close_time' => '20:00:00', 'status' => 'active', 'rating' => 4.6],
        ];
        foreach ($outlets as $o) {
            DB::table('outlets')->insert([...$o, 'created_at' => now(), 'updated_at' => now()]);
        }
        $this->command->info('  ✔ Outlets: ' . count($outlets));
    }
}

/* ─────────────────────────────────────────────────────── */
class PackageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('packages')->truncate();
        $packages = [
            ['name' => 'Basic Wash',       'description' => 'Cuci eksterior standar, semprot angin, lap bodi dasar.',                          'vehicle_type' => 'roda_2', 'price' => 15000,  'duration_minutes' => 20,  'sort_order' => 1],
            ['name' => 'Basic Wash',       'description' => 'Cuci eksterior standar, semprot angin, lap bodi dasar.',                          'vehicle_type' => 'roda_4', 'price' => 35000,  'duration_minutes' => 30,  'sort_order' => 1],
            ['name' => 'Premium Wash',     'description' => 'Basic + semir ban, lap kaca, parfum kabin.',                                      'vehicle_type' => 'roda_2', 'price' => 25000,  'duration_minutes' => 30,  'sort_order' => 2],
            ['name' => 'Premium Wash',     'description' => 'Basic + semir ban, lap kaca, parfum kabin.',                                      'vehicle_type' => 'roda_4', 'price' => 55000,  'duration_minutes' => 45,  'sort_order' => 2],
            ['name' => 'Complete Wash',    'description' => 'Premium + vacuum interior, lap dashboard, pembersih velg.',                       'vehicle_type' => 'roda_2', 'price' => 40000,  'duration_minutes' => 45,  'sort_order' => 3],
            ['name' => 'Complete Wash',    'description' => 'Premium + vacuum interior, lap dashboard, pembersih velg.',                       'vehicle_type' => 'roda_4', 'price' => 90000,  'duration_minutes' => 60,  'sort_order' => 3],
            ['name' => 'Full Detailing',   'description' => 'Complete + wax eksterior, poles bodi, shampoo karpet, parfum premium.',           'vehicle_type' => 'roda_2', 'price' => 75000,  'duration_minutes' => 90,  'sort_order' => 4],
            ['name' => 'Full Detailing',   'description' => 'Complete + wax eksterior, poles bodi, shampoo karpet, parfum premium.',           'vehicle_type' => 'roda_4', 'price' => 180000, 'duration_minutes' => 120, 'sort_order' => 4],
            ['name' => 'Engine Wash',      'description' => 'Cuci mesin dengan tekanan air khusus, degreaser engine, pengeringan terproteksi.', 'vehicle_type' => 'roda_2', 'price' => 50000,  'duration_minutes' => 40,  'sort_order' => 5],
            ['name' => 'Engine Wash',      'description' => 'Cuci mesin dengan tekanan air khusus, degreaser engine, pengeringan terproteksi.', 'vehicle_type' => 'roda_4', 'price' => 100000, 'duration_minutes' => 60,  'sort_order' => 5],
        ];
        foreach ($packages as $p) {
            DB::table('packages')->insert([...$p, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]);
        }
        $this->command->info('  ✔ Packages: ' . count($packages));
    }
}

/* ─────────────────────────────────────────────────────── */
class TechnicianSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('technicians')->truncate();
        $technicians = [
            ['name' => 'Ahmad Fauzi',      'email' => 'ahmad@vw.id',   'phone' => '081234567890', 'specialization' => 'mobil',              'area' => 'Jakarta Selatan', 'outlet_id' => 1, 'rating' => 4.9, 'total_orders' => 248, 'join_date' => '2022-03-01'],
            ['name' => 'Budi Santoso',     'email' => 'budi@vw.id',    'phone' => '081323456789', 'specialization' => 'motor',              'area' => 'Jakarta Barat',   'outlet_id' => 3, 'rating' => 4.7, 'total_orders' => 185, 'join_date' => '2022-06-15'],
            ['name' => 'Citra Putri',      'email' => 'citra@vw.id',   'phone' => '085678901234', 'specialization' => 'mobil',              'area' => 'Jakarta Pusat',   'outlet_id' => 1, 'rating' => 4.8, 'total_orders' => 312, 'join_date' => '2022-01-10'],
            ['name' => 'Dwi Santoro',      'email' => 'dwi@vw.id',     'phone' => '087834567890', 'specialization' => 'motor',              'area' => 'Jakarta Timur',   'outlet_id' => 6, 'rating' => 4.2, 'total_orders' => 67,  'join_date' => '2023-09-05', 'status' => 'inactive'],
            ['name' => 'Eko Prasetyo',     'email' => 'eko@vw.id',     'phone' => '082390123456', 'specialization' => 'mobil',              'area' => 'Bekasi',          'outlet_id' => 2, 'rating' => 4.6, 'total_orders' => 198, 'join_date' => '2022-04-20'],
            ['name' => 'Fitri Handayani',  'email' => 'fitri@vw.id',   'phone' => '081756789012', 'specialization' => 'motor',              'area' => 'Tangerang',       'outlet_id' => 3, 'rating' => 4.5, 'total_orders' => 143, 'join_date' => '2022-08-01'],
            ['name' => 'Galih Prakoso',    'email' => 'galih@vw.id',   'phone' => '089545678901', 'specialization' => 'mobil',              'area' => 'Depok',           'outlet_id' => 4, 'rating' => 4.8, 'total_orders' => 276, 'join_date' => '2022-02-14', 'status' => 'cuti'],
            ['name' => 'Hendra Kurniawan', 'email' => 'hendra@vw.id',  'phone' => '088112345678', 'specialization' => 'motor',              'area' => 'Jakarta Selatan', 'outlet_id' => 1, 'rating' => 4.3, 'total_orders' => 89,  'join_date' => '2023-05-20'],
            ['name' => 'Irfan Maulana',    'email' => 'irfan@vw.id',   'phone' => '082298765432', 'specialization' => 'mobil',              'area' => 'Bekasi',          'outlet_id' => 2, 'rating' => 4.6, 'total_orders' => 156, 'join_date' => '2022-11-01'],
            ['name' => 'Joko Susilo',      'email' => 'joko@vw.id',    'phone' => '085612345678', 'specialization' => 'motor',              'area' => 'Bogor',           'outlet_id' => 5, 'rating' => 4.7, 'total_orders' => 201, 'join_date' => '2022-07-08'],
        ];
        foreach ($technicians as $t) {
            DB::table('technicians')->insert([
                'name'           => $t['name'],       'email'          => $t['email'],
                'phone'          => $t['phone'],      'specialization' => $t['specialization'],
                'area'           => $t['area'],       'outlet_id'      => $t['outlet_id'],
                'status'         => $t['status']      ?? 'active',
                'rating'         => $t['rating'],     'total_orders'   => $t['total_orders'],
                'join_date'      => $t['join_date'],  'created_at'     => now(), 'updated_at' => now(),
            ]);
        }
        $this->command->info('  ✔ Technicians: ' . count($technicians));
    }
}

/* ─────────────────────────────────────────────────────── */
class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('customers')->truncate();
        DB::table('vehicles')->truncate();

        $data = [
            ['name' => 'Budi Santoso',    'email' => 'budi.s@gmail.com',   'phone' => '081234000001'],
            ['name' => 'Siti Rahayu',     'email' => 'siti.r@gmail.com',   'phone' => '081234000002'],
            ['name' => 'Andi Wijaya',     'email' => 'andi.w@gmail.com',   'phone' => '081234000003'],
            ['name' => 'Dewi Kusuma',     'email' => 'dewi.k@gmail.com',   'phone' => '081234000004'],
            ['name' => 'Reza Pratama',    'email' => 'reza.p@gmail.com',   'phone' => '081234000005', 'status' => 'inactive'],
            ['name' => 'Lina Marlina',    'email' => 'lina.m@gmail.com',   'phone' => '081234000006'],
            ['name' => 'Hendra Gunawan',  'email' => 'hendra.g@gmail.com', 'phone' => '081234000007'],
            ['name' => 'Maya Sari',       'email' => 'maya.s@gmail.com',   'phone' => '081234000008'],
            ['name' => 'Fajar Nugroho',   'email' => 'fajar.n@gmail.com',  'phone' => '081234000009'],
            ['name' => 'Rini Hapsari',    'email' => 'rini.h@gmail.com',   'phone' => '081234000010'],
            ['name' => 'Dedi Kurniawan',  'email' => 'dedi.k@gmail.com',   'phone' => '081234000011'],
            ['name' => 'Novia Anggraini', 'email' => 'novia.a@gmail.com',  'phone' => '081234000012'],
        ];

        $vehicles = [
            [1,'roda_4','Toyota','Avanza','Putih','B 1234 ABC',2020],
            [1,'roda_2','Honda','Beat','Hitam','B 5678 DEF',2021],
            [2,'roda_2','Yamaha','NMAX','Biru','B 9012 GHI',2022],
            [3,'roda_4','Honda','CRV','Silver','B 3456 JKL',2019],
            [3,'roda_4','Mitsubishi','Pajero Sport','Hitam','B 7890 MNO',2021],
            [4,'roda_4','Toyota','Fortuner','Putih','B 2345 PQR',2022],
            [5,'roda_2','Suzuki','Satria FU','Merah','B 6789 STU',2020],
            [6,'roda_2','Honda','Vario 150','Abu-abu','B 0123 VWX',2021],
            [7,'roda_4','Suzuki','Ertiga','Silver','B 4567 YZA',2020],
            [8,'roda_2','Honda','Scoopy','Putih','B 8901 BCD',2022],
            [9,'roda_4','Toyota','Innova','Hitam','B 1111 EFG',2021],
            [10,'roda_4','Honda','Brio','Merah','B 2222 HIJ',2022],
        ];

        foreach ($data as $c) {
            DB::table('customers')->insert([
                'name'            => $c['name'],
                'email'           => $c['email'],
                'phone'           => $c['phone'],
                'password'        => Hash::make('password123'),
                'status'          => $c['status'] ?? 'active',
                'created_at'      => now()->subDays(rand(10, 365)),
                'updated_at'      => now(),
            ]);

            // Register on OnoPay
            try {
                $client = new \GuzzleHttp\Client(['cookies' => true]);
                $response = $client->get('http://onopay.web.id/user/register');
                $html = (string) $response->getBody();
                
                preg_match('/name="_token"\s+value="([^"]+)"/', $html, $matches);
                $token = $matches[1] ?? null;
                
                if ($token) {
                    $client->post('http://onopay.web.id/user/register', [
                        'form_params' => [
                            '_token' => $token,
                            'name' => $c['name'],
                            'email' => $c['email'],
                            'phone_number' => $c['phone'],
                            'password' => 'password123',
                            'password_confirmation' => 'password123',
                        ]
                    ]);
                }
            } catch (\Exception $e) {
                // Already registered or skipped
            }

            // Topup Rp 5.000.000
            try {
                $client = new \GuzzleHttp\Client();
                $client->post('http://onopay.web.id/api/v1/payment/topup', [
                    'json' => [
                        'phone_number' => $c['phone'],
                        'amount' => 5000000
                    ]
                ]);
            } catch (\Exception $e) {
                // Ignore topup error
            }
        }

        foreach ($vehicles as [$cId, $type, $brand, $model, $color, $plate, $year]) {
            DB::table('vehicles')->insert([
                'customer_id'   => $cId, 'type'  => $type,   'brand' => $brand,
                'model'         => $model, 'color' => $color, 'license_plate' => $plate,
                'year'          => $year,  'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        $this->command->info('  ✔ Customers: ' . count($data) . ' | Vehicles: ' . count($vehicles));
    }
}

/* ─────────────────────────────────────────────────────── */
class PromoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('promos')->truncate();
        $promos = [
            ['name' => 'Weekend Full Wash',    'code' => 'WFW20',   'type' => 'percentage', 'value' => 20,    'min_transaction' => 50000,  'max_usage' => 500,  'used_count' => 145, 'expires_at' => now()->addMonths(2), 'description' => 'Diskon 20% untuk semua paket cuci di akhir pekan'],
            ['name' => 'First Detailing Promo', 'code' => 'FIRST30', 'type' => 'percentage', 'value' => 30,    'min_transaction' => null,   'max_usage' => 1000, 'used_count' => 0,   'expires_at' => now()->addMonths(6), 'description' => 'Diskon 30% untuk Detailing Pertama'],
            ['name' => 'First Wash Promo',     'code' => 'FIRST50', 'type' => 'percentage', 'value' => 50,    'min_transaction' => null,   'max_usage' => 1000, 'used_count' => 672, 'expires_at' => now()->addMonths(4), 'description' => 'Diskon 50% untuk pengguna baru'],
            ['name' => 'Referral Bonus',       'code' => 'REFER50K','type' => 'nominal',    'value' => 50000, 'min_transaction' => null,   'max_usage' => null, 'used_count' => 312, 'expires_at' => null,                'description' => 'Bonus Rp 50.000 untuk setiap referral berhasil'],
            ['name' => 'Fleet Corporate',      'code' => 'FLEET30', 'type' => 'percentage', 'value' => 30,    'min_transaction' => 200000, 'max_usage' => 200,  'used_count' => 28,  'expires_at' => now()->addMonths(5), 'description' => 'Diskon 30% untuk pemesanan fleet (min. Rp 200.000)'],
            ['name' => 'Happy Hour',           'code' => 'HAPPY15', 'type' => 'percentage', 'value' => 15,    'min_transaction' => 25000,  'max_usage' => 300,  'used_count' => 89,  'expires_at' => now()->addMonths(1), 'description' => 'Diskon 15% untuk booking jam 12:00–14:00'],
            ['name' => 'Gold Promo',           'code' => 'GOLD25',  'type' => 'percentage', 'value' => 25,    'min_transaction' => 75000,  'max_usage' => null, 'used_count' => 201, 'expires_at' => null,                'description' => 'Diskon eksklusif 25% dengan kode GOLD25'],
        ];
        foreach ($promos as $p) {
            DB::table('promos')->insert([
                ...$p,
                'status'     => 'active',
                'starts_at'  => now()->subMonth(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('  ✔ Promos: ' . count($promos));
    }
}

/* ─────────────────────────────────────────────────────── */
class BookingSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('bookings')->truncate();
        DB::table('payments')->truncate();
        DB::table('reviews')->truncate();

        $statuses  = ['completed','completed','completed','in_progress','confirmed','pending','cancelled'];
        $services  = ['home','outlet'];
        $methods   = ['ewallet','va_bank','qris','cod'];
        $providers = ['gopay','ovo','dana','midtrans','xendit'];
        $packages  = [1,3,5,7,9]; // sample package ids

        for ($i = 1; $i <= 30; $i++) {
            $customerId  = rand(1, 12);
            $technicianId= rand(1, 10);
            $outletId    = rand(1, 6);
            $packageId   = $packages[array_rand($packages)];
            $serviceType = $services[array_rand($services)];
            $status      = $statuses[array_rand($statuses)];
            $vehicleType = rand(0, 1) ? 'roda_4' : 'roda_2';
            $vehicleNames= ['Toyota Avanza','Honda Beat','Yamaha NMAX','Honda CRV','Suzuki Ertiga','Honda Vario','Toyota Fortuner'];
            $amount      = in_array($vehicleType, ['roda_4']) ? rand(3,18)*10000 : rand(1,8)*10000;
            $scheduledAt = now()->subDays(rand(0, 30))->setHour(rand(7,18))->setMinute(0)->setSecond(0);

            $bookingId = DB::table('bookings')->insertGetId([
                'booking_code'     => 'VW-' . date('Ymd', strtotime($scheduledAt)) . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_id'      => $customerId,
                'vehicle_name'     => $vehicleNames[array_rand($vehicleNames)],
                'vehicle_type'     => $vehicleType,
                'package_id'       => $packageId,
                'service_type'     => $serviceType,
                'outlet_id'        => $serviceType === 'outlet' ? $outletId : null,
                'technician_id'    => in_array($status, ['assigned','on_way','in_progress','completed']) ? $technicianId : null,
                'service_address'  => $serviceType === 'home' ? 'Jl. Contoh No.' . rand(1,100) . ', Jakarta' : null,
                'scheduled_at'     => $scheduledAt,
                'status'           => $status,
                'subtotal'         => $amount,
                'discount_amount'  => 0,
                'total_amount'     => $amount,
                'notes'            => rand(0,1) ? 'Tolong pakai sabun ekstra ya' : null,
                'completed_at'     => $status === 'completed' ? $scheduledAt->copy()->addHour() : null,
                'created_at'       => $scheduledAt->copy()->subHours(rand(1, 48)),
                'updated_at'       => now(),
            ]);

            // Payment
            $payStatus = match ($status) {
                'completed', 'in_progress', 'confirmed', 'assigned', 'on_way' => 'paid',
                'pending'   => 'pending',
                'cancelled' => rand(0,1) ? 'refunded' : 'paid',
                default     => 'pending',
            };

            DB::table('payments')->insert([
                'booking_id'       => $bookingId,
                'payment_method'   => $methods[array_rand($methods)],
                'payment_provider' => $providers[array_rand($providers)],
                'transaction_id'   => 'TXN-' . strtoupper(Str::random(12)),
                'status'           => $payStatus,
                'amount'           => $amount,
                'paid_at'          => $payStatus === 'paid' ? $scheduledAt->copy()->subMinutes(rand(5,60)) : null,
                'expired_at'       => $payStatus === 'pending' ? now()->addHours(24) : null,
                'refund_amount'    => $payStatus === 'refunded' ? $amount : null,
                'refunded_at'      => $payStatus === 'refunded' ? now()->subDays(rand(1,5)) : null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            // Review (only completed)
            if ($status === 'completed' && rand(0, 1)) {
                $comments = ['Pelayanannya bagus!','Cepat dan bersih.','Teknisinya ramah.','Puas dengan hasilnya.','Akan pesan lagi.','Harga sepadan dengan kualitas.'];
                DB::table('reviews')->insert([
                    'booking_id'    => $bookingId,
                    'customer_id'   => $customerId,
                    'technician_id' => $technicianId,
                    'rating'        => rand(4, 5),
                    'comment'       => $comments[array_rand($comments)],
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        }

        // Generate wash slots for next 7 days
        $outletIds = DB::table('outlets')->where('status','active')->pluck('id');
        $times = ['07:00','08:00','09:00','10:00','11:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00'];
        foreach ($outletIds as $outId) {
            $capacity = DB::table('outlets')->where('id', $outId)->value('capacity_per_hour') ?? 3;
            for ($d = 0; $d <= 7; $d++) {
                foreach ($times as $time) {
                    $booked = rand(0, min(3, $capacity));
                    DB::table('wash_slots')->insertOrIgnore([
                        'outlet_id'    => $outId,
                        'slot_date'    => now()->addDays($d)->toDateString(),
                        'slot_time'    => $time . ':00',
                        'capacity'     => $capacity,
                        'booked_count' => $booked,
                        'status'       => 'available',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }
            }
        }

        $this->command->info('  ✔ Bookings: 30 | Payments: 30 | Wash Slots: generated');
    }
}
