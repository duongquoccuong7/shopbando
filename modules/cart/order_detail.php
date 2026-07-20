<?php
layout('/index/header', 'Order Details');
$order_id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = getSession('user_id');
$list_cou = getAll("SELECT * FROM coupons WHERE status=1");
if (!empty($user_id)) {

    $order = getOne("
    SELECT 
        o.*,
        u.email, 
        u.phone,
        u.fullname,
        ua.province,
        ua.address,
        ua.ward
    FROM orders o 
    INNER JOIN users u 
        ON u.id = o.user_id
    LEFT JOIN user_addresses ua 
        ON ua.user_id = u.id 
    WHERE o.id = $order_id 
    AND o.user_id = $user_id
");
    $orderItems = getAll("
    SELECT
        oi.*,
        p.name,
        pv.image,
        c.name AS color_name,
        s.name AS size_name
    FROM order_items oi

    
    INNER JOIN products p
        ON p.id = oi.product_id

    LEFT JOIN product_variants pv
        ON pv.product_id = oi.product_id
        AND pv.color_id = oi.color_id
        AND pv.size_id = oi.size_id

    LEFT JOIN colors c
        ON c.id = oi.color_id

    LEFT JOIN sizes s
        ON s.id = oi.size_id

    WHERE oi.order_id = $order_id
");
    $subtotal = 0;
    if (!empty($orderItems)) {
        foreach ($orderItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
    }
    $total_amount = !empty($order['total']) ? $order['total'] : $subtotal;
}
?>
<!-- start coupon -->
<div class="wrap_cou">
    <div class="slide_cou">
        <div class="track">
            <?php foreach ($list_cou as $key => $cou): ?>
            <div class="tile_cou">
                <i class="fa-solid fa-angle-left"></i>
                <span><?php echo $cou['name']; ?></span>
                <i class="fa-solid fa-minus"></i>
                <span><?php echo $cou['description']; ?></span>
                <i class="fa-solid fa-minus"></i>
                <span><?php echo $cou['code']; ?></span>
                <i class="fa-solid fa-angle-right"></i>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="main-wrap-content main-wrap-cart">
    <div class="container-order-detail">

        <div class="detail-header">
            <a href="<?php echo _HOST_URL . '/?module=cart&action=order_list'; ?>" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Orders
            </a>

            <h2>
                Order Details - <?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?>
            </h2>

            <?php
            switch ($order['status']) {

                case 1:
                    echo '<span class="status-badge status-pending">Pending</span>';
                    break;

                case 2:
                    echo '<span class="status-badge status-processing">Processing</span>';
                    break;

                case 3:
                    echo '<span class="status-badge status-shipping">Shipping</span>';
                    break;

                case 4:
                    echo '<span class="status-badge status-success">Completed</span>';
                    break;

                case 5:
                    echo '<span class="status-badge status-cancel">Cancelled</span>';
                    break;
            }
            ?>

        </div>

        <!-- Customer & Shipping Information -->
        <div class="detail-grid-info">

            <div class="info-card">
                <h3>
                    <i class="fa-solid fa-location-dot"></i>
                    Shipping Address
                </h3>

                <p><strong><?= htmlspecialchars($order['fullname']) ?></strong></p>

                <p><?= htmlspecialchars($order['phone']) ?></p>
                <p><?= htmlspecialchars($order['province']) ?></p>
                <p><?= htmlspecialchars($order['address']) ?></p>
            </div>

            <div class="info-card">
                <h3>
                    <i class="fa-solid fa-credit-card"></i>
                    Payment Method
                </h3>

                <p>Cash on Delivery (COD)</p>

                <p class="payment-status text-warning">
                    Unpaid
                </p>
            </div>

        </div>

        <!-- Ordered Products -->
        <div class="detail-products-list">

            <h3>Ordered Products</h3>

            <?php foreach ($orderItems as $item): ?>

            <div class="detail-product-item">

                <img src="<?= $item['image'] ?>" class="product-thumb">

                <div class="product-info-text">

                    <h4><?= htmlspecialchars($item['name']) ?></h4>

                    <p>
                        Color:
                        <?= htmlspecialchars($item['color_name']) ?>
                    </p>

                    <p>
                        Size:
                        <?= htmlspecialchars($item['size_name']) ?>
                    </p>

                    <p>
                        Quantity:
                        <?= $item['quantity'] ?>
                    </p>

                </div>

                <div class="product-item-price">

                    $<?= number_format($item['price'], 2) ?>

                </div>

            </div>

            <?php endforeach; ?>
            <!-- Order Summary -->
            <div class="order-summary-box">

                <div class="summary-line">
                    <span>Subtotal:</span>
                    <span>$<?= number_format($subtotal, 2) ?></span>
                </div>

                <div class="summary-line">
                    <span>Shipping Fee:</span>
                    <span> Free</span>
                </div>

                <div class="summary-line total-line">
                    <span>Total Amount:</span>
                    <span class="total-price">
                        $<?= number_format($order['total'], 2) ?>
                    </span>
                </div>

            </div>

        </div>

    </div>
</div>

<?php
layout('/index/footer');
?>