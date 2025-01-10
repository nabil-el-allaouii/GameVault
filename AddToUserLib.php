<?php 
    require_once "classes.php";

    $GameID = $_GET["GameID"];
    

    $newAdd = new UserLibrary();
    $newAdd->AddGameToLib($GameID, $_SESSION["user_id"]);
    header("location: dashboard.php");
?>