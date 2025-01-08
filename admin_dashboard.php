<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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

    if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
        header("location: index.php");
        exit();
    }
    $admin = new admin();

    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        if ($admin->deleteGame($_GET['id'])) {
            header("Location: admin_dashboard.php?success=Game deleted successfully");
            exit();
        } else {
            header("Location: admin_dashboard.php?error=Failed to delete game");
            exit();
        }
    }

    include "admin_dashboard.inc.php";
    ?>


    <div class="dashboard">
        <aside class="sidebar" id="sidebar">
            <nav>
                <ul>
                    <li><a href="#" onclick="showSection('edit_profile')">Edit profile</a></li>
                    <li><a href="#" onclick="showSection('library')">Manage games</a></li>
                    <li><a href="#" onclick="showSection('create_game')">Create game</a></li>
                    <li><a href="#" onclick="showSection('manage_users')">Manage users</a></li>
                    <li><a href="#" onclick="showSection('manage_roles')">Manage roles</a></li>
                    <!-- <li><a href="#">Chat</a></li> -->
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
                                            <h4>All Games</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <?php
                                    $admin = new admin();
                                    $admin->renderAllGames();
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="edit_profile" class="content-section" style="display: none;">
                <div class="profile-card">
                    <h2>Admin Profile</h2>
                    <form action="admin_dashboard.php" method="post" enctype="multipart/form-data">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" placeholder="Enter your username" value="<?php $TheUser = new Rendering();
                                                                                                                    $shownUser = $TheUser->showUser(); ?>" required>
                        <label for="imageUpload">Profile Picture:</label>
                        <input type="text" id="imageUpload" name="imageUpload" placeholder="Enter image URL">
                        <button type="submit" name="update_profile">Update Profile</button>
                    </form>
                </div>
            </div>

            <div id="edit_profile" class="content-section" style="display: none;">
                <div class="profile-card">
                    <h2>Edit Profile</h2>
                    <div class="users-list">
                        <?php $admin->banPlayer(); ?>
                        <hr style="border: 1px solid white;">
                        <?php $admin->unbanPlayer(); ?>
                    </div>
                </div>
            </div>

            <div id="create_game" class="content-section" style="display: none;">
                <h2 style="text-align: center; color: white; margin-bottom: 20px;">Create Game</h2>
                <form method="POST" action="create_game.php">
                    <div class="form-group">
                        <label for="title">Game Title:</label>
                        <input type="text" id="title" name="title" placeholder="Enter game title" required>
                    </div>

                    <div class="form-group">
                        <label for="image">Game Image:</label>
                        <input type="url" id="image" name="image" placeholder="Enter image URL" required>
                    </div>

                    <div class="form-group">
                        <label for="genre">Game Genre:</label>
                        <input type="text" id="genre" name="genre" placeholder="Enter game genre" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Game Description:</label>
                        <textarea id="description" name="description" placeholder="Enter game description" required></textarea>
                    </div>

                    <div class="form-group" id="screenshots-container">
                        <label for="screenshots">Game Screenshots:</label>
                        <input type="url" name="screenshots[]" placeholder="Enter screenshot URL" required>
                    </div>

                    <button type="button" id="add-screenshot">Add Another Screenshot</button>

                    <div class="form-group">
                        <label for="release_date">Game Release Date:</label>
                        <input type="date" id="release_date" name="release_date" placeholder="YYYY-MM-DD HH:MI:SS" required>
                    </div>

                    <input type="submit" value="Create Game" name="create">
                </form>
            </div>

            <div id="manage_users" class="content-section" style="display: none;">
                <div class="profile-card">
                    <h2>Manage Users</h2>
                    <div class="users-list">
                        <?php $admin->banPlayer(); ?>
                        <hr style="border: 1px solid white;">
                        <?php $admin->unbanPlayer(); ?>
                    </div>
                </div>
            </div>

            <?php include "manage_roles.php"; ?>

            <div id="welcome" class="content-section">
                <h2>Welcome to admin dashboard, admin: <?php $TheUser = new Rendering(); 
                                                         $shownUser = $TheUser->showUser(); ?></h2>
                <p>Select an option from the sidebar to create a new game, manage profile or manage roles.</p>
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

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('add-screenshot').addEventListener('click', function() {
                const container = document.getElementById('screenshots-container');
                const newInput = document.createElement('input');
                newInput.type = 'url';
                newInput.name = 'screenshots[]'; // Changed to array notation
                newInput.placeholder = 'Enter screenshot URL';
                newInput.required = true;
                newInput.className = 'screenshot-input';
                container.appendChild(newInput);
            });
        });
    </script>
</body>

</html>