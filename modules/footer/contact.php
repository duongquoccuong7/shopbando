<?php
layout('/index/header', 'Contact Us');
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
        <section class="contact-page">
            <h1>Contact Us</h1>

            <p>
                We'd love to hear from you. Whether you have a question about our
                products, your order, or our services, our team is here to help.
            </p>

            <h2>Customer Support</h2>
            <p>
                Our customer support team is available to assist you with orders,
                returns, exchanges, and any general inquiries.
            </p>

            <h2>Contact Information</h2>
            <p><strong>Email:</strong> support@shoestore.com</p>
            <p><strong>Phone:</strong> +84 123 456 789</p>
            <p><strong>Address:</strong> 123 Main Street, Ho Chi Minh City, Vietnam</p>

            <h2>Business Hours</h2>
            <p>
                Monday – Saturday: 8:00 AM – 6:00 PM
            </p>
            <p>
                Sunday: Closed
            </p>

            <h2>Follow Us</h2>
            <p>
                Stay connected with us through our social media channels to receive
                the latest news, promotions, and product updates.
            </p>
        </section>
    </div>
</div>

<?php
layout('/index/footer');
?>