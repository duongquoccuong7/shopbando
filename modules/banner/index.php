<?php
layout('/dashboard/header', 'Banners');

// Pagination setup
$filter = filterData();
$offset = 0;
$page = 1;
$chuoiwhere = '';
$keyword = '';
$cate = '0';

// Handle Search
if (isGet()) {
    if (isset($filter['keyword'])) {
        $keyword = trim($filter['keyword']);
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
        // Fixed: search by 'title' instead of 'name'
        $chuoiwhere .= "(b.title LIKE '%$keyword%' OR b.description LIKE '%$keyword%')";
    }
}

// Handle Pagination
$maxData = getRows("
    SELECT b.id 
    FROM banners b
    $chuoiwhere
");

$perPage = 10; // Number of items per page
$maxPage = ceil($maxData / $perPage);

if (isset($filter['page'])) {
    $page = (int)$filter['page'];
}
if ($page > $maxPage || $page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $perPage;

$getDetail = getAll("
    SELECT
        b.*,
        p.name AS product_name,
        c.name AS category_name,
        br.name AS brand_name
    FROM banners b
    LEFT JOIN products p
        ON b.product_id = p.id
    LEFT JOIN categories c
        ON b.category_id = c.id
    LEFT JOIN brands br
        ON b.brand_id = br.id
    $chuoiwhere
    ORDER BY b.created_at DESC
    LIMIT $offset, $perPage
");

// Query String handling for pagination links
$query_string = $_SERVER['QUERY_STRING'] ?? '';
if (!empty($query_string)) {
    $query_string = str_replace('&page=' . $page, '', $query_string);
}

$msg      = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
?>

<!-- START MAIN VIEW -->
<div class="main-wrap">
    <?php layout('/dashboard/sidebar'); ?>

    <div class="content-menu">
        <div class="title-list">
            <h2><?php echo !empty($msg) ? getMess($msg, $msg_type) : 'Banners'; ?></h2>

            <!-- Search Form -->
            <form action="" method="GET">
                <input type="hidden" name="module" value="banner">
                <input type="hidden" name="action" value="index">
                <div class="search-index">
                    <input type="text" value="<?php echo !empty($keyword) ? htmlspecialchars($keyword) : ''; ?>"
                        class="search-input" name="keyword" placeholder="Search banner...">
                    <button class="btn-search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>

            <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=banner&action=add"; ?>"
                class="btn-submit">Add New Banner</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Thumbnail</th>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Brand</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($getDetail)): ?>
                    <tr>
                        <td colspan="11" class="text-center" style="font-weight: 500;">No banners found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($getDetail as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td class="name_"><?php echo htmlspecialchars($item['title'] ?? ''); ?></td>
                            <td class="description"><?php echo htmlspecialchars($item['description'] ?? ''); ?></td>
                            <td>
                                <img width="60px" height="30px" src="<?php echo htmlspecialchars($item['thumbnail']); ?>"
                                    alt="No Image">
                            </td>
                            <td><?php echo !empty($item['category_name']) ? htmlspecialchars($item['category_name']) : 'N/A'; ?>
                            </td>
                            <td><?php echo !empty($item['product_name']) ? htmlspecialchars($item['product_name']) : 'N/A'; ?>
                            </td>
                            <td><?php echo !empty($item['brand_name']) ? htmlspecialchars($item['brand_name']) : 'N/A'; ?></td>
                            <td>
                                <?php if ($item['status'] == 1): ?>
                                    <span class="status-active">Active</span>
                                <?php else: ?>
                                    <span class="status-inactive">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $item['created_at']; ?></td>
                            <td><?php echo !empty($item['updated_at']) ? $item['updated_at'] : 'N/A'; ?></td>
                            <td>
                                <div class="btn-list">
                                    <a href="?module=banner&action=edit&id=<?php echo $item['id']; ?>" class="btn-edit">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <a href="?module=banner&action=delete&id=<?php echo $item['id']; ?>" class="btn-delete"
                                        onclick="return confirm('Are you sure you want to delete this banner?')">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- PAGINATION -->
        <?php if ($maxPage > 1): ?>
            <nav class="Page" aria-label="Page navigation">
                <ul class="pagination">
                    <!-- Previous Button -->
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo $query_string; ?>&page=<?php echo $page - 1; ?>">
                                <i class="fa-solid fa-angle-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- Calculate Start & End Pages -->
                    <?php
                    $start = $page - 1;
                    if ($start < 1) {
                        $start = 1;
                    }

                    if ($start > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo $query_string; ?>&page=1">1</a>
                        </li>
                        <?php if ($start > 2): ?>
                            <li class="page-item"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif;

                    $end = $page + 1;
                    if ($end > $maxPage) {
                        $end = $maxPage;
                    }

                    for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                            <a class="page-link"
                                href="?<?php echo $query_string; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor;

                    if ($end < $maxPage): ?>
                        <?php if ($end < $maxPage - 1): ?>
                            <li class="page-item"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link"
                                href="?<?php echo $query_string; ?>&page=<?php echo $maxPage; ?>"><?php echo $maxPage; ?></a>
                        </li>
                    <?php endif; ?>

                    <!-- Next Button -->
                    <?php if ($page < $maxPage): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo $query_string; ?>&page=<?php echo $page + 1; ?>">
                                <i class="fa-solid fa-angle-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php layout('/dashboard/footer'); ?>