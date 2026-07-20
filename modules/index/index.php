<?php
layout('/index/header', 'Home');
$catechild = getAll("SELECT * FROM categories ");
$listbanner = getAll("SELECT * FROM banners WHERE status = 1 ORDER BY sort_order ASC");

$list_pro_feat = getAll("
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
    WHERE p.is_featured = 1
    GROUP BY p.id
    ORDER BY p.created_at ASC
    LIMIT 8
");
$list_pro = getAll("
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
    GROUP BY p.id
    ORDER BY p.created_at ASC
    LIMIT 8
");
$list_pro_best = getAll("
    SELECT
        p.id,
        p.name,
        p.thumbnail,
        c.name AS category_name,
        MIN(pv.sale_price) AS sale_price
    FROM order_items oi
    INNER JOIN products p
        ON p.id = oi.product_id
    LEFT JOIN categories c
        ON c.id = p.category_id
    LEFT JOIN product_variants pv
        ON pv.product_id = p.id
    GROUP BY p.id
    ORDER BY p.created_at DESC
    LIMIT 8
");
$list_pro_spot = getAll("
    SELECT
        id,
        name,
        thumbnail
    FROM products
    WHERE is_spotlight = 1
      AND status = 1
    ORDER BY created_at DESC
    LIMIT 8
");

?>

<!-- start main -->
<div class="main-wrap-content">
    <!-- start sidebar -->
    <div class="sidebar">
        <div class="sidebar-list">
            <?php foreach ($listbanner as $banner): ?>
                <?php if ($banner['sort_order'] != 0): ?>
                    <a href="<?php echo _HOST_URL . "/?module=index&action=index_pro_cate&id=" . $banner['category_id']; ?>"
                        class="sidebar-anchor" style="background-image: url('<?= $banner['thumbnail'] ?>');">
                        <div class="sidebar-item">
                            <div class="content-sidebar">
                                <h2 style="text-transform: uppercase"><?php echo $banner['title']; ?></h2>
                                <span class="sidebar-des"><?php echo $banner['description']; ?></span>
                                <button class="sidebar-button btn">Shop</button>
                            </div>
                        </div>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <div class="slide-btn sidebar-btn">
            <button class="btn-dir sidebar-btn-prev"><i class="fa-solid fa-angle-left"></i></button>
            <button class="btn-dir sidebar-btn-next active"><i class="fa-solid fa-angle-right"></i></button>
        </div>
    </div>
    <!-- end sidebar -->
    <!-- start trending -->
    <div class="slide-topic">
        <span>Featured</span>
        <div class="slide-btn">
            <button class="btn-dir slide-btn-prev"><i class="fa-solid fa-angle-left"></i></button>
            <button class="btn-dir slide-btn-next active"><i class="fa-solid fa-angle-right"></i></button>
        </div>
        <ul class="slide">
            <?php foreach ($list_pro_feat as $pro): ?>
                <li class="slide-item">
                    <a href="<?php echo _HOST_URL . "/?module=index&action=index_pro_detail&id=" . $pro['id']; ?>">
                        <div class="topic">
                            <img class="trending" src="<?php echo $pro['thumbnail']; ?>" alt="">
                            <div class="topic-infor">
                                <span class="topic-name"><?php echo $pro['name']; ?></span>
                                <span class="topic-des"><?php echo $pro['gender_name']; ?></span>
                                <button class="topic-btn btn">Shop</button>
                            </div>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <!-- end trending -->
    <!-- start product new -->
    <div id="new-arrivals" class="slide-product">
        <span>New Arrivals</span>
        <div class="slide-btn">
            <button class="btn-dir slide-btn-prev"><i class="fa-solid fa-angle-left"></i></button>
            <button class="btn-dir slide-btn-next active"><i class="fa-solid fa-angle-right"></i></button>
        </div>
        <ul class="slide">
            <?php foreach ($list_pro as $pro): ?>
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
    <!-- end product new -->

    <!-- start product sale -->
    <div id="new-best" class="slide-product">
        <span>Best Sellers</span>
        <div class="slide-btn">
            <button class="btn-dir slide-btn-prev"><i class="fa-solid fa-angle-left"></i></button>
            <button class="btn-dir slide-btn-next active"><i class="fa-solid fa-angle-right"></i></button>
        </div>
        <ul class="slide">
            <?php foreach ($list_pro_best as $pro): ?>
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
    <!-- start Shop by sport -->
    <div class="slide-topic">
        <span>Shop by sport</span>
        <div class="slide-btn">
            <button class="btn-dir slide-btn-prev"><i class="fa-solid fa-angle-left"></i></button>
            <button class="btn-dir slide-btn-next active"><i class="fa-solid fa-angle-right"></i></button>
        </div>
        <ul class="slide">
            <?php foreach ($catechild as $key => $cate): ?>
                <?php if ($cate['sort_order'] === 4): ?>
                    <li class="slide-item">
                        <a href="<?php echo _HOST_URL . "/?module=index&action=index_pro_cate&id=" . $cate['id']; ?>">
                            <div class="album">
                                <img src="<?php echo $cate['thumbnail']; ?>" alt="">
                                <div class="album-infor">
                                    <span><?php echo $cate['name']; ?></span>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
    <!-- end Shop sport-->
    <!-- start spotlight -->
    <div class="spotlight">
        <div class="title">
            <h1>Spotlight</h1>
            <span>Classic silhouettes and cutting-edge innovation to build your game from the ground up.</span>
        </div>
        <div class="grid-spotlight">
            <?php foreach ($list_pro_spot as $pro_spot): ?>
                <div class="grid-item">
                    <a href="<?php echo _HOST_URL . "/?module=index&action=index_pro_detail&id=" . $pro_spot['id']; ?>"
                        class="link-spotlight">
                        <img class="img_spot" src="<?php echo $pro_spot['thumbnail'] ?> " alt="">
                        <span><?php echo $pro_spot['name'] ?> </span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- end spotlight -->
</div>
<!-- end main -->
<?php
layout('/index/footer');
?>