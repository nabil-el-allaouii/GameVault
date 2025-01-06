<?php   
require_once "classes.php";

if (isset($_POST['create'])) {
    $title = $_POST['title'];
    $image = $_POST['image'];
    $genre = $_POST['genre'];
    $description = $_POST['description'];
    $screenshots = $_POST['screenshots'];
    $release_date = $_POST['release_date'];

    // Create new game instance
    $game = new Game($title, $image, $genre, $description, $screenshots, $release_date);

    // Try to create the game
    if ($game->createGame()) {
        header("Location: admin_dashboard.php?success=Game created successfully");
        exit();
    } else {
        header("Location: admin_dashboard.php?error=Failed to create game");
        exit();
    }
}
