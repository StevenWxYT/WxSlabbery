<?php
require_once 'php/db.php';
require_once 'php/function.php';

$db = new DBConn();
$conn = $db->getConnection();      // ✅ Properly access the connection
$user = new DBFunc($conn);         // ✅ Pass it to your DBFunc class

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $user->loginUser($email, $password);
    } else {
        echo "Please enter both email and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <link rel="icon" type="image/png" href="./img/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="./img/favicon.svg" />
    <link rel="shortcut icon" href="./img/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="./img/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="StevenWx" />
    <link rel="manifest" href="./img/site.webmanifest" />
    <link rel="stylesheet" href="master.css" />
</head>

<body>
    <main>
        <h2>Login Account</h2>
        <form id="loginForm" action="login.php" method="post">
            <div class="form-row-group">
                <div class="form-column">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required />
                </div>

                <div class="form-column">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required />
                </div>

                <div class="form-column remember-me">
                    <label>
                        <input type="checkbox" name="remember" value="1" />
                        Remember Me
                    </label>
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit">Login</button>
            </div>
        </form>


        <div class="register-redirect">
            <p>Don't have an account yet?</p>
            <button type="button" onclick="window.location.href='register.php'">Register Now</button>
        </div>
    </main>
</body>

</html>