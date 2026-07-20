<?php
// set time zone
date_default_timezone_set('Asia/Ho_Chi_Minh');
// set sesion 
session_start();  // tạo mới 1 phiên làm việc hoăc tiếp tục 1 phiên làm việc đã tồn tại
// ob start
ob_start(); // tránh những trường hợp bì lỗi khi sử dụng làm liên quan đến header hoặc cookie

require_once 'config.php';
require_once(_PATH_URL . '/includes/connect.php');
require_once(_PATH_URL . '/includes/database.php');
require_once(_PATH_URL . '/includes/session.php');
require_once(_PATH_URL . '/includes/mailer/Exception.php');
require_once(_PATH_URL . '/includes/mailer/SMTP.php');
require_once(_PATH_URL . '/includes/mailer/PHPMailer.php');
require_once(_PATH_URL . '/includes/functions.php');


$module = _MODULES;
$action = _ACTION;

if (!empty($_GET['module'])) {
    $module = $_GET['module'];
}

if (!empty($_GET['action'])) {
    $action = $_GET['action'];
}

$path = 'modules/' . $module . '/' . $action . '.php';

if (!empty($path)) {
    if (file_exists($path)) {
        require_once $path;
    } else {
        require_once './modules/errors/404.php';
    }
} else {
    require_once './modules/errors/500.php';
}
