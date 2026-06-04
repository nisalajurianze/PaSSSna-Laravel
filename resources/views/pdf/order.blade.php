<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Invoice - PaSSSna Restaurant</title>
    <style>
        /* PDF Specific Styles */
        @page {
            margin: 0.5in;
            size: A4;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }

        .header {
            border-bottom: 3px solid #DC2626;
            padding-bottom: 20px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #DC2626 0%, #FBBF24 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 24px;
        }

        .restaurant-info h1 {
            color: #1E3A8A;
            margin: 0;
            font-size: 28px;
        }

        .restaurant-info p {
            color: #666;
            margin: 5px 0;
            font-size: 14px;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            color: #DC2626;
            margin: 0;
            font-size: 32px;
            font-weight: bold;
        }

        .invoice-title .invoice-number {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            background: linear-gradient(90deg, #1E3A8A 0%, #DC2626 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #1E3A8A;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .info-value {
            color: #333;
            font-size: 14px;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .order-table th {
            background: #1E3A8A;
            color: white;
            text-align: left;
            padding: 12px 15px;
            font-size: 14px;
        }

        .order-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
        }

        .order-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .order-table tr:hover {
            background-color: #f0f8ff;
        }

        .total-section {
            background: linear-gradient(135deg, #fef3c7 0%, #fde8e8 100%);
            border: 2px solid #FBBF24;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 16px;
        }

        .total-row.grand-total {
            border-top: 2px solid #DC2626;
            padding-top: 15px;
            margin-top: 15px;
            font-size: 20px;
            font-weight: bold;
            color: #DC2626;
        }

        .footer {
            border-top: 2px solid #1E3A8A;
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }

        .footer p {
            margin: 5px 0;
        }

        .qr-code {
            text-align: center;
            margin: 20px 0;
        }

        .qr-placeholder {
            display: inline-block;
            width: 100px;
            height: 100px;
            background: #f0f0f0;
            border: 2px dashed #ccc;
            border-radius: 5px;
            text-align: center;
            line-height: 100px;
            color: #999;
            font-size: 12px;
        }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-success {
            background-color: #10b981;
            color: white;
        }

        .badge-pending {
            background-color: #f59e0b;
            color: white;
        }

        .badge-processing {
            background-color: #3b82f6;
            color: white;
        }

        .badge-cancelled {
            background-color: #ef4444;
            color: white;
        }

        .order-status {
            margin-bottom: 20px;
        }

        .status-bar {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin: 20px 0;
        }

        .status-bar::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            right: 0;
            height: 3px;
            background: #e0e0e0;
            z-index: 1;
        }

        .status-step {
            position: relative;
            z-index: 2;
            text-align: center;
            flex: 1;
        }

        .status-dot {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e0e0e0;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            font-size: 12px;
        }

        .status-step.active .status-dot {
            background: #10b981;
            color: white;
        }

        .status-step.completed .status-dot {
            background: #10b981;
            color: white;
        }

        .status-label {
            font-size: 12px;
            color: #666;
        }

        .status-step.active .status-label {
            color: #10b981;
            font-weight: bold;
        }

        .delivery-info {
            background: linear-gradient(135deg, #eff6ff 0%, #fef3c7 100%);
            border: 1px solid #3b82f6;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }

        .delivery-info h4 {
            color: #1e40af;
            margin-top: 0;
            margin-bottom: 10px;
        }

        .note-box {
            background-color: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
        }

        .signature-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px dashed #ccc;
        }

        .signature-line {
            width: 200px;
            border-top: 1px solid #333;
            margin: 40px auto 10px;
            text-align: center;
        }

        .terms {
            font-size: 11px;
            color: #666;
            margin-top: 30px;
            line-height: 1.5;
        }

        .page-break {
            page-break-before: always;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .header {
                border-bottom: 2px solid #DC2626;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo-container">
            <div class="logo">P</div>
            <div class="restaurant-info">
                <h1>PaSSSna Restaurant</h1>
                <p>Premium Dining Experience</p>
                <p>123 Gourmet Street, Food City | +1 (555) 123-4567</p>
                <p>info@passsna.com | www.passsna.com</p>
            </div>
        </div>

        <div class="invoice-title">
            <h2>INVOICE</h2>
            <div class="invoice-number">INV-{{ $order->order_number }}</div>
        </div>
    </div>

    <!-- Order Status -->
    @if($order->order_type !== 'dine_in')
    <div class="order-status">
        <div class="section-title">ORDER STATUS</div>
        <div class="status-bar">
            @php
                $statuses = ['pending', 'confirmed', 'preparing', 'ready', $order->order_type === 'delivery' ? 'delivered' : 'completed'];
                $currentStatusIndex = array_search($order->status, $statuses);
            @endphp

            @foreach($statuses as $index => $status)
                <div class="status-step {{ $index <= $currentStatusIndex ? 'completed' : '' }} {{ $index == $currentStatusIndex ? 'active' : '' }}">
                    <div class="status-dot">
                        @if($index < $currentStatusIndex)
                            ✓
                        @else
                            {{ $index + 1 }}
                        @endif
                    </div>
                    <div class="status-label">
                        {{ ucfirst($status) }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Order Information -->
    <div class="section">
        <div class="section-title">ORDER INFORMATION</div>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <div class="info-label">Order Number</div>
                    <div class="info-value">{{ $order->order_number }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Order Date & Time</div>
                    <div class="info-value">{{ $order->created_at->format('F d, Y - h:i A') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Order Type</div>
                    <div class="info-value">
                        {{ ucfirst(str_replace('_', ' ', $order->order_type)) }}
                        @if($order->table_number)
                            (Table #{{ $order->table_number }})
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <div class="info-item">
                    <div class="info-label">Current Status</div>
                    <div class="info-value">
                        <span class="badge badge-{{ $order->status === 'cancelled' ? 'cancelled' : (($order->status === 'completed') ? 'success' : (($order->status === 'pending') ? 'pending' : 'processing')) }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Payment Method</div>
                    <div class="info-value">{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Payment Status</div>
                    <div class="info-value">
                        <span class="badge {{ $order->payment_status === 'paid' ? 'badge-success' : 'badge-pending' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="section">
        <div class="section-title">CUSTOMER INFORMATION</div>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <div class="info-label">Customer Name</div>
                    <div class="info-value">{{ $order->user->name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email Address</div>
                    <div class="info-value">{{ $order->user->email }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value">{{ $order->customer_phone }}</div>
                </div>
            </div>

            @if(in_array($order->order_type, ['delivery', 'takeaway']))
            <div>
                <div class="info-item">
                    <div class="info-label">Delivery Address</div>
                    <div class="info-value">{{ $order->delivery_address ?? $order->user->address }}</div>
                </div>
                @if($order->estimated_delivery_time)
                <div class="info-item">
                    <div class="info-label">Estimated Delivery Time</div>
                    <div class="info-value">{{ $order->estimated_delivery_time->format('h:i A') }}</div>
                </div>
                @endif
                @if($order->special_instructions)
                <div class="info-item">
                    <div class="info-label">Special Instructions</div>
                    <div class="info-value">{{ $order->special_instructions }}</div>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Delivery Information (for delivery orders) -->
    @if($order->order_type === 'delivery' && $order->delivery_charge > 0)
    <div class="delivery-info">
        <h4>Delivery Information</h4>
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <div class="info-label">Delivery Zone</div>
                    <div class="info-value">Central City Area</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Delivery Time</div>
                    <div class="info-value">30-45 minutes</div>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <div class="info-label">Delivery Partner</div>
                    <div class="info-value">PaSSSna Delivery Team</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tracking Number</div>
                    <div class="info-value">TRK-{{ strtoupper(substr(md5($order->id), 0, 8)) }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Order Items -->
    <div class="section">
        <div class="section-title">ORDER ITEMS</div>
        <table class="order-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 45%;">Item Description</th>
                    <th style="width: 10%;">Size</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 15%;">Unit Price</th>
                    <th style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->menuItem->name ?? $item->item_name ?? $item->name }}</strong>
                        @if(!empty($item->customizations))
                            <br><small>
                            @foreach(json_decode($item->customizations, true) as $key => $value)
                                {{ ucfirst($key) }}: {{ $value }}<br>
                            @endforeach
                            </small>
                        @endif
                        @if(!empty($item->toppings))
                            <br><small>Extra: {{ implode(', ', json_decode($item->toppings, true)) }}</small>
                        @endif
                    </td>
                    <td>{{ ucfirst($item->size) ?? 'Regular' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach

                @if(!empty($order->custom_items))
                @foreach(json_decode($order->custom_items, true) as $index => $customItem)
                <tr>
                    <td>{{ count($order->items) + $index + 1 }}</td>
                    <td>
                        <strong>Custom Meal</strong><br>
                        <small>
                            Base: {{ $customItem['base'] ?? 'Rice' }}<br>
                            Protein: {{ $customItem['protein'] ?? 'Chicken' }}<br>
                            Vegetables: {{ implode(', ', $customItem['vegetables'] ?? []) }}<br>
                            Sauce: {{ $customItem['sauce'] ?? 'Regular' }}
                        </small>
                    </td>
                    <td>{{ $customItem['size'] ?? 'Regular' }}</td>
                    <td>{{ $customItem['quantity'] ?? 1 }}</td>
                    <td>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($customItem['price'] ?? 12.99, 2) }}</td>
                    <td>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format(($customItem['price'] ?? 12.99) * ($customItem['quantity'] ?? 1), 2) }}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Order Summary -->
    <div class="total-section">
        <div class="section-title" style="background: transparent; color: #1E3A8A; padding-left: 0;">ORDER SUMMARY</div>

        <div class="total-row">
            <span>Subtotal:</span>
            <span>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->subtotal, 2) }}</span>
        </div>

        @if($order->tax > 0)
        <div class="total-row">
            <span>Tax ({{ config('restaurant.order.tax_rate', 8) }}%):</span>
            <span>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->tax, 2) }}</span>
        </div>
        @endif

        @if($order->delivery_charge > 0)
        <div class="total-row">
            <span>Delivery Charge:</span>
            <span>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->delivery_charge, 2) }}</span>
        </div>
        @endif

        @if($order->discount > 0)
        <div class="total-row">
            <span>Discount ({{ $order->promo_code ? 'Promo: ' . $order->promo_code : '' }}):</span>
            <span>-{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->discount, 2) }}</span>
        </div>
        @endif

        <div class="total-row grand-total">
            <span>GRAND TOTAL:</span>
            <span>{{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->total, 2) }}</span>
        </div>

        @if($order->payment_method === 'cash_on_delivery')
        <div class="note-box">
            <strong>Cash on Delivery:</strong> Please have exact change ready. Our delivery executive will collect {{ config('restaurant.payment.currency_symbol', 'LKR ') }}{{ number_format($order->total, 2) }} upon delivery.
        </div>
        @endif
    </div>

    <!-- QR Code for Verification -->
    <div class="qr-code">
        <div class="qr-placeholder">
            Order QR<br>Code
        </div>
        <p style="font-size: 12px; color: #666; margin-top: 10px;">
            Scan to verify order authenticity
        </p>
    </div>

    <!-- Special Notes -->
    @if($order->special_instructions)
    <div class="note-box">
        <strong>Customer Note:</strong><br>
        {{ $order->special_instructions }}
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>PaSSSna Restaurant - Premium Dining Experience</strong></p>
        <p>123 Gourmet Street, Food City | Phone: +1 (555) 123-4567 | Email: info@passsna.com</p>
        <p>Business Hours: Mon-Thu 11:00 AM - 10:00 PM, Fri-Sat 11:00 AM - 11:00 PM, Sun 12:00 PM - 9:00 PM</p>

        <div class="signature-section">
            <div class="signature-line"></div>
            <p style="margin-top: 5px;">Authorized Signature</p>
        </div>

        <div class="terms">
            <p><strong>Terms & Conditions:</strong></p>
            <p>1. All prices are in USD and include applicable taxes.</p>
            <p>2. Delivery times are estimates and may vary based on traffic and weather conditions.</p>
            <p>3. For cancellations, please contact us at least 30 minutes before estimated delivery time.</p>
            <p>4. Quality complaints must be reported within 24 hours of delivery.</p>
            <p>5. We reserve the right to refuse service to anyone.</p>
        </div>

        <p style="margin-top: 20px; font-size: 10px; color: #999;">
            Invoice generated on {{ now()->format('F d, Y - h:i A') }} |
            Invoice ID: INV-{{ $order->order_number }} |
            System: PaSSSna Restaurant Management System v1.0
        </p>

        <p style="font-size: 10px; color: #999; margin-top: 10px;">
            Thank you for choosing PaSSSna Restaurant! We hope to serve you again soon.
        </p>
    </div>

    <!-- Page Break for Order Details (if needed) -->
    @if(count($order->items) > 10)
    <div class="page-break"></div>
    <div style="text-align: center; margin: 50px 0;">
        <h2 style="color: #1E3A8A;">Order Details - Page 2</h2>
        <p>Continued from previous page</p>
    </div>
    @endif
</body>
</html>

