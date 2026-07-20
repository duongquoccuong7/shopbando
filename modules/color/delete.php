<?php
$filter = filterData('GET');
if (!empty($filter)) {
    $category_id = $filter['id'];
    $check = getOne("SELECT * FROM colors WHERE id = $category_id");
    if (!empty($check)) {
        // xóa ảnh
        if (
            !empty($check['thumbnail']) &&
            file_exists($check['thumbnail'])
        ) {
            unlink($check['thumbnail']);
        }
        $deleteStatus = delete('colors', "id = $category_id");
        if ($deleteStatus) {
            setSessionFlash('msg', 'Xóa khóa học thành công.');
            redirect('?module=color&action=index');
        }
    } else {
        setSessionFlash('msg', 'Khóa học không tồn tại.');
        setSessionFlash('msg_type', 'danger');
    }
} else {
    setSessionFlash('msg', 'Đã có lỗi xảy ra vui lòng thử lại sau.');
    setSessionFlash('msg_type', 'danger');
}
