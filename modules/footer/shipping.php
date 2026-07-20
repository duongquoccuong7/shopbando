<?php
layout('/index/header', 'Shipping');
$list_cou    = getAll("SELECT * FROM coupons WHERE status=1");
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
<div class="main-wrap-content">
    <div class="container">
        <section class="shipping">
            <h1>Shipping Information</h1>

            <p>
                At <strong>ShoeStore</strong>, we are committed to delivering your
                orders quickly and safely. Please review our shipping information
                below.
            </p>

            <h2>Processing Time</h2>
            <p>
                Orders are typically processed within 1–2 business days after
                payment has been confirmed. Orders placed on weekends or public
                holidays will be processed on the next business day.
            </p>

            <h2>Delivery Time</h2>
            <p>
                Standard shipping usually takes 3–7 business days, depending on
                your location. Delivery times may vary during peak shopping
                seasons or due to unexpected circumstances.
            </p>

            <h2>Shipping Fees</h2>
            <p>
                Shipping costs are calculated at checkout based on your delivery
                address and selected shipping method.
            </p>

            <h2>Order Tracking</h2>
            <p>
                Once your order has been shipped, you will receive a confirmation
                email with a tracking number so you can monitor your package.
            </p>

            <h2>International Shipping</h2>
            <p>
                International shipping may be available for selected countries.
                Delivery times and shipping fees vary depending on the destination.
            </p>

            <h2>Need Help?</h2>
            <p>
                If you have any questions about shipping or your order, please
                contact our customer support team at
                <strong>support@shoestore.com</strong>.
            </p>
        </section>
    </div>
</div>

<?php
layout('/index/footer');
?>