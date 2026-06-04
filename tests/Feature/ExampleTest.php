<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test home page accessibility.
     */
    public function test_home_page_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('PaSSSna Restaurant');
    }

    /**
     * Test menu page accessibility.
     */
    public function test_menu_page_is_accessible(): void
    {
        $response = $this->get('/menu');
        $response->assertStatus(200);
        $response->assertSee('Menu');
    }

    /**
     * Test reservation page accessibility.
     */
    public function test_reservation_page_is_accessible(): void
    {
        $response = $this->get('/reservation');
        $response->assertStatus(200);
        $response->assertSee('Reservation');
    }

    /**
     * Test contact page accessibility.
     */
    public function test_contact_page_is_accessible(): void
    {
        $response = $this->get('/contact');
        $response->assertStatus(200);
        $response->assertSee('Contact Us');
    }

    /**
     * Test customer registration.
     */
    public function test_customer_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => '0712345678',
            'terms' => true,
        ]);

        $response->assertRedirect('/customer/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'customer',
        ]);
    }

    /**
     * Test admin login.
     */
    public function test_admin_can_login(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin.passsna@gmail.com',
            'password' => bcrypt('PaSSSna_log'),
            'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin.passsna@gmail.com',
            'password' => 'PaSSSna_log',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * Test customer dashboard access.
     */
    public function test_customer_can_access_dashboard(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get('/customer/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    /**
     * Test admin dashboard access.
     */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }

    /**
     * Test menu item addition to cart.
     */
    public function test_customer_can_add_item_to_cart(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $item = MenuItem::factory()->create(['is_available' => true]);

        $response = $this->actingAs($customer)->postJson('/cart/add', [
            'item_id' => $item->id,
            'quantity' => 2,
            'size' => 'medium',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Test reservation creation.
     */
    public function test_customer_can_create_reservation(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $table = Table::factory()->create([
            'capacity' => 4,
            'is_active' => true,
            'status' => 'available',
        ]);

        $response = $this->actingAs($customer)->post('/reservation', [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '0712345678',
            'date' => now()->addDays(1)->format('Y-m-d'),
            'time' => '18:00',
            'guests' => 4,
            'tables' => [$table->id],
            'terms' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reservations', [
            'customer_email' => 'test@example.com',
            'number_of_people' => 4,
        ]);
    }

    /**
     * Test order placement.
     */
    public function test_customer_can_place_order(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $item = MenuItem::factory()->create(['is_available' => true]);

        // First add to cart
        $this->actingAs($customer)->post('/cart/add', [
            'item_id' => $item->id,
            'quantity' => 1,
        ]);

        // Then checkout
        $response = $this->actingAs($customer)->post('/checkout', [
            'order_type' => 'takeaway',
            'payment_method' => 'cash',
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'phone' => '0712345678',
            'special_instructions' => 'Test order',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'order_type' => 'takeaway',
        ]);
    }

    /**
     * Test admin can update order status.
     */
    public function test_admin_can_update_order_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);

        $response = $this->actingAs($admin)->post("/admin/orders/{$order->id}/status", [
            'status' => 'preparing',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'preparing',
        ]);
    }

    /**
     * Test PDF invoice generation.
     */
    public function test_order_invoice_pdf_generation(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $order = \App\Models\Order::factory()->create(['user_id' => $customer->id]);

        $response = $this->actingAs($customer)->get("/pdf/invoice/{$order->id}");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /**
     * Test unauthorized access to admin panel.
     */
    public function test_customer_cannot_access_admin_panel(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get('/admin/dashboard');
        $response->assertRedirect('/');
    }

    /**
     * Test forgot password functionality.
     */
    public function test_forgot_password_functionality(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status', 'We have emailed your password reset link!');
    }
}
