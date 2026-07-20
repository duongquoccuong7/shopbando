<?php
layout('/dashboard/header', 'Danh sách màu');
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
        $chuoiwhere .= "(colors.name LIKE '%$keyword%')";
    }
}

//Xử lý phân trang
$maxData = getRows("
    SELECT id 
    FROM colors
    $chuoiwhere
");
$perPage = 12; //Số dòng dữ liệu 1 trang
$maxPage = ceil($maxData / $perPage);
//get Page
if (isset($filter['page'])) {
    $page = $filter['page'];
}
if ($page > $maxPage || $page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $perPage;
$getDetail = getAll(" SELECT * FROM  colors  $chuoiwhere ORDER BY created_at DESC LIMIT $offset, $perPage");
//Xử lý query 
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
    <!-- Thêm danh mục sản phẩm -->
    <div class="content-menu">
        <div class="title-list">
            <h2>Danh sách size giày</h2>
            <form action="" method="GET">
                <input type="hidden" name="module" value="color">
                <input type="hidden" name="action" value="index">
                <div class="search-index">
                    <input type="text" value="<?php echo !empty($keyword) ? $keyword : false; ?>" class="search-input"
                        name="keyword" placeholder="Tìm kiếm ...">
                    <button class=" btn-search" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
            <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=color&action=add" ?>"
                class="btn-submit">Thêm mới
                màu</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Size</th>
                    <th>Mã màu</th>
                    <th>Trạng Thái</th>
                    <th>Ngày tạo</th>
                    <th>Ngày cập nhật</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($getDetail == null): ?>
                    <tr>
                        <td colspan="8" class="text-center" style="font-weight: 500;">
                            <?php echo "Không có màu trong danh sách"; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($getDetail as $key => $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo $item['name']; ?></td>
                        <td><?php echo $item['color_code']; ?></td>
                        <td>
                            <?php if ($item['status'] == 1): ?>
                                <span class="status-active">Đang hoạt động</span>
                            <?php elseif ($item['status'] == 0): ?>
                                <span class="status-inactive">Không hoạt động</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $item['created_at']; ?></td>
                        <td><?php echo $item['updated_at']; ?></td>
                        <td>
                            <div class="btn-list">
                                <a href="?module=color&action=edit&id=<?php echo $item['id'] ?>" class="btn-edit"><i
                                        class="fa-regular fa-pen-to-square"></i></a>
                                <a href="?module=color&action=delete&id=<?php echo $item['id'] ?>" class="btn-delete"
                                    onclick="return  confirm('Bạn có chắc muốn xóa?')"><i
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