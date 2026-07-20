<?php
layout('/dashboard/header', 'Edit Product');
$listbrand = getAll("SELECT * FROM brands ");
$listCategory = getAll("SELECT * FROM categories");
$listsize = getAll("SELECT * FROM sizes");
$listcolor = getAll("SELECT * FROM colors");
$listgen = getAll("SELECT * FROM genders");
$getData = filterData('GET');
$product_id = $getData['id'];
$Data = getOne("SELECT * FROM products WHERE id=$product_id");
$getpro_var = getAll("SELECT * FROM product_variants WHERE product_id=$product_id");

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
        echo 'ok';
        $dataupdate = [
            'name' => $filter['name'],
            'description' => $filter['description'],
            'slug' => $filter['slug'],
            'category_id' => $filter['category_id'],
            'brand_id' => $filter['brand_id'],
            'gender_id' => $filter['gender_id'],
            'is_featured' => $filter['is_featured'],
            'is_spotlight' => $filter['is_spotlight'],
            'thumbnail_color_id' => $filter['thumbnail_color_id'],
            'updated_at' => date('Y-m-d  H:i:s'),
            'status' => $filter['status'],
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
        $updatestatus = update('products', $dataupdate,  $product_id);
        $uploadDir = "./templates/uploads/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        for ($i = 0; $i < count($_POST['size_id']); $i++) {
            // Mặc định giữ ảnh cũ
            $image = $_POST['old_image'][$i];
            // Nếu chọn ảnh mới
            if (!empty($_FILES['image']['name'][$i])) {

                $fileName = time() . '-' . uniqid() . '-' . basename($_FILES['image']['name'][$i]);

                $targetFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'][$i], $targetFile)) {

                    // Xóa ảnh cũ
                    if (!empty($_POST['old_image'][$i]) && file_exists($_POST['old_image'][$i])) {
                        unlink($_POST['old_image'][$i]);
                    }

                    $image = $targetFile;
                }
            }
            $data = [
                'product_id'   => $product_id,
                'size_id'      => $_POST['size_id'][$i],
                'color_id'     => $_POST['color_id'][$i],
                'import_price' => $_POST['import_price'][$i],
                'sale_price'   => $_POST['sale_price'][$i],
                'stock'        => $_POST['stock'][$i],
                'image'        => $image,
                'updated_at' => date('Y:m:d  H:i:s'),
            ];
            $variant_id = $_POST['variant_id'][$i] ?? null;
            if (!empty($variant_id)) {
                update('product_variants', $data, $variant_id);
            } else {
                insert('product_variants', $data);
            }
        }
        if ($updatestatus) {
            setSessionFlash('msg', 'Product updated successfully');
            setSessionFlash('msg_type', 'green');
            redirect('?module=product&action=edit&id=' . $product_id);
        } else {
            setSessionFlash('msg', 'Failed to updated product');
            setSessionFlash('msg_type', 'red');
        }
    } else {
        setSessionFlash('msg', 'Failed to updated product. Please check your input and try again');
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
    <!-- Update Product sản phẩm -->
    <div class="content-menu">
        <div class="title-list">
            <h2><?php echo  !empty($msg) ? getMess($msg, $msg_type) : 'Edit Product' ?></h2>
            <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=product&index" ?>"
                class="btn-submit"><i class="fa-solid fa-backward"></i></a>
        </div>
        <form method="post" enctype="multipart/form-data">
            <div class="add-product">
                <!-- product left -->
                <div class="add-product-left">
                    <!-- tên sản phẩm -->
                    <div class="product-input">
                        <label for="name" class="label-input">
                            <?php echo !empty($errors) ? formError($errors, 'name') : 'Name'; ?></label><br>
                        <input type="text" id="slugname" name="name" value="<?php
                                                                            if (!empty($olddata['name'])) {
                                                                                echo  oldData($olddata, 'name');
                                                                            }   ?>" placeholder=" Name product ...">
                    </div>
                    <!-- mô tả -->
                    <div class="product-input"><label for="description"
                            class="label-input"><?php echo !empty($errors) ? formError($errors, 'description') : 'Description'; ?></label><br>
                        <textarea class="text-edit"
                            style="width:100% ; height:150px;resize:none; font-size:1.4rem; padding:10px" type="text"
                            id="description" name="description" placeholder="Description"><?php
                                                                                            if (!empty($olddata['description'])) {
                                                                                                echo  oldData($olddata, 'description');
                                                                                            }   ?></textarea>
                    </div>

                    <!-- tên sản phẩm -->
                    <div class="product-input product-add">

                        <table class="table_add_pro">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="th_add_pro">Size</th>
                                    <th class="th_add_pro">Color</th>
                                    <th class="th_add_pro">Image</th>
                                    <th class="th_add_pro">Cost</th>
                                    <th class="th_add_pro">Sale</th>
                                    <th class="th_add_pro">Stock</th>
                                    <th class="th_add_pro"><i class="fa-solid fa-plus"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($getpro_var): ?>
                                    <?php foreach ($getpro_var as $var): ?>
                                        <tr>
                                            <input type="hidden" name="variant_id[]" value="<?= $var['id']; ?>">
                                            <input type="hidden" name="old_image[]" value="<?= $var['image']; ?>">
                                            <td style="text-align: center;">
                                                <select name="size_id[]" class="custom-select">
                                                    <?php foreach ($listsize as $item): ?>
                                                        <option value="<?= $item['id']; ?>"
                                                            <?= ($var['size_id'] == $item['id']) ? 'selected' : ''; ?>>
                                                            <?= $item['name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td style=" text-align: center;">
                                                <select name="color_id[]" class="custom-select-color custom-select">
                                                    <?php foreach ($listcolor as $item): ?>
                                                        <option value="<?= $item['id']; ?>"
                                                            <?= ($var['color_id'] == $item['id']) ? 'selected' : ''; ?>>
                                                            <?= $item['name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="file" name="image[]" class="image-input" hidden>
                                                <label class="btn image-label"
                                                    <?= !empty($var['image']) ? 'style="display:none"' : '' ?>>
                                                    <i class="fa-solid fa-plus"></i>
                                                </label>

                                                <img class="preview" width="50" height="30"
                                                    style="<?= !empty($var['image']) ? 'display:block' : 'display:none' ?>;object-fit:cover;"
                                                    src="<?= $var['image']; ?>">
                                            </td>
                                            <td><input type="text" class="import_price"
                                                    value="<?php echo $var['import_price']; ?>" name="import_price[]"></td>
                                            <td><input type="text" class="sale_price" value="<?php echo $var['sale_price']; ?>"
                                                    name="sale_price[]"></td>
                                            <td><input type="text" value="<?php echo $var['stock']; ?>" name="stock[]"></td>
                                            <td>
                                                <a class="delete-db"
                                                    href="?module=product&action=delete_var&id=<?= $var['id']; ?>"
                                                    onclick="return confirm('Bạn có chắc muốn xóa?')">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td style="text-align: center;">
                                            <select name="size_id[]" class="custom-select">
                                                <?php foreach ($listsize as $item): ?>
                                                    <option value="<?php echo $item['id']; ?>">
                                                        <?php echo $item['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td style=" text-align: center;">
                                            <select name="color_id[]" class="custom-select-color custom-select">
                                                <?php foreach ($listcolor as $item): ?>
                                                    <option value="<?php echo $item['id']; ?>">
                                                        <?php echo $item['name']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="file" name="image[]" class="image-input" hidden>
                                            <label class="btn image-label">
                                                <i class="fa-solid fa-plus"></i>
                                            </label>

                                            <img class="preview" width="50" height="30"
                                                style="display:none; object-fit:cover;">
                                        </td>
                                        <td><input type="text" class="import_price" name="import_price[]"></td>
                                        <td><input type="text" class="sale_price" name="sale_price[]"></td>
                                        <td><input type="text" name="stock[]"></td>
                                        <td>
                                            <button type="button" class="delete-row">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- product right -->
                <div class="add-product-right">
                    <!-- slug sản phẩm -->
                    <div class="product-input">
                        <label for="slug" class="label-input">Slug</label><br>
                        <input type="text" id="slug" name="slug" value="<?php
                                                                        if (!empty($olddata['slug'])) {
                                                                            echo  oldData($olddata, 'slug');
                                                                        }   ?>" placeholder="Slug">
                    </div>

                    <!-- danh mục sản phẩm -->
                    <div class="product-input"><label for="category_id" class="label-input">Category
                        </label><br>
                        <select class="edit-select" name="category_id" id="category_id">
                            <?php
                            if (!empty($listCategory) && $listCategory != 0) {
                                foreach ($listCategory as $cate) {
                            ?>
                                    <option value="<?php echo $cate['id']; ?>"
                                        <?php echo (!empty($olddata['category_id']) && $olddata['category_id'] == $cate['id']) ? 'selected' : ''; ?>>
                                        <?php echo $cate['name']; ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <!-- thương hiệu sản phẩm -->
                    <div class="product-input"><label for="brand_id" class="label-input">Brand
                        </label><br>

                        <select class="edit-select" name="brand_id" id="brand_id">
                            <?php
                            if (!empty($listbrand)) {
                                foreach ($listbrand as $brand) {
                            ?>
                                    <option value="<?php echo $brand['id']; ?>"
                                        <?php echo (!empty($olddata['brand_id']) && $olddata['brand_id'] == $brand['id']) ? 'selected' : ''; ?>>
                                        <?php echo $brand['name']; ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Brand sản phẩm -->
                    <div class="product-input"><label for="gender_id" class="label-input">Gender
                        </label><br>
                        <select class="edit-select" name="gender_id" id="gender_id">
                            <?php
                            if (!empty($listgen)) {
                                foreach ($listgen as $gen) {
                            ?>
                                    <option value="<?php echo $gen['id']; ?>"
                                        <?php echo (!empty($olddata['gender_id']) && $olddata['gender_id'] == $gen['id']) ? 'selected' : ''; ?>>
                                        <?php echo $gen['name']; ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <!-- trạng thái -->
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
                    <!-- Brand sản phẩm -->
                    <div class="product-input"><label for="thumbnail_color_id" class="label-input">Color
                        </label><br>
                        <select class="edit-select" name="thumbnail_color_id" id="thumbnail_color_id">
                            <?php
                            if (!empty($listcolor)) {
                                foreach ($listcolor as $thum_color_id) {
                            ?>
                                    <option value="<?php echo $thum_color_id['id']; ?>"
                                        <?php echo (!empty($olddata['thumbnail_color_id']) && $olddata['thumbnail_color_id'] == $thum_color_id['id']) ? 'selected' : ''; ?>>
                                        <?php echo $thum_color_id['name']; ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <!-- featured -->
                    <div class="product-input"><label for="is_featured" class="label-input">Featured
                        </label><br>
                        <select class="edit-select" name="is_featured" id="is_featured">
                            <option value="1" <?php
                                                if (isset($olddata['is_featured']) && $olddata['is_featured'] == 1) {
                                                    echo 'selected';
                                                }
                                                ?>>
                                Featured
                            </option>
                            <option value="0" <?php
                                                if (isset($olddata['is_featured']) && $olddata['is_featured'] == 0) {
                                                    echo 'selected';
                                                }
                                                ?>>
                                No
                            </option>
                        </select>
                    </div>
                    <!-- spotlight-->
                    <div class="product-input"><label for="is_spotlight" class="label-input">Spotlight
                        </label><br>
                        <select class="edit-select" name="is_spotlight" id="is_spotlight">
                            <option value="1" <?php
                                                if (isset($olddata['is_spotlight']) && $olddata['is_spotlight'] == 1) {
                                                    echo 'selected';
                                                }
                                                ?>>
                                Spotlight
                            </option>
                            <option value="0" <?php
                                                if (isset($olddata['is_spotlight']) && $olddata['is_spotlight'] == 0) {
                                                    echo 'selected';
                                                }
                                                ?>>
                                No
                            </option>
                        </select>
                    </div>
                    <!-- ảnh sản phẩm -->
                    <div class="product-input"><label for="thumbnail">Thumbnail</label>
                        <input id="thumbnail" name="thumbnail" type="file" class="form-control">
                        <img id="previewImage" class="preview-image p-3"
                            src="<?php echo !empty($olddata['thumbnail']) ? $olddata['thumbnail'] : false ?>"
                            style="width:150px;" alt="">
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

    const tbody = document.querySelector(".table_add_pro tbody");
    const addBtn = document.querySelector(".table_add_pro thead th:last-child");


    // =======================
    // THÊM DÒNG MỚI
    // =======================
    addBtn.addEventListener("click", function() {

        tbody.insertAdjacentHTML("beforeend", `
        <tr class="new-row">  <td style="text-align:center">
                <select name="size_id[]" class="custom-select">
                    <?php foreach ($listsize as $item): ?>
                    <option value="<?= $item['id'] ?>">
                        <?= $item['name'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </td>


            <td style="text-align:center">
                <select name="color_id[]" class="custom-select-color custom-select">
                    <?php foreach ($listcolor as $item): ?>
                    <option value="<?= $item['id'] ?>">
                        <?= $item['name'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </td>


            <td>
             <input type="hidden" name="variant_id[]" value="">
                <input type="file" 
                       name="image[]" 
                       class="image-input" 
                       hidden>


                <label class="btn image-label">
                    <i class="fa-solid fa-plus"></i>
                </label>


                <img class="preview"
                     width="50"
                     height="30"
                     style="display:none;object-fit:cover">
            </td>


            <td>
                <input type="text"
                       class="import_price"
                       name="import_price[]">
            </td>


            <td>
                <input type="text"
                       class="sale_price"
                       name="sale_price[]">
            </td>


            <td>
                <input type="text"
                       name="stock[]">
            </td>


            <td>
                <button type="button" class="delete-row">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </td>

        </tr>
    `);

    });

    tbody.addEventListener("click", function(e) {

        // =======================
        // CLICK ẢNH HOẶC DẤU +
        // =======================
        const imageBtn = e.target.closest(".preview, .image-label");

        if (imageBtn) {
            const row = imageBtn.closest("tr");

            row.querySelector(".image-input").click();

            return;
        }


        // =======================
        // XÓA DÒNG
        // =======================
        const deleteBtn = e.target.closest(".delete-row");

        if (deleteBtn) {
            const row = deleteBtn.closest("tr");

            row.remove();

            return;
        }

    });

    // Preview ảnh mới
    tbody.addEventListener("change", function(e) {

        if (!e.target.classList.contains("image-input")) return;

        const file = e.target.files[0];

        const row = e.target.closest("tr");

        const preview = row.querySelector(".preview");
        const label = row.querySelector(".image-label");


        if (file) {

            preview.src = URL.createObjectURL(file);
            preview.style.display = "block";

            label.style.display = "none";

        } else {

            preview.src = "";
            preview.style.display = "none";

            label.style.display = "inline-block";
        }

    });





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
    document.getElementById('slugname').addEventListener('input', function() {
        const getValue = this.value;
        document.getElementById('slug').value = createSlug(getValue);

    });



    document.querySelectorAll('.sale_price, .import_price').forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        });
    });
</script>

<?php
layout('/dashboard/footer');
?>