<?php
layout('/dashboard/header', 'Màu');
// lấy dữ liệu
$getData = filterData('GET');
$color_id = $getData['id'];

$Data = getOne("SELECT * FROM colors WHERE id=$color_id");

if (isPost()) {
    $filter = filterData();
    $errors = [];

    //validate name
    if (empty($filter['name'])) {
        $errors['name']['required'] = 'Bạn chưa nhập tên màu ';
    }

    if (empty($errors)) {


        $dataupdate = [
            'name' => $filter['name'],
            'status' => $filter['status'],
            'color_code' => $filter['color_code'],
            'updated_at' => date('Y:m:d  H:i:s'),
        ];

        $updatestatus =  update('colors', $dataupdate, $color_id);
        if ($updatestatus) {
            setSessionFlash('msg', '');
            setSessionFlash('msg_type', 'success');
            redirect('?module=color&action=index');
        } else {
            setSessionFlash('msg', '');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('msg', 'Cập nhật không thành công, vui lòng kiểm tra lại dữ liệu');
        setSessionFlash('msg_type', 'danger');
        setSessionFlash('old_data', $filter);
        setSessionFlash('errors', $errors);
    }
}
$olddata = getSessionFlash('old_data');
if (!empty($Data)) {
    $olddata = $Data;
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
                        <input type="text" id="name" name="name" value="<?php
                                                                        if (!empty($olddata['name'])) {
                                                                            echo  oldData($olddata, 'name');
                                                                        }   ?>" placeholder="Nhập tên màu">
                    </div>
                    <!-- mã màu -->
                    <div class="product-input">
                        <label for="color_code" class="label-input">Tên màu</label><br>
                        <input type="text" id="color_code" name="color_code" value="<?php
                                                                                    if (!empty($olddata['color_code'])) {
                                                                                        echo  oldData($olddata, 'color_code');
                                                                                    }   ?>" placeholder="Nhập mã màu">
                    </div>
                    <!-- trạng thái -->
                    <div class="product-input">
                        <label for="status" class="label-input">Trạng Thái</label><br>
                        <select name="status" id="status">
                            <option style="color:green" value="1" <?php
                                                                    if (isset($olddata['status']) && $olddata['status'] == 1) {
                                                                        echo 'selected';
                                                                    }
                                                                    ?>>
                                Hoạt động
                            </option>

                            <option style="color:red" value="0" <?php
                                                                if (isset($olddata['status']) && $olddata['status'] == 0) {
                                                                    echo 'selected';
                                                                }
                                                                ?>>
                                Không hoạt động
                            </option>
                        </select>
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