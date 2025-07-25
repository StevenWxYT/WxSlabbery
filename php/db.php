<?php
session_start();

class DBConn {
    private $serverhost = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "weather";
    public $conn;

    public function __construct() {
        $this->conn = new mysqli(
            $this->serverhost,
            $this->username,
            $this->password,
            $this->database
        );

        if ($this->conn->connect_error) {
            error_log("Database connection error: " . $this->conn->connect_error);
            die("Connection failed. Please contact administrator.");
        }
    }
}
?>
