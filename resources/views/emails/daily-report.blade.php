<!DOCTYPE html>
<html>
<head>
    <title>Daily Report - {{ $date }}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(to right, #dc2626, #fbbf24); color: white; padding: 30px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
        .card { background: white; padding: 20px; margin: 15px 0; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .stat { display: flex; justify-content: space-between; margin: 10px 0; }
        .stat-value { font-weight: bold; color: #dc2626; }
        .positive { color: #10b981; }
        .negative { color: #ef4444; }
        .footer { text-align: center; padding: 20px; background: #1e3a8a; color: white; font-size: 12px; }
        .section-title { color: #1e3a8a; border-bottom: 2px solid #dc2626; padding-bottom: 10px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Daily Restaurant Report</h1>
            <h2>{{ $date }}</h2>
            <p>PaSSSna Restaurant Management System</p>
        </div>

        <div class="content">
            <p>Hello Admin,</p>
            <p>Here is your daily report summary for {{ $date }}:</p>

            <div class="card">
                <h3 class="section-title">📈 Revenue Overview</h3>
                <div class="stat">
                    <span>Total Revenue:</span>
                    <span class="stat-value">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($stats['total_revenue'], 2) }}</span>
                </div>
                <div class="stat">
                    <span>Total Orders:</span>
                    <span class="stat-value">{{ $stats['order_count'] }}</span>
                </div>
                <div class="stat">
                    <span>Average Order Value:</span>
                    <span class="stat-value">{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($stats['average_order_value'], 2) }}</span>
                </div>
            </div>

            <div class="card">
                <h3 class="section-title">🍽️ Order Breakdown</h3>
                <div class="stat">
                    <span>Dine-in Orders:</span>
                    <span class="stat-value">{{ $stats['order_types']['dine_in'] }}</span>
                </div>
                <div class="stat">
                    <span>Takeaway Orders:</span>
                    <span class="stat-value">{{ $stats['order_types']['takeaway'] }}</span>
                </div>
                <div class="stat">
                    <span>Delivery Orders:</span>
                    <span class="stat-value">{{ $stats['order_types']['delivery'] }}</span>
                </div>
            </div>

            <div class="card">
                <h3 class="section-title">📅 Reservations</h3>
                <div class="stat">
                    <span>Total Reservations:</span>
                    <span class="stat-value">{{ $stats['reservation_count'] }}</span>
                </div>
                <div class="stat">
                    <span>Confirmed Reservations:</span>
                    <span class="stat-value">{{ $stats['confirmed_reservations'] }}</span>
                </div>
            </div>

            <div class="card">
                <h3 class="section-title">👥 Customers</h3>
                <div class="stat">
                    <span>New Customers:</span>
                    <span class="stat-value">{{ $stats['new_customers'] }}</span>
                </div>
                <div class="stat">
                    <span>Returning Customers:</span>
                    <span class="stat-value">{{ $stats['returning_customers'] }}</span>
                </div>
            </div>

            <div class="card">
                <h3 class="section-title">📦 Inventory Status</h3>
                <div class="stat">
                    <span>Low Stock Items:</span>
                    <span class="stat-value negative">{{ $stats['low_stock_items'] }}</span>
                </div>
                <div class="stat">
                    <span>Out of Stock Items:</span>
                    <span class="stat-value negative">{{ $stats['out_of_stock_items'] }}</span>
                </div>
            </div>

            <div class="card">
                <h3 class="section-title">👨‍🍳 Staff Overview</h3>
                <div class="stat">
                    <span>Active Staff:</span>
                    <span class="stat-value">{{ $stats['active_staff'] }}</span>
                </div>
                <div class="stat">
                    <span>Staff on Leave:</span>
                    <span class="stat-value">{{ $stats['staff_on_leave'] }}</span>
                </div>
            </div>

            <p>
                <a href="{{ url('/admin/dashboard') }}" style="background: #dc2626; color: white; padding: 15px 30px; text-decoration: none; display: inline-block; border-radius: 5px; font-weight: bold;">
                    View Full Dashboard
                </a>
            </p>

            <p><em>A detailed PDF report is attached to this email.</em></p>
        </div>

        <div class="footer">
            <p>This is an automated daily report from PaSSSna Restaurant Management System.</p>
            <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
            <p>© {{ date('Y') }} PaSSSna Restaurant. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

