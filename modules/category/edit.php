<?php
layout('/dashboard/header', 'Edit Category');
// lấy danh mục cha
$listCategory = getAll("SELECT * FROM categories WHERE parent_id = 0");

// lấy dữ liệu
$getData = filterData('GET');
$category_id = $getData['id'];

$Data = getOne("SELECT * FROM categories WHERE id=$category_id");


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
        $errors['name']['required'] = 'Name category is required';
    }

    if (empty($errors)) {
        //insert data
        $dataupdate = [
            'name' => $filter['name'],
            'description' => $filter['description'],
            'slug' => $filter['slug'],
            'sort_order' => $filter['sort_order'],
            'parent_id' => !empty($filter['parent_id']) ? $filter['parent_id'] : 0,
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
        $updatestatus =  update('categories', $dataupdate, $category_id);
        if ($updatestatus) {
            setSessionFlash('msg', 'Category updated successfully');
            setSessionFlash('msg_type', 'green');
            redirect('?module=category&action=index');
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
    layout('/dashboard/sidebar');
    ?>
    <!-- Thêm danh mục sản phẩm -->
    <div class="content-menu">
        <div class="title-list">
            <h2><?php echo  !empty($msg) ? getMess($msg, $msg_type) : 'Edit Category' ?></h2>
            <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=category&index" ?>"
                class="btn-submit"><i class="fa-solid fa-backward"></i></a>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="add-product">
                <!-- product left -->
                <div class="add-product-left">
                    <!-- tên danh mục -->
                    <div class="product-input">
                        <label for="name" class="label-input">Name</label><br>
                        <input type="text" id="name" value="<?php
                                                            if (!empty($olddata['name'])) {
                                                                echo  oldData($olddata, 'name');
                                                            }   ?>" name="name" placeholder="Name category ...">
                    </div>
                    <!-- mô tả -->
                    <div class="product-input"><label for="description" class="label-input">Description</label><br>
                        <textarea class="text-edit" id="editor"
                            style="width:100% ; height:150px;resize:none; font-size:1.4rem; padding:10px" type="text"
                            id="description" name="description" placeholder="Description ..."><?php
                                                                                                if (!empty($olddata['description'])) {
                                                                                                    echo  oldData($olddata, 'description');
                                                                                                }   ?></textarea>
                    </div>
                </div>
                <!-- product right -->
                <div class="add-product-left">
                    <!-- tên danh mục -->
                    <div class="product-input">
                        <label for="slug" class="label-input">Slug </label><br>
                        <input type="text" id="slug" value="<?php
                                                            if (!empty($olddata['slug'])) {
                                                                echo  oldData($olddata, 'slug');
                                                            }   ?>" name="slug" placeholder="Slug ...">
                    </div>
                    <!-- sort order -->
                    <div class="product-input">
                        <label for="sort_order" class="label-input">Sort Order</label><br>
                        <select class="edit-select" name="sort_order" id="sort_order">
                            <option value="1" <?php
                                                if (isset($olddata['sort_order']) && $olddata['sort_order'] == 1) {
                                                    echo 'selected';
                                                }
                                                ?>>
                                Category
                            </option>
                            <option value="2" <?php
                                                if (isset($olddata['sort_order']) && $olddata['sort_order'] == 2) {
                                                    echo 'selected';
                                                }
                                                ?>>
                                Featured
                            </option>
                            <option value="3" <?php
                                                if (isset($olddata['sort_order']) && $olddata['sort_order'] == 3) {
                                                    echo 'selected';
                                                }
                                                ?>>
                                Best Seller
                            </option>
                            <option value="4" <?php
                                                if (isset($olddata['sort_order']) && $olddata['sort_order'] == 4) {
                                                    echo 'selected';
                                                }
                                                ?>>
                                Shop by Sport
                            </option>
                        </select>
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
                    <!-- danh mục cha -->
                    <div class="product-input">
                        <label for="parent_id" class="label-input">
                            Parent Category
                        </label><br>
                        <select class="edit-select" name="parent_id" id="parent_id">
                            <option value="0">
                                Parent Category
                            </option>
                            <?php
                            $getCate = getAll("SELECT * FROM categories WHERE id != $category_id");

                            foreach ($getCate as $item):
                            ?>
                                <option value=" <?php echo  $item['id']; ?>" <?php
                                                                                if (
                                                                                    !empty($olddata['parent_id']) &&
                                                                                    $olddata['parent_id'] == $item['id']
                                                                                ) {
                                                                                    echo 'selected';
                                                                                }
                                                                                ?>>
                                    <?php echo  $item['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- giá bán -->
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