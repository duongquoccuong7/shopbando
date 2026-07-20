<?php
if (!defined('_CHECK')) {
    die('Truy cập không hợp lệ');
}
layout('/auth/header', 'Đặt lại mật khẩu');
$filterGet = filterData('GET');
if (!empty($filterGet['token'])) {
    $tokenReset = $filterGet['token'];
}
if (!empty($tokenReset)) {
    //Check token có chính xác hay không
    $checkToken = getOne("SELECT * FROM users WHERE forget_token = '$tokenReset'");

    if (!empty($checkToken)) {
        if (isPost()) {
            $filter = filterData();
            $errors = [];

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
            if (empty($filter['confirm_password'])) {
                $errors['confirm_password']['required'] = 'Nhập lại mật khẩu';
            } else {
                if ($filter['password'] !== trim($filter['confirm_password'])) {
                    $errors['confirm_password']['required'] = 'Mật khẩu không trùng khớp';
                }
            }
            if (empty($errors)) {
                $password = password_hash($filter['password'], PASSWORD_DEFAULT);
                $data = [
                    'password' => $password,
                    'forget_token' => null,
                    'updated_at' => date('Y:m:d H:i:s')
                ];
                $condition = $checkToken['id'];
                $updateStatus = update('users', $data,  $condition);
                if ($updateStatus) {
                    //Gửi mail//Xây dựng gửi mail
                    $emailTo =  $checkToken['email'];
                    //Chủ đề mail
                    $subject = 'Đổi mật khẩu tài khoản thành công!';
                    //Nôi dung mail
                    $content = 'Chúc mừng bạn đổi mật khẩu thành công mật khẩu<br>';
                    $content = 'Nếu không phải bạn thao tác đỏi mật khẩu thì hãy liên hệ ngay admin.<br>';

                    //Gửi mail
                    sendMail($emailTo, $subject, $content);
                    //Tạo session nhăm lưu lại để tránh bị mất dữ liệu
                    setSessionFlash('msg', 'Đổi mật khẩu không thành công. ');
                    setSessionFlash('msg_type', 'green');
                    redirect('?module=auth&action=login');
                } else {
                    setSessionFlash('msg', 'Đã có lỗi xảy ra, vui lòng kiểm tra lại sau');
                    setSessionFlash('msg_type', 'red');
                }
            } else {
                setSessionFlash('msg', 'Đổi mật khẩu không thành công, vui lòng kiểm tra lại dữ liệu');
                setSessionFlash('msg_type', 'red');
                setSessionFlash('old_data', $filter);
                setSessionFlash('errors', $errors);
            }
        }
    } else {
        redirect('?module=errors&action=404');
    }
} else {
    redirect('?module=errors&action=404');
}

$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$olddata = getSessionFlash('old_data');
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
            <h2>Đăt lại mật khẩu</h2>
            <?php if (!empty($msg)) {
                getMess($msg, $msg_type);
            }
            ?>
        </div>
        <form class="login-form" id="loginForm" novalidate method="POST" action="" enctype="multipart/form-data">
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
                <span class="btn-text">Gửi</span>
                <div class="btn-loader">
                    <svg class="loader-circle" viewBox="0 0 50 50">
                        <circle class="loader-path" cx="25" cy="25" r="12" fill="none" stroke="currentColor"
                            stroke-width="3" />
                    </svg>
                </div>
            </button>
        </form>
    </div>
</div>
<?php
layout('auth/footer');
?>