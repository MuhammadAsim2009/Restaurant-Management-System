<?php
require_once '../includes/db.php';
require_once '../includes/auth_guard.php'; // Ensure only admin can print
require_once '../includes/site_settings.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$order_id) {
    die("Invalid Order ID");
}

try {
    // Fetch Order
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    if (!$order) {
        die("Order not found");
    }

    // Fetch Items
    $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #ORD-<?php echo $order['id']; ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 40px;
            font-size: 14px;
            line-height: 1.5;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo-box h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            text-transform: uppercase;
        }

        .logo-box p {
            margin: 5px 0 0 0;
            font-size: 12px;
            font-weight: 700;
            color: #666;
        }

        .order-info {
            text-align: right;
        }

        .order-info h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
        }

        .order-info p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .detail-block h3 {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 10px 0;
            color: #666;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        .detail-block p {
            margin: 0;
            font-weight: 700;
            font-size: 14px;
        }

        .detail-block .address {
            font-weight: 400;
            font-style: italic;
            font-size: 12px;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        th {
            text-align: left;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1px;
            padding: 12px 8px;
            border-bottom: 2px solid #000;
        }

        td {
            padding: 12px 8px;
            border-bottom: 1px solid #eee;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .totals {
            margin-left: auto;
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .total-row.grand {
            border-top: 2px solid #000;
            margin-top: 10px;
            padding-top: 15px;
            font-size: 18px;
            font-weight: 800;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px dashed #ccc;
            padding-top: 20px;
        }

        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }

        .print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #000;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Print Now</button>

    <div class="header">
        <div class="logo-box">
            <h1><?php echo htmlspecialchars($SETTINGS['site_name'] ?? 'KITCHEN 86'); ?></h1>
            <p>Official Order Invoice</p>
        </div>
        <div class="order-info">
            <h2>#ORD-<?php echo $order['id']; ?></h2>
            <p><?php echo date('F j, Y | g:i A', strtotime($order['created_at'])); ?></p>
        </div>
    </div>

    <div class="details-grid">
        <div class="detail-block">
            <h3>Customer</h3>
            <p><?php echo htmlspecialchars($order['customer_name']); ?></p>
            <p style="font-weight: 400; font-size: 12px;"><?php echo htmlspecialchars($order['phone']); ?></p>
        </div>
        <div class="detail-block">
            <h3>Order Summary</h3>
            <p><?php echo strtoupper($order['order_type']); ?> | <?php echo strtoupper($order['payment_method']); ?></p>
            <p class="address"><?php echo $order['address'] ?: 'Customer Pickup'; ?></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Description</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td>
                    <div style="font-weight: 700;"><?php echo htmlspecialchars($item['item_name']); ?></div>
                    <div style="font-size: 10px; color: #666; text-transform: uppercase;"><?php echo $item['item_type'] ?: 'item'; ?></div>
                </td>
                <td class="text-center"><?php echo $item['quantity']; ?></td>
                <td class="text-right">PKR <?php echo number_format($item['item_price']); ?></td>
                <td class="text-right">PKR <?php echo number_format($item['subtotal']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span style="color: #666; font-size: 12px; font-weight: 700; text-transform: uppercase;">Subtotal</span>
            <span style="font-weight: 700;">PKR <?php echo number_format($order['subtotal']); ?></span>
        </div>
        <div class="total-row">
            <span style="color: #666; font-size: 12px; font-weight: 700; text-transform: uppercase;">Delivery Fee</span>
            <span style="font-weight: 700;">PKR <?php echo number_format($order['delivery_fee']); ?></span>
        </div>
        <div class="total-row grand">
            <span style="text-transform: uppercase;">Grand Total</span>
            <span>PKR <?php echo number_format($order['total']); ?></span>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for choosing <?php echo htmlspecialchars($SETTINGS['site_name'] ?? 'Kitchen 86'); ?>! We hope you enjoy your meal.</p>
        <p>This is a computer-generated invoice.</p>
    </div>

    <script>
        // Auto-trigger print dialog after a short delay to ensure everything is rendered
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
