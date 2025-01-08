<?php
require_once "classes.php";
$user_id = $_GET["id"];
$newBan = new admin();

if ($newBan->unbanUser($user_id)) {
    header("Location: admin_dashboard.php");
    exit();
} else {
    header("Location: admin_dashboard.php?error");
    exit();
}
?>