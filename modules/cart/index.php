<?php
layout('/index/header', 'Home');
$getData = filterData('GET');
$list_cou = getAll("SELECT * FROM coupons WHERE status=1");
// Kiểm tra xem user_id có tồn tại không (Đã đăng nhập hay chưa)
$user_id = isset($getData['id']) ? $getData['id'] : null;

$cart_id = null;
$get_item = []; // Mặc định khởi tạo là mảng rỗng để tránh lỗi dữ liệu

if (!empty($user_id)) {
    // Nếu có user_id, tiến hành tìm giỏ hàng đang hoạt động
    $get_cart = getOne("SELECT * FROM carts WHERE user_id = $user_id AND status=1");
    if (!empty($get_cart)) {
        $cart_id = $get_cart['id'];
    }
}

$get_cou = getAll("SELECT * FROM coupons");

// Chỉ truy vấn database lấy sản phẩm nếu tìm thấy cart_id hợp lệ
if (!empty($cart_id)) {
    $get_item = getAll("
    SELECT 
        ci.id AS cart_item_id,
        ci.product_id,
        ci.color_id,
        ci.size_id,
        p.name AS pro_name,
        c.name AS color_name,
        s.name AS size_name,    
        ci.quantity,
        pv.sale_price AS price,

        COALESCE(
            NULLIF(pv.image, ''),
            (
                SELECT pv2.image
                FROM product_variants pv2
                WHERE pv2.product_id = pv.product_id
                  AND pv2.color_id = pv.color_id
                  AND pv2.image IS NOT NULL
                  AND pv2.image <> ''
                ORDER BY RAND()
                LIMIT 1
            )
        ) AS pro_image

    FROM cart_items ci

    JOIN products p
        ON ci.product_id = p.id

    JOIN product_variants pv
        ON ci.product_id = pv.product_id
        AND ci.color_id = pv.color_id
        AND ci.size_id = pv.size_id

    JOIN colors c
        ON ci.color_id = c.id

    JOIN sizes s
        ON ci.size_id = s.id

    WHERE ci.cart_id = $cart_id;
    ");
}

$total = 0;
$total_quantity = 0;

// Tính toán tổng tiền nếu có sản phẩm
if (!empty($get_item)) {
    foreach ($get_item as $item) {
        $total += $item['price'] * $item['quantity'];
        $total_quantity += $item['quantity'];
    }
}

// Xử lý khi nhấn nút Checkout
if (isPost() && isset($_POST['checkout'])) {

    if (empty($get_item)) {
        setSessionFlash('msg', 'Your cart is empty.');
        redirect("?module=cart&action=index" . ($user_id ? "&id=$user_id" : ""));
    }

    $products = [];

    foreach ($get_item as $item) {

        $products[] = [
            'cart_id' => $get_cart['id'],
            'product_id'   => $item['product_id'],
            'cart_item_id' => $item['cart_item_id'],

            'name'         => $item['pro_name'],
            'image'        => $item['pro_image'],

            'color_id'     => $item['color_id'],
            'color_name'   => $item['color_name'],

            'size_id'      => $item['size_id'],
            'size_name'    => $item['size_name'],

            'price'        => $item['price'],
            'quantity'     => $item['quantity'],

            'subtotal'     => $item['price'] * $item['quantity']

        ];
    }

    $order = [

        'user_id'    => $user_id,
        'total'      => $total,
        'status'     => 1,
        'created_at' => date('Y-m-d H:i:s'),

        'products'   => $products

    ];
    setSession('orders', $order);

    redirect("?module=cart&action=checkout" . ($user_id ? "&id=$user_id" : ""));
}
?>
<!-- start coupon -->
<div class="wrap_cou">
    <div class="slide_cou">
        <div class="track">
            <?php foreach ($list_cou as $key => $cou): ?>
            <div class="tile_cou">
                <i class="fa-solid fa-angle-left"></i>
                <span><?php echo $cou['name']; ?></span>
                <i class="fa-solid fa-minus"></i>
                <span><?php echo $cou['description']; ?></span>
                <i class="fa-solid fa-minus"></i>
                <span><?php echo $cou['code']; ?></span>
                <i class="fa-solid fa-angle-right"></i>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="main-wrap-content main-wrap-cart">
    <form method="POST">
        <div class="cart_page">

            <?php if (!empty($get_item)): ?>

            <div class="cart_left">
                <?php foreach ($get_item as $key => $cart_item): ?>
                <div class="cart_item">
                    <img src="<?php echo $cart_item['pro_image']; ?>" alt="">
                    <div class="cart_content">
                        <div class="cart_info">
                            <h3><?php echo $cart_item['pro_name']; ?></h3>
                            <p><?php echo $cart_item['color_name']; ?></p>
                            <p><?php echo $cart_item['size_name']; ?></p>
                            <div class="cart_action">
                                <div class="quantity">
                                    <button type="button" class="qty-minus"
                                        data-id="<?= $cart_item['cart_item_id'] ?>">-</button>
                                    <input type="number" value="<?= $cart_item['quantity'] ?>" readonly>
                                    <button type="button" class="qty-plus"
                                        data-id="<?= $cart_item['cart_item_id'] ?>">+</button>
                                </div>
                                <a href="?module=cart&action=delete&id=<?php echo $cart_item['cart_item_id'] ?>"
                                    class="btn-delete delete-cart"><i class="fa-regular fa-trash-can"></i></a>
                            </div>
                        </div>
                        <div class="cart_price">
                            <?= number_format($cart_item['price'], 0, '.', ',') ?>
                            <i class="icon-pro-mon fa-solid fa-dong-sign"></i>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="cart_right">
                <h2>Summary</h2>
                <div class="summary_row">
                    <span>Subtotal</span>
                    <span><?= number_format($total, 0, '.', ',') ?><i
                            class="icon-pro-mon fa-solid fa-dong-sign"></i></span>
                </div>

                <div class="summary_row">
                    <span>Estimated Delivery</span>
                    <span>Free</span>
                </div>

                <hr>

                <div class="summary_row total">
                    <span>Total</span>
                    <span><?= number_format($total, 0, '.', ',') ?><i
                            class="icon-pro-mon fa-solid fa-dong-sign"></i></span>
                </div>

                <button type="submit" name="checkout" class="checkout">
                    Checkout
                </button>
            </div>

            <?php else: ?>
            <div class="cart_empty" style="text-align: center; width: 100%; padding: 60px 20px;">
                <i class="fa-solid fa-basket-shopping" style="font-size: 60px; color: #ccc; margin-bottom: 20px;"></i>
                <h2 style="font-size:1.8rem">Your cart is empty.</h2>
                <p style="color: #777; margin-top: 10px; margin-bottom: 25px; font-size:1.8rem">
                    Please sign in or add items to your cart to continue shopping.
                </p>
                <a href="<?php echo _HOST_URL . "/?module=index&action=index"; ?>" class="checkout"
                    style="display: inline-block; text-decoration: none; width: auto; padding: 12px 40px; background: #000; color: #fff; border-radius: 4px;">
                    Continue Shopping
                </a>
            </div>
            <?php endif; ?>

        </div>
    </form>
</div>
<!-- end main -->
<script>
$(".qty-plus").click(function() {

    let id = $(this).data("id");
    let input = $(this).parent().find("input");
    let quantity = Number(input.val()) + 1;

    $.ajax({
        url: "?module=cart&action=update_quantity",
        type: "POST",
        dataType: "json",
        data: {
            id: id,
            quantity: quantity
        },
        success: function(res) {

            if (res.status == "success") {
                input.val(quantity);
            } else {
                alert(res.message);
            }

        }
    });

});

$(".qty-minus").click(function() {

    let id = $(this).data("id");
    let input = $(this).parent().find("input");
    let quantity = Number(input.val());

    if (quantity > 1) {

        quantity--;

        $.ajax({
            url: "?module=cart&action=update_quantity",
            type: "POST",
            dataType: "json",
            data: {
                id: id,
                quantity: quantity
            },
            success: function(res) {

                if (res.status == "success") {
                    input.val(quantity);
                } else {
                    alert(res.message);
                }

            }
        });

    }

});
</script>
<?php
layout('/index/footer');
?>