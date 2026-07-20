<?php
$filter = filterData('GET');
if (!empty($filter)) {
    $product_id = $filter['id'];
    $check = getOne("SELECT * FROM products WHERE id = $product_id");
    $variants = getAll("SELECT * FROM product_variants WHERE product_id = $product_id");
    if (!empty($check)) {
        // xóa ảnh
        if (
            !empty($check['thumbnail']) &&
            file_exists($check['thumbnail'])
        ) {
            unlink($check['thumbnail']);
        }
        foreach ($variants as $variant) {
            if (!empty($variant['image']) && file_exists($variant['image'])) {
                unlink($variant['image']);
            }
        }
        delete('product_variants', "product_id = $product_id");
        $deleteStatus = delete('products', "id = $product_id");
        if ($deleteStatus) {
            setSessionFlash('msg', 'Xóa sản phẩm thành công.');
            redirect('?module=product&action=index');
        }
    } else {
        setSessionFlash('msg', ' sản phẩm không tồn tại.');
        setSessionFlash('msg_type', 'red');
    }
} else {
    setSessionFlash('msg', 'Đã có lỗi xảy ra vui lòng thử lại sau.');
    setSessionFlash('msg_type', 'red');
}
