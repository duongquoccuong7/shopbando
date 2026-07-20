<?php
layout('/index/header', 'Home');


$getData     = filterData('GET');
$category_id = !empty($getData['id']) ? (int)$getData['id'] : 0;
$keyword     = !empty($getData['keyword']) ? trim($getData['keyword']) : '';
$gender_id   = !empty($_GET['gender']) ? (int)$_GET['gender'] : 0;
$size_id     = !empty($_GET['size']) ? (int)$_GET['size'] : 0;
$color_id    = !empty($_GET['color']) ? (int)$_GET['color'] : 0;
$sort        = !empty($_GET['sort']) ? (int)$_GET['sort'] : 0;

$list_cou    = getAll("SELECT * FROM coupons WHERE status=1");
$list_size   = getAll("SELECT * FROM sizes");
$list_color  = getAll("SELECT * FROM colors");
$list_gender = getAll("SELECT * FROM genders");
$cate_child  = getAll("SELECT * FROM categories");


$cate_name   = getOne("SELECT * FROM categories WHERE id = $category_id");
$parent      = !empty($cate_name) ? getOne("SELECT * FROM categories WHERE id = " . (int)$cate_name['parent_id']) : [];
$cate_all_id = getAll("SELECT id FROM categories WHERE parent_id = $category_id");
function getSortUrl($sortValue)
{
    $params = $_GET;
    $params['sort'] = $sortValue;
    return '?' . http_build_query($params);
}

$sql = "
    SELECT p.*, c.name AS category_name, MIN(pv.sale_price) AS sale_price
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN product_variants pv ON pv.product_id = p.id
    WHERE 1=1
";

if (!empty($keyword)) {
    $safe_keyword = addslashes($keyword);
    $sql .= " AND (p.name LIKE '%{$safe_keyword}%' OR p.description LIKE '%{$safe_keyword}%')";
} else {
    if ($category_id > 0) {
        $sql .= " AND p.category_id = $category_id";
    }
}
if ($gender_id > 0)  $sql .= " AND p.gender_id = $gender_id";
if ($size_id > 0)    $sql .= " AND pv.size_id = $size_id";
if ($color_id > 0)   $sql .= " AND pv.color_id = $color_id";

$sql .= " GROUP BY p.id";

// Xử lý sắp xếp công bằng cho cả 2 trường hợp
$order_clause = " ORDER BY p.id DESC";
switch ($sort) {
    case 1:
        $order_clause = " ORDER BY p.name ASC";
        break;
    case 2:
        $order_clause = " ORDER BY p.created_at DESC";
        break;
    case 3:
        $order_clause = " ORDER BY MIN(pv.sale_price) DESC";
        break;
    case 4:
        $order_clause = " ORDER BY MIN(pv.sale_price) ASC";
        break;
}
$sql .= $order_clause;
$cate_pro = getAll($sql);

$cate_pro_2 = [];
if (count($cate_pro) == 0 && !empty($cate_all_id)) {
    $child_ids = array_column($cate_all_id, 'id');
    $child_ids_str = implode(',', array_map('intval', $child_ids));

    if (!empty($child_ids_str)) {
        $sql_child = "
            SELECT p.*, c.name AS category_name, MIN(pv.sale_price) AS sale_price
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_variants pv ON pv.product_id = p.id
            WHERE p.category_id IN ($child_ids_str)
            GROUP BY p.id
        " . $order_clause;

        $cate_pro_2 = getAll($sql_child);
    }
}

$display_products = (count($cate_pro) > 0) ? $cate_pro : $cate_pro_2;
?>

<!-- START MAIN VIEW -->

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

