<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Alert</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc2626; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9fafb; }
        .item { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #dc2626; }
        .footer { text-align: center; padding: 20px; background: #f3f4f6; color: #666; font-size: 12px; }
        .alert { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚨 Low Stock Alert</h1>
            <p>PaSSSna Restaurant Inventory System</p>
        </div>

        <div class="content">
            <p>Hello Admin,</p>
            <p>The following items are running low in stock and need to be reordered:</p>

            <div class="alert">⚠️ Total Low Stock Items: {{ $total_items }}</div>

            @foreach($items as $item)
            <div class="item">
                <h3>{{ $item->name }}</h3>
                <p><strong>Current Quantity:</strong> {{ $item->current_quantity }}</p>
                <p><strong>Minimum Required:</strong> {{ $item->minimum_quantity }}</p>
                <p><strong>Category:</strong> {{ $item->category }}</p>
                <p><strong>Supplier:</strong> {{ $item->supplier ?? 'N/A' }}</p>
            </div>
            @endforeach

            <p>Please take necessary action to restock these items as soon as possible.</p>

            <p>
                <a href="{{ url('/admin/inventory') }}" style="background: #dc2626; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; border-radius: 5px;">
                    View Inventory Dashboard
                </a>
            </p>
        </div>

        <div class="footer">
            <p>This is an automated notification from PaSSSna Restaurant Management System.</p>
            <p>Generated on: {{ $date }}</p>
            <p>© {{ date('Y') }} PaSSSna Restaurant. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

