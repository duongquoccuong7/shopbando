<?php
$filter = filterData('GET');
if (!empty($filter)) {
    $category_id = $filter['id'];
    $check = getOne("SELECT * FROM categories WHERE id = $category_id");
    if (!empty($check)) {
        // xóa ảnh
        if (
            !empty($check['thumbnail']) &&
            file_exists($check['thumbnail'])
        ) {
            unlink($check['thumbnail']);
        }
        $deleteStatus = delete('categories', "id = $category_id");
        if ($deleteStatus) {
            setSessionFlash('msg', 'Xóa danh mục thành công.');
            setSessionFlash('msg_type', 'green');
            redirect('?module=category&action=index');
        }
    } else {
        setSessionFlash('msg', 'danh mục không tồn tại.');
        setSessionFlash('msg_type', 'red');
    }
} else {
    setSessionFlash('msg', 'Đã có lỗi xảy ra vui lòng thử lại sau.');
    setSessionFlash('msg_type', 'red');
}
