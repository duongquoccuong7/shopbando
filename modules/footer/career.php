<?php
layout('/index/header', 'Careers');
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
        <section class="career">
            <h1>Join Our Team</h1>

            <p>
                At <strong>ShoeStore</strong>, we believe our people are the key to our success.
                We are always looking for passionate, creative, and motivated individuals
                to join our growing team.
            </p>

            <h2>Why Work With Us?</h2>
            <ul>
                <li>✔ Friendly and supportive work environment</li>
                <li>✔ Opportunities for career growth</li>
                <li>✔ Competitive salary and benefits</li>
                <li>✔ Training and professional development</li>
            </ul>

            <h2>Open Positions</h2>

            <div class="job">
                <h3>Sales Associate</h3>
                <p>Assist customers, manage product displays, and provide excellent customer service.</p>
            </div>

            <div class="job">
                <h3>Warehouse Assistant</h3>
                <p>Receive, organize, and prepare products for delivery.</p>
            </div>

            <div class="job">
                <h3>Marketing Intern</h3>
                <p>Support marketing campaigns and help grow our online presence.</p>
            </div>

            <h2>How to Apply</h2>
            <p>
                If you are interested in joining our team, please send your resume to:
            </p>

            <p>
                <strong>Email:</strong> careers@shoestore.com
            </p>

            <p>
                We look forward to hearing from you!
            </p>
        </section>
    </div>
</div>

<?php
layout('/index/footer');
?>