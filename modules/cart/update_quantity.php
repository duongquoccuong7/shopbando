<?php

if (isPost()) {

    $filter = filterData();
    $id = $filter['id'];
    $quantity = $filter['quantity'];

    $check = getOne("SELECT * FROM cart_items WHERE id = $id");

    if (!empty($check)) {

        $data = [
            'quantity' => $quantity,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $updateStatus = update('cart_items', $data, $id);

        if ($updateStatus) {

            echo json_encode([
                'status' => 'success'
            ]);
        } else {

            echo json_encode([
                'status' => 'error',
                'message' => 'Update failed.'
            ]);
        }
    } else {

        echo json_encode([
            'status' => 'error',
            'message' => 'Cart item not found.'
        ]);
    }

    exit;
}
