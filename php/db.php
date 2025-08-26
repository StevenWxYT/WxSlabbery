<?php
// Start session safely (universal)
if (!isset($_SESSION)) {
    session_start();
}

// Check if DBConn class is already defined
if (!class_exists('DBConn')) {
    class DBConn {
        private $serverhost = "localhost";
        private $username   = "root";
        private $password   = "";
        private $database   = "weather";
        private $conn;

        public function __construct() {
            // Create the MySQLi connection
            $this->conn = new mysqli(
                $this->serverhost,
                $this->username,
                $this->password,
                $this->database
            );

            // Handle connection errors (universal)
            if (mysqli_connect_errno()) {
                error_log("Database connection error: " . mysqli_connect_error());
                die(json_encode(array(
                    'success' => false,
                    'message' => 'Database connection failed.'
                )));
            }
        }

        // Return the active connection
        public function getConnection() {
            return $this->conn;
        }
    }
}

// Automatically create the connection globally if not already created
if (!isset($conn)) {
    $db   = new DBConn();
    $conn = $db->getConnection();
}
?>
