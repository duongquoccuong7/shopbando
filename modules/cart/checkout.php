<?php
layout('/index/header', 'Checkout');

$getData = filterData('GET');
$user_id = $getData['id'] ?? null;
$user = getOne("
    SELECT
        users.*,
        user_addresses.province,
        user_addresses.ward,
        user_addresses.address
    FROM users
    LEFT JOIN user_addresses
        ON users.id = user_addresses.user_id
    WHERE users.id = $user_id
");


$orders = getSession('orders') ?? [];

$totalPrice = 0;
$old_data = getSessionFlash('old_data');
if (!empty($user)) {
    $old_data = $user;
}
$order = getSession('orders');

$products = $order['products'];
$total = $order['total'];
if (isPost()) {
    $filter = filterData('POST');

    // ==========================
    // Lưu địa chỉ
    // ==========================

    $checkAddress = getOne("SELECT id FROM user_addresses WHERE user_id = $user_id");

    $dataAddress = [
        'province'   => trim($filter['province']),
        'ward'       => trim($filter['ward']),
        'address'    => trim($filter['address']),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    if (empty($checkAddress)) {

        $dataAddress['user_id'] = $user_id;
        $dataAddress['created_at'] = date('Y-m-d H:i:s');

        insert('user_addresses', $dataAddress);
    } else {

        update(
            'user_addresses',
            $dataAddress,
            $checkAddress['id']
        );
    }

    // ==========================
    // Kiểm tra tồn kho
    // ==========================

    foreach ($products as $item) {

        $variant = getOne("
        SELECT stock
        FROM product_variants
        WHERE product_id = {$item['product_id']}
        AND color_id = {$item['color_id']}
        AND size_id = {$item['size_id']}
    ");

        if (empty($variant) || $variant['stock'] < $item['quantity']) {

            setSessionFlash(
                'msg',
                $item['name'] . ' is out of stock.'
            );

            redirect("?module=cart&action=checkout&id=$user_id");
            exit;
        }
    }

    // ==========================
    // Tạo đơn hàng
    // ==========================

    $dataOrder = [

        'user_id'    => $user_id,
        'total'      => $total,
        'status'     => 1,
        'created_at' => date('Y-m-d H:i:s')

    ];

    $order_id = insert('orders', $dataOrder);

    if ($order_id) {

        foreach ($products as $item) {

            //======================
            // Thêm order_items
            //======================

            $orderItem = [

                'order_id'   => $order_id,
                'product_id' => $item['product_id'],
                'color_id'   => $item['color_id'],
                'size_id'    => $item['size_id'],
                'price'      => $item['price'],
                'quantity'   => $item['quantity'],
                'created_at' => date('Y-m-d H:i:s')

            ];

            insert('order_items', $orderItem);

            //======================
            // Trừ tồn kho
            //======================

            $variant = getOne("
    SELECT id, stock
    FROM product_variants
    WHERE product_id = {$item['product_id']}
    AND color_id = {$item['color_id']}
    AND size_id = {$item['size_id']}
");

            $newStock = $variant['stock'] - $item['quantity'];

            update(
                'product_variants',
                [
                    'stock' => $newStock
                ],
                $variant['id']
            );
            //======================
            // Xóa sản phẩm khỏi giỏ
            //======================

            delete(
                'cart_items',
                "id = {$item['cart_item_id']}"
            );
        }

        //======================
        // Đóng giỏ hàng
        //======================

        update(
            'carts',
            [
                'status' => 0
            ],
            $item['cart_id']
        );

        //======================
        // Xóa session checkout
        //======================

        removeSession('orders');

        setSessionFlash(
            'msg',
            'Order placed successfully.'
        );

        redirect("?module=cart&action=index&id=$user_id");
    }

    setSessionFlash(
        'msg',
        'Order failed.'
    );

    redirect("?module=cart&action=checkout&id=$user_id");
}
?>

<div class="main-wrap-content main-wrap-cart">
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="checkout-container">
            <!-- LEFT -->
            <div class="checkout-left">

                <h2 class="checkout-title">Order Review</h2>

                <!-- Product -->
                <?php foreach ($products as $item): ?>

                    <div class="checkout-item">

                        <div class="checkout-image">

                            <img src="<?= $item['image'] ?>">

                        </div>

                        <div class="checkout-info">

                            <h3><?= $item['name'] ?></h3>

                            <div class="checkout-row">
                                <span>Color</span>
                                <strong><?= $item['color_name'] ?></strong>
                            </div>

                            <div class="checkout-row">
                                <span>Size</span>
                                <strong><?= $item['size_name'] ?></strong>
                            </div>

                            <div class="checkout-row">
                                <span>Quantity</span>
                                <strong><?= $item['quantity'] ?></strong>
                            </div>

                            <div class="checkout-row">
                                <span>Unit Price</span>
                                <strong><?= number_format($item['price']) ?>đ</strong>
                            </div>

                        </div>

                        <div class="checkout-price">

                            <p>Total</p>

                            <h3><?= number_format($item['subtotal']) ?>đ</h3>

                        </div>

                    </div>

                <?php endforeach; ?>



            </div>

            <!-- RIGHT -->
            <div class="checkout-right">

                <h2 class="checkout-title">Shipping Information</h2>


                <div class="checkout-form">

                    <label>Full Name</label>
                    <input readonly type="text" placeholder="Enter your full name" value="<?php
                                                                                            if (!empty($old_data['fullname'])) {
                                                                                                echo  oldData($old_data, 'fullname');
                                                                                            }   ?>">

                </div>

                <div class="checkout-form">

                    <label>Email</label>
                    <input readonly type="email" placeholder="Enter your email" value="<?php
                                                                                        if (!empty($old_data['email'])) {
                                                                                            echo  oldData($old_data, 'email');
                                                                                        }   ?>">

                </div>

                <div class="checkout-form">

                    <label>Phone Number</label>
                    <input readonly type="text" placeholder="Enter your phone" value="<?php
                                                                                        if (!empty($old_data['phone'])) {
                                                                                            echo  oldData($old_data, 'phone');
                                                                                        }   ?>">

                </div>

                <div class="checkout-form">

                    <label>Province</label>

                    <input name="province" type="text" placeholder="Street Address" value="<?php
                                                                                            if (!empty($old_data['province'])) {
                                                                                                echo  oldData($old_data, 'province');
                                                                                            }   ?>">

                </div>


                <div class="checkout-form">


                    <label>Ward</label>

                    <input name="ward" type="text" placeholder="Street Address" value="<?php
                                                                                        if (!empty($old_data['ward'])) {
                                                                                            echo  oldData($old_data, 'ward');
                                                                                        }   ?>">

                </div>

                <div class="checkout-form">

                    <label>Address</label>

                    <input name="address" type="text" placeholder="Street Address" value="<?php
                                                                                            if (!empty($old_data['address'])) {
                                                                                                echo  oldData($old_data, 'address');
                                                                                            }   ?>">

                </div>

                <div class="checkout-summary">

                    <div class="summary-item">

                        <span>Total Products</span>

                        <strong><?= count($products) ?></strong>

                    </div>

                    <div class="summary-item">

                        <span>Shipping Fee</span>

                        <strong>Free</strong>

                    </div>

                    <div class="summary-item total-price">

                        <span>Grand Total</span>

                        <strong><?= number_format($total) ?>đ</strong>

                    </div>

                </div>

                <button type="submit" class="checkout-button">
                    Place Order
                </button>
            </div>

        </div>
    </form>
</div>

<?php
layout('/index/footer');
?>