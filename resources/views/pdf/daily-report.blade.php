<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Report - {{ $date }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; line-height: 1.6; color: #333; }
        .header { background: linear-gradient(to right, #dc2626, #fbbf24); color: white; padding: 30px; text-align: center; }
        .content { padding: 20px; }
        .card { background: white; padding: 20px; margin: 15px 0; border: 1px solid #e5e7eb; border-radius: 10px; }
        .stat { display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
        .stat:last-child { border-bottom: none; }
        .stat-value { font-weight: bold; color: #dc2626; }
        .section-title { color: #1e3a8a; border-bottom: 2px solid #dc2626; padding-bottom: 10px; margin-top: 30px; font-size: 18px; }
        .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .table th { background: #1e3a8a; color: white; padding: 12px; text-align: left; }
        .table td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        .table tr:nth-child(even) { background: #f9fafb; }
        .positive { color: #10b981; }
        .negative { color: #ef4444; }
        .footer { text-align: center; padding: 20px; margin-top: 40px; border-top: 2px solid #1e3a8a; font-size: 12px; color: #666; }
        .logo { width: 80px; height: 80px; background: linear-gradient(to right, #dc2626, #fbbf24); border-radius: 50%; display: inline-block; line-height: 80px; color: white; font-weight: bold; font-size: 24px; }
        .highlight { background: #fffbeb; padding: 20px; border-radius: 10px; border-left: 4px solid #fbbf24; margin: 20px 0; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">P</div>
        <h1>PaSSSna Restaurant</h1>
        <h2>Daily Operations Report</h2>
        <h3>{{ $date }}</h3>
        <p>Generated on: {{ now()->format('F d, Y \a\t H:i') }}</p>
    </div>

    <div class="content">
        <!-- Executive Summary -->
        <div class="highlight">
            <h3>📋 Executive Summary</h3>
            <p>This report provides a comprehensive overview of restaurant operations for {{ $date }}.
               Key metrics include total revenue, order statistics, customer data, inventory status,
               and staff performance.</p>
        </div>

        <!-- Revenue Section -->
        <h3 class="section-title">💰 Revenue & Sales</h3>
        <div class="card">
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
            @if(isset($comparison['revenue_growth']))
            <div class="stat">
                <span>Revenue Growth vs Yesterday:</span>
                <span class="stat-value {{ $comparison['revenue_growth'] >= 0 ? 'positive' : 'negative' }}">
                    {{ $comparison['revenue_growth'] >= 0 ? '+' : '' }}{{ number_format($comparison['revenue_growth'], 2) }}%
                </span>
            </div>
            @endif
        </div>

        <!-- Order Breakdown -->
        <h3 class="section-title">📊 Order Breakdown</h3>
        <div class="card">
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

        <!-- Top Selling Items -->
        @if(isset($top_items) && count($top_items) > 0)
        <h3 class="section-title">🏆 Top Selling Items</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Quantity Sold</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category }}</td>
                    <td>{{ $item->total_quantity }}</td>
                    <td>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->total_revenue, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="page-break"></div>

        <!-- Peak Hours -->
        @if(isset($peak_hours) && count($peak_hours) > 0)
        <h3 class="section-title">⏰ Peak Hours Analysis</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Hour</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                    <th>Avg. Order Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($peak_hours as $hour)
                <tr>
                    <td>{{ $hour->hour }}:00 - {{ $hour->hour + 1 }}:00</td>
                    <td>{{ $hour->order_count }}</td>
                    <td>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($hour->total_revenue, 2) }}</td>
                    <td>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ $hour->order_count > 0 ? number_format($hour->total_revenue / $hour->order_count, 2) : '0.00' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Customer Statistics -->
        <h3 class="section-title">👥 Customer Analytics</h3>
        <div class="card">
            <div class="stat">
                <span>New Customers:</span>
                <span class="stat-value">{{ $stats['new_customers'] }}</span>
            </div>
            <div class="stat">
                <span>Returning Customers:</span>
                <span class="stat-value">{{ $stats['returning_customers'] }}</span>
            </div>
            @if(isset($comparison['customer_growth']))
            <div class="stat">
                <span>Customer Growth vs Yesterday:</span>
                <span class="stat-value {{ $comparison['customer_growth'] >= 0 ? 'positive' : 'negative' }}">
                    {{ $comparison['customer_growth'] >= 0 ? '+' : '' }}{{ number_format($comparison['customer_growth'], 2) }}%
                </span>
            </div>
            @endif
        </div>

        <!-- Inventory Status -->
        <h3 class="section-title">📦 Inventory Status</h3>
        <div class="card">
            <div class="stat">
                <span>Low Stock Items:</span>
                <span class="stat-value negative">{{ $stats['low_stock_items'] }}</span>
            </div>
            <div class="stat">
                <span>Out of Stock Items:</span>
                <span class="stat-value negative">{{ $stats['out_of_stock_items'] }}</span>
            </div>
        </div>

        <!-- Staff Performance -->
        @if(isset($staff_performance) && count($staff_performance) > 0)
        <h3 class="section-title">👨‍🍳 Top Performing Staff</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Staff Name</th>
                    <th>Role</th>
                    <th>Orders Handled</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staff_performance as $staff)
                <tr>
                    <td>{{ $staff->name }}</td>
                    <td>{{ $staff->role }}</td>
                    <td>{{ $staff->orders_count }}</td>
                    <td>{{ $staff->is_on_leave ? 'On Leave' : 'Active' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <!-- Recommendations -->
        <div class="highlight">
            <h3>💡 Recommendations & Actions</h3>
            @if($stats['low_stock_items'] > 0)
            <p>🚨 <strong>Action Required:</strong> {{ $stats['low_stock_items'] }} items are running low in stock.
               Please review the inventory dashboard and place orders with suppliers.</p>
            @endif

            @if($stats['total_revenue'] < 1000)
            <p>📉 <strong>Revenue Alert:</strong> Daily revenue is below target. Consider running promotions
               or optimizing menu prices.</p>
            @endif

            @if($stats['new_customers'] < 5)
            <p>👥 <strong>Customer Acquisition:</strong> Low number of new customers. Consider marketing campaigns
               or referral programs.</p>
            @endif

            @if($stats['order_count'] > 0 && $stats['average_order_value'] < 25)
            <p>🛍️ <strong>Upsell Opportunity:</strong> Average order value is low. Train staff on upselling
               techniques and create combo offers.</p>
            @endif
        </div>
    </div>

    <div class="footer">
        <p><strong>PaSSSna Restaurant Management System</strong></p>
        <p>123 Gourmet Street, Food City | Phone: +1 (555) 123-4567</p>
        <p>Email: info@passsna.com | Website: www.passsna.com</p>
        <p>This is an automatically generated report. For questions, contact the system administrator.</p>
        <p>Report ID: DR-{{ date('Ymd-His') }} | Page {{ $pdf->getNumPages() }} of {{ $pdf->getNumPages() }}</p>
    </div>
</body>
</html>

