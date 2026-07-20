<?php
layout('/dashboard/header', 'Thêm size');
$getdata = getAll('SELECT * FROM sizes');
if (isPost()) {
    $filter = filterData();
    $errors = [];

    //validate name
    if (empty($filter['name'])) {
        $errors['name']['required'] = 'Bạn chưa nhập size giày';
    }

    if (empty($errors)) {

        //insert data
        $datainsert = [
            'name' => $filter['name'],
            'created_at' => date('Y:m:d  H:i:s'),
            'status' => 1
        ];
        $insertstatus =  insert('sizes', $datainsert);
        if ($insertstatus) {
            setSessionFlash('msg', '');
            setSessionFlash('msg_type', 'success');
            redirect('?module=sizes&action=add');
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
                        <label for="name" class="label-input">Size</label><br>
                        <input type="text" id="name" name="name" placeholder="Nhập size">
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