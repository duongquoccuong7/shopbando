<?php
layout('/index/header', 'Home');
$getData = filterData('GET');
$product_id = $getData['id'];
$list_cou = getAll("SELECT * FROM coupons WHERE status=1");
$product = getOne("SELECT * FROM products WHERE id =$product_id");
$cur_cate_id = $product['category_id'];
$listpro = getAll("
    SELECT
        p.*,
        c.name AS category_name,
        g.name AS gender_name,
        MIN(pv.sale_price) AS sale_price
    FROM products p
    LEFT JOIN categories c
        ON c.id = p.category_id
    LEFT JOIN genders g
        ON g.id = p.gender_id
    LEFT JOIN product_variants pv
        ON pv.product_id = p.id
    WHERE p.category_id = $cur_cate_id
      AND p.id != $product_id
    GROUP BY p.id
    ORDER BY p.created_at DESC
    LIMIT 8
");
$gender = getOne("
    SELECT *
    FROM genders
    WHERE id = {$product['gender_id']}
");
$brand = getOne("
    SELECT *
    FROM brands
    WHERE id = {$product['brand_id']}
");
$sql = ("
    SELECT DISTINCT
        pv.*,
        c.name AS color_name,
        c.color_code,
        s.name AS size_name
    FROM product_variants pv
    LEFT JOIN colors c
        ON pv.color_id = c.id
    LEFT JOIN sizes s
        ON pv.size_id = s.id
    WHERE pv.product_id = $product_id
    ORDER BY c.name, s.name
");
$img_pro_color = getOne($sql);
$pro_var = getAll($sql);
// Danh sách màu
$colors = getAll("
    SELECT DISTINCT
        c.id,
        c.name,
        c.color_code
    FROM product_variants pv
    JOIN colors c
        ON c.id = pv.color_id
    WHERE pv.product_id = $product_id
");
// Danh sách size
$sizes = getAll("
    SELECT DISTINCT
        s.id,
        s.name,
        pv.color_id
    FROM product_variants pv
    JOIN sizes s
        ON s.id = pv.size_id
    WHERE pv.product_id = $product_id
");

$colorImages = [];

foreach ($pro_var as $item) {
    if (
        !isset($colorImages[$item['color_id']]) &&
        !empty($item['image'])
    ) {
        $colorImages[$item['color_id']] = $item;
    }
}
$user_id = $_SESSION['user_id'] ?? null;

$is_favorited = false;

if ($user_id) {
    $favorite = getOne("
        SELECT id
        FROM favorites
        WHERE user_id = $user_id
        AND product_id = {$product['id']}
    ");

    $is_favorited = !empty($favorite);
}

// submit add product
if (isPost()) {
    $filter = filterData();
    $errors = [];

    if (empty($filter['size_id'])) {
        $errors['size_id']['required'] = 'Size is required';
    }
    if (empty($filter['color_id'])) {
        $errors['color_id']['required'] = 'Color is required';
    }

    if (!empty($errors)) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'status'  => 'validation_error',
            'errors'  => $errors,
            'message' => 'Please select color and size.'
        ]);
        exit;
    }

    if (!empty($user_id)) {
        $current_time = date("Y-m-d H:i:s");

        $cart = getOne("SELECT id FROM carts WHERE user_id = $user_id AND status = 1 LIMIT 1");

        if (!empty($cart)) {
            $cart_id = $cart['id'];
        } else {
            $cart_data = [
                'user_id'    => $user_id,
                'status'     => 1,
                'created_at' => $current_time,
            ];

            $cart_id = insert('carts', $cart_data);
        }

        if ($cart_id) {
            $color_id = $filter['color_id'];
            $size_id = $filter['size_id'];

            $get_item = getOne("SELECT id, quantity FROM cart_items 
                                WHERE cart_id = $cart_id 
                                AND product_id = $product_id 
                                AND color_id = $color_id 
                                AND size_id = $size_id LIMIT 1");

            if (!empty($get_item)) {
                $item_id = $get_item['id'];

                $update_data = [
                    'quantity'   => $get_item['quantity'] + 1,
                    'updated_at' => $current_time
                ];

                $action = update('cart_items', $update_data, $item_id);
                $msg = 'Product quantity updated successfully';
            } else {
                $insert_data = [
                    'cart_id'    => $cart_id,
                    'product_id' => $product_id,
                    'color_id'   => $color_id,
                    'size_id'    => $size_id,
                    'quantity'   => 1,
                    'created_at' => $current_time
                ];

                $action = insert('cart_items', $insert_data);
                $msg = 'Product added to cart successfully';
            }

            if ($action) {
                if (ob_get_length()) {
                    ob_clean();
                }

                header('Content-Type: application/json');
                echo json_encode([
                    'status'  => 'success',
                    'message' => $msg
                ]);
                exit;
            } else {
                if (ob_get_length()) {
                    ob_clean();
                }

                header('Content-Type: application/json');
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Có lỗi xảy ra, không thể xử lý giỏ hàng.'
                ]);
                exit;
            }
        }
    } else {
        if (ob_get_length()) {
            ob_clean();
        }

        header('Content-Type: application/json');
        echo json_encode([
            'status'  => 'login_required',
            'message' => 'You need to log in to purchase this product.'
        ]);
        exit;
    }
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
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
<!-- start main -->
<div class="main-wrap-content main-wrap-product-detail">

    <!-- start product detail -->
    <form id="add-to-cart-form" action="?module=index&action=index_pro_detail&id=<?php echo $product_id; ?>"
        method="POST" enctype="multipart/form-data">
        <div class="product-detail">
            <div class="img-product-list">
                <div class="img-product-detail-list">
                    <ul class="img-list">
                        <li data-image="<?php echo $product['thumbnail']; ?>" class="item-img"> <img
                                src="<?php echo $product['thumbnail']; ?>" alt=""></li>
                        <?php foreach ($pro_var as $key => $img_pro): ?>
                            <?php if (!empty($img_pro['image'])): ?>
                                <li data-color="<?= $img_pro['color_id'] ?>" data-image="<?php echo $img_pro['image']; ?>"
                                    class="item-img"> <img src="<?php echo $img_pro['image']; ?>" alt=""></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="img-product"><img id="img-thumb" class="img-product-detail"
                        src="<?php echo $product['thumbnail']; ?>"></img>
                </div>
            </div>
            <div class="product-detail-infor">
                <div class="product_info">
                    <h2><?php echo $product['name']; ?></h2>
                    <span class="product_type"><?php echo $gender['name']; ?></span>
                    <div class="pro-price">
                        <span class="pro_price_sale">3,109,000 đ</span>
                    </div>
                </div>
                <!-- color  product -->
                <div class="list_img_color">
                    <input type="hidden" id="selected_color_id" name="color_id"
                        value="<?= $product['thumbnail_color_id'] ?>">
                    <?php
                    foreach ($colorImages as $color_img):
                        $isActive = ($color_img['color_id'] == $product['thumbnail_color_id']) ? 'active_thumbnail' : '';
                    ?>
                        <div>
                            <img data-color="<?= $color_img['color_id'] ?>" class="img_c <?= $isActive ?>"
                                src="<?= $color_img['image'] ?>" alt="" style="background-color: black;">

                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- size  product -->
                <div class="product_filter">
                    <div class="product_size filter-group">
                        <span class="filter-pro-title">
                            <span><?php echo !empty($errors) ? formError($errors, 'size_id') : 'Size'; ?></span></span>
                        <div class="size-list">
                            <input type="text" id="selected_size_id" name="size_id" hidden value="">

                            <?php foreach ($sizes as $size): ?>
                                <div data-color="<?= $size['color_id'] ?>" data-size-id="<?= $size['id'] ?>"
                                    class="size-item select_size">
                                    <?php echo $size['name']; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="product_button">
                    <button type="submit" class="btn-product-detail pro-btn-add">Add to Bag</button>
                    <button id="click_far" type="button" data-product="<?= $product['id'] ?>"
                        class="btn-product-detail pro-btn-favorite <?= $is_favorited ? 'active_far' : '' ?>">

                        Favorite <i class="fa-regular fa-heart"></i>

                    </button>
                </div>
                <div class="product_des">
                    <span name="" id="">
                        <?php echo $product['description']; ?>
                    </span>
                    <div class="origin-group">
                        <div class="product-origin">
                            <i class="fa-brands fa-servicestack"></i>
                            <span class="label">Color:</span>
                            <div class="color-list">
                                <?php foreach ($colors as $key => $pro_color): ?>
                                    <span><?= $pro_color['name'] ?><?= $key < count($colors) - 1 ? ' /' : '' ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="product-origin">
                            <i class="fa-brands fa-servicestack"></i>
                            <span class="info-label">Country / Region of Origin:</span>
                            <div class="info-value">
                                <span><?= $brand['country'] ?></span>
                            </div>
                        </div>
                        <div class="product-origin">
                            <i class="fa-brands fa-servicestack"></i>
                            <span class="info-label">Style:</span>
                            <div class="info-value">
                                <span>IR5903-010</span>
                            </div>
                        </div>
                        <!-- START: Reviews Section -->
                        <div class="accordion-container">
                            <!-- 1. SIZE & FIT -->
                            <div class="accordion-item">
                                <button class="accordion-header" type="button">
                                    <span class="accordion-title">Size & Fit</span>
                                    <i class="fa-solid fa-chevron-up toggle-icon"></i>
                                </button>
                                <div class="accordion-content">
                                    <ul>
                                        <li>Snug fit; If you prefer a slightly looser fit, we recommend ordering half a
                                            size up</li>
                                        <li><a href="#" class="nike-link">Size Guide</a></li>
                                    </ul>
                                </div>
                            </div>

                            <!-- 2. FREE DELIVERY AND RETURNS -->
                            <div class="accordion-item">
                                <button class="accordion-header" type="button">
                                    <span class="accordion-title">Free Delivery and Returns</span>
                                    <i class="fa-solid fa-chevron-up toggle-icon"></i>
                                </button>
                                <div class="accordion-content">
                                    <p>Your order of S$75 or more gets free standard delivery.</p>
                                    <ul>
                                        <li>Standard delivered 1-3 Business Days</li>
                                        <li>Express delivered 0-2 Business Days</li>
                                    </ul>
                                    <p>Orders are processed and delivered Monday-Friday (excluding public holidays).</p>
                                    <p>Nike Members enjoy <a href="#" class="nike-link">free returns</a>. <a href="#"
                                            class="nike-link">Exclusions apply</a>.</p>
                                </div>
                            </div>

                            <!-- 3. REVIEWS -->
                            <div class="accordion-item">
                                <button class="accordion-header" type="button">
                                    <div class="accordion-title">Reviews (0)</div>
                                    <div class="reviews-stars">
                                        <i class="fa-regular fa-star"></i>
                                        <i class="fa-regular fa-star"></i>
                                        <i class="fa-regular fa-star"></i>
                                        <i class="fa-regular fa-star"></i>
                                        <i class="fa-regular fa-star"></i>
                                        <i class="fa-solid fa-chevron-down toggle-icon"></i>
                                    </div>
                                </button>
                                <div class="accordion-content">
                                    <p class="no-reviews-text">There are no reviews for this product yet.</p>
                                </div>
                            </div>
                        </div>
                        <!-- END: Reviews Section -->
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- end product detail -->

</div>
<div class="main-wrap-content main-detail">
    <div class="product-highlights-container">
        <!-- 1. Headline -->
        <h2 class="highlight-headline">
            Responsive cushioning and a tailored fit supercharge your breakaway speed.
        </h2>

        <!-- 2. Product Specs Grid -->
        <div class="specs-grid">
            <!-- Item 1: Advantage -->
            <div class="spec-item">
                <div class="spec-icon">
                    <!-- Icon Speed/Stopwatch -->
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="13" r="8" />
                        <path d="M12 9v4l2 2" />
                        <path d="M5 3 2 6" />
                        <path d="m22 6-3-3" />
                        <path d="M12 2v3" />
                    </svg>
                </div>
                <div class="spec-label">
                    Advantage <span class="info-icon">i</span>
                </div>
                <div class="spec-value">Speed</div>
            </div>

            <!-- Item 2: Fit -->
            <div class="spec-item">
                <div class="spec-icon">
                    <!-- Icon Fit/Sole -->
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2a4 4 0 0 0-4 4v12a4 4 0 0 0 8 0V6a4 4 0 0 0-4-4z" />
                        <path d="M6 12h2" />
                        <path d="M16 12h2" />
                    </svg>
                </div>
                <div class="spec-label">
                    Fit <span class="info-icon">i</span>
                </div>
                <div class="spec-value">Snug</div>
            </div>

            <!-- Item 3: Designed For -->
            <div class="spec-item">
                <div class="spec-icon">
                    <!-- Icon Grass/Firm Ground -->
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 20h16" />
                        <path d="M6 20v-6" />
                        <path d="M10 20v-8" />
                        <path d="M14 20v-5" />
                        <path d="M18 20v-7" />
                    </svg>
                </div>
                <div class="spec-label">
                    Designed For <span class="info-icon">i</span>
                </div>
                <div class="spec-value">Firm Ground</div>
            </div>

            <!-- Item 4: Tier -->
            <div class="spec-item">
                <div class="spec-icon">
                    <!-- Icon Tier/Shield/Stars -->
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg>
                </div>
                <div class="spec-label">
                    Tier <span class="info-icon">i</span>
                </div>
                <div class="spec-value">Pro</div>
            </div>
        </div>

        <!-- 3. What's New Section -->
        <div class="whats-new-section">
            <h3 class="whats-new-title">What's New?</h3>
            <ul class="whats-new-list">
                <li>Lighter than the Superfly 10 Pro.</li>
                <li>FlyWeave upper provides a streamlined fit.</li>
            </ul>
        </div>
        <div class="product-size-chart">
            <h3 class="size-title">Size Chart </h3>

            <table class="size-table">
                <thead>
                    <tr>
                        <th>EU</th>
                        <th>US</th>
                        <th>UK</th>
                        <th>Foot Length (cm)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>38</td>
                        <td>6</td>
                        <td>5</td>
                        <td>24.0</td>
                    </tr>
                    <tr>
                        <td>39</td>
                        <td>6.5</td>
                        <td>5.5</td>
                        <td>24.5</td>
                    </tr>
                    <tr>
                        <td>40</td>
                        <td>7</td>
                        <td>6</td>
                        <td>25.0</td>
                    </tr>
                    <tr>
                        <td>41</td>
                        <td>8</td>
                        <td>7</td>
                        <td>26.0</td>
                    </tr>
                    <tr>
                        <td>42</td>
                        <td>8.5</td>
                        <td>7.5</td>
                        <td>26.5</td>
                    </tr>
                    <tr>
                        <td>43</td>
                        <td>9.5</td>
                        <td>8.5</td>
                        <td>27.5</td>
                    </tr>
                    <tr>
                        <td>44</td>
                        <td>10</td>
                        <td>9</td>
                        <td>28.0</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- start product sale -->
    <div class="slide-product">
        <span>You might also like </span>
        <div class="slide-btn">
            <button class="btn-dir slide-btn-prev"><i class="fa-solid fa-angle-left"></i></button>
            <button class="btn-dir slide-btn-next active"><i class="fa-solid fa-angle-right"></i></button>
        </div>
        <ul class="slide">
            <?php foreach ($listpro as $pro): ?>
                <li class="slide-item">
                    <a href="<?php echo _HOST_URL . "/?module=index&action=index_pro_detail&id=" . $pro['id']; ?>">
                        <div class="product">
                            <img class="img-product-sale" src="<?php echo $pro['thumbnail']; ?>" alt="">
                            <div class="product-infor">
                                <span class="product-name"><?php echo $pro['name']; ?></span>
                                <span class="product-des"><?php echo $pro['category_name']; ?></span>
                                <span class="product-price-sale"><?= number_format($pro['sale_price'], 0, '.', ',') ?>
                                    <i class="icon-pro-mon fa-solid fa-dong-sign"></i></span>
                            </div>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <!-- end product sale -->
</div>
<!-- end main -->
<script>
    const items = document.querySelectorAll(".item-sidebar");

    items.forEach(item => {
        item.querySelector(".item-filter").addEventListener("click", () => {
            item.classList.toggle("active2");
        });
    });

    // hover img
    const img_pro = document.querySelectorAll(".item-img");
    const img_thumb = document.getElementById("img-thumb");
    img_pro.forEach(item => {
        item.addEventListener("mouseenter", function() {
            img_thumb.src = this.dataset.image;
        });
    });

    const active_thumb = document.querySelectorAll(".img_c");
    active_thumb.forEach(item => {
        item.addEventListener("click", function() {
            active_thumb.forEach(img => {
                img.classList.remove("active_thumbnail");
            });
            this.classList.add("active_thumbnail");
        });
    });
    const select_si = document.querySelectorAll(".select_size");
    select_si.forEach(item => {
        item.addEventListener("click", function() {
            select_si.forEach(img => {
                img.classList.remove("active_thumbnail");
            });
            this.classList.add("active_thumbnail");
        });
    });

    // click đổi màu
    const colors = document.querySelectorAll(".img_c");
    const thumbs = document.querySelectorAll(".item-img");
    const mainImg = document.getElementById("img-thumb");
    const sizes = document.querySelectorAll(".size-item");


    const inputColor = document.getElementById("selected_color_id");
    const inputSize = document.getElementById("selected_size_id");

    // Lấy màu mặc định
    const defaultColor = document.querySelector(".img_c.active_thumbnail")?.dataset?.color;

    function filterByColor(colorId) {
        let firstImage = null;

        thumbs.forEach(item => {
            if (item.dataset.color == colorId) {
                item.style.display = "block";
                if (!firstImage) {
                    firstImage = item.dataset.image;
                }
            } else {
                item.style.display = "none";
            }
        });

        sizes.forEach(size => {
            if (size.dataset.color == colorId) {
                size.style.display = "flex";
            } else {
                size.style.display = "none";
                size.classList.remove("active_size");
            }
        });

        if (firstImage) {
            mainImg.src = firstImage;
        }
    }

    if (defaultColor) {
        filterByColor(defaultColor);
    }

    colors.forEach(color => {
        color.addEventListener("click", function() {
            colors.forEach(c => c.classList.remove("active_thumbnail"));
            this.classList.add("active_thumbnail");

            const colorId = this.dataset.color;

            if (inputColor) inputColor.value = colorId;
            if (inputSize) inputSize.value = "";

            filterByColor(colorId);
        });
    });

    sizes.forEach(size => {
        size.addEventListener("click", function() {
            sizes.forEach(s => s.classList.remove("active_size"));

            this.classList.add("active_size");
            const sizeId = this.dataset.sizeId;
            if (inputSize) inputSize.value = sizeId;

            console.log(" Curent size ID in form:", inputSize.value);
        });
    });

    //Favorite
    document.addEventListener("DOMContentLoaded", function() {

        const favoriteBtn = document.getElementById("click_far");

        if (!favoriteBtn) return;

        favoriteBtn.addEventListener("click", function() {
            const productId = this.dataset.product;

            $.ajax({
                url: "?module=index&action=favorite",
                type: "POST",
                data: {
                    product_id: productId
                },
                dataType: "json",
                success: function(response) {
                    switch (response.status) {
                        case 'success':
                        case true:
                            favoriteBtn.classList.toggle("active_far", response.favorite);
                            break;

                        case 'login_required':
                            Swal.fire({
                                icon: 'warning',
                                title: 'Login Required',
                                text: response.message ||
                                    'You need to log in to favorite this product.'
                            }).then(() => {
                                window.location.href = '?module=users&action=login';
                            });
                            break;

                        case 'error':
                        default:
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: response.message || 'An error occurred.'
                            });
                            break;
                    }
                },
                error: function(xhr, status, error) {
                    let message = 'An unexpected error occurred. Please try again.';
                    try {
                        let res = JSON.parse(xhr.responseText);
                        if (res.message) message = res.message;
                    } catch (e) {}

                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: message
                    });
                }
            });
        });

    });


    jQuery(document).ready(function($) {

        console.log("Hệ thống AJAX giỏ hàng đã sẵn sàng!");

        $('#add-to-cart-form').on('submit', function(e) {
            e.preventDefault();

            let formData = $(this).serialize();
            let formAction = $(this).attr('action');

            $.ajax({
                url: formAction,
                type: 'POST',
                data: formData,
                dataType: 'json',

                success: function(response) {

                    switch (response.status) {

                        case 'success':
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            break;

                        case 'login_required':
                            Swal.fire({
                                icon: 'warning',
                                title: 'Login Required',
                                text: response.message
                            }).then(() => {
                                window.location.href = '?module=users&action=login';
                            });
                            break;

                        case 'error':
                        default:
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: response.message
                            });
                            break;
                    }
                },

                error: function(xhr, status, error) {

                    console.log(xhr.responseText);

                    let message = 'An unexpected error occurred. Please try again.';

                    try {
                        let response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            message = response.message;
                        }
                    } catch (e) {}

                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: message
                    });
                }
            });

        });

    });

    var cacNut = document.querySelectorAll('.accordion-header');

    for (var i = 0; i < cacNut.length; i++) {
        cacNut[i].onclick = function() {

            this.parentElement.classList.toggle('active');
        };
    }
</script>
<?php
layout('/index/footer');
?>