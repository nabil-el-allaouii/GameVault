<?php
require_once 'database.php';
session_start();


class register extends connection
{
    public $register_error = "";

    public function userExists($username, $email)
    {
        $query = $this->conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $query->bindParam(":username", $username);
        $query->bindParam(":email", $email);
        $query->execute();
        return $query->rowCount() > 0;
    }

    private function validateInputs($username, $email, $password, $confirmPassword)
    {
        if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
            $this->register_error = "All fields are required!";
            return false;
        } elseif (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
            $this->register_error = "Username must be 4-20 characters long and can only contain letters, numbers, and underscores.";
            return false;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->register_error = "Invalid email address.";
            return false;
        } elseif (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[@$!%*?&#]/', $password)) {
            $this->register_error = "Password must be at least 8 characters long and include letters, numbers, and special characters.";
            return false;
        } elseif ($password !== $confirmPassword) {
            $this->register_error = "Passwords do not match.";
            return false;
        }
        return true; //all validations passed
    }

    public function registerUser($username, $email, $password, $confirmPassword)
    {
        if ($this->userExists($username, $email)) {
            $this->register_error = "User already exists!";
            return false;
        }
        if (!$this->validateInputs($username, $email, $password, $confirmPassword)) {
            return false;
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = $this->conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $query->bindParam(":username", $username);
        $query->bindParam(":email", $email);
        $query->bindParam(":password", $hashedPassword);

        if ($query->execute()) {
            return true;
        } else {
            $this->register_error = "An error occurred during registration.";
            return false;
        }
    }
}



class login extends connection
{
    public $login_error = "";

    public function login($email, $password)
    {
        $query = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $query->bindParam(":email", $email);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($query->rowCount() > 0) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_role'] = $user['user_role'];

                // if the user role is admin
                if ($user['user_role'] === 'admin') {
                    header('location: admin_dashboard.php');
                    exit();
                }

                // if not an admin redirect to user dashboard
                header('Location: dashboard.php');
                exit();
            } else {
                $this->login_error = "Invalid email or password!";
                return false;
            }
        } else {
            $this->login_error = "Invalid email or password!";
            return false;
        }
    }
}



class users extends connection
{

    private $username;
    private $profilePic;
    private $user_id;

    public function __construct($username, $user_id, $profilePic)
    {
        parent::__construct();
        $this->username = $username;
        $this->user_id = $user_id;
        $this->profilePic = $profilePic;
    }


    public function modify()
    {
        $stmt = "UPDATE users SET username = :username , profile_pic = :profile_pic WHERE user_id = :userID";
        $modifyquery = $this->conn->prepare($stmt);
        $modifyquery->bindParam(":username", $this->username);
        $modifyquery->bindParam(":userID", $this->user_id);
        $modifyquery->bindParam(":profile_pic", $this->profilePic);
        $modifyquery->execute();
    }

    public function getDefaultProfilePic($userID)
    {
        $stmt = "SELECT profile_pic from users where user_id = :user_id";
        $GetDefaultPic = $this->conn->prepare($stmt);
        $GetDefaultPic->bindParam(":user_id", $userID);
        $GetDefaultPic->execute();
        return $DefaultPic = $GetDefaultPic->fetchColumn();
    }
}



class Game extends connection
{
    private $title;
    private $image;
    private $genre;
    private $description;
    private $screenshots;
    private $release_date;

    public function __construct($title, $image, $genre, $description, $screenshots, $release_date)
    {
        parent::__construct();
        $this->title = $title;
        $this->image = $image;
        $this->genre = $genre;
        $this->description = $description;
        $this->screenshots = $screenshots;
        $this->release_date = $release_date;
    }

