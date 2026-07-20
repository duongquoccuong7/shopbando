<?php
layout('/dashboard/header', 'Add product');
$listbrand = getAll("SELECT * FROM brands ");
$listCategory = getAll("SELECT * FROM categories");
$listsize = getAll("SELECT * FROM sizes");
$listcolor = getAll("SELECT * FROM colors");
$listgen = getAll("SELECT * FROM genders");

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
        //Xử lý thumbnail upload ảnh
        $uploadDir = './templates/uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true); //Tạo mới thư mục upload nếu chưa có
        }
        $fileName = basename($_FILES['thumbnail']['name']);
        $targetFile =  $uploadDir . time() . '-' . $fileName;
        $thumb = '';
        $checkMove = move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetFile);
        if ($checkMove) {
            $thumb = $targetFile;
        }
        //insert data
        $datainsert = [
            'name' => $filter['name'],
            'description' => $filter['description'],
            'slug' => $filter['slug'],
            'category_id' => $filter['category_id'],
            'brand_id' => $filter['brand_id'],
            'gender_id' => $filter['gender_id'],
            'created_at' => date('Y:m:d  H:i:s'),
            'thumbnail' => $thumb,
            'status' => 1
        ];
        $product_id = insert('products', $datainsert);
        if ($product_id) {
            for ($i = 0; $i < count($_POST['size_id']); $i++) {
                $img = '';

                if (!empty($_FILES['image']['name'][$i])) {

                    $fileName = time() . '-' . uniqid() . '-' . basename($_FILES['image']['name'][$i]);
                    $target = $uploadDir . $fileName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'][$i], $target)) {
                        $img = $target;
                    }
                }
                $data = [
                    'product_id' => $product_id,
                    'size_id' => $_POST['size_id'][$i],
                    'color_id' => $_POST['color_id'][$i],
                    'import_price' => $_POST['import_price'][$i],
                    'sale_price' => $_POST['sale_price'][$i],
                    'stock' => $_POST['stock'][$i],
                    'image' => $img,
                ];
                // upload ảnh thứ $i
                insert('product_variants', $data);
                // INSERT vào bảng product_detail
            }
            setSessionFlash('msg', 'Product added successfully');
            setSessionFlash('msg_type', 'green');
            redirect('?module=product&action=index');
        } else {
            setSessionFlash('msg', 'Failed to add product');
            setSessionFlash('msg_type', 'red');
        }
    } else {
        setSessionFlash('msg', 'Failed to add product. Please check your input and try again');
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
    layout('/dashboard/sidebar'); ?>
    <!-- Add product sản phẩm -->
    <div class="content-menu">
        <div class="title-list">
            <h2><?php echo  !empty($msg) ? getMess($msg, $msg_type) : 'Add product' ?></h2>
            <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=product&index" ?>"
                class="btn-submit"><i class="fa-solid fa-backward"></i></a>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="add-product">
                <!-- product left -->
                <div class="add-product-left">
                    <!-- tên sản phẩm -->
                    <div class="product-input">
                        <label for="name" class="label-input">
                            <?php echo !empty($errors) ? formError($errors, 'name') : 'Name'; ?></label><br>
                        <input type="text" id="name" name="name" placeholder=" Name product ...">
                    </div>
                    <!-- mô tả -->
                    <div class="product-input"><label for="description"
                            class="label-input"><?php echo !empty($errors) ? formError($errors, 'description') : 'Description'; ?></label><br></label><br>
                        <textarea class="text-edit"
                            style="width:100% ; height:150px;resize:none; font-size:1.4rem; padding:10px" type="text"
                            id="description" name="description" placeholder="Desctiption ..."></textarea>
                    </div>

                    <!-- Name products -->
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
                                    <td><input type="text" class="stock" name="stock[]"></td>
                                    <td><i class="fa-regular fa-trash-can"></i></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- product right -->
                <div class="add-product-right">
                    <!-- slug sản phẩm -->
                    <div class="product-input">
                        <label for="slug"
                            class="label-input"><?php echo !empty($errors) ? formError($errors, 'slug') : 'Slug'; ?></label><br></label><br>
                        <input type="text" id="slug" name="slug" placeholder="Slug ...">
                    </div>
                    <!-- Categoty product -->
                    <div class="product-input"><label for="category_id" class="label-input">Categoty
                        </label><br>
                        <select class="edit-select" name="category_id" id="category_id">
                            <?php
                            if (!empty($listCategory) && $listCategory != 0) {
                                foreach ($listCategory as $item) {
                            ?>
                            <option value="<?php echo $item['id']; ?>">
                                <?php echo $item['name']; ?>
                            </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Brand sản phẩm -->
                    <div class="product-input"><label for="brand_id" class="label-input">Brand
                        </label><br>
                        <select class="edit-select" name="brand_id" id="brand_id">
                            <?php
                            if (!empty($listbrand)) {
                                foreach ($listbrand as $item) {
                            ?>
                            <option value="<?php echo $item['id']; ?>">
                                <?php echo $item['name']; ?>
                            </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Brand sản phẩm -->
                    <div class="product-input"><label for="brand_id" class="label-input">Gender
                        </label><br>
                        <select class="edit-select" name="brand_id" id="brand_id">
                            <?php
                            if (!empty($listgen)) {
                                foreach ($listgen as $item) {
                            ?>
                            <option value="<?php echo $item['id']; ?>">
                                <?php echo $item['name']; ?>
                            </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <!-- ảnh sản phẩm -->
                    <div class="product-input"><label for="thumbnail">Thumbnail</label>
                        <input id="thumbnail" name="thumbnail" type="file" class="form-control">
                        <img id="previewImage" class="preview-image p-3" src="#" style="display:none;width:200px;"
                            alt="">
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

// thêm dòng product variants
const tbody = document.querySelector(".table_add_pro tbody");
const addBtn = document.querySelector(".table_add_pro thead th:last-child");


// Thêm dòng mới
addBtn.addEventListener("click", function() {

    const firstRow = tbody.querySelector("tr");
    const newRow = firstRow.cloneNode(true);

    // Lấy giá dòng đầu
    const importPrice = firstRow.querySelector(".import_price").value;
    const salePrice = firstRow.querySelector(".sale_price").value;
    const stock = firstRow.querySelector(".stock").value;

    // Reset input
    newRow.querySelectorAll("input").forEach(input => {
        input.value = "";
    });


    // Giữ giá
    newRow.querySelector(".import_price").value = importPrice;
    newRow.querySelector(".sale_price").value = salePrice;
    newRow.querySelector(".stock").value = stock;


    // Reset select
    newRow.querySelectorAll("select").forEach(select => {
        select.selectedIndex = 0;
    });


    // Reset ảnh
    const preview = newRow.querySelector(".preview");
    preview.src = "";
    preview.style.display = "none";


    // Hiện lại dấu +
    const label = newRow.querySelector(".image-label");
    label.style.display = "inline-block";


    tbody.appendChild(newRow);
});


// Click label ảnh + xóa dòng
tbody.addEventListener("click", function(e) {


    // Xóa dòng
    if (e.target.closest(".fa-trash-can")) {

        if (tbody.querySelectorAll("tr").length > 1) {
            e.target.closest("tr").remove();
        } else {
            alert("Phải có ít nhất 1 dòng.");
        }

        return;
    }


    // Chọn ảnh
    const label = e.target.closest(".image-label");

    if (label) {

        const row = label.closest("tr");

        row.querySelector(".image-input").click();
    }


    // Click vào ảnh để đổi ảnh
    if (e.target.classList.contains("preview")) {

        const row = e.target.closest("tr");

        row.querySelector(".image-input").click();
    }

});


// Preview ảnh
tbody.addEventListener("change", function(e) {

    if (!e.target.classList.contains("image-input")) return;


    const file = e.target.files[0];

    const row = e.target.closest("tr");

    const preview = row.querySelector(".preview");
    const label = row.querySelector(".image-label");


    if (file) {

        // Hiện ảnh
        preview.src = URL.createObjectURL(file);
        preview.style.display = "block";


        // Ẩn dấu +
        label.style.display = "none";

    } else {

        // Không có ảnh
        preview.src = "";
        preview.style.display = "none";


        // Hiện dấu +
        label.style.display = "inline-block";
    }

});
// 
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

// 
const inputs = [
    document.querySelectorAll('.sale_price'),
    document.querySelectorAll('.import_price'),
];

inputs.forEach(input => {
    input.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    });
});
</script>
<?php
layout('/dashboard/footer');
?>