<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use App\Console\Commands\GenerateDailyReport;
use App\Models\User;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Inventory;
use App\Models\Staff;
use App\Models\Report;
use App\Mail\DailyReport;
use Carbon\Carbon;
use Mockery;

class GenerateDailyReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        User::factory()->create(['role' => 'admin', 'email' => 'admin@test.com', 'is_active' => true]);
        User::factory()->create(['role' => 'customer', 'email' => 'customer@test.com']);
        Order::factory()->create(['status' => 'completed', 'total' => 100.00, 'created_at' => now()]);
        Reservation::factory()->create(['status' => 'confirmed', 'reservation_date' => now()]);
        Inventory::factory()->create(['current_quantity' => 5, 'minimum_quantity' => 10, 'is_active' => true]);
        Staff::factory()->create(['status' => Staff::STATUS_ACTIVE]);
    }

    public function test_command_can_be_executed()
    {
        // Mock PDF facade
        $pdfMock = Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $pdfMock->shouldReceive('save')->once()->andReturnSelf();

        PDF::shouldReceive('loadView')
            ->once()
            ->andReturn($pdfMock);

        // Mock Mail facade
        Mail::shouldReceive('to->send')
            ->once()
            ->andReturn(null);

        $this->artisan('report:generate-daily')
            ->expectsOutput('Generating daily report...')
            ->expectsOutput('Daily report sent to: admin@test.com')
            ->expectsOutput('Daily report generated successfully: daily-report-' . now()->format('Y-m-d') . '.pdf')
            ->assertExitCode(0);

        // Assert that a report was created in the database
        $admin = User::where('email', 'admin@test.com')->firstOrFail();
        $this->assertDatabaseHas('reports', [
            'report_type' => 'daily',
            // SQLite stores `date` columns as text, and Laravel may serialize `date` casts with time (00:00:00).
            'start_date' => Carbon::today()->startOfDay()->toDateTimeString(),
            'end_date' => Carbon::today()->startOfDay()->toDateTimeString(),
            'generated_by' => $admin->id,
        ]);
    }

    public function test_collect_daily_stats()
    {
        $command = new GenerateDailyReport();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('collectDailyStats');
        $method->setAccessible(true);

        $today = Carbon::today();
        $stats = $method->invoke($command, $today);

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_revenue', $stats);
        $this->assertArrayHasKey('order_count', $stats);
        $this->assertArrayHasKey('order_types', $stats);
        $this->assertArrayHasKey('reservation_count', $stats);
        $this->assertArrayHasKey('new_customers', $stats);
        $this->assertArrayHasKey('low_stock_items', $stats);
        $this->assertArrayHasKey('active_staff', $stats);
    }

    public function test_collect_comparison_stats()
    {
        $command = new GenerateDailyReport();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('collectComparisonStats');
        $method->setAccessible(true);

        $yesterday = Carbon::yesterday();
        $comparison = $method->invoke($command, $yesterday);

        $this->assertIsArray($comparison);
        $this->assertArrayHasKey('revenue_growth', $comparison);
        $this->assertArrayHasKey('yesterday_revenue', $comparison);
        $this->assertArrayHasKey('order_count_growth', $comparison);
        $this->assertArrayHasKey('customer_growth', $comparison);
    }

    public function test_get_top_selling_items()
    {
        $command = new GenerateDailyReport();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('getTopSellingItems');
        $method->setAccessible(true);

        $today = Carbon::today();
        $topItems = $method->invoke($command, $today);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $topItems);
        // Assuming we have order items, check structure
        if ($topItems->isNotEmpty()) {
            $first = $topItems->first();
            $this->assertObjectHasProperty('name', $first);
            $this->assertObjectHasProperty('total_quantity', $first);
            $this->assertObjectHasProperty('total_revenue', $first);
        }
    }

    public function test_get_peak_hours()
    {
        $command = new GenerateDailyReport();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('getPeakHours');
        $method->setAccessible(true);

        $today = Carbon::today();
        $peakHours = $method->invoke($command, $today);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $peakHours);
        if ($peakHours->isNotEmpty()) {
            $first = $peakHours->first();
            $this->assertObjectHasProperty('hour', $first);
            $this->assertObjectHasProperty('order_count', $first);
            $this->assertObjectHasProperty('total_revenue', $first);
        }
    }

    public function test_get_reservation_stats()
    {
        $command = new GenerateDailyReport();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('getReservationStats');
        $method->setAccessible(true);

        $today = Carbon::today();
        $reservationStats = $method->invoke($command, $today);

        $this->assertIsArray($reservationStats);
        $this->assertArrayHasKey('by_status', $reservationStats);
        $this->assertArrayHasKey('by_time_slot', $reservationStats);
        $this->assertArrayHasKey('average_party_size', $reservationStats);
    }

    public function test_get_staff_performance()
    {
        $command = new GenerateDailyReport();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('getStaffPerformance');
        $method->setAccessible(true);

        $today = Carbon::today();
        $staffPerformance = $method->invoke($command, $today);

        // Assuming we have staff, check if it's a collection
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $staffPerformance);
    }

    public function test_calculate_growth()
    {
        $command = new GenerateDailyReport();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('calculateGrowth');
        $method->setAccessible(true);

        $yesterday = Carbon::yesterday();

        // Test orders growth
        $growth = $method->invoke($command, 'orders', $yesterday);
        $this->assertIsFloat($growth);

        // Test customers growth
        $growth = $method->invoke($command, 'customers', $yesterday);
        $this->assertIsFloat($growth);

        // Test invalid type
        $growth = $method->invoke($command, 'invalid', $yesterday);
        $this->assertEquals(0, $growth);
    }

    public function test_get_returning_customers()
    {
        $command = new GenerateDailyReport();
        $reflection = new \ReflectionClass($command);
        $method = $reflection->getMethod('getReturningCustomers');
        $method->setAccessible(true);

        $today = Carbon::today();
        $returning = $method->invoke($command, $today);

        $this->assertIsInt($returning);
        $this->assertGreaterThanOrEqual(0, $returning);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
