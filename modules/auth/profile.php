    <?php
    if (!defined('_CHECK')) {
        die('Truy cập không hợp lệ');
    }
    layout('/dashboard/header', 'Hồ sơ');
    $getData = filterData('GET');
    $token = getSession('token_login');
    $getInfor = [];
    if (!empty($token)) {
        $check = getOne("SELECT * FROM login_token WHERE token ='$token'");
        if (!empty($check)) {
            $user_id = $check['user_id'];
            $Data = getOne("SELECT * FROM users WHERE id=$user_id");
            $getInfor = getOne("SELECT * FROM user_addresses WHERE user_id = $user_id");
            if (isPost()) {
                $filter = filterData();
                $errors = [];
                //validate fullname
                if (empty(trim($filter['fullname']))) {
                    $errors['fullname']['required'] = 'Họ và tên chưa được nhập';
                } else {
                    if (strlen(trim($filter['fullname'])) < 5) {
                        $errors['fullname']['length'] = 'Họ và tên phải lớn hơn 5 ký tự';
                    }
                }

                //Validate Email
                if (empty(trim($filter['email']))) {
                    $errors['email']['required'] = 'Email chưa được nhập';
                } else {
                    if (!validateEmail($filter['email'])) {
                        $errors['email']['isEmail'] = 'Email không đúng định dạng';
                    }
                }

                //validate phone
                if (empty(trim($filter['phone']))) {
                    $errors['phone']['required'] = 'Số điện thoại chưa được nhập';
                } else {
                    if (!isPhone($filter['phone'])) {
                        $errors['phone']['isPhone'] = 'Số điện thoại không đúng định dạng';
                    }
                }
                if (empty($errors)) {

                    $activeToken = sha1(uniqid() . time());
                    $dataupdate = [
                        'fullname' => $filter['fullname'],
                        'email' => $filter['email'],
                        'phone' => $filter['phone'],
                        'updated_at' => date('Y:m:d H:i:s'),
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
                    $user_add = [
                        'user_id' => $user_id,
                        'province' => $filter['province'],
                        'ward' => $filter['ward'],
                        'address_detail' => $filter['address_detail']
                    ];
                    $updatestatus =  update('users', $dataupdate, $user_id);
                    if ($updatestatus) {
                        if ($getInfor['user_id'] == $user_id) {
                            $user_add['updated_at'] = date('Y:m:d H:i:s');
                            update('user_addresses', $user_add, $user_id);
                            setSessionFlash('msg', 'Cập nhật tài khoản thành công');
                            setSessionFlash('msg_type', 'green');
                            redirect('?module=auth&action=profile&id=' . $user_id);
                            exit;
                        } else {
                            $user_add['created_at'] = date('Y:m:d H:i:s');
                            insert('user_addresses', $user_add);
                            setSessionFlash('msg', 'Cập nhật tài khoản thành công');
                            setSessionFlash('msg_type', 'green');
                            redirect('?module=auth&action=profile&id=' . $user_id);
                            exit;
                        }
                    } else {
                        setSessionFlash('msg', 'Cập nhật tài khoản không thành công');
                        setSessionFlash('msg_type', 'red');
                    }
                } else {
                    $mess = 'Dữ liệu không hợp lệ ,hãy kiểm tra lại!';
                    $color = 'red';
                    setSessionFlash('oldData', $filter);
                    setSessionFlash('errors', $errors);
                }
            }
        }
    }
    $msg = getSessionFlash('msg');
    $msg_type = getSessionFlash('msg_type');
    $olddata = getSessionFlash('oldData');
    $errorsArr = getSessionFlash('errors');
    if (!empty($Data)) {
        $olddata = $Data;
        if (!empty($getInfor)) {
            $olddata = array_merge($olddata, $getInfor);
        }
    }
    ?>


    <!-- start add user-->
    <div class="main-wrap">
        <?php
        layout('/dashboard/sidebar');
        ?>
        <!-- Thêm mã giảm sản phẩm -->
        <div class="content-menu">
            <?php if (!empty($msg) && !empty($msg_type)) : ?>
                <div class="anoun-mess">
                    <?php getMess($msg, $msg_type); ?>
                </div>
            <?php endif; ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="add-product">
                    <div class="profile-avatar">
                        <img id="previewImage" class="profile-img"
                            src="<?php echo !empty($olddata['thumbnail']) ? $olddata['thumbnail'] : false ?>" alt="">
                        <div class="upload-wrap">
                            <input type="file" id="thumbnail" name="thumbnail" hidden>
                            <label for="thumbnail">Chọn ảnh</label>
                        </div>
                    </div>
                    <!-- product left -->
                    <div class="add-product-left">
                        <!-- tên mã giảm -->
                        <div class="product-input">
                            <label for="fullname" class="label-input">
                                <?php
                                echo 'Họ và tên';
                                if (!empty($errorsArr['fullname'])) {
                                    echo ' - ' . formError($errorsArr, 'fullname');
                                }
                                ?>
                            </label><br>
                            <input type="text" id="fullname" name="fullname" placeholder=" Tên ..." value="<?php
                                                                                                            if (!empty($olddata['fullname'])) {
                                                                                                                echo  oldData($olddata, 'fullname');
                                                                                                            }   ?>">
                        </div>
                        <!-- email -->
                        <div class="product-input">
                            <label for="email" class="label-input">
                                <?php
                                echo 'Email';
                                if (!empty($errorsArr['email'])) {
                                    echo ' - ' . formError($errorsArr, 'email');
                                }
                                ?>
                            </label><br>
                            <input type="text" id="email" name="email" placeholder=" Email ..." value="<?php
                                                                                                        if (!empty($olddata['email'])) {
                                                                                                            echo  oldData($olddata, 'email');
                                                                                                        }   ?>">

                        </div>
                        <!-- phone -->
                        <div class="product-input">
                            <label for="phone" class="label-input">
                                <?php
                                echo 'Số điện thoại';
                                if (!empty($errorsArr['phone'])) {
                                    echo ' - ' . formError($errorsArr, 'phone');
                                }
                                ?>
                            </label><br>
                            <input type="text" id="phone" name="phone" placeholder=" Số điện thoại ..." value="<?php
                                                                                                                if (!empty($olddata['phone'])) {
                                                                                                                    echo  oldData($olddata, 'phone');
                                                                                                                }   ?>">

                        </div>

                        <div class="product-input">
                            <label for="address_detail" class="label-input">
                                <?php
                                echo 'Địa chỉ chi tiết';
                                if (!empty($errorsArr['address_detail'])) {
                                    echo ' - ' . formError($errorsArr, 'address_detail');
                                }
                                ?>
                            </label><br>
                            <input type="text" id="address_detail" name="address_detail"
                                placeholder=" Địa chỉ chi tiết ..." value="<?php
                                                                            if (!empty($olddata['address_detail'])) {
                                                                                echo  oldData($olddata, 'address_detail');
                                                                            }   ?>">

                        </div>


                    </div>
                    <!-- product right -->
                    <div class="add-product-right">
                        <div class="product-input"><label for="role" class="label-input">Vai trò
                            </label><br>
                            <select name="role" class="edit-select" id="role">
                                <option value="admin" <?php
                                                        if (isset($olddata['role']) && $olddata['role'] == 'admin') {
                                                            echo 'selected';
                                                        }
                                                        ?>>Admin</option>
                                <option value="user" <?php
                                                        if (isset($olddata['role']) && $olddata['role'] == 'user') {
                                                            echo 'selected';
                                                        }
                                                        ?>>
                                    User
                                </option>
                            </select>
                        </div>
                        <div class="product-input">
                            <label for="status" class="label-input">Trạng thái</label><br>
                            <select name="status" class="edit-select" id="status">
                                <?php if (isset($olddata['status']) && $olddata['status'] == 1): ?>
                                    <option value="1" <?= $olddata['status'] == 1 ? 'selected' : '' ?>>
                                        Chưa kích hoạt
                                    </option>
                                <?php endif; ?>
                                <option value="2" <?= $olddata['status'] == 2 ? 'selected' : '' ?>>
                                    Kích hoạt
                                </option>
                                <option value="3" <?= $olddata['status'] == 3 ? 'selected' : '' ?>>
                                    Tạm khóa
                                </option>

                            </select>
                        </div>
                        <!-- tỉnh  -->
                        <div class="product-input">
                            <label for="province" class="label-input">Tỉnh - Thành Phố </label><br>
                            <select name="province" class="edit-select" id="province">
                                <option value="">Chọn tỉnh</option>
                            </select>
                        </div>
                        <div class="product-input">
                            <label for="ward" class="label-input">Phường - Xã</label><br>
                            <select name="ward" class="edit-select" id="ward">
                                <option value="">Phường - Xã </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="btn-wrap">
                    <button type="submit" class="btn-submit btn-profile">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
        <!-- kết thúc -->
    </div>
    <!-- end main -->
    <script>
        const provinceSelect = document.getElementById("province");
        const wardSelect = document.getElementById("ward");

        // Giá trị cũ từ PHP
        const oldProvince = "<?php echo $oldProvince ?? ''; ?>";
        const oldWard = "<?php echo $olddata['ward'] ?? ''; ?>";

        let provinces = [];

        // Đọc file JSON
        fetch("/assets/json/provinces.json")
            .then(res => {
                if (!res.ok) {
                    throw new Error("Không thể đọc file provinces.json");
                }
                return res.json();
            })
            .then(json => {

                provinces = json;

                // Load danh sách tỉnh
                provinces.forEach(province => {

                    const option = document.createElement("option");
                    option.value = province.Code;
                    option.textContent = province.FullName;

                    if (String(province.Code) === String(oldProvince)) {
                        option.selected = true;
                    }

                    provinceSelect.appendChild(option);
                });

                // Nếu có tỉnh cũ thì load xã
                if (oldProvince) {
                    loadWards(oldProvince, oldWard);
                }

            })
            .catch(err => {
                console.error(err);
            });

        // Khi đổi tỉnh
        provinceSelect.addEventListener("change", function() {
            loadWards(this.value);
        });

        // Load xã theo tỉnh
        function loadWards(provinceCode, selectedWard = "") {

            wardSelect.innerHTML = '<option value="">Chọn phường/xã</option>';

            if (!provinceCode) return;

            const province = provinces.find(p => String(p.Code) === String(provinceCode));

            if (!province || !Array.isArray(province.Wards)) return;

            province.Wards.forEach(ward => {

                const option = document.createElement("option");

                option.value = ward.Code;
                option.textContent = ward.FullName;

                if (String(ward.Code) === String(selectedWard)) {
                    option.selected = true;
                }

                wardSelect.appendChild(option);
            });
        }

        // Preview ảnh
        document.getElementById("thumbnail").addEventListener("change", function() {

            const file = this.files[0];

            if (file) {
                document.getElementById("previewImage").src =
                    URL.createObjectURL(file);
            }

        });
    </script>
    <?php
    layout('dashboard/footer');
    ?>