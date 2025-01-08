<?php 

if (isset($_POST['update_profile'])) {
    $updated_username = $_POST['username'];
    $updated_pic = $_POST['imageUpload'];
    if (!empty($updated_pic)) {
        if(strpos($updated_pic , ".png") !== false || strpos($updated_pic , ".jpg") !== false || strpos($updated_pic , ".jpeg") !== false){
            $newModify = new users($updated_username, $_SESSION["user_id"], $updated_pic);
            $newModify->modify();
            header("location: admin_dashboard.php");
            exit();
        }else{
            header("location: admin_dashboard.php?error= invalid image format");
            exit();
        }
    } else {
        $defaultPic = new users("", "", "");
        $pic = $defaultPic->getDefaultProfilePic($_SESSION["user_id"]);
        $newModify = new users($updated_username, $_SESSION["user_id"], $pic);
        $newModify->modify();
        header("location: admin_dashboard.php");
    }
}

