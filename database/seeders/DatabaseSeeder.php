<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Akun Admin (Bari Achmad)
        $admin = User::firstOrCreate(['email' => 'admin@gmail.com'], [
            'name'     => 'Bari Achmad (Admin)',
            'password' => Hash::make('password123'),
            'is_admin' => true,
            'role'     => 'admin',
        ]);

        if (! $admin->is_admin || $admin->role !== 'admin') {
            $admin->is_admin = true;
            $admin->role     = 'admin';
            $admin->save();
        }

        // 2. Akun Kasir (untuk uji perbedaan role Admin vs Kasir)
        $kasir = User::firstOrCreate(['email' => 'kasir@gmail.com'], [
            'name'     => 'Rina Wulandari (Kasir)',
            'password' => Hash::make('password123'),
            'is_admin' => false,
            'role'     => 'kasir',
        ]);

        if ($kasir->role !== 'kasir') {
            $kasir->role = 'kasir';
            $kasir->save();
        }

        // 3. Seed Layanan Standar
        if (! Service::exists()) {
            $this->call(ServiceSeeder::class);
        }

        // 4. Seed Contoh Pesanan Awal agar Dashboard & Laporan langsung hidup
        if (! Order::exists()) {
            $reguler = Service::where('service_name', 'Cuci Kering Setrika Reguler')->first();
            $express = Service::where('service_name', 'Cuci Kilat Express 4 Jam')->first();
            $jas     = Service::where('service_name', 'Dry Cleaning Jas / Blazer')->first();

            if ($reguler && $jas) {
                $o1 = Order::create([
                    'customer_name'   => 'Dina Putri',
                    'whatsapp_number' => '081234567890',
                    'total_price'     => ($reguler->price_per_unit * 2) + ($jas->price_per_unit * 1),
                    'status'          => 'Antrean',
                ]);
                $o1->orderDetails()->create([
                    'service_id'    => $reguler->id,
                    'qty_or_weight' => 2,
                    'subtotal'      => $reguler->price_per_unit * 2,
                ]);
                $o1->orderDetails()->create([
                    'service_id'    => $jas->id,
                    'qty_or_weight' => 1,
                    'subtotal'      => $jas->price_per_unit * 1,
                ]);
            }

            if ($express) {
                $o2 = Order::create([
                    'customer_name'   => 'Nadia Kusuma',
                    'whatsapp_number' => '082199000404',
                    'total_price'     => $express->price_per_unit * 3,
                    'status'          => 'Dicuci',
                ]);
                $o2->orderDetails()->create([
                    'service_id'    => $express->id,
                    'qty_or_weight' => 3,
                    'subtotal'      => $express->price_per_unit * 3,
                ]);
            }

            if ($reguler) {
                $o3 = Order::create([
                    'customer_name'   => 'Andi Saputra',
                    'whatsapp_number' => '081234500101',
                    'total_price'     => $reguler->price_per_unit * 4,
                    'status'          => 'Selesai',
                ]);
                $o3->orderDetails()->create([
                    'service_id'    => $reguler->id,
                    'qty_or_weight' => 4,
                    'subtotal'      => $reguler->price_per_unit * 4,
                ]);
            }
        }
    }
}
