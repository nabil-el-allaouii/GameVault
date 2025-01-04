<?php 
require_once 'database.php';
require_once 'classes.php';

$register = new Register();

$profile_pic = "";
if (isset($_SESSION['user_id'])) {
    $profile_pic = $register->getProfilePic($_SESSION['user_id']);
}
 

?>

<header class="header">
<style>
    .profile-pic {
        width: 35px;
        height: 35px;
      
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
                                        <li><a href="./anime-details.php">Anime Details</a></li>
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
                            if(isset($_SESSION['username'])){                            
                                echo '<a href="./dashboard.php"><img src="' . $profile_pic . '" alt="Profile Picture" class="profile-pic"></a>';
                            }else{
                                echo '<a href="./login.php"><span class="icon_profile"></span></a>';
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div id="mobile-menu-wrap"></div>
        </div>
    </header>
