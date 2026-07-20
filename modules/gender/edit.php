<?php
layout('/dashboard/header', 'Edit Gender');
$getData = filterData('GET');
$gen_id = $getData['id'];
$Data = getOne("SELECT * FROM genders WHERE id = $gen_id");

if (isPost()) {
    $filter = filterData();
    $errors = [];

    //validate name
    if (empty($filter['name'])) {
        $errors['name']['required'] = 'Size brand is required';
    }
    if (empty($errors)) {

        //insert data
        $dataupdate = [
            'name' => $filter['name'],
            'updated_at' => date('Y:m:d  H:i:s'),
            'status' => $filter['status']
        ];
        $insertstatus =  update('genders', $dataupdate, $gen_id);
        if ($insertstatus) {
            setSessionFlash('msg', 'Gender updated successfully');
            setSessionFlash('msg_type', 'green');
            redirect('?module=gender&action=index');
        } else {
            setSessionFlash('msg', 'Failed to updated gender');
            setSessionFlash('msg_type', 'red');
        }
    } else {
        setSessionFlash('msg', 'Failed to updated gender. Please check your input and try again');
        setSessionFlash('msg_type', 'red');
        setSessionFlash('old_data', $filter);
        setSessionFlash('errors', $errors);
    }
}
$olddata = getSessionFlash('old_data');
if (!empty($Data)) {
    $olddata = $Data;
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
            <h2><?php echo  !empty($msg) ? getMess($msg, $msg_type) : 'Edit gender' ?></h2>
            <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=gender&index" ?>"
                class="btn-submit"><i class="fa-solid fa-backward"></i></a>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="add-product">
                <!-- product left -->
                <div class="add-product-left" style="margin-bottom: 0 !important;">
                    <!-- tên danh mục -->
                    <div class="product-input">
                        <label for="name"
                            class="label-input"><?php echo !empty($errors) ? formError($errors, 'name') : 'Name'; ?></label><br></label><br>
                        <input type="text" id="name" value="<?php
                                                            if (!empty($olddata['name'])) {
                                                                echo  oldData($olddata, 'name');
                                                            }   ?>" name="name" placeholder="Gender ...">
                    </div>
                    <!-- trạng thái -->
                    <div class="product-input">
                        <label for="status" class="label-input">Trạng Thái</label><br>
                        <select class="edit-select" name="status" id="status">
                            <option value="1" <?php
                                                if (isset($olddata['status']) && $olddata['status'] == 1) {
                                                    echo 'selected';
                                                }
                                                ?>>
                                Active
                            </option>

                            <option value="0" <?php
                                                if (isset($olddata['status']) && $olddata['status'] == 0) {
                                                    echo 'selected';
                                                }
                                                ?>>
                                Inactive
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-submit">
                Submit
            </button>
        </form>
    </div>
    <!-- kết thúc -->
</div>
<!-- end main -->
<?php
layout('/dashboard/footer');
?>