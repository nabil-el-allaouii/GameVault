<?php
require_once "classes.php";

if (!isset($_SESSION["user_id"]) || !isset($_GET["GameID"])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$game_id = $_GET["GameID"];

$userLib = new UserLibrary();
$userLib->addToFavorites($_GET["GameID"], $user_id);
header("Location: dashboard.php#favorites");
