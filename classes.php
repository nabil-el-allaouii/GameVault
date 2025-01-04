<?php 
require_once 'database.php';
session_start();


class register extends connection{
    public $register_error = "";

    public function userExists($username, $email){
        $query = $this->conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $query->bindParam(":username", $username);
        $query->bindParam(":email", $email);
        $query->execute();
        return $query->rowCount() > 0;
    }

    private function validateInputs($username, $email, $password, $confirmPassword) {
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
    
    public function registerUser($username, $email, $password, $confirmPassword) {
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

    public function getProfilePic($user_id) {
            $query = $this->conn->prepare("SELECT profile_pic FROM users WHERE user_id = :user_id");
            $query->bindParam(':user_id', $user_id);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_ASSOC);

            if ($result && !empty($result['profile_pic'])) {
                return $result['profile_pic'];
            }
            
    }

}



class login extends connection{
    public $login_error = "";

    public function login($email, $password){
        $query = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
        $query->bindParam(":email", $email);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($query->rowCount() > 0) {
            if(password_verify($password, $user['password'])){
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_id'] = $user['user_id'];
                // echo "Login successful!";
                return true;
            }else {
                $this->login_error = "Invalid email or password!";
                return false;
            }
        }else {
            $this->login_error = "Invalid email or password!";
            return false;
        }
    }
}



class users extends connection {

    private $username;
    private $profilePic;
    private $user_id;

    public function __construct($username, $user_id , $profilePic)
    {
        parent::__construct();
        $this->username = $username;
        $this->user_id = $user_id;
        $this->profilePic = $profilePic;
    }


    public function modify(){
        $stmt = "UPDATE users SET username = :username , profile_pic = :profile_pic WHERE user_id = :userID";
        $modifyquery = $this->conn->prepare($stmt);
        $modifyquery->bindParam(":username", $this->username);
        $modifyquery->bindParam(":userID" , $this->user_id);
        $modifyquery->bindParam(":profile_pic" , $this->profilePic);
        $modifyquery->execute();
    }

    public function getDefaultProfilePic($userID){
        $stmt = "SELECT profile_pic from users where user_id = :user_id";
        $GetDefaultPic = $this->conn->prepare($stmt);
        $GetDefaultPic->bindParam(":user_id", $userID);
        $GetDefaultPic->execute();
        return $DefaultPic = $GetDefaultPic->fetchColumn();
    }


}




?>