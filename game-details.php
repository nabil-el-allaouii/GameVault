<?php
require_once "classes.php";

if (!isset($_GET["Game_id"])) {
    header("location: index.php");
}

if (isset($_POST['send_message']) && isset($_SESSION['user_id'])) {
    $chat = new Chat();
    $chat->sendMessage($_GET['Game_id'], $_SESSION['user_id'], $_POST['chat_content']);
    header("Location: game-details.php?Game_id=" . $_GET['Game_id']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Anime Template">
    <meta name="keywords" content="Anime, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Game Vault</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/plyr.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link rel="stylesheet" href="css/game_details_style.css" type="text/css">
</head>

<body>

    <!-- Header Section Begin -->
    <?php include "header.php" ?>
    <!-- Header End -->

    <!-- Breadcrumb Begin -->
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="./index.php"><i class="fa fa-home"></i> Home</a>
                        <a href="#">Categories</a>
                        <span>Games</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Anime Section Begin -->
    <section class="anime-details spad">
        <div class="container">
            <?php
            require_once "database.php";

            $gameidGet = $_GET["Game_id"];
            $gameDetails = new Rendering();
            echo $Getdetails = $gameDetails->ShowGameDetails($gameidGet);

            $GetScreens = $gameDetails->ShowScreens($gameidGet);
            ?>


            <div class="row">
                <div class="col-lg-8 col-md-8">
                    <div class="anime__details__review">
                        <div class="section-title">
                            <h5>Reviews</h5>
                        </div>
                        <!-- here -->
                        <?php $reviewRender = new review();
                        $reviewRender->RenderReview($_GET["Game_id"]);
                        ?>

                    </div>
                    <div class="anime__details__form">
                        <?php if (!isset($_SESSION["user_status"])) : ?>
                            <div class="review-message review-login-required">
                                <i class="fa fa-lock"></i>
                                <p>Please <a href="login.php">login</a> to write a review</p>
                            </div>
                        <?php elseif ($_SESSION["user_status"] !== "banned" && $_SESSION["user_role"] === "player"): ?>
                            <div class="section-title">
                                <h5>Your Review</h5>
                            </div>
                            <form method="POST" action="SendReview.php">
                                <input name="gameID" type="hidden" value="<?php echo $_GET["Game_id"]; ?>">
                                <textarea name="comment" placeholder="Your Comment" required></textarea>
                                <select name="rating" required class="rating-select">
                                    <option value="" disabled selected>Select Rating /5</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                                <button type="submit" name="submit_review"><i class="fa fa-location-arrow"></i> Review</button>
                            </form>
                        <?php elseif ($_SESSION["user_role"] === "admin"): ?>

                        <?php else: ?>
                            <div class="review-message review-banned">
                                <i class="fa fa-ban"></i>
                                <p>You are banned from reviewing!</p>
                            </div>
                        <?php endif ?>

                    </div>

                    <!-- //chat -->
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="anime__details__sidebar">
                        <div class="section-title">
                            <h5>Chat Room</h5>
                        </div>
                        <div class="chat-container">
                            <div class="chat-messages">
                                <?php
                                $chat = new Chat();
                                echo $chat->getMessages($_GET['Game_id']);
                                ?>
                            </div>

                            <?php
                            if (!isset($_SESSION['user_id'])): ?>
                                <div class="chat-login-prompt">
                                    <p>Please <a href="login.php">login</a> to join the chat</p>
                                </div>
                            <?php
                            elseif (isset($_SESSION['user_status']) && $_SESSION['user_status'] === 'banned'): ?>
                                <div class="chat-banned-message">
                                    <i class="fa fa-ban"></i>
                                    <p>You are banned from chatting!</p>
                                </div>

                            <?php
                            else: ?>
                                <form method="POST" class="chat-input">
                                    <input type="text" name="chat_content" placeholder="Type your message..." required>
                                    <button type="submit" name="send_message"><i class="fa fa-paper-plane"></i></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Anime Section End -->

    <!-- Footer Section Begin -->
    <?php include "footer.php" ?>

    <!-- Search model end -->

    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/player.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/mixitup.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get all screenshot items
            const screenshotItems = document.querySelectorAll('.screenshot__item');

            // Add click event to each screenshot item
            screenshotItems.forEach(item => {
                item.addEventListener('click', function() {
                    const preview = this.querySelector('.screenshot__preview');
                    const overlay = document.createElement('div');
                    overlay.className = 'screenshot__overlay';

                    // Add overlay and show preview
                    document.body.appendChild(overlay);
                    setTimeout(() => {
                        preview.classList.add('active');
                        overlay.classList.add('active');
                    }, 10);

                    // Close on overlay click
                    overlay.addEventListener('click', function() {
                        preview.classList.remove('active');
                        overlay.classList.remove('active');
                        setTimeout(() => {
                            overlay.remove();
                        }, 200);
                    });
                });
            });
        });

        var objDiv = document.querySelector(".chat-messages");
        objDiv.scrollTop = objDiv.scrollHeight;
    </script>

</body>

</html>