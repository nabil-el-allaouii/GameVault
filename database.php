<?php 
class connection{
    private $host = 'localhost';
    private $user = 'root';
    private $password = '';
    private $dbname = 'GameVault';

    protected $conn;
    private $error;

    public function __construct(){
        try{
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connection successful!<br>";
            return $this->conn;
            
        }catch (PDOException $e){
            $this->error = $e->getMessage();
            echo "Connection failed: " . $this->error . "<br>";
            return $this->conn;
        }
    }

    public function getError() {
        return $this->error;
    }

}

?>