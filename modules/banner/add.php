<?php
layout('/dashboard/header', 'Add Banner');

// Retrieve parent categories, brands, and products for dropdown selects
$listCategory = getAll("SELECT * FROM categories");
$listbrand    = getAll("SELECT * FROM brands");
$listproduct  = getAll("SELECT * FROM products");

if (isPost()) {
    $filter = filterData();
    $errors = [];

    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $brand_id    = !empty($_POST['brand_id'])    ? (int)$_POST['brand_id']    : null;
    $product_id  = !empty($_POST['product_id'])  ? (int)$_POST['product_id']  : null;

    // Validate description 
    if (empty($filter['description'])) {
        $errors['description']['required'] = 'Please enter a description';
    }

    // Validate banner title
    if (empty($filter['title'])) {
        $errors['title']['required'] = 'Please enter a banner title';
    }

    if (empty($errors)) {
        // Handle thumbnail image upload
        $uploadDir = './templates/uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if it does not exist
        }

        $fileName   = basename($_FILES['thumbnail']['name']);
        $targetFile = $uploadDir . time() . '-' . $fileName;
        $thumb      = '';

        $checkMove = move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetFile);
        if ($checkMove) {
            $thumb = $targetFile;
        }

        // Insert data array
        $datainsert = [
            'title'       => $filter['title'],
            'description' => $filter['description'],
            'sort_order'  => $filter['sort_order'],
            'category_id' => $category_id,
            'brand_id'    => $brand_id,
            'product_id'  => $product_id,
            'created_at'  => date('Y-m-d H:i:s'), // Corrected MySQL datetime format
            'thumbnail'   => $thumb,
            'status'      => 1
        ];

        $insertstatus = insert('banners', $datainsert);
        if ($insertstatus) {
            setSessionFlash('msg', 'Banner added successfully.');
            setSessionFlash('msg_type', 'green');
            redirect('?module=banner&action=index');
        } else {
            setSessionFlash('msg', 'Failed to add banner.');
            setSessionFlash('msg_type', 'red');
        }
    } else {
        setSessionFlash('msg', 'Failed to add banner, please check your input.');
        setSessionFlash('msg_type', 'red');
        setSessionFlash('old_data', $filter);
        setSessionFlash('errors', $errors);
    }
}

$msg       = getSessionFlash('msg');
$msg_type  = getSessionFlash('msg_type');
$olddata   = getSessionFlash('old_data');
$errorsArr = getSessionFlash('errors');
?>

<!-- START MAIN VIEW -->
<div class="main-wrap">
    <?php layout('/dashboard/sidebar'); ?>

    <!-- Add Product Banner -->
    <div class="content-menu">
        <div class="title-list">
            <h2><?php echo !empty($msg) ? getMess($msg, $msg_type) : 'Add Banner'; ?></h2>
            <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=banner&action=index"; ?>"
                class="btn-submit">
                <i class="fa-solid fa-backward"></i>
            </a>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="add-product">
                <!-- Product Left -->
                <div class="add-product-left">
                    <!-- Banner Title -->
                    <div class="product-input">
                        <label for="title" class="label-input">
                            Banner Title
                            <?php if (!empty($errorsArr['title'])): ?>
                                - <?php echo formError($errorsArr, 'title'); ?>
                            <?php endif; ?>
                        </label><br>
                        <input type="text" id="title" name="title" placeholder="Banner Title..."
                            value="<?php echo htmlspecialchars(oldData($olddata, 'title')); ?>">
                    </div>

                    <!-- Description -->
                    <div class="product-input">
                        <label for="description" class="label-input">
                            Description
                            <?php if (!empty($errorsArr['description'])): ?>
                                - <?php echo formError($errorsArr, 'description'); ?>
                            <?php endif; ?>
                        </label><br>
                        <textarea class="text-edit"
                            style="width:100%; height:150px; resize:none; font-size:1.4rem; padding:10px"
                            id="description" name="description"
                            placeholder="Banner Description..."><?php echo htmlspecialchars(oldData($olddata, 'description')); ?></textarea>
                    </div>
                </div>

                <!-- Product Right -->
                <div class="add-product-right">
                    <!-- Display Priority Order -->
                    <div class="product-input">
                        <label for="sort_order" class="label-input">Display Order</label><br>
                        <input type="number" id="sort_order" name="sort_order" placeholder="Priority Order"
                            value="<?php echo htmlspecialchars(oldData($olddata, 'sort_order')); ?>">
                    </div>

                    <!-- Target Product -->
                    <div class="product-input">
                        <label for="product_id" class="label-input">Product</label><br>
                        <select class="edit-select" name="product_id" id="product_id">
                            <option value="">None Selected</option>
                            <?php if (!empty($listproduct)) : ?>
                                <?php foreach ($listproduct as $item) : ?>
                                    <option value="<?php echo $item['id']; ?>"
                                        <?php echo (isset($olddata['product_id']) && $olddata['product_id'] == $item['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Target Category -->
                    <div class="product-input">
                        <label for="category_id" class="label-input">Category</label><br>
                        <select class="edit-select" name="category_id" id="category_id">
                            <option value="">None Selected</option>
                            <?php if (!empty($listCategory)) : ?>
                                <?php foreach ($listCategory as $item) : ?>
                                    <option value="<?php echo $item['id']; ?>"
                                        <?php echo (isset($olddata['category_id']) && $olddata['category_id'] == $item['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Target Brand -->
                    <div class="product-input">
                        <label for="brand_id" class="label-input">Brand</label><br>
                        <select class="edit-select" name="brand_id" id="brand_id">
                            <option value="">None Selected</option>
                            <?php if (!empty($listbrand)) : ?>
                                <?php foreach ($listbrand as $item) : ?>
                                    <option value="<?php echo $item['id']; ?>"
                                        <?php echo (isset($olddata['brand_id']) && $olddata['brand_id'] == $item['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Thumbnail Image Upload -->
                    <div class="product-input">
                        <label for="thumbnail" class="label-input">Thumbnail</label><br>
                        <input id="thumbnail" name="thumbnail" type="file" class="form-control">
                        <img id="previewImage" class="preview-image p-3" src="#" style="display:none; width:200px;"
                            alt="Banner Preview">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit">Submit</button>
        </form>
    </div>
</div>

<!-- Image Preview Script -->
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

<?php layout('/dashboard/footer'); ?>