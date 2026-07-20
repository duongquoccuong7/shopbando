<?php
layout('/dashboard/header', 'Add Brand');

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
            'country' => $filter['country'],
            'created_at' => date('Y:m:d  H:i:s'),
            'thumbnail' => $thumb,
            'status' => 1
        ];
        $insertstatus =  insert('brands', $datainsert);
        if ($insertstatus) {
            setSessionFlash('msg', 'Brand added successfully');
            setSessionFlash('msg_type', 'success');
            redirect('?module=brands&action=index');
        } else {
            setSessionFlash('msg', 'Failed to add brand');
            setSessionFlash('msg_type', 'red');
        }
    } else {
        setSessionFlash('msg', 'Failed to add brand. Please check your input and try again');
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
    layout('/dashboard/sidebar');  ?>
    <!-- Add Brand sản phẩm -->
    <div class="content-menu">
        <div class="title-list">
            <h2><?php echo  !empty($msg) ? getMess($msg, $msg_type) : 'Add Brand' ?></h2>
            <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=category&index" ?>"
                class="btn-submit"><i class="fa-solid fa-backward"></i></a>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="add-product">
                <!-- product left -->
                <div class="add-product-left">
                    <!--  Name -->
                    <div class="product-input">
                        <label for="name" class="label-input"> Name</label><br>
                        <input type="text" id="name" name="name" placeholder="Name brand ...">
                    </div>
                    <!-- mô tả -->
                    <div class="product-input"><label for="description" class="label-input">Description</label><br>
                        <textarea class="text-edit"
                            style="width:100% ; height:150px;resize:none; font-size:1.4rem; padding:10px" type="text"
                            id="description" name="description" placeholder="Description ..."></textarea>
                    </div>
                </div>
                <!-- product right -->
                <div class="add-product-right">
                    <!--  Name -->
                    <div class="product-input">
                        <label for="slug" class="label-input">Slug</label><br>
                        <input type="text" id="slug" name="slug" placeholder="slug .. ">
                    </div>
                    <div class="product-input">
                        <label for="country" class="label-input">Country</label><br>
                        <input type="text" id="country" name="country" placeholder="Country ... ">
                    </div>
                    <!-- giá bán -->
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