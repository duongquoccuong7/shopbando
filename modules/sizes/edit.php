<?php
layout('/dashboard/header', 'Size');
// lấy dữ liệu
$getData = filterData('GET');
$size_id = $getData['id'];

$Data = getOne("SELECT * FROM sizes WHERE id=$size_id");

if (isPost()) {
    $filter = filterData();
    $errors = [];

    //validate name
    if (empty($filter['name'])) {
        $errors['name']['required'] = 'Bạn chưa nhập size ';
    }

    if (empty($errors)) {


        $dataupdate = [
            'name' => $filter['name'],
            'status' => $filter['status'],
            'updated_at' => date('Y:m:d  H:i:s'),
        ];
        $updatestatus =  update('sizes', $dataupdate, $size_id);
        if ($updatestatus) {
            setSessionFlash('msg', '');
            setSessionFlash('msg_type', 'success');
            redirect('?module=sizes&action=index');
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
                        <label for="name" class="label-input">Size</label><br>
                        <input type="text" id="name" name="name" value="<?php
                                                                        if (!empty($olddata['name'])) {
                                                                            echo  oldData($olddata, 'name');
                                                                        }   ?>" placeholder="Nhập size">
                    </div>
                    <!-- trạng thái -->
                    <div class="product-input">
                        <label for="status" class="label-input">Trạng Thái</label><br>
                        <select name="status" id="status">
                            <option value="1" <?php
                                                if (isset($olddata['status']) && $olddata['status'] == 1) {
                                                    echo 'selected';
                                                }
                                                ?>>
                                Hoạt động
                            </option>

                            <option value="0" <?php
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