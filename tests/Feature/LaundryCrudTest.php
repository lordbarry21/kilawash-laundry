<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaundryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_crud_services_and_orders_on_sqlite(): void
    {
        $admin = User::create([
            'name' => 'Bari Achmad (Admin)',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'is_admin' => true,
            'role' => 'admin',
        ]);

        // 1. Create service
        $resCreate = $this->actingAs($admin)->post('/admin/services', [
            'service_name' => 'Cuci Kering Setrika Reguler',
            'category' => 'Kiloan',
            'price_per_unit' => 7000,
            'unit_type' => 'Kg',
            'description' => 'Layanan reguler',
        ]);
        $resCreate->assertRedirect('/admin/services');

        $service = Service::firstOrFail();

        // 2. Edit page
        $this->actingAs($admin)->get("/admin/services/{$service->id}/edit")->assertOk();

        // 3. Update service (same values + changed values)
        $resUpdate = $this->actingAs($admin)->put("/admin/services/{$service->id}", [
            'service_name' => 'Cuci Kering Setrika Reguler',
            'category' => 'Kiloan',
            'price_per_unit' => 7500,
            'unit_type' => 'Kg',
            'description' => 'Layanan reguler update',
        ]);
        $resUpdate->assertRedirect('/admin/services');
        $this->assertDatabaseHas('services', ['id' => $service->id, 'price_per_unit' => 7500]);

        // 4. Create order via /checkout
        $resOrder = $this->actingAs($admin)->post('/checkout', [
            'customer_name' => 'Dina Putri',
            'whatsapp_number' => '081234567890',
            'items' => [$service->id => 2],
        ]);
        $resOrder->assertRedirect('/pemesanan');

        $order = Order::firstOrFail();
        $this->assertEquals(15000, $order->total_price);

        // 5. Update service AFTER order exists (same unit_type)
        $resUpdate2 = $this->actingAs($admin)->put("/admin/services/{$service->id}", [
            'service_name' => 'Cuci Kering Setrika Reguler Plus',
            'category' => 'Kiloan',
            'price_per_unit' => 8000,
            'unit_type' => 'Kg',
            'description' => 'Updated after order exists',
        ]);
        $resUpdate2->assertRedirect('/admin/services');

        // 6. Update order status
        $resStatus = $this->actingAs($admin)->patch("/admin/orders/{$order->id}/status", [
            'status' => 'Dicuci',
        ]);
        $resStatus->assertStatus(302);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'Dicuci']);

        // 7. Upload & remove profile avatar via compressed avatar_data
        $tinyPng = 'data:image/png;base64,' . base64_encode(str_repeat('A', 64));
        $resAvatar = $this->actingAs($admin)->patch('/profile', [
            'name' => 'Bari Achmad (Admin)',
            'email' => 'admin@gmail.com',
            'avatar_data' => $tinyPng,
        ]);
        $resAvatar->assertRedirect('/profile');
        $this->assertEquals($tinyPng, $admin->fresh()->avatar);

        $resRemove = $this->actingAs($admin)->patch('/profile', [
            'name' => 'Bari Achmad (Admin)',
            'email' => 'admin@gmail.com',
            'remove_avatar' => '1',
        ]);
        $resRemove->assertRedirect('/profile');
        $this->assertNull($admin->fresh()->avatar);
    }
}
