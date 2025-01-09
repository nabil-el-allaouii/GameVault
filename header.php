<?php
require_once 'database.php';
require_once 'classes.php';


$profile_pic = "";

$defaultPic = new users("", "", "");
if (isset($_SESSION["username"])) {
    $profile_pic = $defaultPic->getDefaultProfilePic($_SESSION["user_id"]);
}


?>

<header class="header">
    <style>
        .profile-pic {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
    <div class="container">
        <div class="row">
            <div class="col-lg-2">
                <div class="header__logo">
                    <a href="./index.php">
                        <img src="img/Game vault.png" alt="">
                    </a>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="header__nav">
                    <nav class="header__menu mobile-menu">
                        <ul>
                            <li class="active"><a href="./index.php">Homepage</a></li>
                            <li><a href="#">Categories <span class="arrow_carrot-down"></span></a>
                                <ul class="dropdown">
                                    <li><a href="#">Categories</a></li>
                                    <li><a href="#">Game Details</a></li>
                                    <li><a href="#">Blog Details</a></li>
                                </ul>
                            </li>
                            <li><a href="#">Our Blog</a></li>
                            <li><a href="#">Contacts</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="header__right">
                    <a href="#" class="search-switch"><span class="icon_search"></span></a>
                    <?php
                    if (isset($_SESSION['username'])) {
                        if ($_SESSION['user_role'] === 'admin') {
                            echo '<a href="./admin_dashboard.php"><img src="' . $profile_pic . '" alt="Profile Picture" class="profile-pic"></a>';
                        }
                        elseif($_SESSION['user_role'] !='admin'){
                        echo '<a href="./dashboard.php"><img src="' . $profile_pic . '" alt="Profile Picture" class="profile-pic"></a>';
                        }
                    } else {
                        echo '<a href="./login.php"><span class="icon_profile"></span></a>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div id="mobile-menu-wrap"></div>
    </div>
</header>