<?php
layout('/index/header', 'Home');
$list_cou = getAll("SELECT * FROM coupons WHERE status=1");
$getData = filterData('GET');
$user_id = $getData['id'] ?? null;

if (empty($user_id)) {
    setSessionFlash('msg', 'Please login first.');
    redirect('?module=users&action=login');
}

$listFavorite = getAll("
    SELECT
        f.id AS favorite_id,
        p.id,
        p.name,
        p.thumbnail,
        c.name AS category_name,
        b.name AS brand_name
    FROM favorites f
    INNER JOIN products p
        ON p.id = f.product_id
    LEFT JOIN categories c
        ON c.id = p.category_id
    LEFT JOIN brands b
        ON b.id = p.brand_id
    WHERE f.user_id = $user_id
    ORDER BY f.created_at DESC
");
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
<div class="main-wrap-content main-wrap-fav">

    <div class="favorite-page">

        <div class="favorite-header">

            <?php if (!empty($listFavorite)): ?>
                <span class="favorite-count">
                    <?= count($listFavorite) ?> Products
                </span>
            <?php endif; ?>

        </div>

        <?php if (!empty($listFavorite)): ?>

            <div class="favorite-list">

                <?php foreach ($listFavorite as $item): ?>

                    <div class="favorite-item">

                        <a href="?module=index&action=index_pro_detail&id=<?= $item['id'] ?>" class="favorite-image">

                            <?php if (!empty($item['thumbnail'])): ?>

                                <img src="<?= $item['thumbnail'] ?>" alt="<?= htmlspecialchars($item['name']) ?>">

                            <?php else: ?>

                                <img src="<?= _HOST_URL_TEMPLATES ?>/assets/images/no-image.png" alt="No Image">

                            <?php endif; ?>

                        </a>

                        <div class="favorite-info">

                            <a href="?module=index&action=index_pro_detail&id=<?= $item['id'] ?>" class="favorite-name">

                                <?= htmlspecialchars($item['name']) ?>

                            </a>

                            <div class="favorite-meta">

                                <span>
                                    <?= htmlspecialchars($item['category_name']) ?>
                                </span>

                                <span>
                                    <?= htmlspecialchars($item['brand_name']) ?>
                                </span>

                            </div>

                        </div>

                        <a href="?module=favorite&action=delete&id=<?= $item['favorite_id'] ?>" class="favorite-remove"
                            onclick="return confirm('Remove this product from favorites?')">

                            <i class="fa-solid fa-trash"></i>

                        </a>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="favorite-empty">

                <i class="fa-regular fa-heart"></i>

                <h3>Your favorite list is empty</h3>

                <p>Save the products you love to find them quickly later.</p>

                <a href="?module=index&action=index" class="favorite-btn">
                    Continue Shopping
                </a>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php
layout('/index/footer');
?>