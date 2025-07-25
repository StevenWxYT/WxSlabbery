<?php
include("php/function.php");

$db = new DBConn();
$user = new DBFunc($db->conn);

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
