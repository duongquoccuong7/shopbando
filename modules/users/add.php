<?php
if (!defined('_CHECK')) {
    die('Invalid access');
}
layout('/dashboard/header', 'Add New User');
if (!empty($_POST)) {
    $filter = filterData();
    $errors = [];

    // validate fullname
    if (empty(trim($filter['fullname']))) {
        $errors['fullname']['required'] = 'Full name is required';
    } else {
        if (strlen(trim($filter['fullname'])) < 5) {
            $errors['fullname']['length'] = 'Full name must be greater than 5 characters';
        }
    }

    // Validate Email
    if (empty(trim($filter['email']))) {
        $errors['email']['required'] = 'Email is required';
    } else {
        if (!validateEmail($filter['email'])) {
            $errors['email']['isEmail'] = 'Invalid email format';
        } else {
            $email = $filter['email'];
            $checkEmail = getRows("SELECT * FROM users WHERE email ='$email' ");
            if ($checkEmail > 0) {
                $errors['email']['check'] = 'Email already exists';
            }
        }
    }

    // validate phone
    if (empty(trim($filter['phone']))) {
        $errors['phone']['required'] = 'Phone number is required';
    } else {
        if (!isPhone($filter['phone'])) {
            $errors['phone']['isPhone'] = 'Invalid phone number format';
        }
    }

    // validate password
    if (empty(trim($filter['password']))) {
        $errors['password']['required'] = 'Password is required';
    } else {
        $password = trim($filter['password']);
        if (strlen(trim($filter['password'])) < 2) {
            $errors['password']['length'] = 'Password must be at least 8 characters';
        }
        // if (preg_match('/\s/', $password)) {
        //     $errors['password']['space'] = 'Password must not contain spaces';
        // }

        // if (!preg_match('/[0-9]/', $password)) {
        //     $errors['password']['number'] = 'Password must contain at least 1 number';
        // }
        // if (!preg_match('/[\W_]/', $password)) {
        //     $errors['password']['special'] = 'Password must contain at least 1 special character';
        // }
    }

    // validate confirm password
    if (empty(trim($filter['confirm_password']))) {
        $errors['confirm_password']['required'] = 'Please re-enter password';
    } else {
        if (trim($filter['password']) !== trim($filter['confirm_password'])) {
            $errors['confirm_password']['check'] = 'Passwords do not match';
        }
    }

    if (empty($errors)) {
        $activeToken = sha1(uniqid() . time());
        $insertdata = [
            'fullname' => $filter['fullname'],
            'email' => $filter['email'],
            'phone' => $filter['phone'],
            'role' => $filter['role'],
            'password' => password_hash($filter['password'], PASSWORD_DEFAULT), // hash password
            'created_at' => date('Y:m:d H:i:s'),
            'active_token' => $activeToken,
            'status' => 1,
        ];
        if (insert('users', $insertdata)) {
            $id = $conn->lastInsertId();
            $addressData = [
                'user_id' => $id,
                'created_at' => date('Y:m:d H:i:s'),
            ];
            insert('user_addresses', $addressData);
            // Build and send email
            $emailTo = $filter['email'];
            // Email subject
            $subject = 'Account Activation';
            // Email content
            $content = 'Congratulations! You have successfully registered an account.<br>';
            $content .= 'To activate your account, please click on the link below: <br>';
            $content .= _HOST_URL . '/?module=auth&action=active&token=' . $activeToken . '<br>';

            // Send email
            $mailStatus = sendMail($emailTo, $subject, $content);
            if ($mailStatus) {
                setSessionFlash('msg', 'Account added successfully, please activate your account');
                setSessionFlash('msg_type', 'green');
                redirect('?module=users&action=add');
                exit();
            } else {
                setSessionFlash('msg', 'Account created, but sending email failed');
                setSessionFlash('msg_type', 'red');
            }
        } else {
            setSessionFlash('msg', 'Failed to add account, please try again');
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
?>

<!-- start add user-->
<div class="main-wrap">
    <?php
    layout('/dashboard/sidebar');
    ?>
    <!-- Add product discount code -->
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

                    <!-- pass -->
                    <div class="product-input">
                        <label for="password" class="label-input">
                            <?php
                            echo 'Password';
                            if (!empty($errorsArr['password'])) {
                                echo ' - ' . formError($errorsArr, 'password');
                            }
                            ?>
                        </label><br>
                        <input type="text" id="password" name="password" placeholder=" Password ...">

                    </div>
                    <!-- confirm pass -->
                    <div class="product-input">
                        <label for="confirm_password" class="label-input">
                            <?php
                            echo 'Confirm Password';
                            if (!empty($errorsArr['confirm_password'])) {
                                echo ' - ' . formError($errorsArr, 'confirm_password');
                            }
                            ?>
                        </label><br>
                        <input type="text" id="confirm_password" name="confirm_password"
                            placeholder=" Confirm password ...">

                    </div>
                </div>
                <!-- product right -->
                <div class="add-product-right">
                    <!-- Role -->
                    <div class="product-input"><label for="role" class="label-input">Role
                        </label><br>
                        <select name="role" class="edit-select" id="role">
                            <option value="admin">Admin</option>
                            <option value="user">
                                User
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
    // Function to convert text into slug
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