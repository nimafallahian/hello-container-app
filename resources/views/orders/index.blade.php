<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unprocessed Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .actions {
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #ffc107;
            color: #000;
        }
    </style>
</head>
<body>
    <h1>Unprocessed Orders</h1>
    
    <div class="actions">
        <a href="{{ route('orders.create') }}" class="btn">Create New Order</a>
    </div>
    
    @if($unprocessedOrders->isEmpty())
        <div class="empty-state">
            <p>No unprocessed orders found.</p>
            <p>Orders with payment pending (freight_payer_self = false) and no BL release date will appear here.</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Contract Number</th>
                    <th>BL Number</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Days Pending</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unprocessedOrders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->contract_number }}</td>
                        <td>{{ $order->bl_number }}</td>
                        <td><span class="status status-pending">Payment Pending</span></td>
                        <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>{{ floor($order->created_at->diffInDays(now())) }} days</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <p style="margin-top: 20px; color: #666;">
            Total unprocessed orders: {{ $unprocessedOrders->count() }}
        </p>
    @endif
</body>
</html> 