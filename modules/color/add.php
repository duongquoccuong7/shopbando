<?php
layout('/dashboard/header', 'Thêm màu');
$getdata = getAll('SELECT * FROM colors');
if (isPost()) {
    $filter = filterData();
    $errors = [];

    //validate name
    if (empty($filter['name'])) {
        $errors['name']['required'] = 'Bạn chưa nhập màu';
    }
    if (empty($filter['color_code'])) {
        $errors['color_code']['required'] = 'Bạn chưa mã màu';
    }

    if (empty($errors)) {

        //insert data
        $datainsert = [
            'name' => $filter['name'],
            'color_code' => $filter['color_code'],
            'created_at' => date('Y:m:d  H:i:s'),
            'status' => 1
        ];
        $insertstatus =  insert('colors', $datainsert);
        if ($insertstatus) {
            setSessionFlash('msg', '');
            setSessionFlash('msg_type', 'success');
            redirect('?module=color&action=add');
        } else {
            setSessionFlash('msg', '');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('msg', 'Thêm  không thành công, vui lòng kiểm tra lại dữ liệu');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('old_data', $filter);
        setSessionFlash('errors', $errors);
    }
}
?>
<!-- start main -->
<div class="main-wrap">
    <?php
    layout('/dashboard/sidebar');


    ?>
    <!-- Thêm danh mục sản phẩm -->
    <div class="content-menu">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="add-product">
                <!-- product left -->
                <div class="add-product-left" style="margin-bottom: 0 !important;">
                    <!-- tên danh mục -->
                    <div class="product-input">
                        <label for="name" class="label-input">Tên màu</label><br>
                        <input type="text" id="name" name="name" placeholder="Nhập tên màu ...">
                    </div>
                    <!-- mã màu -->
                    <div class="product-input">
                        <label for="color_code" class="label-input">Mã màu</label><br>
                        <input type="text" id="color_code" name="color_code" placeholder="Nhập mã màu ...">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-submit">
                Gửi
            </button>
        </form>
    </div>
    <!-- kết thúc -->
</div>
<!-- end main -->
<?php
layout('/dashboard/footer');
?>