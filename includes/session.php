<?php

//Set session 
function setSession($key, $value)
{
    if (!empty(session_id())) {
        $_SESSION[$key] = $value;
        return true;
    }
    return false;
}

//Lấy dữ liệu từ session
function getSession($key = '')
{
    if (empty($key)) {
        return $_SESSION;
    } else {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }
    return false;
}

//Xóa session 
function removeSession($key = '')
{
    if (empty($key)) {
        session_destroy();
        return true;
    } else {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]); //chỉ xóa key củ session
        }
        return true;
    }
    return false;
}

//Set session flash
function setSessionFlash($key, $value)
{
    $key = $key . 'Flash'; // nối thêm text là flash
    $rel = setSession($key, $value);
    return $rel;
}
function getSessionFlash($key)
{
    $key = $key . 'Flash';
    $rel = getSession($key);
    removeSession($key);
    return $rel;
}