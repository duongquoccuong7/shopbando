<?php
layout('dashboard/header', 'Dashboard');
$user_id = getSession('user_id');
$get_ad = getOne("SELECT * FROM users WHERE id = $user_id AND role='admin' ");

$get_pro = getAll("SELECT * FROM products WHERE status=1");
$get_order = getALL("SELECT * FROM orders ");
$get_user = getAll("SELECT * FROM users");
$listLatestOrder = getAll("
SELECT
    o.id,
    o.total,
    o.status,
    o.created_at,

    u.fullname,

    (
        SELECT p.name
        FROM order_items oi
        INNER JOIN products p
            ON p.id = oi.product_id
        WHERE oi.order_id = o.id
        LIMIT 1
    ) AS product_name

FROM orders o

LEFT JOIN users u
    ON o.user_id = u.id

ORDER BY o.created_at DESC

LIMIT 5
");
$listOutStock = getAll("
 SELECT
    p.name,
    pv.image,
    SUM(pv.stock) AS stock
FROM products p
JOIN product_variants pv ON p.id = pv.product_id
WHERE p.status = 1
GROUP BY p.id
HAVING stock <= 5;
");
$listTopProduct = getAll("
SELECT
    p.id,
    p.name,
    p.thumbnail,
    SUM(oi.quantity) AS total_sold

FROM order_items oi

INNER JOIN products p
ON oi.product_id = p.id

INNER JOIN orders o
ON oi.order_id = o.id

WHERE o.status = 4

GROUP BY p.id, p.name, p.thumbnail

ORDER BY total_sold DESC

LIMIT 3
");
$listRevenue = getAll("
SELECT
    DATE(created_at) AS order_date,
    SUM(total) AS revenue
FROM orders
WHERE status = 4
GROUP BY DATE(created_at)
");
$labels7 = [];
$revenues7 = [];

foreach ($listRevenue as $item) {
    $labels7[] = date('d/m', strtotime($item['order_date']));
    $revenues7[] = (float)$item['revenue'];
}
$listRevenueYear = getAll("
SELECT
    MONTH(created_at) AS month,
    SUM(total) AS revenue
FROM orders
WHERE status = 4
AND YEAR(created_at) = YEAR(CURDATE())
GROUP BY MONTH(created_at)
ORDER BY MONTH(created_at)
");

$revenueByMonth = array_fill(1, 12, 0);

foreach ($listRevenueYear as $item) {
    $revenueByMonth[(int)$item['month']] = (float)$item['revenue'];
}

// Chart năm
$labelsYear = [
    'Jan',
    'Feb',
    'Mar',
    'Apr',
    'May',
    'Jun',
    'Jul',
    'Aug',
    'Sep',
    'Oct',
    'Nov',
    'Dec'
];

$data = array_values($revenueByMonth);
?>

<div class="main-wrap">
    <?php layout('dashboard/sidebar'); ?>

    <div class="content-menu">

        <div class="dashboard">

            <div class="dashboard-title">
                <h2>Dashboard</h2>
                <p>Welcome back, <?= $get_ad['fullname'];   ?> 👋</p>
            </div>

            <!-- Statistics -->
            <div class="dashboard-card">

                <a href="<?php echo _HOST_URL . "/?module=product&action=index" ?>">
                    <div class="card">
                        <div class="icon bg-blue">
                            <i class="fa-solid fa-box"></i>
                        </div>
                        <div class="info">
                            <h3><?php echo count($get_pro) ?></h3>
                            <span>Products</span>
                        </div>
                    </div>
                </a>

                <a href=" <?php echo _HOST_URL . "/?module=order&action=index" ?>">
                    <div class="card">
                        <div class="icon bg-green">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <div class="info">
                            <h3><?php echo count($get_order) ?></h3>
                            <span>Orders</span>
                        </div>
                    </div>
                </a>

                <a href="<?php echo _HOST_URL . "/?module=users&action=index" ?>">
                    <div class="card">
                        <div class="icon bg-orange">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="info">
                            <h3><?php echo count($get_user) ?></h3>
                            <span>Customers</span>
                        </div>
                    </div>
                </a>

                <a href="">
                    <div class="card">
                        <div class="icon bg-red">
                            <i class="fa-solid fa-dollar-sign"></i>
                        </div>
                        <div class="info">
                            <h3><?= number_format(array_sum(array_column($listRevenue, 'revenue')), 0, ',', '.') ?>
                                đ</h3>
                            <span>Revenue</span>
                        </div>
                    </div>
                </a>

            </div>
            <div class="dashboard-chart">

                <!-- Revenue 7 days -->
                <div class="chart-box">
                    <div class="box-title">
                        <h3>Revenue (Last 7 Days)</h3>
                    </div>

                    <canvas id="chart7day"></canvas>
                </div>
                <!-- Top Products -->

                <div class="product-box">

                    <div class="box-title">
                        <h3>Top Selling</h3>
                    </div>

                    <?php if (!empty($listTopProduct)): ?>

                        <?php foreach ($listTopProduct as $item): ?>

                            <div class="product">

                                <img src="<?= $item['thumbnail'] ?>" alt="">

                                <div>
                                    <h4><?= $item['name'] ?></h4>
                                    <span><?= $item['total_sold'] ?> sold</span>
                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="product">
                            <div>
                                <h4>No sales data.</h4>
                            </div>
                        </div>

                    <?php endif; ?>

                </div>

            </div>
            <div class="chart-year">

                <div class="box-title">
                    <h3>Revenue (12 Months)</h3>
                </div>

                <canvas id="chartYear"></canvas>

            </div>
            <!-- Content -->
            <div class="dashboard-content">

                <!-- Orders -->
                <div class="table-box">

                    <div class="box-title">
                        <h3>Latest Orders</h3>
                    </div>

                    <table>

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Product</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>View</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (empty($listLatestOrder)): ?>

                                <tr>
                                    <td colspan="5" style="text-align:center;">
                                        No orders
                                    </td>
                                </tr>

                            <?php endif; ?>

                            <?php foreach ($listLatestOrder as $item): ?>

                                <tr>

                                    <td><?= $item['id'] ?></td>

                                    <td style=" max-width: 200px;
    width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;"><?= $item['fullname'] ?></td>

                                    <td style=" max-width: 200px;
    width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;"><?= $item['product_name'] ?></td>

                                    <td><?= number_format($item['total']) ?>đ</td>

                                    <td>

                                        <?php
                                        $status = (int)$item['status'];

                                        if ($status == 1) {
                                            echo '<span class="status pending">Pending</span>';
                                        } elseif ($status == 2) {
                                            echo '<span class="status success">Processing</span>';
                                        } elseif ($status == 3) {
                                            echo '<span class="status success">Shipping</span>';
                                        } elseif ($status == 4) {
                                            echo '<span class="status success">Completed</span>';
                                        } elseif ($status == 5) {
                                            echo '<span class="status cancel">Cancelled</span>';
                                        }
                                        ?>

                                    </td>

                                    <td>
                                        <a href="?module=order&action=edit&id=<?= $item['id'] ?>" class="btn-view">
                                            <i class="fa-regular fa-eye"></i>
                                        </a>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>



                <!-- Low stock -->
                <div class="stock-box">
                    <div class="box-title">
                        <h3>Low Stock Products</h3>
                    </div>
                    <?php if (empty($listOutStock)): ?>
                        <div class="empty-stock">
                            <i class="fa-solid fa-circle-check"></i>
                            <p>No low stock products.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($listOutStock as $key => $item): ?>
                            <div class="stock-item">
                                <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=120">
                                <div>
                                    <h4><?= $item['name']; ?></h4>
                                    <span>Only <b><?= $item['stock'] ?></b> left</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>

        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels7 = <?= json_encode($labels7) ?>;
    const revenues7 = <?= json_encode($revenues7) ?>;

    new Chart(document.getElementById('chart7day'), {
        type: 'line',
        data: {
            labels: labels7,
            datasets: [{
                label: 'Revenue (VND)',
                data: revenues7
            }]
        }
    });



    const labelsYear = <?= json_encode($labelsYear) ?>;
    const revenuesYear = <?= json_encode($data) ?>;

    new Chart(document.getElementById('chartYear'), {
        type: 'bar',
        data: {
            labels: labelsYear,
            datasets: [{
                label: 'Revenue (VND)',
                data: revenuesYear,
                backgroundColor: '#1cc88a'
            }]
        }
    });
</script>

<?php
layout('dashboard/footer');
?>