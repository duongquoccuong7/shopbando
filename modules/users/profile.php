<?php
layout('/index/header', 'Profile');
$getData = filterData('GET');
$list_cou = getAll("SELECT * FROM coupons WHERE status=1");
$user_id = $getData['id'];
$user = getOne("SELECT * FROM users WHERE id = $user_id");
$user_add = getOne("SELECT * FROM user_addresses WHERE user_id = $user_id");

if (isPost()) {

    $filter = filterData();

    $fullname = trim($filter['fullname']);
    $email = trim($filter['email']);
    $phone = trim($filter['phone']);
    $province = trim($filter['province']);
    $ward = trim($filter['ward']);
    $address = trim($filter['address']);


    $userData = [
        'fullname'   => $fullname,
        'email'      => $email,
        'phone'      => $phone,
        'updated_at' => date('Y-m-d H:i:s')
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
            !empty($user['thumbnail']) &&
            file_exists($user['thumbnail'])
        ) {
            unlink($user['thumbnail']);
        }
        $thumb = '';
        $checkMove = move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetFile);
        if ($checkMove) {
            $thumb = $targetFile;
        }
        $userData['thumbnail'] = $thumb;
    }
    update('users', $userData,  $user_id);

    if (empty($user_add)) {

        $addressData = [
            'user_id'    => $user_id,
            'province'   => $province,
            'ward'       => $ward,
            'address'    => $address,
            'is_default' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $status = insert('user_addresses', $addressData);
    } else {
        $addressData = [
            'province'   => $province,
            'ward'       => $ward,
            'address'    => $address,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $id = $user_add['id'];
        $status = update(
            'user_addresses',
            $addressData,
            $id
        );
    }

    if ($status) {

        setSessionFlash('msg', 'Profile updated successfully.');
    } else {

        setSessionFlash('msg', 'Update failed.');
    }

    redirect("?module=users&action=profile&id=$user_id");
}

$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
?>
<!-- start coupon -->
<div class="wrap_cou">
    <div class="slide_cou">
        <div class="track">
            <?php foreach ($list_cou as $key => $cou): ?>
            <div class="tile_cou">
                <i class="fa-solid fa-angle-left"></i>
                <span><?php echo $cou['name']; ?></span>
                <i class="fa-solid fa-minus"></i>
                <span><?php echo $cou['description']; ?></span>
                <i class="fa-solid fa-minus"></i>
                <span><?php echo $cou['code']; ?></span>
                <i class="fa-solid fa-angle-right"></i>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="main-wrap-content">
    <form method="POST" enctype="multipart/form-data">
        <div class="profile">

            <div class="profile_left">
                <div class="profile_avatar">

                    <img id="preview-avatar" src="<?php echo !empty($user['thumbnail']) ? $user['thumbnail'] : false ?>"
                        alt="Avatar">

                    <input type="file" name="thumbnail" id="avatar" accept="image/*" hidden>

                    <label for="avatar" class="btn-avatar">
                        Change Image
                    </label>

                </div>

                <div class="profile_left_user">
                    <h3><?= $user['fullname'] ?></h3>
                    <p><i class="icons-foot fa-regular fa-envelope"></i><?= $user['email'] ?></p>
                    <p><i class="fa-solid fa-signal icons-foot"></i> <?= $user['phone'] ?></p>
                    <p><i class=" icons-foot fa-solid fa-location-pin"></i>
                        <?= !empty($user_add) ? $user_add['province'] : '' ?>
                    </p>
                </div>

            </div>


            <div class="profile_right">

                <h2>My Profile</h2>

                <div class="profile_group">
                    <label>Full Name</label>
                    <input type="text" name="fullname" value="<?= $user['fullname'] ?>">
                </div>

                <div class="profile_group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= $user['email'] ?>">
                </div>

                <div class="profile_group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= $user['phone'] ?>">
                </div>

                <div class="profile_group">
                    <label>Province</label>
                    <input type="text" name="province" value="<?= !empty($user_add) ? $user_add['province'] : '' ?>">
                </div>

                <div class="profile_group">
                    <label>Ward</label>
                    <input type="text" name="ward" value="<?= !empty($user_add) ? $user_add['ward'] : '' ?>">
                </div>

                <div class="profile_group">
                    <label>Address</label>
                    <textarea name="address"><?= !empty($user_add) ? $user_add['address'] : '' ?></textarea>
                </div>

                <div class="profile_group">
                    <label>Created At</label>
                    <input type="text" value="<?= $user['created_at'] ?>" readonly>
                </div>

                <div class="profile_btn">
                    <button type="submit" name="update_profile">
                        Update Profile
                    </button>

                    <a href="?module=users&action=change_password">
                        <button type="button">
                            Change Password
                        </button>
                    </a>
                </div>

            </div>
        </div>
    </form>
</div>
<script>
const inputAvatar = document.getElementById("avatar");
const previewAvatar = document.getElementById("preview-avatar");

inputAvatar.addEventListener("change", function() {

    const file = this.files[0];

    if (file) {

        const reader = new FileReader();

        reader.onload = function(e) {
            previewAvatar.src = e.target.result;
        };

        reader.readAsDataURL(file);

    }

});
</script>
<?php
layout('/index/footer');
?>