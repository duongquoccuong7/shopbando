<?php
layout('/dashboard/header', 'Add Gender');
$getdata = getAll('SELECT * FROM genders');
if (isPost()) {
    $filter = filterData();
    $errors = [];

    //validate name
    if (empty($filter['name'])) {
        $errors['name']['required'] = 'Size brand is required';
    }
    if (empty($errors)) {

        //insert data
        $datainsert = [
            'name' => $filter['name'],
            'created_at' => date('Y:m:d  H:i:s'),
            'status' => 1
        ];
        $insertstatus =  insert('genders', $datainsert);
        if ($insertstatus) {
            setSessionFlash('msg', 'Gender added successfully');
            setSessionFlash('msg_type', 'green');
            redirect('?module=gender&action=add');
        } else {
            setSessionFlash('msg', 'Failed to add gender');
            setSessionFlash('msg_type', 'red');
        }
    } else {
        setSessionFlash('msg', 'Failed to add gender. Please check your input and try again');
        setSessionFlash('msg_type', 'red');
        setSessionFlash('old_data', $filter);
        setSessionFlash('errors', $errors);
    }
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
            <h2><?php echo  !empty($msg) ? getMess($msg, $msg_type) : 'Add gender' ?></h2>
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
                        <input type="text" id="name" name="name" placeholder="Gender ...">
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