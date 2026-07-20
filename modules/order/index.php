<?php
layout('/dashboard/header', 'Order');


$filter = filterData();

$page = 1;
$offset = 0;
$perPage = 8;

$keyword = '';
$where = '';


if (isGet() && !empty($filter['keyword'])) {

    $keyword = trim($filter['keyword']);

    $where = "WHERE (
        o.id LIKE '%$keyword%'
        OR u.fullname LIKE '%$keyword%'
        OR u.email LIKE '%$keyword%'
        OR u.phone LIKE '%$keyword%'
    )";
}



$maxData = getRows("
    SELECT o.id
    FROM orders o
    LEFT JOIN users u
    ON o.user_id = u.id
    $where
");

$maxPage = ceil($maxData / $perPage);

if (!empty($filter['page'])) {
    $page = (int)$filter['page'];
}

if ($page < 1) {
    $page = 1;
}

if ($page > $maxPage && $maxPage > 0) {
    $page = $maxPage;
}

$offset = ($page - 1) * $perPage;



$listOrder = getAll("
SELECT

    o.id,
    o.total,
    o.payment_method,
    o.status,
    o.created_at,
    o.updated_at,

    u.fullname,
    u.email,
    u.phone

FROM orders o

LEFT JOIN users u
ON o.user_id = u.id

$where

ORDER BY o.created_at DESC

LIMIT $offset,$perPage
");



$query_string = '';

if (!empty($_SERVER['QUERY_STRING'])) {

    $query_string = $_SERVER['QUERY_STRING'];

    $query_string = preg_replace('/&page=\d+/', '', $query_string);
}



$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
?>

<div class="main-wrap">

    <?php layout('dashboard/sidebar'); ?>

    <div class="content-menu">

        <div class="title-list">

            <h2><?= !empty($msg) ? getMess($msg, $msg_type) : 'Order List'; ?></h2>

            <form action="" method="GET">

                <input type="hidden" name="module" value="order">
                <input type="hidden" name="action" value="index">

                <div class="search-index">

                    <input type="text" class="search-input" name="keyword" value="<?= $keyword ?>"
                        placeholder="Search order...">

                    <button class="btn-search" type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>

                </div>

            </form>
            <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=category&action=add" ?>"
                class="btn-submit"><i class="fa-solid fa-circle-plus"></i></a>

        </div>

        <table>

            <thead>
                <tr>

                    <th>ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th>#</th>
                </tr>
            </thead>

            <tbody>
            <tbody>

                <?php if (empty($listOrder)): ?>

                    <tr>
                        <td colspan="9" class="text-center">
                            No orders found.
                        </td>
                    </tr>

                <?php endif; ?>


                <?php foreach ($listOrder as $item): ?>

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
    text-overflow: ellipsis;"><?= $item['email'] ?></td>

                        <td><?= $item['phone'] ?></td>

                        <td><?= number_format($item['total']) ?>đ</td>

                        <td>

                            <?php

                            if ($item['payment_method'] == 1) {

                                echo "Cash";
                            } elseif ($item['payment_method'] == 2) {

                                echo "online";
                            }
                            ?>

                        </td>

                        <td>

                            <?php

                            switch ($item['status']) {

                                case 1:
                                    echo '<span class="status-pending">Pending</span>';
                                    break;

                                case 2:
                                    echo '<span class="status-processing">Processing</span>';
                                    break;

                                case 3:
                                    echo '<span class="status-shipping">Shipping</span>';
                                    break;

                                case 4:
                                    echo '<span class="status-active">Completed</span>';
                                    break;

                                case 5:
                                    echo '<span class="status-inactive">Cancelled</span>';
                                    break;

                                default:
                                    echo '<span>Unknown</span>';
                            }

                            ?>

                        </td>

                        <td>

                            <?= date('d/m/Y', strtotime($item['created_at'])) ?>

                        </td>

                        <td>

                            <?= !empty($item['updated_at'])
                                ? date('d-m-Y', strtotime($item['updated_at']))
                                : '---'
                            ?>

                        </td>
                        <td>
                            <div class="btn-list">
                                <a href="?module=order&action=edit&id=<?php echo $item['id'] ?>" class="btn-edit"><i
                                        class="fa-regular fa-pen-to-square"></i></a>
                                <a href="?module=order&action=delete&id=<?php echo $item['id'] ?>" class="btn-delete"
                                    onclick="return  confirm('Bạn có chắc muốn xóa khóa học?')"><i
                                        class="fa-regular fa-trash-can"></i></a>
                            </div>
                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

        <!-- Pagination -->

        <nav class="Page">

            <ul class="pagination">

                <?php if ($page > 1): ?>

                    <li class="page-item">

                        <a class="page-link" href="?<?= $query_string ?>&page=<?= $page - 1 ?>">

                            <i class="fa-solid fa-angle-left"></i>

                        </a>

                    </li>

                <?php endif; ?>

                <?php

                $start = max(1, $page - 1);

                $end = min($maxPage, $page + 1);

                ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>

                    <li class="page-item <?= $page == $i ? 'active' : '' ?>">

                        <a class="page-link" href="?<?= $query_string ?>&page=<?= $i ?>">

                            <?= $i ?>

                        </a>

                    </li>

                <?php endfor; ?>

                <?php if ($page < $maxPage): ?>

                    <li class="page-item">

                        <a class="page-link" href="?<?= $query_string ?>&page=<?= $page + 1 ?>">

                            <i class="fa-solid fa-angle-right"></i>

                        </a>

                    </li>

                <?php endif; ?>

            </ul>

        </nav>

    </div>

</div>

<?php layout('/dashboard/footer'); ?>