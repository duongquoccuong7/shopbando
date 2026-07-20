<?php
if (!defined('_CHECK')) {
    die('Truy cập không hợp lệ');
}
layout('/auth/header', 'Đăng nhập');
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
    if (empty($errors)) {
        //Kiẻm tra dữ liệu
        $email = $filter['email'];
        $password = $filter['password'];
        // kiểm tra email
        $checkEmail = getOne("SELECT * FROM users WHERE email = '$email'");
        if (!empty($checkEmail)) {
            if ($checkEmail['status'] != 2) {
                setSessionFlash('msg', 'Tài khoản chưa được kích hoạt');
                setSessionFlash('msg_type', 'red');
                redirect('?module=auth&action=login');
            }
            if (!empty($password)) {
                $checkStatus = password_verify($password, $checkEmail['password']);
                if ($checkStatus) {
                    //TK login 1 nơi
                    $user_id = $checkEmail['id'];
                    $checkAlrealdy = getRows("SELECT * FROM login_token WHERE user_id =$user_id");
                    // if ($checkAlrealdy > 0) {
                    //     // setSessionFlash('msg', 'Tài khoản đang được đăng nhập tại nơi khác, vui lòng thử lại sau');
                    //     // setSessionFlash('msg_type', 'danger');
                    //     // redirect('?module=auth&action=login');
                    // } else {
                    //Tạo token và insert vào bảng token_login
                    delete('login_token', "user_id=$user_id");
                    $token = sha1(uniqid() . time());
                    //Gán token_login lên session
                    setSession('token_login', $token);
                    $data = [
                        'token' => $token,
                        'created_at' => date('Y-m-d H:i:s'),
                        'user_id' => $checkEmail['id']
                    ];
                    $insert_Token = insert('login_token', $data);
                    if ($insert_Token) {
                        //Điều hướng đến dashboard
                        setSession('user_id', $checkEmail['id']);
                        redirect('?module=dashboard&action=index');
                    } else {
                        setSessionFlash('msg', 'Đăng nhập không thành công, vui lòng kiểm tra lại dữ liệu');
                        setSessionFlash('msg_type', 'red');
                    }
                }
            } else {
                setSessionFlash('msg', 'Đăng nhập không thành công, vui lòng kiểm tra lại dữ liệu');
                setSessionFlash('msg_type', 'red');
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
            <h2>Đăng nhập</h2>
            <?php
            if (!empty($msg)) {
                getMess($msg, $msg_type);
            }
            ?>
        </div>

        <form class="login-form" id="loginForm" novalidate method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <div class="input-wrapper">
                    <input type="email" id="email" value="<?php
                                                            if (!empty($olddata['email'])) {
                                                                echo  oldData($olddata, 'email');
                                                            }   ?>" name="email" required autocomplete="email">
                    <label for="email">Email</label>
                    <div class="input-line"></div>
                    <div class="ripple-container"></div>
                </div>
                <span class="error-message" id="emailError"></span>
            </div>

            <div class="form-group">
                <div class="input-wrapper password-wrapper">
                    <input type="password" id="password" name="password" required autocomplete="current-password">
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

            <div class="form-options">
                <a href="<?php echo _HOST_URL . "/?module=auth&action=forgotpass" ?>" class="forgot-password">Quên mật
                    khẩu?</a>
            </div>
            <button type="submit" class="login-btn material-btn">
                <div class="btn-ripple"></div>
                <span class="btn-text">Đăng nhập</span>
                <div class="btn-loader">
                    <svg class="loader-circle" viewBox="0 0 50 50">
                        <circle class="loader-path" cx="25" cy="25" r="12" fill="none" stroke="currentColor"
                            stroke-width="3" />
                    </svg>
                </div>
            </button>
        </form>

        <div class="signup-link">
            <p>Bạn chưa có tài khoản? <a href="<?php echo _HOST_URL . "/?module=auth&action=register" ?>"
                    class="create-account">Đăng ký</a></p>
        </div>


    </div>
</div>
<?php
layout('auth/footer');
?>