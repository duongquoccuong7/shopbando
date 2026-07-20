<?php
layout('/dashboard/header', 'List Product');
//phân trang
$filter = filterData();
$offset = 0;
$page = 1;
$chuoiwhere = '';
$keyword = '';
$cate = '0';
//tìm kiếm
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
        $chuoiwhere .= "(products.name LIKE '%$keyword%' OR products.description LIKE '%$keyword%')";
    }
}

//Xử lý phân trang
$maxData = getRows("
    SELECT id 
    FROM products
    $chuoiwhere
");
$perPage = 7; //Số dòng dữ liệu 1 trang
$maxPage = ceil($maxData / $perPage);
//get Page
if (isset($filter['page'])) {
    $page = $filter['page'];
}
if ($page > $maxPage || $page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $perPage;
$getDetail = getAll("
    SELECT 
        products.*,
        categories.name AS category_name,
        brands.name AS brand_name,
         colors.name AS color_name

    FROM products

    LEFT JOIN categories 
        ON products.category_id = categories.id

    LEFT JOIN brands 
        ON products.brand_id = brands.id
        
        LEFT JOIN colors 
        ON products.thumbnail_color_id = colors.id

    $chuoiwhere

    ORDER BY products.created_at DESC

    LIMIT $offset, $perPage
");
//Xử lý query 
$_SERVER['QUERY_STRING'];
if (!empty($_SERVER['QUERY_STRING'])) {
    $query_string = $_SERVER['QUERY_STRING'];
    $query_string = str_replace('&page=' . $page, '', $query_string);
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
?>
<!-- start main -->
<div class="main-wrap">
    <?php
    layout('/dashboard/sidebar'); ?>
    <!-- Thêm danh mục sản phẩm -->
    <div class="content-menu">
        <div class="title-list">
            <h2><?php echo  !empty($msg) ? getMess($msg, $msg_type) : 'Product' ?></h2>
            <form action="" method="GET">
                <input type="hidden" name="module" value="product">
                <input type="hidden" name="action" value="index">
                <div class="search-index">
                    <input type="text" value="<?php echo !empty($keyword) ? $keyword : false; ?>" class="search-input"
                        name="keyword" placeholder="Search ...">
                    <button class=" btn-search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
            <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=product&action=add" ?>"
                class="btn-submit"><i class="fa-solid fa-circle-plus"></i></a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Thumbnail</th>
                    <th>Color</th>
                    <th>Brand</th>
                    <th>Danh Mục</th>
                    <th>Category</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th>#</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($getDetail == null): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="font-weight: 500;"><?php echo "Không có sản phẩm"; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($getDetail as $key => $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td class="name_"><?php echo $item['name']; ?></td>
                        <td class="description"><?php echo $item['description']; ?></td>
                        <td><img width="60px" height="30px" src="<?php echo $item['thumbnail']; ?>" alt="Không có ảnh">
                        </td>
                        <td><?php echo $item['color_name']; ?></td>
                        <td><?php
                            echo $item['brand_name'];
                            ?></td>
                        <td><?php
                            echo $item['category_name'];
                            ?></td>

                        <td>
                            <?php if ($item['status'] == 1): ?>
                                <span class="status-active">Active</span>
                            <?php elseif ($item['status'] == 0): ?>
                                <span class="status-inactive">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d-m-Y', strtotime($item['created_at'])) ?></td>
                        <td>
                            <?php echo date('d-m-Y', strtotime($item['updated_at'])) ?>
                        </td>
                        <td>
                            <div class="btn-list">
                                <a href="?module=product&action=edit&id=<?php echo $item['id'] ?>" class="btn-edit"><i
                                        class="fa-regular fa-pen-to-square"></i></a>
                                <a href="?module=product&action=delete&id=<?php echo $item['id'] ?>" class="btn-delete"
                                    onclick="return  confirm('Bạn có chắc xóa sản phẩm?')"><i
                                        class="fa-regular fa-trash-can"></i></a>
                            </div>
                        </td>
                    </tr>
                <?Php endforeach; ?>
            </tbody>
        </table>
        <!-- panigate -->
        <nav class="Page" aria-label="Page navigation example">
            <ul class="pagination">
                <!-- Xử lý nút trước -->
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link"
                            href="?<?php echo $query_string ?>&page=<?php echo $page - 1 ?>"><i
                                class="fa-solid fa-angle-left"></i></a></li>
                <?php endif; ?>
                <!-- Tính vị trí bắt đầu  -->
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
                    <!-- Xử lý nút sau -->
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
    <!-- kết thúc -->
</div>
<!-- end main -->

<?php
layout('/dashboard/footer');
?>