<?php
require_once 'classes.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $admin = new admin();
    if ($admin->editGame($_POST['game_id'])) {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: admin_dashboard.php?error=Failed to update game');
    }
    exit();
}
header('Location: admin_dashboard.php');
