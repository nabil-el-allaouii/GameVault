
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
    <?php include "header.php"; 
     if(!isset($_SESSION["username"])){
        header("location: index.php");
    }?>
    

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
                <h2>Game Library</h2>
                <p>Here you can manage your game library.</p>
                <button class="add-game-btn">Add Game</button>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Rating</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Game entries will be dynamically generated here -->
                    </tbody>
                </table>
            </div>

            <div id="profile" class="content-section" style="display: none;">
                <h2>User Profile</h2>
                <form>
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>

                    <button type="submit">Update Profile</button>
                </form>
            </div>

            <div id="game-details" class="content-section" style="display: none;">
                <h2>Game Details</h2>
                <p>Details about your games will be displayed here.</p>
            </div>

            <div id="welcome" class="content-section">
                <h2>Welcome to Your Dashboard</h2>
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