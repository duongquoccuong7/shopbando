<?php
if (isLogin()) {
    $token = getSession('token_login');
    $removeToken = delete('login_token', "token='$token'");
    if ($removeToken) {
        removeSession('token_login');
        removeSession('user_id');
        setSessionFlash('msg', 'Đăng xuất thành công');
        setSessionFlash('msg_type', 'green');
        redirect('?module=auth&action=login');
    } else {
        setSessionFlash('msg', 'Lỗi hệ thống xin vui lòng thử lại sau');
        setSessionFlash('msg_type', 'red');
    }
} else {
    setSessionFlash('msg', 'Lỗi hệ thống xin vui lòng thử lại sau');
    setSessionFlash('msg_type', 'red');
    redirect('?module=auth&action=login');
}
