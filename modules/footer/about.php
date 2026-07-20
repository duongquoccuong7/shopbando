<?php
layout('/index/header', 'About Us');
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
        <section class="about">
            <h1>About Us</h1>

            <p>
                Welcome to <strong>ShoeStore</strong>, your trusted destination for quality
                footwear. We are passionate about providing stylish, comfortable,
                and affordable shoes for everyone.
            </p>

            <p>
                Our collection includes sneakers, running shoes, casual shoes,
                and many other styles from trusted brands. Every product is
                carefully selected to ensure excellent quality and customer
                satisfaction.
            </p>

            <h2>Our Mission</h2>
            <p>
                Our mission is to make shopping for shoes easy, enjoyable,
                and convenient by offering high-quality products and excellent
                customer service.
            </p>

            <h2>Why Choose Us?</h2>
            <ul>
                <li>✔ High-quality products</li>
                <li>✔ Affordable prices</li>
                <li>✔ Fast and secure delivery</li>
                <li>✔ Friendly customer support</li>
            </ul>

            <h2>Contact Us</h2>
            <p>
                If you have any questions or need assistance, please feel free
                to contact us. We are always happy to help.
            </p>
        </section>
    </div>
</div>

<?php
layout('/index/footer');
?>