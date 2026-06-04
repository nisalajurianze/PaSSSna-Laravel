<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\MenuItem;
use App\Models\Reservation;
use App\Models\Inventory;
use App\Models\Staff;
use App\Models\Promotion;
use App\Services\OrderService;
use App\Services\ReservationService;
use App\Services\InventoryService;
use App\Models\Table;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user model relationships.
     */
    public function test_user_has_orders(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(Order::class, $user->orders->first());
        $this->assertEquals($order->id, $user->orders->first()->id);
    }

    /**
     * Test user role detection.
     */
    public function test_user_role_detection(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isCustomer());
        $this->assertTrue($customer->isCustomer());
        $this->assertFalse($customer->isAdmin());
    }

    /**
     * Test order status colors.
     */
    public function test_order_status_colors(): void
    {
        $order = new Order();

        $this->assertEquals('yellow', $order->getStatusColor('pending'));
        $this->assertEquals('green', $order->getStatusColor('completed'));
        $this->assertEquals('red', $order->getStatusColor('cancelled'));
        $this->assertEquals('orange', $order->getStatusColor('preparing'));
    }

    /**
     * Test order number generation.
     */
    public function test_order_number_generation(): void
    {
        $order = Order::factory()->create(['id' => 123]);
        $orderNumber = $order->generateOrderNumber();

        $this->assertStringContainsString('ORD-', $orderNumber);
        $this->assertStringContainsString('123', $orderNumber);
    }

    /**
     * Test menu item availability toggle.
     */
    public function test_menu_item_availability_toggle(): void
    {
        $item = MenuItem::factory()->create(['is_available' => true]);

        $item->toggleAvailability();
        $this->assertFalse($item->is_available);

        $item->toggleAvailability();
        $this->assertTrue($item->is_available);
    }

    /**
     * Test reservation status update.
     */
    public function test_reservation_status_update(): void
    {
        $reservation = Reservation::factory()->create(['status' => 'pending']);

        $reservation->updateStatus('confirmed');
        $this->assertEquals('confirmed', $reservation->status);

        $reservation->updateStatus('cancelled');
        $this->assertEquals('cancelled', $reservation->status);
    }

    /**
     * Test inventory stock check.
     */
    public function test_inventory_stock_check(): void
    {
        $inventory = Inventory::factory()->create([
            'current_stock' => 10,
            'min_stock' => 5,
        ]);

        $this->assertFalse($inventory->isLowStock());

        $inventory->current_stock = 3;
        $inventory->save();

        $this->assertTrue($inventory->isLowStock());
    }

    /**
     * Test staff role assignment.
     */
    public function test_staff_role_assignment(): void
    {
        $staff = Staff::factory()->create(['role' => 'chef']);

        $this->assertEquals('chef', $staff->role);
        $this->assertTrue($staff->isChef());
        $this->assertFalse($staff->isWaiter());
    }

    /**
     * Test promotion validity check.
     */
    public function test_promotion_validity_check(): void
    {
        $activePromotion = Promotion::factory()->create([
            'is_active' => true,
            'valid_from' => now()->subDay(),
            'valid_to' => now()->addDay(),
        ]);

        $expiredPromotion = Promotion::factory()->create([
            'is_active' => true,
            'valid_from' => now()->subDays(2),
            'valid_to' => now()->subDay(),
        ]);

        $this->assertTrue($activePromotion->isValid());
        $this->assertFalse($expiredPromotion->isValid());
    }

    /**
     * Test order service calculation.
     */
    public function test_order_service_calculation(): void
    {
        $orderService = new OrderService();

        $subtotal = 1000;
        $taxRate = 8;
        $deliveryCharge = 300;

        $total = $orderService->calculateTotal($subtotal, $taxRate, $deliveryCharge);

        $expectedTotal = $subtotal + ($subtotal * $taxRate / 100) + $deliveryCharge;
        $this->assertEquals($expectedTotal, $total);
    }

    /**
     * Test reservation service availability check.
     */
    public function test_reservation_service_availability(): void
    {
        $reservationService = new ReservationService();

        $table = Table::factory()->create(['status' => 'available', 'is_active' => true]);

        $date = now()->addDay()->format('Y-m-d');
        $time = '18:00';
        $durationMinutes = 90;

        $startAt = \Carbon\Carbon::parse("{$date} {$time}");
        $endAt = $startAt->copy()->addMinutes($durationMinutes);

        $reservation = Reservation::factory()->create([
            'reservation_date' => $date,
            'reservation_time' => $time,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'duration_minutes' => $durationMinutes,
            'status' => 'confirmed',
        ]);

        $reservation->tables()->sync([$table->id]);
        $reservation->update(['table_numbers' => [$table->table_number], 'table_count' => 1]);

        $available = $reservationService->getAvailableTables($date, $time, 2, $durationMinutes);

        $this->assertFalse($available->pluck('id')->contains($table->id));
    }

    /**
     * Test inventory service low stock alert.
     */
    public function test_inventory_service_low_stock_alert(): void
    {
        $inventoryService = new InventoryService();

        // Create low stock items
        Inventory::factory()->create(['current_stock' => 2, 'min_stock' => 5]);
        Inventory::factory()->create(['current_stock' => 1, 'min_stock' => 5]);

        $lowStockItems = $inventoryService->getLowStockItems();

        $this->assertCount(2, $lowStockItems);
    }

    /**
     * Test menu item with offer price.
     */
    public function test_menu_item_with_offer_price(): void
    {
        $item = MenuItem::factory()->create([
            'price' => 1000,
            'offer_price' => 800,
        ]);

        $this->assertEquals(1000, $item->price);
        $this->assertEquals(800, $item->offer_price);
        $this->assertEquals(200, $item->getDiscountAmount());
        $this->assertEquals(20, $item->getDiscountPercentage());
    }

    /**
     * Test order item calculation.
     */
    public function test_order_item_calculation(): void
    {
        $item = MenuItem::factory()->create(['price' => 500]);

        $orderItem = new \App\Models\OrderItem([
            'menu_item_id' => $item->id,
            'quantity' => 2,
            'price' => 500,
        ]);

        $this->assertEquals(1000, $orderItem->getTotal());
    }

    /**
     * Test user password validation.
     */
    public function test_user_password_validation(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $this->assertTrue(\Hash::check('password123', $user->password));
        $this->assertFalse(\Hash::check('wrongpassword', $user->password));
    }

    /**
     * Test staff shift schedule.
     */
    public function test_staff_shift_schedule(): void
    {
        $staff = Staff::factory()->create();
        $schedule = \App\Models\ShiftSchedule::factory()->create([
            'staff_id' => $staff->id,
            'shift_date' => now()->next(\Carbon\Carbon::MONDAY)->toDateString(),
            'start_time' => '09:00',
            'end_time' => '17:00',
        ]);

        $this->assertInstanceOf(\App\Models\ShiftSchedule::class, $staff->schedules->first());
        $this->assertEquals('Monday', $staff->schedules->first()->day_of_week);
    }
}
