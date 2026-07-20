<?php
layout('/dashboard/header', 'Order Detail');;
$getData = filterData('GET');
$order_id = $getData['id'];
$order = getOne("
SELECT

o.*,

u.fullname,
u.email,
u.phone,

ua.province,
ua.ward,
ua.address

FROM orders o

LEFT JOIN users u
ON o.user_id=u.id

LEFT JOIN user_addresses ua
ON ua.user_id=u.id

WHERE o.id=$order_id
");

$listProduct = getAll("
SELECT
    oi.*,

    p.name AS product_name,

    pv.image,

    c.name AS color_name,

    s.name AS size_name

FROM order_items oi

INNER JOIN products p
ON oi.product_id = p.id

INNER JOIN product_variants pv
ON pv.product_id = oi.product_id
AND pv.color_id = oi.color_id
AND pv.size_id = oi.size_id

INNER JOIN colors c
ON oi.color_id = c.id

INNER JOIN sizes s
ON oi.size_id = s.id

WHERE oi.order_id = $order_id
");


if (isPost()) {

    $filter = filterData();

    $errors = [];

    if (empty($filter['province'])) {
        $errors['province'] = 'Province is required';
    }

    if (empty($filter['ward'])) {
        $errors['ward'] = 'Ward is required';
    }

    if (empty($filter['address'])) {
        $errors['address'] = 'Address is required';
    }

    if (empty($errors)) {

        $oldOrder = getOne("
            SELECT status
            FROM orders
            WHERE id = $order_id
        ");

        if (
            $filter['status'] == 5 &&
            $oldOrder['status'] != 5
        ) {
            $orderItems = getAll("
                SELECT
                    product_id,
                    color_id,
                    size_id,
                    quantity
                FROM order_items
                WHERE order_id = $order_id
            ");

            foreach ($orderItems as $item) {

                $variant = getOne("
                    SELECT
                        id,
                        stock
                    FROM product_variants
                    WHERE product_id = {$item['product_id']}
                    AND color_id = {$item['color_id']}
                    AND size_id = {$item['size_id']}
                ");

                if (!empty($variant)) {

                    $newStock = $variant['stock'] + $item['quantity'];
                    echo '<pre>';
                    print_r($newStock);
                    echo '</pre>';

                    var_dump($variant['id']);

                    update(
                        'product_variants',
                        [
                            'stock' => $newStock
                        ],
                        $variant['id']
                    );
                }
            }
        }
        $dataUpdate = [

            'fullname'        => $filter['fullname'],
            'phone'            => $filter['phone'],
            'address'         => $filter['address'],
            'payment_method'  => $filter['payment_method'],
            'status'          => $filter['status'],
            'updated_at'      => date('Y-m-d H:i:s')

        ];
        $updateStatus = update('orders', $dataUpdate, $order_id);

        if ($updateStatus) {
            setSessionFlash('msg', 'Order updated successfully');
            setSessionFlash('msg_type', 'green');

            redirect('?module=order&action=edit&id=' . $order_id);
        } else {

            setSessionFlash('msg', 'Failed to update order');
            setSessionFlash('msg_type', 'red');
        }
    } else {
        setSessionFlash('msg', 'Please check your input.');
        setSessionFlash('msg_type', 'red');
        setSessionFlash('old_data', $filter);
        setSessionFlash('errors', $errors);
    }
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
?>

<div class="main-wrap">

    <?php layout('dashboard/sidebar'); ?>

    <div class="content-menu">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="title-list">
                <h2><?php echo  !empty($msg) ? getMess($msg, $msg_type) : ' Order Detail' ?></h2>
                <a style="text-decoration: none;" href="<?php echo _HOST_URL . "/?module=order&index" ?>"
                    class="btn-submit"><i class="fa-solid fa-backward"></i></a>
            </div>
            <div class="add-product">

                <!-- Left -->
                <div class="add-product-left">

                    <!-- Name -->
                    <div class="product-input">
                        <label class="label-input">Name</label><br>
                        <input name="fullname" type="text" value="<?= $order['fullname'] ?> " readonly>
                    </div>

                    <!-- Phone -->
                    <div class="product-input">
                        <label class="label-input">Phone</label><br>
                        <input name="phone" type="text" value="<?= $order['phone'] ?>" readonly>
                    </div>

                    <!-- Email -->
                    <div class="product-input">
                        <label class="label-input">Email</label><br>
                        <input type="text" value="<?= $order['email'] ?>" readonly>
                    </div>

                    <!-- Address -->
                    <div class="product-input">
                        <label class="label-input">Address</label><br>
                        <textarea name="address"
                            style="width:100%;height:120px;resize:none;padding:10px;font-size:1.4rem"><?= $order['address'] ?></textarea>
                    </div>

                </div>

                <!-- Right -->
                <div class="add-product-left">

                    <!-- Province -->
                    <div class="product-input">
                        <label class="label-input">Province</label><br>
                        <input name="province" type="text" value="<?= $order['province'] ?>" readonly>
                    </div>

                    <!-- Ward -->
                    <div class="product-input">
                        <label class="label-input">Ward</label><br>
                        <input name="ward" type="text" value="<?= $order['ward'] ?>" readonly>
                    </div>

                    <!-- Payment Method -->
                    <div class="product-input">
                        <label class="label-input">Payment Method</label><br>

                        <select class="edit-select" name="payment_method">

                            <option value="1" <?= $order['payment_method'] == 1 ? 'selected' : '' ?>>
                                Cash on Delivery
                            </option>

                            <option value="2" <?= $order['payment_method'] == 2 ? 'selected' : '' ?>>
                                Online Payment
                            </option>

                        </select>
                    </div>

                    <!-- Status -->
                    <div class="product-input">
                        <label class="label-input">Status</label><br>

                        <select class="edit-select" name="status">

                            <option value="1" <?= $order['status'] == 1 ? 'selected' : '' ?>>
                                Pending
                            </option>

                            <option value="2" <?= $order['status'] == 2 ? 'selected' : '' ?>>
                                Processing
                            </option>

                            <option value="3" <?= $order['status'] == 3 ? 'selected' : '' ?>>
                                Shipping
                            </option>

                            <option value="4" <?= $order['status'] == 4 ? 'selected' : '' ?>>
                                Completed
                            </option>

                            <option value="5" <?= $order['status'] == 5 ? 'selected' : '' ?>>
                                Cancelled
                            </option>

                        </select>
                    </div>

                </div>

            </div>

            <h3 style="margin:30px 0 15px;">Products</h3>

            <table>

                <thead>

                    <tr>

                        <th>Image</th>
                        <th>Product</th>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($listProduct as $item): ?>

                        <tr>

                            <td>

                                <img src=" <?= $item['image'] ?>" width="50">

                            </td>

                            <td><?= $item['product_name'] ?></td>

                            <td><?= $item['color_name'] ?></td>

                            <td><?= $item['size_name'] ?></td>

                            <td><?= $item['quantity'] ?></td>

                            <td><?= number_format($item['price']) ?>đ</td>

                            <td><?= number_format($item['price'] * $item['quantity']) ?>đ</td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

            <div style="margin-top:25px">

                <button type="submit" class="btn-submit">
                    Update
                </button>

            </div>

        </form>

    </div>

</div>

<?php layout('/dashboard/footer'); ?>