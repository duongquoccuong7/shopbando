<?php
$user_id = getSession('user_id');
$listCate = getAll("SELECT * FROM categories WHERE parent_id = 0 AND status = 1");
$catechild = getAll("SELECT * FROM categories WHERE status = 1");
$keyword = '';
$cate = '0';

if (isGet()) {
    if (isset($filter['keyword'])) {
        $keyword = $filter['keyword'];
        setSessionFlash('key', $keyword);
    }
    if (isset($filter['cate'])) {
        $cate = $filter['cate'];
    }

    if (!empty($keyword)) {
        $redirectUrl = "/?module=index&action=index_pro_cate&keyword=" . urlencode($keyword);
        header("Location: " . $redirectUrl);
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="icon" href="<?= _HOST_URL_TEMPLATES ?>/uploads/logo.png?v=<?= time() ?>">
    <link rel="stylesheet" href="/shopbando/templates/assets/css/reset.css">
    <link rel="stylesheet" href="/shopbando/templates/assets/css/styleindex.css?v=<?php echo rand(); ?>">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto+Condensed:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <!-- Top Header -->
    <header class="wrap">
        <div class="main-wrap">
            <div class="wrap-header">
                <a href="<?php echo _HOST_URL . "/?module=index&action=index"; ?>">
                    <img src="<?= _HOST_URL_TEMPLATES ?>/uploads/logo2.png?v=<?= time() ?>" class="logo-header">
                </a>
                <div class="menu-header">
                    <ul class="list-header">
                        <?php if (!empty($user_id)): ?>
                        <li class="item-header user-dropdown-wrapper">
                            <a href="<?php echo _HOST_URL . "/?module=users&action=profile&id=" . $user_id; ?>">
                                <i class="fa-regular fa-user"></i>
                            </a>
                            <ul class="user-dropdown-menu">
                                <li><a href="<?php echo _HOST_URL . "/?module=users&action=profile&id=" . $user_id; ?>"><i
                                            class="fa-regular fa-id-card"></i> My account</a></li>
                                <li><a href="<?php echo _HOST_URL . "/?module=cart&action=order_cart"; ?>"><i
                                            class="fa-solid fa-boxes-shopping"></i> Order list</a></li>
                                <li class="divider"></li>
                                <li><a href="<?php echo _HOST_URL . "/?module=users&action=logout" ?>"
                                        class="logout-link"><i class="fa-regular fa-circle-left"></i> Logout</a></li>
                            </ul>
                        </li>
                        <?php else: ?>
                        <li class="item-header"><a
                                href="<?php echo _HOST_URL . "/?module=users&action=register" ?>">Sign Up</a></li>
                        <li class="item-header"><a href="<?php echo _HOST_URL . "/?module=users&action=login" ?>">Sign
                                In</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation Bar -->
    <nav class="main-wrap">
        <div class="wrap-nav">
            <a href="<?php echo _HOST_URL . "/?module=index&action=index"; ?>">
                <img src="<?= _HOST_URL_TEMPLATES ?>/uploads/logo.png?v=<?= time() ?>" class="logo-nav">
            </a>
            <div class="menu-nav">
                <ul class="list-nav">
                    <?php foreach ($listCate as $value): ?>
                    <li class="item-nav">
                        <div class="nav-link"><a href=""><span><?php echo $value['name']; ?></span></a></div>
                        <div class="list-menu list-menu-1">
                            <?php foreach ($catechild as $child): ?>
                            <?php if ($child['parent_id'] == $value['id']): ?>
                            <div class="item-list">
                                <ul class="menu-name">
                                    <a
                                        href="<?php echo _HOST_URL . "/?module=index&action=index_pro_cate&id=" . $child['id']; ?>">
                                        <span><?php echo $child['name']; ?></span>
                                    </a>
                                    <?php foreach ($catechild as $item_child): ?>
                                    <?php if ($item_child['parent_id'] == $child['id']): ?>
                                    <a
                                        href="<?php echo _HOST_URL . "/?module=index&action=index_pro_cate&id=" . $item_child['id']; ?>">
                                        <li class="menu-item"><?php echo $item_child['name']; ?></li>
                                    </a>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="menu-search">
                <div class="search">
                    <button class="search-button" type="button"><i class="fa-solid fa-magnifying-glass"></i></button>
                    <input id="search_trigger" class="search-input" type="text" placeholder="Search ..." readonly>
                </div>
                <div class="favorite">
                    <a href="<?php echo _HOST_URL . "/?module=favorite&action=index&id=" . $user_id; ?>"><i
                            class="fa-regular fa-heart"></i></a>
                </div>
                <div class="cart">
                    <a href="<?php echo _HOST_URL . "/?module=cart&action=index&id=" . $user_id; ?>"><i
                            class="fa-solid fa-cart-arrow-down"></i></a>
                </div>

                <!-- Form Overlay Nike Search -->
                <form action="" method="GET">
                    <input type="hidden" name="module" value="index">
                    <input type="hidden" name="action" value="index_pro_cate">
                    <div id="search_backdrop" class="nike-search-backdrop"></div>
                    <div id="search_overlay" class="nike-search-overlay">
                        <div class="nike-search-header">
                            <div class="nike-search-box">
                                <button type="submit" class="nike-search-btn"><i
                                        class="fa-solid fa-magnifying-glass"></i></button>
                                <input value="<?php echo !empty($keyword) ? $keyword : ''; ?>" name="keyword"
                                    id="search_main_input" class="nike-search-input" type="text"
                                    placeholder="Search ...">
                            </div>
                            <button id="cancel_search" type="button" class="nike-cancel-btn">Cancel</button>
                        </div>
                        <div class="nike-search-content">
                            <div class="popular-terms">
                                <p>Popular Search Terms</p>
                                <span class="nike-tag">Nike</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </nav>