<?php 
require_once "classes.php";
$GameIdDel = $_GET["gameID"];
$newDelete = new UserLibrary();
$delete = $newDelete->DeleteGame($GameIdDel);
header("location: dashboard.php");
