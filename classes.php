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
            foreach ($GamesShow as $game) {
                echo "<div class='row'>
                                    <div class='col-lg-4 col-md-6 col-sm-6'>
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
                                                <h5><a href='#'>{$game["game_title"]}</a></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
            }
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
}



class admin extends connection
{
    public function showAllGames()
    {
        $stmt = "SELECT * FROM game";
        $ShowStmt = $this->conn->prepare($stmt);
        $ShowStmt->execute();
        $AllGames = $ShowStmt->fetchAll(PDO::FETCH_ASSOC);


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
                                <li>Active</li>
                                <li>' . $game['game_genre'] . '</li>
                            </ul>
                            <h5><a href="#">' . $game['game_title'] . '</a></h5>
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
                                <li>Active</li>
                                <li>' . $game['game_genre'] . '</li>
                            </ul>
                            <h5><a href="#">' . $game['game_title'] . '</a></h5>
                        </div>
                    </div>
                </div>';
        }
    }
}

class UserLibrary extends connection
{
    public function DeleteGame($game_id)
    {
        $stmt = "DELETE from user_library where game_id = :game_id";
        $deleteQu = $this->conn->prepare($stmt);
        $deleteQu->bindParam(":game_id", $game_id);
        $deleteQu->execute();
    }
}
