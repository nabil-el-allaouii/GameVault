<?php
include "classes.php";
$admin = new Admin();
if ($admin->removeAdmin($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
} else {
    header("Location: admin_dashboard.php?error");
    exit();
}
?>