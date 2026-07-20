<?php
if (!defined('_CHECK')) {
    die('Invalid access');
}
layout('auth/header', 'Sign In');
if (isPost()) {
    $filter = filterData();
    $errors = [];

    //validate email
    if (empty(trim($filter['email']))) {
        $errors['email']['required'] = 'Email has not been entered';
    } else {
        //Valid email format, check if email exists
        if (!validateEmail(trim($filter['email']))) {
            $errors['email']['isEmail'] = 'Invalid email format';
        }
    }

    //validate password
    if (empty(trim($filter['password']))) {
        $errors['password']['required'] = 'Password has not been entered';
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

    if (empty($errors)) {
        //Check data
        $email = $filter['email'];
        $password = $filter['password'];
        // check email
        $checkEmail = getOne("SELECT * FROM users WHERE email = '$email'");
        if (!empty($checkEmail)) {
            if ($checkEmail['status'] != 2) {
                setSessionFlash('msg', 'Account has not been activated');
                setSessionFlash('msg_type', 'red');
                redirect('?module=auth&action=login');
            }
            if (!empty($password)) {
                $checkStatus = password_verify($password, $checkEmail['password']);
                if ($checkStatus) {
                    //Single device login restriction
                    $user_id = $checkEmail['id'];
                    $checkAlrealdy = getRows("SELECT * FROM login_token WHERE user_id =$user_id");
                    // if ($checkAlrealdy > 0) {
                    //     setSessionFlash('msg', 'Account is currently logged in elsewhere, please try again later');
                    //     setSessionFlash('msg_type', 'danger');
                    //     redirect('?module=auth&action=login');
                    // } else {
                    //Create token and insert into login_token table
                    delete('login_token', "user_id=$user_id");
                    $token = sha1(uniqid() . time());
                    //Assign token_login to session
                    setSession('token_login', $token);
                    $data = [
                        'token' => $token,
                        'created_at' => date('Y-m-d H:i:s'),
                        'user_id' => $checkEmail['id']
                    ];
                    $insert_Token = insert('login_token', $data);
                    if ($insert_Token) {
                        //Redirect to dashboard
                        setSession('user_id', $checkEmail['id']);
                        redirect('?module=dashboard&action=index');
                    } else {
                        setSessionFlash('msg', 'Login failed, please check your credentials');
                        setSessionFlash('msg_type', 'red');
                    }
                }
            } else {
                setSessionFlash('msg', 'Login failed, please check your credentials');
                setSessionFlash('msg_type', 'red');
            }
        }
    } else {
        setSessionFlash('msg', 'Login failed, please check your credentials');
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
            <h2>Sign In</h2>
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
                                                                echo oldData($olddata, 'email');
                                                            } ?>" name="email" required autocomplete="email">
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
                <a href="<?php echo _HOST_URL . "/?module=auth&action=forgotpass" ?>" class="forgot-password">Forgot
                    password?</a>
            </div>
            <button type="submit" class="login-btn material-btn">
                <div class="btn-ripple"></div>
                <span class="btn-text">Sign In</span>
                <div class="btn-loader">
                    <svg class="loader-circle" viewBox="0 0 50 50">
                        <circle class="loader-path" cx="25" cy="25" r="12" fill="none" stroke="currentColor"
                            stroke-width="3" />
                    </svg>
                </div>
            </button>
        </form>

        <div class="signup-link">
            <p>Don't have an account? <a href="<?php echo _HOST_URL . "/?module=auth&action=register" ?>"
                    class="create-account">Sign Up</a></p>
        </div>


    </div>
</div>
<?php
layout('auth/footer');
?>