<div class="main-wrap-content main-wrap-product-list">
    <div class="drop-menu">
        <span>
            <?= !empty($cate_name) ? htmlspecialchars($cate_name['name']) . ' (' . count($display_products) . ')' : '' ?>
        </span>
        <div class="btn-sort">
            <span>Sort by</span>
            <div class="menu-sort">
                <i class="fa-solid fa-sliders"></i>
                <ul class="sort-list">
                    <li class="sort-name"><a href="<?= getSortUrl(1) ?>">Name</a></li>
                    <li class="sort-name"><a href="<?= getSortUrl(2) ?>">New</a></li>
                    <li class="sort-name"><a href="<?= getSortUrl(3) ?>">Price: High - Low</a></li>
                    <li class="sort-name"><a href="<?= getSortUrl(4) ?>">Price: Low - High</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="wrap-product-list">
        <div class="product-menu">
            <!-- Danh mục cấp 3 liên quan -->
            <div class="menu-name">
                <ul class="product-name-list">
                    <?php if (!empty($cate_name)): ?>
                        <?php foreach ($cate_child as $value): ?>
                            <?php if ($value['parent_id'] == $cate_name['parent_id']): ?>
                                <li class="name-list-item <?= $value['id'] == $category_id ? 'active' : '' ?>">
                                    <a href="<?= _HOST_URL . "/?module=index&action=index_pro_cate&id=" . $value['id']; ?>">
                                        <?= htmlspecialchars($value['name']) ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- BỘ LỌC SIDEBAR -->
            <div class="filter-box">
                <form method="GET" action="">
                    <input type="hidden" name="module" value="index">
                    <input type="hidden" name="action" value="index_pro_cate">
                    <input type="hidden" name="id" value="<?= $category_id ?>">
                    <?php if ($sort > 0): ?>
                        <input type="hidden" name="sort" value="<?= $sort ?>">
                    <?php endif; ?>

                    <ul class="filter-list">
                        <!-- Lọc theo giới tính -->
                        <li class="filter-item item-sidebar">
                            <div class="item-filter">
                                <span>Gender</span>
                                <i class="fa-solid fa-angle-right icon-right-menu"></i>
                            </div>
                            <ul class="dropdown-menu-child">
                                <?php foreach ($list_gender as $gender): ?>
                                    <li class="item-menu-child">
                                        <label>
                                            <input type="radio" name="gender" value="<?= $gender['id'] ?>"
                                                <?= ($gender_id == $gender['id']) ? 'checked' : '' ?>
                                                onchange="this.form.submit()">
                                            <?= htmlspecialchars($gender['name']) ?>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>

                        <!-- Lọc theo Size -->
                        <li class="filter-item item-sidebar">
                            <div class="item-filter">
                                <span>Size</span>
                                <i class="fa-solid fa-angle-right icon-right-menu"></i>
                            </div>
                            <ul class="dropdown-menu-child">
                                <?php foreach ($list_size as $size): ?>
                                    <li class="item-menu-child">
                                        <label>
                                            <input type="radio" name="size" value="<?= $size['id'] ?>"
                                                <?= ($size_id == $size['id']) ? 'checked' : '' ?>
                                                onchange="this.form.submit()">
                                            <?= htmlspecialchars($size['name']) ?>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>

                        <!-- Lọc theo Màu sắc -->
                        <li class="filter-item item-sidebar">
                            <div class="item-filter">
                                <span>Color</span>
                                <i class="fa-solid fa-angle-right icon-right-menu"></i>
                            </div>
                            <ul class="dropdown-menu-child">
                                <?php foreach ($list_color as $color): ?>
                                    <li class="item-menu-child">
                                        <label>
                                            <input type="radio" name="color" value="<?= $color['id'] ?>"
                                                <?= ($color_id == $color['id']) ? 'checked' : '' ?>
                                                onchange="this.form.submit()">
                                            <?= htmlspecialchars($color['name']) ?>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    </ul>
                </form>
            </div>
        </div>

        <!-- GRID HIỂN THỊ SẢN PHẨM -->
        <div class="product-case">
            <div class="product-grid">
                <?php if (!empty($display_products)): ?>
                    <?php foreach ($display_products as $pro): ?>
                        <div class="product-gird-item slide-item">
                            <a href="<?= _HOST_URL . "/?module=index&action=index_pro_detail&id=" . $pro['id']; ?>">
                                <img class="img_pro_cate" src="<?= htmlspecialchars($pro['thumbnail']); ?>"
                                    alt="<?= htmlspecialchars($pro['name']); ?>">
                                <div class="product-infor">
                                    <span class="product-name"><?= htmlspecialchars($pro['name']); ?></span>
                                    <span class="product-des"><?= htmlspecialchars($pro['category_name']); ?></span>
                                    <span class="product-price-sale">
                                        <?= number_format($pro['sale_price'], 0, '.', ',') ?>
                                        <i class="icon-pro-mon fa-solid fa-dong-sign"></i>
                                    </span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-product">No matching products found.</div>
                <?php endif; ?>
            </div>

            <!-- DANH MỤC LIÊN QUAN -->
            <div class="product-related">
                <span>Related Categories</span>
                <ul class="related-list">
                    <?php if (!empty($parent) && $parent['parent_id'] != 0): ?>
                        <?php foreach ($cate_child as $cate_re): ?>
                            <?php if ($cate_re['parent_id'] == $cate_name['parent_id']): ?>
                                <a href="<?= _HOST_URL . "/?module=index&action=index_pro_cate&id=" . $cate_re['id']; ?>">
                                    <li class="related-item"><?= htmlspecialchars($cate_re['name']) ?></li>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    // Xử lý đóng/mở menu sidebar bộ lọc
    document.querySelectorAll(".item-sidebar").forEach(item => {
        const trigger = item.querySelector(".item-filter");
        if (trigger) {
            trigger.addEventListener("click", () => {
                item.classList.toggle("active2");
            });
        }
    });

    // Cài đặt delay animation cho coupon (nếu có slide-content)
    document.querySelectorAll(".slide-content").forEach((slide, index) => {
        slide.style.animationDelay = `${index * 4}s`;
    });
</script>

<?php
layout('/index/footer');
?>