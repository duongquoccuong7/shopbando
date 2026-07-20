<?php
if (!defined('_CHECK')) {
    die('Truy cập không hợp lệ');
}
layout('/auth/header', 'Đăng ký tài khoản');
if (!empty($_POST)) {
    $filter = filterData();
    $errors = [];

    //validate fullname
    if (empty(trim($filter['fullname']))) {
        $errors['fullname']['required'] = 'Họ và tên chưa được nhập';
    }

    //Validate Email
    if (empty(trim($filter['email']))) {
        $errors['email']['required'] = 'Email chưa được nhập';
    } else {
        if (!validateEmail($filter['email'])) {
            $errors['email']['isEmail'] = 'Email không đúng định dạng';
        } else {
            $email = $filter['email'];
            $checkEmail = getRows("SELECT * FROM users WHERE email ='$email' ");
            if ($checkEmail > 0) {
                $errors['email']['check'] = 'Email đã tồn tại';
            }
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

    //validate password
    if (empty(trim($filter['password']))) {
        $errors['password']['required'] = 'Mật khẩu chưa được nhập';
    } else {
        $password = trim($filter['password']);
        if (strlen(trim($filter['password'])) < 2) {
            $errors['password']['length'] = 'Mật khẩu phải ít nhất 8 ký tự';
        }
        // if (preg_match('/\s/', $password)) {

        //     $errors['password']['space'] = 'Mật khẩu không được chứa khoảng trắng';
        // }

        // if (!preg_match('/[0-9]/', $password)) {

        //     $errors['password']['number'] = 'Mật khẩu phải chứa ít nhất 1 số';
        // }
        // if (!preg_match('/[\W_]/', $password)) {

        //     $errors['password']['special'] = 'Mật khẩu phải chứa ít nhất 1 ký tự đặc biệt';
        // }
    }

    //validate confirm password
    if (empty(trim($filter['confirm_password']))) {
        $errors['confirm_password']['required'] = ' Vui lòng nhập lại mật khẩu';
    } else {
        if (trim($filter['password']) !== trim($filter['confirm_password'])) {
            $errors['confirm_password']['check'] = 'Mật khẩu Không trùng khớp';
        }
    }

    if (empty($errors)) {
        $activeToken = sha1(uniqid() . time());
        $insertdata = [
            'fullname' => $filter['fullname'],
            'email' => $filter['email'],
            'phone' => $filter['phone'],
            'role' => 'admin',
            'password' => password_hash($filter['password'], PASSWORD_DEFAULT), //mã hóa password
            'created_at' => date('Y:m:d H:i:s'),
            'active_token' => $activeToken,
            'status' => 1,
        ];
        $rel = insert('users', $insertdata);

        if ($rel) {
            //Xây dựng gửi mail
            $emailTo = $filter['email'];
            //Chủ đề mail
            $subject = 'Kích hoạt tài khoản học online';
            //Nôi dung mail
            $content = 'Chúc mừng bạn đăng ký thành công tài khoản!<br>';
            $content .= 'Để kích hoạt tài khoản , bạn hãy click  vào đường link bên dưới: <br>';
            $content .= _HOST_URL . '/?module=auth&action=active&token=' . $activeToken . '<br>';
            //Gửi mail
            sendMail($emailTo, $subject, $content);
            //Tạo session nhăm lưu lại để tránh bị mất dữ liệu
            setSessionFlash('msg', 'Đăng ký thành công, vui lòng kích hoạt tài khoản');
            setSessionFlash('msg_type', 'green');
        } else {
            setSessionFlash('msg', 'Đăng ký không thành công, vui lòng kiểm tra lại');
            setSessionFlash('msg_type', 'red');
        }
    } else {
        $mess = 'Dữ liệu không hợp lệ ,hãy kiểm tra lại!';
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
            <h2>Đăng ký</h2>
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
                        value="<?php echo !empty($oldData['fullname']) ?  $oldData['fullname'] : null; ?>" required
                        autocomplete="fullname">
                    <label for="fullname">Họ & Tên</label>
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
                                                                            echo  oldData($oldData, 'email');
                                                                        }   ?>" required autocomplete="email">
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
                                                                            echo  oldData($oldData, 'phone');
                                                                        }   ?>" required autocomplete="phone">
                    <label for="phone">Số điện thoại</label>
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
                    <label for="password">Mật khẩu</label>
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
                    <label for="confirm_password">Xác nhận mật khẩu</label>
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
                <span class="btn-text">ĐĂNG KÝ</span>
                <div class="btn-loader">
                    <svg class="loader-circle" viewBox="0 0 50 50">
                        <circle class="loader-path" cx="25" cy="25" r="12" fill="none" stroke="currentColor"
                            stroke-width="3" />
                    </svg>
                </div>
            </button>
        </form>

        <div class="signup-link">
            <p>Đã có tài khoản? <a href="<?php echo _HOST_URL . "/?module=auth&action=login" ?>"
                    class="create-account">Đăng
                    nhập</a></p>
        </div>
    </div>
</div>
<?php
layout('auth/footer');
?>