<?php
if (!defined('_CHECK')) {
    die('Truy cập không hợp lệ');
}
layout('/auth/header', 'Sign In');
$filter = filterData('GET');
$success = '';
if (!empty($filter['token'])) {
    $token = $filter['token'];
    $checkToken = getOne("SELECT * FROM users WHERE active_token = '$token'");
    $success = 1;
} else {
    $success = '';
}
?>


<!-- start form login -->
<div class="login-container">
    <div class="login-card" style="display: flex;justify-content: center;align-items: center;">
        <form>
            <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                <p style="font-size: 1.4rem; text-transform: uppercase;" class="lead fw-normal mb-0 me-3">
                    <?php if ($success == 1) {
                        //thực hiện update dữ liệu
                        $data = [
                            'status' => 2,
                            'active_token' => null,
                            'updated_at' => date('Y:m:d H:i:s')
                        ];
                        echo 'Kích hoạt tài khoản thành công!';
                        $condition = $checkToken['id'];
                        update('users', $data, $condition);
                    } else {
                        echo 'Kích hoạt tài khoản không thành công, đường link không tồn tại';
                    } ?>
                </p>
            </div>
            <?php if ($success == 1) {
            ?>
                <p class="small fw-bold mt-2 pt-1 mb-0"><a style="text-decoration: none; font-size: 1.2rem;"
                        href="<?php echo _HOST_URL; ?>?module=auth&action=login" class="link-button">Đăng nhập
                        ngay</a></p>
            <?php
            } ?>
        </form>
    </div>
</div>
<?php
layout('auth/footer');
?>