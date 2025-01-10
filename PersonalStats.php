<?php 
    require_once "classes.php";
    if(isset($_POST["update_stats"])){
       $personal_score =  $_POST["personalScore"];
       $playtime = $_POST["playTime"];
       $gameStatus = $_POST["gameStatus"];


       $newUpdateStats = new Users("","","");
       $newUpdateStats->AddPersonalStats($gameStatus,$personal_score,$playtime);

       header("location: dashboard.php");
    }

?>