    public function createGame()
    {
        //insert game details
        $stmt = "INSERT INTO game (game_title, game_pic, game_genre, game_description, game_release) 
                VALUES (:title, :image, :genre, :description, :release_date)";

        $createGame = $this->conn->prepare($stmt);
        $createGame->bindParam(":title", $this->title);
        $createGame->bindParam(":image", $this->image);
        $createGame->bindParam(":genre", $this->genre);
        $createGame->bindParam(":description", $this->description);
        $createGame->bindParam(":release_date", $this->release_date);

        $createGame->execute();

        //get the last inserted game id
        $game_id = $this->conn->lastInsertId();

        //insert screenshots of this game
        $stmt = "INSERT INTO screenshots (game_id, screen_image) VALUES (:game_id, :screenshot_url)";
        $createScreenshot = $this->conn->prepare($stmt);

        foreach ($this->screenshots as $screenshot) {
            $createScreenshot->bindParam(":game_id", $game_id);
            $createScreenshot->bindParam(":screenshot_url", $screenshot);
            $createScreenshot->execute();
        }

        return true;
    }
}


class Rendering extends connection
{
    public function showGames()
    {
        $stmt = "SELECT * FROM user_library JOIN game ON user_library.game_id = game.game_id WHERE user_library.user_id = :user_id";
        $ShowStmt = $this->conn->prepare($stmt);
        $ShowStmt->bindParam(":user_id", $_SESSION["user_id"]);
        $ShowStmt->execute();
        $GamesShow = $ShowStmt->fetchAll();

        if (is_array($GamesShow) && !empty($GamesShow)) {
            echo "<div class='row'>";
            foreach ($GamesShow as $game) {
                echo "<div class='col-lg-4 col-md-6 col-sm-6'>
                        <div class='product__item'>
                            <div class='product__item__pic set-bg'>
                                <img src='{$game["game_pic"]}' alt=''>
                                <div class='ep'>18 / 18</div>
                                <div class='comment'><i class='fa fa-comments'></i> 11</div>
                                <div class='view'><i class='fa fa-eye'></i> 9141</div>
                                <div class='game__details__overlay'>
                                    <div class='game__stat'>
                                        <i class='fa fa-star'></i>
                                        <span>Personal Score: {$game["personal_score"]}/10</span>
                                    </div>
                                    <div class='game__stat'>
                                        <i class='fa fa-clock-o'></i>
                                        <span>Playtime: {$game["play_time"]}h</span>
                                    </div>
                                    <div class='game__stat'>
                                        <i class='fa fa-gamepad'></i>
                                        <span>Status: {$game["game_status"]}</span>
                                    </div>
                                    <a href='deleteGameUser.php?gameID={$game['game_id']}'>
                                    <button class='remove-game-btn'>
                                        <i class='fa fa-trash'></i> Remove from Library
                                    </button>
                                    </a>
                                </div>
                            </div>
                            <div class='product__item__text'>
                                <ul>
                                    <li>{$game["game_genre"]}</li>
                                    <li>Movie</li>
                                </ul>
                                <h5><a href='game-details.php?Game_id={$game['game_id']}'>{$game["game_title"]}</a></h5>
                            </div>
                        </div>
                    </div>";
            }
            echo "</div>";
        } else {
            echo "There are no games";
        }
    }

    public function showUser()
    {
        $stmt = "SELECT username from users where user_id = :user_id";
        $Userquery = $this->conn->prepare($stmt);
        $Userquery->bindParam(":user_id", $_SESSION["user_id"]);
        $Userquery->execute();
        echo $TheUser = $Userquery->fetchColumn();
    }

