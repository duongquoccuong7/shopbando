<?php
if (!defined('_CHECK')) {
    die('Invalid access');
}
layout('auth/header', 'Register');
if (!empty($_POST)) {
    $filter = filterData();
    $errors = [];

    //validate fullname
    if (empty(trim($filter['fullname']))) {
        $errors['fullname']['required'] = 'Full name is required';
    }

    //Validate Email
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

    //validate phone
    if (empty(trim($filter['phone']))) {
        $errors['phone']['required'] = 'Phone number is required';
    } else {
        if (!isPhone($filter['phone'])) {
            $errors['phone']['isPhone'] = 'Invalid phone number format';
        }
    }

    //validate password
    if (empty(trim($filter['password']))) {
        $errors['password']['required'] = 'Password is required';
    } else {
        $password = trim($filter['password']);
        if (strlen(trim($filter['password'])) < 8) {
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

    //validate confirm password
    if (empty(trim($filter['confirm_password']))) {
        $errors['confirm_password']['required'] = 'Please re-enter your password';
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
            'role' => 'admin',
            'password' => password_hash($filter['password'], PASSWORD_DEFAULT), //hash password
            'created_at' => date('Y:m:d H:i:s'),
            'active_token' => $activeToken,
            'status' => 1,
        ];
        $rel = insert('users', $insertdata);

        if ($rel) {
            //Construct email sending
            $emailTo = $filter['email'];
            //Email subject
            $subject = 'Activate your online learning account';
            //Email content
            $content = 'Congratulations on successfully registering your account!<br>';
            $content .= 'To activate your account, please click on the link below: <br>';
            $content .= _HOST_URL . '/?module=auth&action=active&token=' . $activeToken . '<br>';
            //Send email
            sendMail($emailTo, $subject, $content);
            //Set flash session to store data
            setSessionFlash('msg', 'Registration successful, please activate your account');
            setSessionFlash('msg_type', 'green');
        } else {
            setSessionFlash('msg', 'Registration failed, please check again');
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
$oldData = getSessionFlash('oldData');
$errorsArr = getSessionFlash('errors');
?>


<!-- start form login -->
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="material-logo">
                <div class="logo-layers">
                    <div class="layer layer-1"></div>
                    <div class="layer layer-2"></div>
                    <div class="layer layer-3"></div>
                </div>
            </div>
            <h2>Register</h2>
            <?php
            if (!empty($msg)) {
                getMess($msg, $msg_type);
            }
            ?>
        </div>
        <form method="POST" action="" enctype="multipart/form-data" class="login-form" id="loginForm" novalidate>
            <!-- start fullname -->
            <div class="form-group">
                <div class="input-wrapper">
                    <input type="text" id="fullname" name="fullname"
                        value="<?php echo !empty($oldData['fullname']) ? $oldData['fullname'] : null; ?>" required
                        autocomplete="fullname">
                    <label for="fullname">Full Name</label>
                    <div class="input-line"></div>
                    <div class="ripple-container"></div>
                </div>
                <span class="error-message" id="FullnameError"></span>
            </div>
            <!-- end fullname -->
            <!-- start mail -->
            <div class="form-group">
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" value="<?php if (!empty($oldData['email'])) {
                                                                            echo oldData($oldData, 'email');
                                                                        } ?>" required autocomplete="email">
                    <label for="email">Email</label>
                    <div class="input-line"></div>
                    <div class="ripple-container"></div>
                </div>
                <span class="error-message" id="EmailError"></span>
            </div>
            <!-- end mail -->
            <!-- start phone -->
            <div class="form-group">
                <div class="input-wrapper ">
                    <input type="text" id="phone" name="phone" value="<?php if (!empty($oldData['phone'])) {
                                                                            echo oldData($oldData, 'phone');
                                                                        } ?>" required autocomplete="phone">
                    <label for="phone">Phone Number</label>
                    <div class="input-line"></div>
                    <div class="ripple-container"></div>
                </div>
                <span class="error-message" id="phoneError"></span>
            </div>
            <!-- end phone-->
            <!-- start password -->
            <div class="form-group">
                <div class="input-wrapper password-wrapper">
                    <input type="password" id="password" name="password" value="" required
                        autocomplete="current-password">
                    <label for="password">Password</label>
                    <div class="input-line"></div>
                    <button type="button" class="password-toggle" id="passwordToggle"
                        aria-label="Toggle password visibility">
                        <div class="toggle-ripple"></div>
                        <span class="toggle-icon"></span>
                    </button>
                    <div class="ripple-container"></div>
                </div>
                <span class="error-message" id="passwordError"></span>
            </div>
            <!-- end password -->
            <!-- start confirm password -->
            <div class="form-group">
                <div class="input-wrapper password-wrapper">
                    <input type="password" id="confirm_password" name="confirm_password" value="" required
                        autocomplete="current-password">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-line"></div>
                    <button type="button" class="password-toggle" id="confirmPasswordToggle"
                        aria-label="Toggle password visibility">
                        <div class="toggle-ripple"></div>
                        <span class="toggle-icon"></span>
                    </button>
                    <div class="ripple-container"></div>
                </div>
                <span class="error-message" id="confirmPasswordError"></span>
            </div>
            <!-- end confirm password -->
            <button type="submit" class="login-btn material-btn">
                <div class="btn-ripple"></div>
                <span class="btn-text">REGISTER</span>
                <div class="btn-loader">
                    <svg class="loader-circle" viewBox="0 0 50 50">
                        <circle class="loader-path" cx="25" cy="25" r="12" fill="none" stroke="currentColor"
                            stroke-width="3" />
                    </svg>
                </div>
            </button>
        </form>

        <div class="signup-link">
            <p>Already have an account? <a href="<?php echo _HOST_URL . "/?module=auth&action=login" ?>"
                    class="create-account">Sign in</a></p>
        </div>
    </div>
</div>
<?php
layout('auth/footer');
?>