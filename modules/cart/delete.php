<?php
$getData = filterData('GET');

if (!empty($getData['id'])) {

    $cart_item_id = (int)$getData['id'];
    $cartItem = getOne("SELECT * FROM cart_items WHERE id = $cart_item_id");

    if (!empty($cartItem)) {
        $cart = getOne("SELECT user_id FROM carts WHERE id = {$cartItem['cart_id']}");

        $deleteStatus = delete('cart_items', "id = $cart_item_id");

        if ($deleteStatus) {
            setSessionFlash('msg', 'Product removed successfully.');
            setSessionFlash('msg_type', 'green');

            redirect('?module=cart&action=index&id=' . $cart['user_id']);
        }
    } else {
        setSessionFlash('msg', 'Product not found.');
        setSessionFlash('msg_type', 'red');
        redirect('?module=cart&action=index');
    }
} else {
    setSessionFlash('msg', 'An error occurred. Please try again later.');
    setSessionFlash('msg_type', 'red');
    redirect('?module=cart&action=index');
}