    public function ShowGameDetails($gameID)
    {
        $stmt = "SELECT * from game where game_id = :game_id";
        $DetailQuery = $this->conn->prepare($stmt);
        $DetailQuery->bindParam(":game_id", $gameID);
        $DetailQuery->execute();
        $detail = $DetailQuery->fetch();

        $checkstmt = "select user_role from users where user_id = :user_id";
        $checking = $this->conn->prepare($checkstmt);
        $checking->bindParam(":user_id", $_SESSION["user_id"]);
        $checking->execute();
        $isthere = $checking->fetch();

        if (empty($detail)) {
            header("location: index.php");
        }

        $AddToLibraryCheck = '';
        if ($isthere["user_role"] === "player") {
            $AddToLibraryCheck = "<div class='anime__details__btn'>
                                <a href='#' class='follow-btn'><i class='fa fa-plus'></i> Add to Library</a>
                                <a href='dashboard.php' class='watch-btn'><span>See in Dashboard</span> <i
                                        class='fa fa-angle-right'></i></a>
                            </div>";
        } else {
            $AddToLibraryCheck = "<div class='anime__details__btn'>
                                <a href='admin_dashboard.php' class='watch-btn'><span>See in Dashboard</span> <i
                                        class='fa fa-angle-right'></i></a>
                            </div>";
        }

        return "<div class='anime__details__content'>
                <div class='row'>
                    <div class='col-lg-3'>
                        <div class='anime__details__pic set-bg'>
                            <img src='{$detail['game_pic']}'>
                            <div class='comment'><i class='fa fa-comments'></i> 11</div>
                            <div class='view'><i class='fa fa-eye'></i> 9141</div>
                        </div>
                    </div>
                    <div class='col-lg-9'>
                        <div class='anime__details__text'>
                            <div class='anime__details__title'>
                                <h3>{$detail["game_title"]}</h3>
                            </div>
                            <div class='anime__details__rating'>
                                <div class='rating'>
                                    <a href='#'><i class='fa fa-star'></i></a>
                                    <a href='#'><i class='fa fa-star'></i></a>
                                    <a href='#'><i class='fa fa-star'></i></a>
                                    <a href='#'><i class='fa fa-star'></i></a>
                                    <a href='#'><i class='fa fa-star-half-o'></i></a>
                                </div>
                                <span>1.029 Votes</span>
                            </div>
                            <p>{$detail["game_description"]}</p>
                            <div class='anime__details__widget'>
                                <div class='row'>
                                    <div class='col-lg-12'>
                                        <ul>
                                            <li><span>Date aired:</span>{$detail["game_release"]}</li>
                                            <li><span>Genre:</span>{$detail["game_genre"]}</li>
                                            <li><span>Rating:</span> {$detail["average_score"]} / 10</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                                {$AddToLibraryCheck}
                        </div>
                    </div>
                </div>
            </div>";
    }


    public function ShowScreens($gameID)
    {
        $stmt = "SELECT * from screenshots where game_id = :game_id";
        $showscreen = $this->conn->prepare($stmt);
        $showscreen->bindParam(":game_id", $gameID);
        $showscreen->execute();
        $screens = $showscreen->fetchAll();

        echo "<div class='game__screenshots'>
                <h5>Screenshots</h5>
                <div class='row g-2'>";
        foreach ($screens as $screen) {
            echo "
                    <div class='col-4'>
                        <div class='screenshot__item'>
                            <img src='{$screen["screen_image"]}' alt='Screenshot 1'>
                            <div class='screenshot__preview'>
                                <img src='{$screen["screen_image"]}' alt='Screenshot 1'>
                            </div>
                        </div>
                    </div>";
        }
        echo "</div>
            </div>";
    }
}



class admin extends connection
{
    public function showAllGames()
    {
        $stmt = "SELECT * FROM game";
        $ShowStmt = $this->conn->prepare($stmt);
        $ShowStmt->execute();
        $AllGames = $ShowStmt->fetchAll();


        foreach ($AllGames as $game) {
            echo '
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="product__item">
                        <input type="hidden" name="game_id" value="' . $game['game_id'] . '">
                        <div class="product__item__pic set-bg">
                            <img src="' . $game['game_pic'] . '">
                            <div class="ep">18 / 18</div>
                            <div class="comment"><i class="fa fa-comments"></i> 11</div>
                            <div class="view"><i class="fa fa-eye"></i> 9141</div>
                        </div>
                        <div class="product__item__text">
                            <ul>
                            <li>' . $game['game_genre'] . '</li>
                            <li>' . $game['game_release'] . '</li>
                            </ul>
                            <h5><a href="game-details.php?Game_id=' . $game['game_id'] . '">' . $game['game_title'] . '</a></h5>
                        </div>
                    </div>
                </div>';
        }
    }

