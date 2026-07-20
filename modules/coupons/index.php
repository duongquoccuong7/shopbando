<?php
layout('/dashboard/header', 'Coupon List');
?>
<!-- start main -->
<div class="main-wrap">
    <?php
    layout('/dashboard/sidebar');

    // Pagination
    $filter = filterData();
    $offset = 0;
    $page = 1;
    $chuoiwhere = '';
    $keyword = '';
    $cate = '0';

    // Search
    if (isGet()) {
        if (isset($filter['keyword'])) {
            $keyword = $filter['keyword'];
        }
        if (isset($filter['cate'])) {
            $cate = $filter['cate'];
        }
        if (!empty($keyword)) {
            if (strpos($chuoiwhere, 'WHERE') === false) {
                $chuoiwhere .= ' WHERE ';
            } else {
                $chuoiwhere .= ' AND ';
            }
            $chuoiwhere .= "(coupons.name LIKE '%$keyword%' OR coupons.description LIKE '%$keyword%')";
        }
    }

    // Pagination Process
    $maxData = getRows("
        SELECT id 
        FROM coupons
        $chuoiwhere
    ");
    $perPage = 10; // Number of items per page
    $maxPage = ceil($maxData / $perPage);

    // Get Page
    if (isset($filter['page'])) {
        $page = $filter['page'];
    }
    if ($page > $maxPage || $page < 1) {
        $page = 1;
    }
    $offset = ($page - 1) * $perPage;
    $getDetail = getAll(" SELECT * FROM coupons $chuoiwhere ORDER BY created_at DESC LIMIT $offset, $perPage");

    // Query string process
    $_SERVER['QUERY_STRING'];
    if (!empty($_SERVER['QUERY_STRING'])) {
        $query_string = $_SERVER['QUERY_STRING'];
        $query_string = str_replace('&page=' . $page, '', $query_string);
    }

    ?>
    <!-- Content Menu -->
    <div class="content-menu">
        <div class="title-list">
            <h2>Coupon List</h2>
            <form action="" method="GET">
                <input type="hidden" name="module" value="coupons">
                <input type="hidden" name="action" value="index">
                <div class="search-index">
                    <input type="text" value="<?php echo !empty($keyword) ? $keyword : false; ?>" class="search-input"
                        name="keyword" placeholder="Search...">
                    <button class="btn-search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
            <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=coupons&action=add" ?>"
                class="btn-submit">Add New Coupon</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Discount Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($getDetail == null): ?>
                <tr>
                    <td colspan="9" class="text-center" style="font-weight: 500;">
                        <?php echo "No coupons found"; ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php foreach ($getDetail as $key => $item): ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo $item['name']; ?></td>
                    <td><?php echo $item['code']; ?></td>
                    <td><?php if ($item['type'] == 0) {
                                echo 'Fixed Amount';
                            } else if ($item['type'] == 1) {
                                echo 'Percentage (%)';
                            } ?></td>
                    <td><?php echo date('Y-m-d', strtotime($item['start'])); ?></td>
                    <td><?php echo date('Y-m-d', strtotime($item['end'])); ?></td>
                    <td>
                        <?php if ($item['status'] == 1): ?>
                        <span class="status-active">Active</span>
                        <?php elseif ($item['status'] == 0): ?>
                        <span class="status-inactive">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $item['created_at']; ?></td>
                    <td>
                        <div class="btn-list">
                            <a href="?module=coupons&action=edit&id=<?php echo $item['id'] ?>" class="btn-edit"><i
                                    class="fa-regular fa-pen-to-square"></i></a>
                            <a href="?module=coupons&action=delete&id=<?php echo $item['id'] ?>" class="btn-delete"
                                onclick="return confirm('Are you sure you want to delete this coupon?')"><i
                                    class="fa-regular fa-trash-can"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav class="Page" aria-label="Page navigation example">
            <ul class="pagination">
                <!-- Previous Button -->
                <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link"
                        href="?<?php echo $query_string ?>&page=<?php echo $page - 1 ?>"><i
                            class="fa-solid fa-angle-left"></i></a></li>
                <?php endif; ?>

                <!-- Start Page Calculation -->
                <?php
                $start = $page - 1;
                if ($start < 1) {
                    $start = 1;
                } ?>
                <?php if ($start > 1): ?>
                <li class="page-item"><a class="page-link"
                        href="?<?php echo $query_string ?>&page=<?php echo $page - 1 ?>">...</a></li>
                <?php endif;
                $end = $page + 1;
                if ($end > $maxPage) {
                    $end = $maxPage;
                } ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                <li class="page-item <?php echo ($page == $i) ? 'active' : false ?>"><a class="page-link"
                        href="?<?php echo $query_string ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                <?php
                endfor;
                if ($end < $maxPage): ?>
                <li class="page-item"><a class="page-link"
                        href="?<?php echo $query_string ?>&page=<?php echo $page + 1 ?>">...</a></li>
                <?php endif; ?>

                <!-- Next Button -->
                <?php if ($page < $maxPage): ?>
                <li class="page-item"><a class="page-link"
                        href="?<?php echo $query_string ?>&page=<?php echo $page + 1 ?>"><i
                            class="fa-solid fa-angle-right"></i></a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <!-- end content-menu -->
</div>
<!-- end main -->

<?php
layout('/dashboard/footer');
?>