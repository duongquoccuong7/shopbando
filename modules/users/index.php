<?php
layout('/dashboard/header', 'User List');
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
        $chuoiwhere .= "(users.fullname LIKE '%$keyword%')";
    }
}

// Handle pagination
$maxData = getRows("
    SELECT id 
    FROM users
    $chuoiwhere
");
$perPage = 12; // Number of items per page
$maxPage = ceil($maxData / $perPage);
// Get page
if (isset($filter['page'])) {
    $page = $filter['page'];
}
if ($page > $maxPage || $page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $perPage;
$getDetail = getAll(" SELECT * FROM users $chuoiwhere ORDER BY created_at DESC LIMIT $offset, $perPage");
// Process query string
$_SERVER['QUERY_STRING'];
if (!empty($_SERVER['QUERY_STRING'])) {
    $query_string = $_SERVER['QUERY_STRING'];
    $query_string = str_replace('&page=' . $page, '', $query_string);
}
?>
<!-- start main -->
<div class="main-wrap">
    <?php
    layout('/dashboard/sidebar');
    ?>
    <!-- Add product category -->
    <div class="content-menu">
        <?php if (!empty($msg) && !empty($msg_type)) : ?>
            <div class="anoun-mess">
                <?php getMess($msg, $msg_type); ?>
            </div>
        <?php endif; ?>
        <div class="title-list">
            <h2>User List</h2>
            <form action="" method="GET">
                <input type="hidden" name="module" value="users">
                <input type="hidden" name="action" value="index">
                <div class="search-index">
                    <input type="text" value="<?php echo !empty($keyword) ? $keyword : false; ?>" class="search-input"
                        name="keyword" placeholder="Search...">
                    <button class="btn-search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
            <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=users&action=add" ?>"
                class="btn-submit">Add New User</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Updated At</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($getDetail == null): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="font-weight: 500;"><?php echo "No users found"; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($getDetail as $key => $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo $item['fullname']; ?></td>
                        <td><?php echo $item['email']; ?></td>
                        <td><?php echo $item['phone']; ?></td>
                        <td style="color:#ce440f"><?php echo $item['role']; ?></td>
                        <td>
                            <?php if ($item['status'] == 1): ?>
                                <span class="status-inactive">Inactive</span>
                            <?php elseif ($item['status'] == 2): ?>
                                <span class="status-active">Active</span>
                            <?php elseif ($item['status'] == 3): ?>
                                <span class="status-blocked">Blocked</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $item['updated_at']; ?></td>
                        <td>
                            <div class="btn-list">
                                <a href="?module=users&action=edit&id=<?php echo $item['id'] ?>" class="btn-edit"><i
                                        class="fa-regular fa-pen-to-square"></i></a>
                                <a href="?module=users&action=delete&id=<?php echo $item['id'] ?>" class="btn-delete"
                                    onclick="return confirm('Are you sure you want to delete?')"><i
                                        class="fa-regular fa-trash-can"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- paginate -->
        <nav class="Page" aria-label="Page navigation example">
            <ul class="pagination">
                <!-- Handle previous button -->
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link"
                            href="?<?php echo $query_string ?>&page=<?php echo $page - 1 ?>"><i
                                class="fa-solid fa-angle-left"></i></a></li>
                <?php endif; ?>
                <!-- Calculate start position -->
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
                    <!-- Handle next button -->
                <?php
                endfor;
                if ($end < $maxPage): ?>
                    <li class="page-item"><a class="page-link"
                            href="?<?php echo $query_string ?>&page=<?php echo $page + 1 ?>">...</a></li>
                <?php endif; ?>
                <?php if ($page < $maxPage): ?>
                    <li class="page-item"><a class="page-link"
                            href="?<?php echo $query_string ?>&page=<?php echo $page + 1 ?>"><i
                                class="fa-solid fa-angle-right"></i></a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <!-- end -->
</div>
<!-- end main -->

<?php
layout('/dashboard/footer');
?>