<?php
if (!defined('_CHECK')) {
    die('Invalid access');
}
layout('/dashboard/header', 'Add New User');
$getData = filterData('GET');
$data_id = $getData['id'];
$Data = getOne("SELECT * FROM users WHERE id=$data_id");
if (isPost()) {
    $filter = filterData();
    $errors = [];

    // Validate fullname
    if (empty(trim($filter['fullname']))) {
        $errors['fullname']['required'] = 'Full name is required';
    }

    // Validate Email
    if (empty(trim($filter['email']))) {
        $errors['email']['required'] = 'Email is required';
    } else {
        if (!validateEmail($filter['email'])) {
            $errors['email']['isEmail'] = 'Invalid email format';
        }
    }

    // Validate phone
    if (empty(trim($filter['phone']))) {
        $errors['phone']['required'] = 'Phone number is required';
    } else {
        if (!isPhone($filter['phone'])) {
            $errors['phone']['isPhone'] = 'Invalid phone number format';
        }
    }
    if (empty($errors)) {
        $activeToken = sha1(uniqid() . time());
        if ($filter['status'] == 2) {
            $dataupdate = [
                'fullname' => $filter['fullname'],
                'email' => $filter['email'],
                'phone' => $filter['phone'],
                'role' => $filter['role'],
                'updated_at' => date('Y:m:d H:i:s'),
                'active_token' => null,
                'status' => $filter['status'],
            ];
        }
        if ($filter['status'] == 1 || $filter['status'] == 3) {
            $dataupdate = [
                'fullname' => $filter['fullname'],
                'email' => $filter['email'],
                'phone' => $filter['phone'],
                'role' => $filter['role'],
                'updated_at' => date('Y:m:d H:i:s'),
                'status' => $filter['status'],
            ];
        }
        $updatestatus = update('users', $dataupdate, $data_id);
        if ($updatestatus && $filter['status'] == 2) {
            // Build email sending
            $emailTo = $filter['email'];
            // Email subject
            $subject = 'Account Activation Successful';
            // Email content
            $content = 'Your account has been successfully activated!';
            // Send email
            sendMail($emailTo, $subject, $content);
            // Create session flash to prevent data loss
            setSessionFlash('msg', 'Account has been activated');
            setSessionFlash('msg_type', 'green');
            exit;
        } else if ($updatestatus) {
            setSessionFlash('msg', 'Account details updated successfully');
            setSessionFlash('msg_type', 'green');
        } else {
            setSessionFlash('msg', 'Update failed');
            setSessionFlash('msg_type', 'red');
        }
    } else {
        $mess = 'Invalid data, please check again!';
        $color = 'red';
        setSessionFlash('oldData', $filter);
        setSessionFlash('errors', $errors);
    }
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$olddata = getSessionFlash('oldData');
$errorsArr = getSessionFlash('errors');
if (!empty($Data)) {
    $olddata = $Data;
}
?>


<!-- start add user-->
<div class="main-wrap">
    <?php
    layout('/dashboard/sidebar');
    ?>
    <!-- Add user -->
    <div class="content-menu">
        <?php if (!empty($msg) && !empty($msg_type)) : ?>
        <div class="anoun-mess">
            <?php getMess($msg, $msg_type); ?>
        </div>
        <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="add-product">
                <!-- product left -->
                <div class="add-product-left">
                    <!-- fullname -->
                    <div class="product-input">
                        <label for="fullname" class="label-input">
                            <?php
                            echo 'Full Name';
                            if (!empty($errorsArr['fullname'])) {
                                echo ' - ' . formError($errorsArr, 'fullname');
                            }
                            ?>
                        </label><br>
                        <input type="text" id="fullname" name="fullname" placeholder=" Name ..." value="<?php
                                                                                                        if (!empty($olddata['fullname'])) {
                                                                                                            echo oldData($olddata, 'fullname');
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
                                                                                                        echo oldData($olddata, 'email');
                                                                                                    }   ?>">

                    </div>
                    <!-- phone -->
                    <div class="product-input">
                        <label for="phone" class="label-input">
                            <?php
                            echo 'Phone Number';
                            if (!empty($errorsArr['phone'])) {
                                echo ' - ' . formError($errorsArr, 'phone');
                            }
                            ?>
                        </label><br>
                        <input type="text" id="phone" name="phone" placeholder=" Phone number ..." value="<?php
                                                                                                            if (!empty($olddata['phone'])) {
                                                                                                                echo oldData($olddata, 'phone');
                                                                                                            }   ?>">

                    </div>
                </div>
                <!-- product right -->
                <div class="add-product-right">
                    <!-- Role -->
                    <div class="product-input"><label for="role" class="label-input">Role
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
                        <label for="status" class="label-input">Status</label><br>
                        <select name="status" class="edit-select" id="status">

                            <?php if (isset($olddata['status']) && $olddata['status'] == 1): ?>
                            <option value="1" <?= $olddata['status'] == 1 ? 'selected' : '' ?>>
                                Inactive
                            </option>
                            <?php endif; ?>

                            <option value="2" <?= $olddata['status'] == 2 ? 'selected' : '' ?>>
                                Active
                            </option>

                            <option value="3" <?= $olddata['status'] == 3 ? 'selected' : '' ?>>
                                Blocked
                            </option>

                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-submit">
                Submit
            </button>
        </form>
    </div>
    <!-- end -->
</div>
<!-- end main -->
<script>
// Function to convert text to slug
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

const inputs = [
    document.getElementById('discount_value'),
    document.getElementById('min_order'),
    document.getElementById('max_discount')
];

inputs.forEach(input => {
    input.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    });
});
</script>
<?php
layout('dashboard/footer');
?>