<?php
require_once "classes.php";

if (!isset($_SESSION["user_id"]) || !isset($_GET["gameID"])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$game_id = $_GET["gameID"];

$userLib = new UserLibrary();
$userLib->removeFromFavorites($game_id, $user_id);

header("Location: dashboard.php#favorites");
exit();
