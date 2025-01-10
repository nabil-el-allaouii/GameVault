<?php 
    require_once "classes.php";

    if(isset($_POST["submit_review"])){
        $review_content = $_POST["comment"];
        $review_rating = $_POST["rating"];
        $game_id = $_POST["gameID"];
        $user_id = $_SESSION["user_id"];
        if(!empty($review_content) && !empty($review_rating)){
            $newReview = new review();
            $newReview->SubmitReview($review_content,$review_rating,$user_id,$game_id);
            header("location: game-details.php?Game_id={$game_id}");
        }
    }
?>