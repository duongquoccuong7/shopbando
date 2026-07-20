<?php
layout('/index/header', 'Privacy Policy');
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
        <section class="privacy">
            <h1>Privacy Policy</h1>

            <p>
                At <strong>ShoeStore</strong>, we value your privacy and are committed
                to protecting your personal information.
            </p>

            <h2>Information We Collect</h2>
            <p>
                We may collect your name, email address, phone number, shipping
                address, and payment information when you place an order or create
                an account.
            </p>

            <h2>How We Use Your Information</h2>
            <ul>
                <li>Process and deliver your orders.</li>
                <li>Provide customer support.</li>
                <li>Improve our products and services.</li>
                <li>Send updates, promotions, and important notifications.</li>
            </ul>

            <h2>Information Security</h2>
            <p>
                We use appropriate security measures to protect your personal
                information from unauthorized access, disclosure, or misuse.
            </p>

            <h2>Cookies</h2>
            <p>
                Our website may use cookies to improve your browsing experience,
                remember your preferences, and analyze website traffic.
            </p>

            <h2>Your Rights</h2>
            <p>
                You have the right to access, update, or request the deletion of
                your personal information at any time.
            </p>

            <h2>Contact Us</h2>
            <p>
                If you have any questions about this Privacy Policy, please contact us at:
            </p>

            <p>
                <strong>Email:</strong> support@shoestore.com
            </p>
        </section>
    </div>
</div>

<?php
layout('/index/footer');
?>