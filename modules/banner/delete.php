<?php
$filter = filterData('GET');
if (!empty($filter)) {
    $banner_id = $filter['id'];
    $check = getOne("SELECT * FROM banners WHERE id = $banner_id");
    if (!empty($check)) {
        // xóa ảnh
        if (
            !empty($check['thumbnail']) &&
            file_exists($check['thumbnail'])
        ) {
            unlink($check['thumbnail']);
        }
        $deleteStatus = delete('banners', "id = $banner_id");
        if ($deleteStatus) {
            setSessionFlash('msg', 'Xóa danh mục thành công.');
            setSessionFlash('msg_type', 'green');
            redirect('?module=banner&action=index');
        }
    } else {
        setSessionFlash('msg', 'danh mục không tồn tại.');
        setSessionFlash('msg_type', 'red');
    }
} else {
    setSessionFlash('msg', 'Đã có lỗi xảy ra vui lòng thử lại sau.');
    setSessionFlash('msg_type', 'red');
}
