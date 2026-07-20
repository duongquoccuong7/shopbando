<?php

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {

    echo json_encode([
        'status' => 'login_required',
        'message' => 'You need to log in to perform this action.'
    ]);
    exit;
}

$product_id = (int)$_POST['product_id'];

$favorite = getOne("
    SELECT id
    FROM favorites
    WHERE user_id = $user_id
    AND product_id = $product_id
");

if ($favorite) {

    delete('favorites', "id = {$favorite['id']}");

    echo json_encode([
        'status' => true,
        'favorite' => false
    ]);
} else {

    insert('favorites', [
        'user_id'    => $user_id,
        'product_id' => $product_id,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    echo json_encode([
        'status' => true,
        'favorite' => true
    ]);
}

exit;