    function showPlayers()
    {
        $stmt = "SELECT * FROM users WHERE user_role = 'player'";
        $ShowStmt = $this->conn->prepare($stmt);
        $ShowStmt->execute();
        $players = $ShowStmt->fetchAll();

        foreach ($players as $user) {
            echo '<div class="user-item">
                    <div class="user-info">
                        <img src="' . htmlspecialchars($user['profile_pic']) . '" class="user-avatar">
                        <div class="user-details">
                            <h4>' . htmlspecialchars($user['username']) . '</h4>
                        </div>
                    </div>
                    <div class="user-actions">
                        <a href="make_admin.php?id=' . $user['user_id'] . '" class="action-btn">Make Admin</a>
                    </div>
                </div>';
        }
    }

    function showAdmins()
    {
        $stmt = "SELECT * FROM users WHERE user_role = 'admin' AND user_id != :user_id";
        $ShowStmt = $this->conn->prepare($stmt);
        $ShowStmt->bindParam(":user_id", $_SESSION["user_id"]);
        $ShowStmt->execute();
        $admins = $ShowStmt->fetchAll();

        foreach ($admins as $user) {
            echo '<div class="user-item">
                    <div class="user-info">
                        <img src="' . htmlspecialchars($user['profile_pic']) . '" class="user-avatar" >
                        <div class="user-details">
                            <h4>' . htmlspecialchars($user['username']) . '</h4>
                        </div>
                    </div>
                    <div class="user-actions">
                        <a href="remove_admin.php?id=' . $user['user_id'] . '" class="action-btn">Remove Admin</a>
                    </div>
                </div>';
        }
    }

    public function makeAdmin($user_id)
    {
        try {
            $stmt = "UPDATE users SET user_role = 'admin' WHERE user_id = :user_id";
            $makeAdmin = $this->conn->prepare($stmt);
            $makeAdmin->bindParam(":user_id", $user_id);
            return $makeAdmin->execute();  // Returns true if successful, false if failed
        } catch (PDOException $e) {
            return false;
        }
    }

    public function removeAdmin($user_id)
    {
        try {
            $stmt = "UPDATE users SET user_role = 'player' WHERE user_id = :user_id";
            $removeAdmin = $this->conn->prepare($stmt);
            $removeAdmin->bindParam(":user_id", $user_id);
            return $removeAdmin->execute();  // Returns true if successful, false if failed
        } catch (PDOException $e) {
            return false;
        }
    }

    public function renderAllGames()
    {
        $stmt = "SELECT * FROM game";
        $ShowStmt = $this->conn->prepare($stmt);
        $ShowStmt->execute();
        $AllGames = $ShowStmt->fetchAll(PDO::FETCH_ASSOC);

        echo '<div class="row">';
        foreach ($AllGames as $game) {
            echo ' <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="product__item">
                        <input type="hidden" name="game_id" value="' . $game['game_id'] . '">
                        <div class="product__item__pic set-bg">
                            <img src="' . $game['game_pic'] . '">
                            <div class="ep">18 / 18</div>
                            <div class="comment"><i class="fa fa-comments"></i> 11</div>
                                <div class="view"><i class="fa fa-eye"></i> 9141</div>

                                    <div class="game__details__overlay">
                                        <a href="admin_dashboard.php?action=delete&id=' . $game['game_id'] . '" style="color: #ff0001; border: 2px solid #ff0001; border-radius: 5px; background-color: #ffffffb3; cursor: pointer; margin-bottom: 10px; display: block; padding: 5px" onclick="return confirm(\'Are you sure you want to delete this game?\');">
                                            <i class="fa fa-trash"></i> Delete Game
                                        </a>
                                        <a href="#" onclick="showEditForm(' . $game['game_id'] . ', \'' . $game['game_title'] . '\', \'' . $game['game_description'] . '\', \'' . $game['game_genre'] . '\', \'' . $game['game_release'] . '\', \'' . $game['game_pic'] . '\')" style="color: #0066ff; border: 2px solid #0066ff; border-radius: 5px; background-color: #ffffffb3; cursor: pointer; display: block;padding-inline: 12px;padding-block: 4px">
                                            <i class="fa fa-edit"></i> Edit Game
                                        </a>
                                    </div>

                                </div>
                            <div class="product__item__text">
                            <ul>
                                <li>' . $game['game_genre'] . '</li>
                                <li>' . $game['game_release'] . '</li>
                            </ul>
                            <h5><a href="game-details.php?Game_id=' . $game['game_id'] . '">' . $game['game_title'] . '</a></h5>
                        </div>
                    </div>
                </div>';
        }
        echo '</div>';
    }

