<?php
$filter = filterData('GET');

if (!empty($filter['id'])) {

    $data_id = (int)$filter['id'];

    $check = getOne("SELECT * FROM users WHERE id = $data_id");

    if (!empty($check)) {

        $deleteStatus = delete('users', "id = $data_id");

        if ($deleteStatus) {
            setSessionFlash('msg', 'Xóa người dùng thành công.');
            setSessionFlash('msg_type', 'green');
        } else {
            setSessionFlash('msg', 'Xóa người dùng thất bại.');
            setSessionFlash('msg_type', 'red');
        }
    } else {
        setSessionFlash('msg', 'Người dùng không tồn tại.');
        setSessionFlash('msg_type', 'red');
    }
} else {
    setSessionFlash('msg', 'ID người dùng không hợp lệ.');
    setSessionFlash('msg_type', 'red');
}

redirect('?module=users&action=index');
