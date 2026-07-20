<?php
layout('/dashboard/header', 'Edit Category');
// lấy dữ liệu
$getData = filterData('GET');
$brand_id = $getData['id'];

$Data = getOne("SELECT * FROM brands WHERE id=$brand_id");
if (isPost()) {
    $filter = filterData();
    $errors = [];
    //validate description 
    if (empty($filter['description'])) {
        $errors['description']['required'] = 'Description is required';
    }
    //validate slug
    if (empty($filter['slug'])) {
        $errors['slug']['required'] = 'Slug is required';
    }
    //validate name
    if (empty($filter['name'])) {
        $errors['name']['required'] = 'Name brand is required';
    }

    if (empty($errors)) {
        //insert data
        $dataupdate = [
            'name' => $filter['name'],
            'description' => $filter['description'],
            'slug' => $filter['slug'],
            'country' => $filter['country'],
            'status' => $filter['status'],
            'updated_at' => date('Y:m:d  H:i:s')
        ];
        if (!empty($_FILES['thumbnail']['name'])) {
            //Xử lý thumbnail upload ảnh
            $uploadDir = './templates/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 077, true); //Tạo mới thư mục upload nếu chưa có
            }
            $fileName = basename($_FILES['thumbnail']['name']);
            $targetFile =  $uploadDir . time() . '-' . $fileName;
            // xóa ảnh cũ
            if (
                !empty($Data['thumbnail']) &&
                file_exists($Data['thumbnail'])
            ) {
                unlink($Data['thumbnail']);
            }
            $thumb = '';
            $checkMove = move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetFile);
            if ($checkMove) {
                $thumb = $targetFile;
            }
            $dataupdate['thumbnail'] = $thumb;
        }
        $updatestatus =  update('brands', $dataupdate, $brand_id);
        if ($updatestatus) {
            setSessionFlash('msg', 'Category updated successfully');
            setSessionFlash('msg_type', 'success');
            redirect('?module=brands&action=index');
        } else {
            setSessionFlash('msg', 'Failed to updated category');
            setSessionFlash('msg_type', 'red');
        }
    } else {
        setSessionFlash('msg', 'Failed to updated category. Please check your input and try again');
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
    layout('/dashboard/sidebar');  ?>
    <!-- Thêm thương hiệu sản phẩm -->
    <div class="content-menu">
        <div class="title-list">
            <h2><?php echo  !empty($msg) ? getMess($msg, $msg_type) : 'Update Brand' ?></h2>
            <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=category&index" ?>"
                class="btn-submit"><i class="fa-solid fa-backward"></i></a>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="add-product">
                <!-- product left -->
                <div class="add-product-left">
                    <!-- tên thương hiệu -->
                    <div class="product-input">
                        <label for="name" class="label-input">Name</label><br>
                        <input type="text" id="name" name="name" value="<?php
                                                                        if (!empty($olddata['name'])) {
                                                                            echo  oldData($olddata, 'name');
                                                                        }   ?>" placeholder=" Name ...">
                    </div>
                    <!-- mô tả -->
                    <div class="product-input"><label for="description" class="label-input">Description</label><br>
                        <textarea class="text-edit"
                            style="width:100% ; height:150px;resize:none; font-size:1.4rem; padding:10px" type="text"
                            id="description" name="description" placeholder="Description ..."><?php
                                                                                                if (!empty($olddata['description'])) {
                                                                                                    echo  oldData($olddata, 'description');
                                                                                                }   ?></textarea>
                    </div>
                </div>
                <!-- product right -->
                <div class="add-product-right">
                    <!-- tên thương hiệu -->
                    <div class="product-input">
                        <label for="slug" class="label-input">Slug</label><br>
                        <input type="text" id="slug" name="slug" value="<?php
                                                                        if (!empty($olddata['slug'])) {
                                                                            echo  oldData($olddata, 'slug');
                                                                        }   ?>" placeholder="slug ..">
                    </div>

                    <div class="product-input">
                        <label for="country" class="label-input">Country </label><br>
                        <input type="text" id="country" name="country" value="<?php
                                                                                if (!empty($olddata['country'])) {
                                                                                    echo  oldData($olddata, 'country');
                                                                                }   ?>" placeholder="Country ...  ">
                    </div>
                    <!-- Status -->
                    <div class="product-input">
                        <label for="status" class="label-input">Status</label><br>
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
                    <!-- ảnh -->
                    <div class="product-input"><label for="thumbnail">Thumbnail</label>
                        <input id="thumbnail" name="thumbnail" type="file" class="form-control">
                        <img id="previewImage" class="preview-image p-3"
                            src="<?php echo !empty($olddata['thumbnail']) ? $olddata['thumbnail'] : false ?>"
                            style="width:150px;" alt="">
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
<script>
    const thumbInput = document.getElementById('thumbnail');
    const previewImg = document.getElementById('previewImage');
    thumbInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.setAttribute('src', e.target.result);
                previewImg.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            previewImg.style.display = 'none';
        }
    });
</script>
<script>
    //Hàm giúp chuyển text thành slug
    function createSlug(str) {
        return str.toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }
    document.getElementById('name').addEventListener('input', function() {
        const getValue = this.value;
        document.getElementById('slug').value = createSlug(getValue);

    });
</script>
<?php
layout('/dashboard/footer');
?>