<?php
layout('/dashboard/header', 'Add banner');
// Get parent banner data
$listCategory = getAll("SELECT * FROM categories");
$listbrand = getAll("SELECT * FROM brands");
$listproduct = getAll("SELECT * FROM products");
// Get request data
$getData = filterData('GET');
$banner_id = $getData['id'];

$Data = getOne("SELECT * FROM banners WHERE id=$banner_id");
if (isPost()) {
    $filter = filterData();
    $errors = [];

    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $brand_id = !empty($_POST['brand_id']) ? (int)$_POST['brand_id'] : null;
    $product_id = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;

    // Validate description 
    if (empty($filter['description'])) {
        $errors['description']['required'] = 'Description is required';
    }
    // Validate title/name
    if (empty($filter['title'])) {
        $errors['title']['required'] = 'Banner title is required';
    }

    if (empty($errors)) {

        // Insert/Update data
        $dataupdate = [
            'title' => $filter['title'],
            'description' => $filter['description'],
            'sort_order' => $filter['sort_order'],
            'category_id' => $category_id,
            'brand_id' => $brand_id,
            'product_id' => $product_id,
            'updated_at' => date('Y:m:d H:i:s'),
            'status' => $filter['status'],
        ];
        if (!empty($_FILES['thumbnail']['name'])) {
            // Handle image upload for thumbnail
            $uploadDir = './templates/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 077, true); // Create upload folder if it doesn't exist
            }
            $fileName = basename($_FILES['thumbnail']['name']);
            $targetFile = $uploadDir . time() . '-' . $fileName;

            // Delete old image
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
        $updatestatus = update('banners', $dataupdate, $banner_id);
        if ($updatestatus) {
            setSessionFlash('msg', 'Banner updated successfully.');
            setSessionFlash('msg_type', 'green');
            redirect('?module=banner&action=index');
        } else {
            setSessionFlash('msg', 'Failed to update banner.');
            setSessionFlash('msg_type', 'red');
        }
    } else {
        setSessionFlash('msg', 'Banner update failed, please check your input.');
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
    layout('/dashboard/sidebar'); ?>
    <!-- Add product banner -->
    <div class="content-menu">
        <div class="title-list">
            <h2><?php echo !empty($msg) ? getMess($msg, $msg_type) : 'Add Banner' ?></h2>
            <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=banner&index" ?>"
                class="btn-submit"><i class="fa-solid fa-backward"></i></a>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="add-product">
                <!-- product left -->
                <div class="add-product-left">
                    <!-- Banner title -->
                    <div class="product-input">
                        <label for="title" class="label-input">Banner Title</label><br>
                        <input type="text" id="title" name="title" value="<?php
                                                                            if (!empty($olddata['title'])) {
                                                                                echo oldData($olddata, 'title');
                                                                            } ?>" placeholder="Banner Title">
                    </div>
                    <!-- Description -->
                    <div class="product-input"><label for="description" class="label-input">Description</label><br>
                        <textarea class="text-edit"
                            style="width:100% ; height:150px;resize:none; font-size:1.4rem; padding:10px" type="text"
                            id="description" name="description" placeholder="Banner Description"><?php
                                                                                                    if (!empty($olddata['description'])) {
                                                                                                        echo oldData($olddata, 'description');
                                                                                                    } ?></textarea>
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
                </div>
                <!-- product right -->
                <div class="add-product-right">
                    <!-- Display order -->
                    <div class="product-input">
                        <label for="sort_order" class="label-input">Display Order</label><br>
                        <input type="number" id="sort_order" value="<?php
                                                                    if (!empty($olddata['sort_order'])) {
                                                                        echo oldData($olddata, 'sort_order');
                                                                    } ?>" name="sort_order"
                            placeholder="Priority Order">
                    </div>
                    <!-- Product -->
                    <div class="product-input"><label for="product_id" class="label-input">Product
                        </label><br>
                        <select class="edit-select" name="product_id" id="product_id">
                            <option value="">None</option>
                            <?php
                            if (!empty($listproduct)) {
                                foreach ($listproduct as $item) {
                            ?>
                                    <option value=" <?php echo $item['id']; ?>" <?php
                                                                                if (
                                                                                    !empty($olddata['product_id']) &&
                                                                                    $olddata['product_id'] == $item['id']
                                                                                ) {
                                                                                    echo 'selected';
                                                                                }
                                                                                ?>>
                                        <?php echo $item['name']; ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Category -->
                    <div class="product-input"><label for="category_id" class="label-input">Category
                        </label><br>
                        <select class="edit-select" name="category_id" id="category_id">
                            <option value="">None</option>
                            <?php
                            if (!empty($listCategory)) {
                                foreach ($listCategory as $item) {
                            ?>
                                    <option value=" <?php echo $item['id']; ?>" <?php
                                                                                if (
                                                                                    !empty($olddata['category_id']) &&
                                                                                    $olddata['category_id'] == $item['id']
                                                                                ) {
                                                                                    echo 'selected';
                                                                                }
                                                                                ?>>
                                        <?php echo $item['name']; ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Brand -->
                    <div class="product-input"><label for="brand_id" class="label-input">Brand
                        </label><br>
                        <select class="edit-select" name="brand_id" id="brand_id">
                            <option value="">None</option>
                            <?php
                            if (!empty($listbrand)) {
                                foreach ($listbrand as $item) {
                            ?>
                                    <option value=" <?php echo $item['id']; ?>" <?php
                                                                                if (
                                                                                    !empty($olddata['brand_id']) &&
                                                                                    $olddata['brand_id'] == $item['id']
                                                                                ) {
                                                                                    echo 'selected';
                                                                                }
                                                                                ?>>
                                        <?php echo $item['name']; ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Thumbnail -->
                    <div class="product-input"><label for="thumbnail">Thumbnail</label>
                        <input id="thumbnail" name="thumbnail" type="file" class="form-control">
                        <img id="previewImage" class="preview-image p-3"
                            src="<?php echo !empty($olddata['thumbnail']) ? $olddata['thumbnail'] : false ?>"
                            style="width:200px;" alt="">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-submit">
                Submit
            </button>
        </form>
    </div>
    <!-- End -->
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

<?php
layout('/dashboard/footer');
?>