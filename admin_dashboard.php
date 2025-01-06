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
    ?>


    <div class="dashboard">
        <aside class="sidebar" id="sidebar">
            <nav>
                <ul>
                    <li><a href="#" onclick="showSection('create_game')">Create game</a></li>
                    <li><a href="#" onclick="showSection('manage_users  ')">Manage users</a></li>
                    <li><a href="#" onclick="showSection('manage_roles')">Manage roles</a></li>
                    <!-- <li><a href="#">Chat</a></li> -->
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
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
                        <input type="text" id="release_date" name="release_date" placeholder="YYYY-MM-DD HH:MI:SS" required>
                    </div>

                    <input type="submit" value="Create Game" name="create">
                </form>
            </div>

            <div id="manage_users" class="content-section" style="display: none;">
                <div class="profile-card">
                    <h2>Manage users</h2>
                    <!-- <form action="dashboard.php" method="post" enctype="multipart/form-data">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required>
                        <label for="imageUpload">Profile Picture:</label>
                        <input type="text" id="imageUpload" name="imageUpload" placeholder="Enter image URL">
                        <button type="submit" name="update_profile">Update Profile</button>
                    </form> -->
                </div>
            </div>

            <div id="manage_roles" class="content-section" style="display: none;">
                <h2>Manage roles</h2>
                <!-- <p>Details about your games will be displayed here.</p> -->
            </div>

            <div id="welcome" class="content-section">
                <h2>Welcome to admin Dashboard</h2>
                <p>Select an option from the sidebar to create games, manages profile manage roles.</p>
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