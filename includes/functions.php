<?php

// library Laravel
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Hàm layout dùng cho Auth login , register , forgotpass, active , changepass
function layout($name, $title = '')
{
    if (file_exists(_PATH_URL_TEMPLATES . '/layouts/' . $name . '.php')) {
        require_once(_PATH_URL_TEMPLATES . '/layouts/' . $name . '.php');
    }
}

// Hàm hỗ trợ gửi mail, thư viện php mailer
function sendMail($emailTo, $subject, $content)
{
    //Import PHPMailer classes into the global namespace
    //These must be at the top of your script, not inside a function
    //Create an instance; passing `true` enables exceptions


    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'cuongduong11072002@gmail.com';                     //SMTP username
        $mail->Password   = 'okck brdz lrqk idpm';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        //Recipients
        $mail->setFrom('cuongduong11072002@gmail.com', 'CUONG');
        $mail->addAddress($emailTo);     //Add a recipient
        //Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';                               //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $content;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];
        return $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

//Kiểm tra phương thức post

function isPost()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        return true;
    }
    return false;
}

//Kiểm tra phương thức get
function isGet()
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        return true;
    }
    return false;
}


//Hàm lọc dữ liệu
function filterData($method = '')
{
    $filterArr = [];
    if (empty($method)) {
        if (isGet()) {
            if (!empty($_GET)) {
                foreach ($_GET as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        //xử lý dữ liêu là dạng mảng
                        $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        //xử lý dữ liệu không phải dạng mảng
                        $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        }
        if (isPost()) {
            if (!empty($_POST)) {

                foreach ($_POST as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        //xử lý dữ liêu là dạng mảng
                        $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        //xử lý dữ liệu không phải dạng mảng
                        $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        }
    } else {
        if ($method == 'GET') {
            if (!empty($_GET)) {
                foreach ($_GET as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        //xử lý dữ liêu là dạng mảng
                        $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        //xử lý dữ liệu không phải dạng mảng
                        $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        } else if ($method == 'POST') {
            if (!empty($_POST)) {

                foreach ($_POST as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        //xử lý dữ liêu là dạng mảng
                        $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        //xử lý dữ liệu không phải dạng mảng
                        $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        }
    }
    return $filterArr;
}

// Validate email
function validateEmail($email)
{
    if (!empty($email)) {
        $checkEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    return $checkEmail;
}

//Validate Int
function validateInt($number)
{
    if (!empty($number)) {
        $checknumber = filter_var($number, FILTER_VALIDATE_INT);
    }
    return $checknumber;
}

//Validate phone
function isPhone($phone)
{
    $phoneFirst = false;
    if ($phone[0] == '0') {
        $phoneFirst = true;
        $phone = substr($phone, 1);
    }
    $checkPhone = false;
    if (validateInt($phone)) {
        $checkPhone = true;
    }
    if ($phoneFirst && $checkPhone) {
        if (strlen($phone) == 9) {
            return true;
        }
    } else {
        return false;
    }
}

//Hàm thông báo lỗi 

function getMess($mess, $color)
{
    echo '<span style="color:' . $color . '">' . $mess . '</span>';
}


//Hàm hiển thị lỗi
function formError($error, $fieldName)
{
    return !empty($error[$fieldName])
        ? '<span class="error">' . reset($error[$fieldName]) . '</span>'
        : '';
}

//Hàm hiển thị lại giá tri cũ
function oldData($data, $fieldname)
{
    return !empty($data[$fieldname]) ? $data[$fieldname] : null;
}

//Hàm chuyển hướng
function redirect($path, $pathfull = false)
{
    if ($pathfull) {
        header("Location:$path");
        exit();
    } else {
        $url = _HOST_URL . $path;
        header("Location:$url");
        exit();
    }
}

//Hàm Check_login
function isLogin()
{
    $checkLogin = false;
    $token_login = getSession('token_login');
    $checkToken = getOne("SELECT * FROM login_token WHERE token='$token_login'");
    if (!empty($checkToken)) {
        $checkLogin = true;
    } else {
        removeSession('token_login');
    }
    return $checkLogin;
}