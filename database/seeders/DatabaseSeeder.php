<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $firebaseUrl = rtrim((string) env('FIREBASE_DB_URL', 'https://laundry-asas-default-rtdb.asia-southeast1.firebasedatabase.app'), '/');

        try {
            $response = Http::timeout(10)->get($firebaseUrl . '/laravel_db.json');
            $snapshot = $response->successful() ? $response->json() : null;
        } catch (\Throwable) {
            $snapshot = null;
        }

        if (! is_array($snapshot) || empty($snapshot['users']) || empty($snapshot['services'])) {
            $this->call(ServiceSeeder::class);
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('order_details')->truncate();
        DB::table('orders')->truncate();
        DB::table('services')->truncate();
        DB::table('users')->truncate();

        foreach ((array) ($snapshot['users'] ?? []) as $u) {
            if (! is_array($u)) continue;
            DB::table('users')->insert([
                'id' => $u['id'],
                'name' => $u['name'],
                'email' => $u['email'],
                'email_verified_at' => $u['email_verified_at'] ?? now(),
                'password' => $u['password'],
                'remember_token' => $u['remember_token'] ?? null,
                'is_admin' => (bool) ($u['is_admin'] ?? false),
                'role' => $u['role'] ?? 'customer',
                'avatar' => $u['avatar'] ?? null,
                'created_at' => $u['created_at'] ?? now(),
                'updated_at' => $u['updated_at'] ?? now(),
            ]);
        }

        foreach ((array) ($snapshot['services'] ?? []) as $s) {
            if (! is_array($s)) continue;
            DB::table('services')->insert([
                'id' => $s['id'],
                'service_name' => $s['service_name'],
                'category' => $s['category'],
                'price_per_unit' => $s['price_per_unit'],
                'unit_type' => $s['unit_type'],
                'description' => $s['description'] ?? null,
                'created_at' => $s['created_at'] ?? now(),
                'updated_at' => $s['updated_at'] ?? now(),
            ]);
        }

        foreach ((array) ($snapshot['orders'] ?? []) as $o) {
            if (! is_array($o)) continue;
            DB::table('orders')->insert([
                'id' => $o['id'],
                'customer_name' => $o['customer_name'],
                'whatsapp_number' => $o['whatsapp_number'],
                'total_price' => $o['total_price'],
                'status' => $o['status'],
                'created_at' => $o['created_at'] ?? now(),
                'updated_at' => $o['updated_at'] ?? now(),
            ]);
        }

        foreach ((array) ($snapshot['order_details'] ?? []) as $d) {
            if (! is_array($d)) continue;
            DB::table('order_details')->insert([
                'id' => $d['id'],
                'order_id' => $d['order_id'],
                'service_id' => $d['service_id'],
                'qty_or_weight' => $d['qty_or_weight'],
                'subtotal' => $d['subtotal'],
                'created_at' => $d['created_at'] ?? now(),
                'updated_at' => $d['updated_at'] ?? now(),
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
