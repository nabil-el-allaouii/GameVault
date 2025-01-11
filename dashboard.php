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
    if ($_SESSION["user_role"] === "admin") {
        header("location: admin_dashboard.php");
    }

    include "dashboard.inc.php";



    ?>


    <div class="dashboard">
        <aside class="sidebar" id="sidebar">
            <nav>
                <ul>
                    <li><a href="#" onclick="showSection('library')">Library</a></li>
                    <li><a href="#" onclick="showSection('favorites')">Favorites</a></li>
                    <li><a href="#" onclick="showSection('profile')">Profile</a></li>
                    <li><a href="#" onclick="showSection('history')">History</a></li>
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
                                <?php $avail = new Rendering();
                                $showAv = $avail->showGames();  ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="favorites" class="content-section" style="display: none;">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="trending__product">
                                <div class="row">
                                    <div class="col-lg-8 col-md-8 col-sm-8">
                                        <div class="section-title">
                                            <h4>Your favorite Games</h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                        <div class="btn__all">
                                            <a href="#" class="primary-btn">View All <span class="arrow_right"></span></a>
                                        </div>
                                    </div>
                                </div>
                                <?php $favorites = new UserLibrary();
                                $favorites->showFavorites($_SESSION["user_id"]);  ?>
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
                                                                                                                    $shownUser = $TheUser->showUser(); ?>" required>
                        <label for="imageUpload">Profile Picture:</label>
                        <input type="text" id="imageUpload" name="imageUpload" placeholder="Enter image URL">
                        <button type="submit" name="update_profile">Update Profile</button>
                    </form>
                </div>
            </div>

            <div id="history" class="content-section" style="display: none;">
                <div class="history-container">
                    <div class="history-header">
                        <h2>Gaming History</h2>
                        <p>Your recently visited games</p>
                    </div>
                    <div class="history-list">
                        <?php $newHistory = new Rendering();
                        $newHistory->showHistorique(); ?>
                    </div>
                </div>
            </div>

            <div id="welcome" class="content-section">
                <h2>Welcome to Your Dashboard <?php $TheUser = new Rendering();
                                                $shownUser = $TheUser->showUser(); ?></h2>
                <p>Select an option from the sidebar to manage your games, update your profile, or view game details.</p>
            </div>
        </main>
    </div>

    <?php include "footer.php"; ?>

    <div class="game-stats-modal" id="gameStatsModal">
        <div class="game-stats-form">
            <h3>Update Game Stats</h3>
            <form action="PersonalStats.php" method="POST">
                <input type="hidden" id="gameId" name="gameId">
                <div class="form-group">
                    <label for="personalScore">Personal Score</label>
                    <input type="number" id="personalScore" name="personalScore" min="0" required>
                </div>
                <div class="form-group">
                    <label for="playTime">Play Time (hours)</label>
                    <input type="number" id="playTime" name="playTime" min="0" required>
                </div>
                <div class="form-group">
                    <label for="gameStatus">Status</label>
                    <select id="gameStatus" name="gameStatus" required>
                        <option value="in-progress">In Progress</option>
                        <option value="finished">Finished</option>
                        <option value="abandoned">Abandoned</option>
                    </select>
                </div>
                <div class="modal-buttons">
                    <button type="button" class="cancel-btn" onclick="closeGameStats()">Cancel</button>
                    <button type="submit" class="save-btn" name="update_stats">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

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

        function openGameStats() {
            document.getElementById('gameStatsModal').classList.add('active');
        }

        function closeGameStats() {
            document.getElementById('gameStatsModal').classList.remove('active');
        }
    </script>
</body>

</html>