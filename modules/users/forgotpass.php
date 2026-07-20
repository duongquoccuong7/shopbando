<?php
if (!defined('_CHECK')) {
    die('Truy cập không hợp lệ');
}
layout('/auth/header', 'Quên mật khẩu');
if (isPost()) {
    $filter = filterData();
    $errors = [];
    //validate email
    if (empty(trim($filter['email']))) {
        $errors['email']['required'] = 'Email chưa nhập';
    } else {
        //Đúng định dạng email , emaill này đã tồn tại chưa
        if (!validateEmail(trim($filter['email']))) {
            $errors['email']['isEmail'] = 'Email không đúng định dạng';
        }
    }
    if (empty($errors)) {
        //Xử lý và gửi mail
        if (!empty($filter['email'])) {
            $email = $filter['email'];
            //Check mail
            $checkEmail = getOne("SELECT *FROM users WHERE email ='$email'");
            if (!empty($checkEmail)) { {
                } //Update forgot_token vào bảng users
                $forget_token = sha1(uniqid() . time());
                $data = [
                    'forget_token' => $forget_token,
                ];
                $condition = $checkEmail['id'];
                $updateStatus =  update('users', $data, $condition);
                if ($updateStatus) {

                    //Xây dựng gửi mail
                    $emailTo =  $email;
                    //Chủ đề mail
                    $subject = 'Quên mật khẩu tài khoản';
                    //Nôi dung mail
                    $content = 'Reset mật khẩu hệ thống!<br>';
                    $content = 'Bạn đang yêu cầu reset lại mật khẩu.<br>';
                    $content .= 'Để thay đổi mật khẩu tài khoản , bạn hãy click  vào đường link bên dưới: <br>';
                    $content .= _HOST_URL . '/?module=users&action=reset&token=' . $forget_token . '<br>';

                    //Gửi mail
                    sendMail($emailTo, $subject, $content);
                    //Tạo session nhăm lưu lại để tránh bị mất dữ liệu
                    setSessionFlash('msg', 'Quên mật khẩu tài khoản thành công vui lòng kiểm tra Email.');
                    setSessionFlash('msg_type', 'green');
                    redirect('?module=users&action=forgotpass');
                    exit;
                } else {
                    setSessionFlash('msg', 'Đã có lỗi xảy ra, vui lòng thử lại sau.');
                    setSessionFlash('msg_type', 'red');
                }
            }
        }
    } else {
        setSessionFlash('msg', 'Đăng nhập không thành công, vui lòng kiểm tra lại dữ liệu');
        setSessionFlash('msg_type', 'red');
        setSessionFlash('old_data', $filter);
        setSessionFlash('errors', $errors);
    }
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
            <h2>Quên mật khẩu</h2>
            <?php if (!empty($msg)) {
                getMess($msg, $msg_type);
            }
            ?>
        </div>
        <form class="login-form" id="loginForm" novalidate method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" required>
                    <label for="email">Email</label>
                    <div class="input-line"></div>
                    <div class="ripple-container"></div>
                </div>
                <span class="error-message" id="emailError"></span>
            </div>
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