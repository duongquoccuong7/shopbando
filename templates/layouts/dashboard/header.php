<?php
$user_id = getSession('user_id');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/shopbando/templates/assets/css/reset.css">
    <link rel="stylesheet" href="/shopbando/templates/assets/css/styles.css?v=<?php echo rand(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <title><?php echo $title; ?></title>
</head>

<body>
    <!-- start header -->
    <header>
        <div class="header-dashboar">
            <div class="dash-inforadmin">
                <div class="img-admin-dash"></div>
                <span>AdminLTE</span>
                <div class="profile-admin"><i class="fa-solid fa-bars icon-admin-profile"></i>
                    <ul class="list-ad-menu">
                        <li class="item-ad-menu"><a
                                href="<?php echo _HOST_URL . "/?module=auth&action=profile&id=" . $user_id ?>"
                                style="text-decoration: none; color:black;">My account</a></li>
                        <li class="item-ad-menu"><a href="<?php echo _HOST_URL . "/?module=auth&action=logout" ?>"
                                style="text-decoration: none; color:black;">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="dash-infor">
                <div class="dash-infor-left">
                    <span>Home / Dashboar</span>
                </div>
                <div class="dash-infor-right">
                    <a href="<?php echo _HOST_URL . "/?module=auth&action=logout" ?>">
                        <i class="fa-regular fa-bell icon-bell"></i></a>
                    <form action="">
                        <div class="form-search">
                            <i class="fa-solid fa-magnifying-glass icon-search"></i>
                            <input type="text" placeholder="search">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </header>
    <!-- end header -->