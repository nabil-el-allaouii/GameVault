<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="css/dashboard-style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/plyr.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
</head>

<body>
    <?php
    require_once "database.php";
    include "header.php";
    require_once "classes.php";

    if (!isset($_SESSION["username"])) {
        header("location: index.php");
    }

    if (isset($_POST['update_profile'])) {
        $updated_username = $_POST['username'];
        $updated_pic = $_POST['imageUpload'];
        if (!empty($updated_pic)) {
            $newModify = new users($updated_username, $_SESSION["user_id"], $updated_pic);
            $newModify->modify();
            header("location: dashboard.php");
        } else {
            $defaultPic = new users("", "", "");
            $pic = $defaultPic->getDefaultProfilePic($_SESSION["user_id"]);
            $newModify = new users($updated_username, $_SESSION["user_id"], $pic);
            $newModify->modify();
            header("location: dashboard.php");
        }
    }



    ?>


    <div class="dashboard">
        <aside class="sidebar" id="sidebar">
            <nav>
                <ul>
                    <li><a href="#" onclick="showSection('library')">Library</a></li>
                    <li><a href="#" onclick="showSection('profile')">Profile</a></li>
                    <li><a href="#" onclick="showSection('game-details')">Game Details</a></li>
                    <li><a href="#">Chat</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <div id="library" class="content-section" style="display: none;">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="trending__product">
                                <div class="row">
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <div class="section-title">
                                            <h4>Your Games</h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="btn__all">
                                            <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="product__item">
                                            <div class="product__item__pic set-bg">
                                                <img src="img/trending/trend-4.jpg" alt="">
                                                <div class="ep">18 / 18</div>
                                                <div class="comment"><i class="fa fa-comments"></i> 11</div>
                                                <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                                <div class="game__details__overlay">
                                                    <div class="game__stat">
                                                        <i class="fa fa-star"></i>
                                                        <span>Personal Score: 8.5/10</span>
                                                    </div>
                                                    <div class="game__stat">
                                                        <i class="fa fa-clock-o"></i>
                                                        <span>Playtime: 45h</span>
                                                    </div>
                                                    <div class="game__stat">
                                                        <i class="fa fa-gamepad"></i>
                                                        <span>Status: In Progress</span>
                                                    </div>
                                                    <button class="remove-game-btn">
                                                        <i class="fa fa-trash"></i> Remove from Library
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="product__item__text">
                                                <ul>
                                                    <li>Active</li>
                                                    <li>Movie</li>
                                                </ul>
                                                <h5><a href="#">Code Geass: Hangyaku no Lelouch R2</a></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="profile" class="content-section" style="display: none;">
                <div class="profile-card">
                    <h2>User Profile</h2>
                    <form action="dashboard.php" method="post" enctype="multipart/form-data">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" placeholder="Enter your username" value="<?php $TheUser = new Rendering();
                                                    $shownUser = $TheUser->showUser();?>" required>
                        <label for="imageUpload">Profile Picture:</label>
                        <input type="text" id="imageUpload" name="imageUpload" placeholder="Enter image URL">
                        <button type="submit" name="update_profile">Update Profile</button>
                    </form>
                </div>
            </div>

            <div id="game-details" class="content-section" style="display: none;">
                <h2>Game Details</h2>
                <p>Details about your games will be displayed here.</p>
            </div>

            <div id="welcome" class="content-section">
                <h2>Welcome to Your Dashboard <?php $TheUser = new Rendering();
                                                $shownUser = $TheUser->showUser();?></h2>
                <p>Select an option from the sidebar to manage your games, update your profile, or view game details.</p>
            </div>
        </main>
    </div>

    <?php include "footer.php"; ?>

    <script>
        function showSection(section) {
            // Hide all sections
            const sections = document.querySelectorAll('.content-section');
            sections.forEach((sec) => {
                sec.style.display = 'none';
            });

            // Show the selected section
            document.getElementById(section).style.display = 'block';
        }

        // Show the welcome section by default
        showSection('welcome');
    </script>
</body>

</html>