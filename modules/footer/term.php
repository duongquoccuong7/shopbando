<?php
layout('/index/header', 'Terms & Conditions');
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
        <section class="terms">
            <h1>Terms & Conditions</h1>

            <p>
                Welcome to <strong>ShoeStore</strong>. By accessing and using our
                website, you agree to comply with the following terms and conditions.
            </p>

            <h2>Use of Our Website</h2>
            <p>
                You agree to use this website only for lawful purposes and in a
                manner that does not infringe upon the rights of others or restrict
                their use of the website.
            </p>

            <h2>Orders and Payments</h2>
            <p>
                All orders are subject to product availability and confirmation of
                payment. We reserve the right to cancel or refuse any order if
                necessary.
            </p>

            <h2>Shipping and Returns</h2>
            <p>
                Shipping times may vary depending on your location. Please review
                our return policy before requesting a return or exchange.
            </p>

            <h2>Intellectual Property</h2>
            <p>
                All content on this website, including text, images, logos, and
                graphics, is the property of ShoeStore and may not be copied,
                reproduced, or distributed without permission.
            </p>

            <h2>Limitation of Liability</h2>
            <p>
                ShoeStore is not responsible for any indirect or consequential
                damages resulting from the use of our website or products.
            </p>

            <h2>Changes to These Terms</h2>
            <p>
                We may update these Terms & Conditions at any time. Continued use
                of the website constitutes acceptance of any changes.
            </p>

            <h2>Contact Us</h2>
            <p>
                If you have any questions regarding these Terms & Conditions,
                please contact us at <strong>support@shoestore.com</strong>.
            </p>
        </section>
    </div>
</div>

<?php
layout('/index/footer');
?>