<?php
layout('/index/header', 'FAQ');
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
        <section class="faq">
            <h1>Frequently Asked Questions</h1>

            <div class="faq-item">
                <h3>How can I place an order?</h3>
                <p>
                    Browse our products, add your favorite items to the cart,
                    and proceed to checkout to complete your purchase.
                </p>
            </div>

            <div class="faq-item">
                <h3>What payment methods do you accept?</h3>
                <p>
                    We accept major credit/debit cards and other secure online
                    payment methods available during checkout.
                </p>
            </div>

            <div class="faq-item">
                <h3>How long does shipping take?</h3>
                <p>
                    Orders are usually delivered within 3–7 business days,
                    depending on your location.
                </p>
            </div>

            <div class="faq-item">
                <h3>Can I return or exchange a product?</h3>
                <p>
                    Yes. Products can be returned or exchanged within 30 days
                    if they are unused and in their original condition.
                </p>
            </div>

            <div class="faq-item">
                <h3>How can I track my order?</h3>
                <p>
                    Once your order has been shipped, you will receive a tracking
                    number by email.
                </p>
            </div>

            <div class="faq-item">
                <h3>How can I contact customer support?</h3>
                <p>
                    You can contact us via email at
                    <strong>support@shoestore.com</strong> or call our customer
                    service during business hours.
                </p>
            </div>
        </section>
    </div>
</div>

<?php
layout('/index/footer');
?>