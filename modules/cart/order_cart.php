<?php
layout('/index/header', 'My Orders');
$user_id = getSession('user_id');
if (!empty($user_id))
    $listOrders = getAll("
    SELECT *
    FROM orders
    WHERE user_id = $user_id
    ORDER BY created_at DESC
");
$list_cou = getAll("SELECT * FROM coupons WHERE status=1");
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
    <div class="container-order">
        <h2>My Orders</h2>
        <p class="sub-title">
            Manage and track all your orders placed in our store.
        </p>

        <div class="order-table-responsive">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($listOrders)): ?>
                    <?php foreach ($listOrders as $item): ?>

                    <tr>
                        <td>
                            <strong>#DH-<?= str_pad($item['id'], 5, '0', STR_PAD_LEFT) ?></strong>
                        </td>

                        <td>
                            <?= date('d/m/Y', strtotime($item['created_at'])) ?>
                        </td>

                        <td class="order-price">
                            $<?= number_format($item['total'], 2) ?>
                        </td>

                        <td>

                            <?php
                                    switch ($item['status']) {

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

                                        default:
                                            echo '<span class="status-badge">Unknown</span>';
                                    }
                                    ?>

                        </td>

                        <td>
                            <a href="<?php echo _HOST_URL . '/?module=cart&action=order_detail&id=' . $item['id']; ?>"
                                class="btn-view-detail">
                                View Details
                            </a>
                        </td>

                    </tr>

                    <?php endforeach; ?>

                    <?php else: ?>

                    <tr>
                        <td colspan="5" style="text-align:center;padding:40px;">
                            No orders found.
                        </td>
                    </tr>

                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
layout('/index/footer');
?>