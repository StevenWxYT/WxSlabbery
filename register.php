<?php
require_once 'php/db.php';
require_once 'php/function.php';

$db = new DBConn();
$conn = $db->getConnection();      // ✅ Correctly retrieve the connection
$user = new DBFunc($conn);         // ✅ Pass it to DBFunc

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($email) && !empty($password)) {
        $user->registerUser($username, $email, $password);
    } else {
        echo "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register</title>
    <link rel="icon" type="image/png" href="./img/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="./img/favicon.svg" />
    <link rel="shortcut icon" href="./img/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="./img/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="StevenWx" />
    <link rel="manifest" href="./img/site.webmanifest" />
    <link rel="stylesheet" href="master.css">
</head>

<body>
    <main>
        <h2>Register Account</h2>
        <form id="registerForm" action="register.php" method="post">
            <div class="form-row-group">
                <div class="form-column">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </div>

                <div class="form-column">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="form-column">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="form-column">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit">Register</button>
            </div>
        </form>

        <div class="register-redirect">
            <p>Already have an account?</p>
            <button type="button" onclick="window.location.href='login.php'">Back to Login</button>
        </div>
    </main>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(event) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;

            if (password !== confirm) {
                event.preventDefault();
                alert('Passwords do not match!');
            }
        });
    </script>
</body>

</html>