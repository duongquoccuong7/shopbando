<?php
$filter = filterData('GET');
if (!empty($filter)) {
    $brand_id = $filter['id'];
    $check = getOne("SELECT * FROM brands WHERE id = $brand_id");
    if (!empty($check)) {
        // xóa ảnh
        if (
            !empty($check['thumbnail']) &&
            file_exists($check['thumbnail'])
        ) {
            unlink($check['thumbnail']);
        }
        $deleteStatus = delete('brands', "id = $brand_id");
        if ($deleteStatus) {
            setSessionFlash('msg', 'Xóa khóa học thành công.');
            redirect('?module=brands&action=index');
        }
    } else {
        setSessionFlash('msg', 'Khóa học không tồn tại.');
        setSessionFlash('msg_type', 'red');
    }
} else {
    setSessionFlash('msg', 'Đã có lỗi xảy ra vui lòng thử lại sau.');
    setSessionFlash('msg_type', 'red');
}