    public function deleteGame($game_id)
    {
        $stmt3 = "DELETE FROM game WHERE game_id = $game_id";
        $deleteGame = $this->conn->query($stmt3);
        return $deleteGame;
    }

    public function editGame($game_id)
    {
        $stmt = "UPDATE game SET 
                game_title = :game_title, 
                game_description = :game_description, 
                game_genre = :game_genre, 
                game_release = :game_release, 
                game_pic = :game_pic 
                WHERE game_id = :game_id";

        $editGame = $this->conn->prepare($stmt);

        $editGame->bindParam(":game_title", $_POST['title']);
        $editGame->bindParam(":game_description", $_POST['description']);
        $editGame->bindParam(":game_genre", $_POST['genre']);
        $editGame->bindParam(":game_release", $_POST['release_date']);
        $editGame->bindParam(":game_pic", $_POST['image']);
        $editGame->bindParam(":game_id", $game_id);

        return $editGame->execute();
    }


    public function banPlayer()
    {
        $stmt = "SELECT * FROM users WHERE user_role = 'player' AND banned = 'safe'";
        $ShowStmt = $this->conn->prepare($stmt);
        $ShowStmt->execute();
        $players = $ShowStmt->fetchAll();

        foreach ($players as $user) {
            echo '<div class="user-item">
                    <div class="user-info">
                        <img src="' . htmlspecialchars($user['profile_pic']) . '" class="user-avatar">
                        <div class="user-details">
                            <h4>' . htmlspecialchars($user['username']) . '</h4>
                        </div>
                    </div>
                    <div class="user-actions">
                        <a href="ban_user.php?id=' . $user['user_id'] . '" class="action-btn">Ban user</a>
                    </div>
                </div>';
        }
    }

    public function banUser($user_id)
    {
        $stmt = "UPDATE users SET banned = 'banned' WHERE user_id = :user_id";
        $banUser = $this->conn->prepare($stmt);
        $banUser->bindParam(":user_id", $user_id);
        return $banUser->execute();
    }

    public function unbanPlayer()
    {
        $stmt = "SELECT * FROM users WHERE user_role = 'player' AND banned = 'banned'";
        $ShowStmt = $this->conn->prepare($stmt);
        $ShowStmt->execute();
        $players = $ShowStmt->fetchAll();

        foreach ($players as $user) {
            echo '<div class="user-item">
                    <div class="user-info">
                        <img src="' . htmlspecialchars($user['profile_pic']) . '" class="user-avatar">
                        <div class="user-details">
                            <h4>' . htmlspecialchars($user['username']) . '</h4>
                        </div>
                    </div>
                    <div class="user-actions">
                        <a href="unban_user.php?id=' . $user['user_id'] . '" class="action-btn">Unban user</a>
                    </div>
                </div>';
        }
    }

    public function unbanUser($user_id)
    {
        $stmt = "UPDATE users SET banned = 'safe' WHERE user_id = :user_id";
        $unbanUser = $this->conn->prepare($stmt);
        $unbanUser->bindParam(":user_id", $user_id);
        return $unbanUser->execute();
    }
}
