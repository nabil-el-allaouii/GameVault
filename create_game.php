<?php
require_once "classes.php";

if (isset($_POST['create'])) {
    $title = $_POST['title'];
    $image = $_POST['image'];
    $genre = $_POST['genre'];
    $description = $_POST['description'];
    $screenshots = $_POST['screenshots'];
    $release_date = $_POST['release_date'];

    // Validate main image
    if (strpos($image, '.png') !== false || strpos($image, '.jpg') !== false || strpos($image, '.jpeg') !== false) {

        // Validate each screenshot URL
        $valid_screenshots = true;
        foreach ($screenshots as $screenshot) {
            if (
                strpos($screenshot, '.png') === false && strpos($screenshot, '.jpg') === false && strpos($screenshot, '.jpeg') === false){
                $valid_screenshots = false;
                break;
            }
        }

        if ($valid_screenshots) {
            $game = new Game($title, $image, $genre, $description, $screenshots, $release_date);

            if ($game->createGame()) {
                header("Location: admin_dashboard.php?success=Game created successfully");
                exit();
            } else {
                header("Location: admin_dashboard.php?error=Failed to create game");
                exit();
            }
        } else {
            header("Location: admin_dashboard.php?error=Invalid screenshot format");
            exit();
        }
    } else {
        header("Location: admin_dashboard.php?error=Invalid image format");
        exit();
    }
}
