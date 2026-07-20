<?php
$filter = filterData('GET');
if (!empty($filter)) {
    $pro_var_id = $filter['id'];
    $check = getOne("SELECT * FROM product_variants WHERE id = $pro_var_id");
    $product_id = $check['product_id'];
    if (!empty($check)) {
        // xóa ảnh
        if (
            !empty($check['image']) &&
            file_exists($check['image'])
        ) {
            unlink($check['image']);
        }
        $deleteStatus = delete('product_variants', "id = $pro_var_id");
        if ($deleteStatus) {
            setSessionFlash('msg', 'Xóa sản phẩm thành công.');
            redirect('?module=product&action=edit&id=' . $product_id);
        }
    } else {
        setSessionFlash('msg', ' sản phẩm không tồn tại.');
        setSessionFlash('msg_type', 'red');
    }
} else {
    setSessionFlash('msg', 'Đã có lỗi xảy ra vui lòng thử lại sau.');
    setSessionFlash('msg_type', 'red');
}
