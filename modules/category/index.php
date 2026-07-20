<?php
layout('/dashboard/header', 'Category');
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
        $chuoiwhere .= "(categories.name LIKE '%$keyword%' OR categories.description LIKE '%$keyword%')";
    }
}

//Xử lý phân trang
$maxData = getRows("
    SELECT id 
    FROM categories
    $chuoiwhere
");
$perPage = 15; //Số dòng dữ liệu 1 trang
$maxPage = ceil($maxData / $perPage);
//get Page
if (isset($filter['page'])) {
    $page = $filter['page'];
}
if ($page > $maxPage || $page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $perPage;
$getDetail = getAll(" SELECT * FROM  categories  $chuoiwhere ORDER BY created_at DESC LIMIT $offset, $perPage");
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
    layout('/dashboard/sidebar');


    ?>
    <!-- Thêm danh mục sản phẩm -->
    <div class="content-menu">
        <div class="title-list">
            <h2><?php echo  !empty($msg) ? getMess($msg, $msg_type) : 'Category' ?></h2>
            <form action="" method="GET">
                <input type="hidden" name="module" value="category">
                <input type="hidden" name="action" value="index">
                <div class="search-index">
                    <input type="text" value="<?php echo !empty($keyword) ? $keyword : false; ?>" class="search-input"
                        name="keyword" placeholder="Search ...">
                    <button class=" btn-search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
            <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=category&action=add" ?>"
                class="btn-submit"><i class="fa-solid fa-circle-plus"></i></a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Parent</th>
                    <th>Sort</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th>#</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($getDetail == null): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="font-weight: 500;"><?php echo "No "; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($getDetail as $key => $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td class="name_"><?php echo $item['name']; ?></td>
                        <td class="description"><?php echo $item['description']; ?></td>
                        </td>
                        <td><?php
                            if ($item['parent_id'] == 0) {

                                echo 'Danh mục cha';
                            } else {

                                $parent = getOne(
                                    "SELECT name FROM categories WHERE id = " . $item['parent_id']
                                );

                                echo !empty($parent['name']) ? $parent['name'] : 'Không tồn tại';
                            }
                            ?></td>
                        <td>
                            <?php if ($item['sort_order'] == 1): ?>
                                <span class="">Category</span>
                            <?php elseif ($item['sort_order'] == 2): ?>
                                <span class="">Featured</span>
                            <?php elseif ($item['sort_order'] == 3): ?>
                                <span class="">Best Seller</span>
                            <?php elseif ($item['sort_order'] == 4): ?>
                                <span class="">Shop by Sport</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($item['status'] == 1): ?>
                                <span class="status-active">Active</span>
                            <?php elseif ($item['status'] == 0): ?>
                                <span class="status-inactive">Inactive</span>
                            <?php endif; ?>
                        </td>

                        <td><?php echo $item['created_at']; ?></td>
                        <td><?php echo $item['updated_at']; ?></td>
                        <td>
                            <div class="btn-list">
                                <a href="?module=category&action=edit&id=<?php echo $item['id'] ?>" class="btn-edit"><i
                                        class="fa-regular fa-pen-to-square"></i></a>
                                <a href="?module=category&action=delete&id=<?php echo $item['id'] ?>" class="btn-delete"
                                    onclick="return  confirm('Bạn có chắc muốn xóa khóa học?')"><i
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