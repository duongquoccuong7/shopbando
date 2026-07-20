<?php
const _CHECK = true; // kiểm tra xem đăng nhập truy cập có hợp lệ không

const _MODULES = 'dashboard';
const _ACTION  = 'index';

// khai báo database 
const _HOST = 'localhost';
const _DB = 'websiteshoes';
const _USER = 'root';
const _PASS = '';
const _DRIVER = 'mysql';

//debug error
const  _DEBUG = true; // có lỗi thì  hiện ra 

// thiết  lập host
define('_HOST_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/shopbando');
define('_HOST_URL_TEMPLATES', _HOST_URL . '/templates');

//  thiết  lập path
define('_PATH_URL', __DIR__);
define('_PATH_URL_TEMPLATES', _PATH_URL . '/templates');
