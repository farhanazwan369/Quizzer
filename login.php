<?php
require 'config/database.php';
session_start();

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role_id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $role_id);

    if ($stmt->num_rows == 1 && $stmt->fetch() && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['role_id'] = $role_id;
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Quizzer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #D6B5B5;
        }
		
		footer{
		text-align: center;
		}

        .login-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            height: 100vh;
        }

        .left-column {
            text-align: center;
        }

        .left-column img {
            max-width: 100%; /* Adjust to the maximum width you want */
            height: auto;
            margin-bottom: 20px; /* Adjust the margin bottom */
        }

        .left-column h1 {
            margin-bottom: 10px; /* Adjust the margin bottom */
        }

        .right-column {
            max-width: 40%; /* Adjust the maximum width of the right column */
            text-align: center;
        }

        .right-column img {
            max-width: 100%; /* Adjust to the maximum width you want */
            height: auto;
        }

        form {
            margin-top: 20px;
        }

        input[type="text"],
        input[type="password"],
        button,
        .checkbox-container {
            margin-top: 10px;
            padding: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        .checkbox-container {
            display: flex;
            align-items: center; /* Align items vertically */
        }

        .checkbox-container input[type="checkbox"] {
            margin-right: 5px; /* Add some space between checkbox and label */
        }

        .button-group {
            display: flex;
            justify-content: space-between;
        }

        button {
            background-color: #007bff;
            border: none;
            color: #fff;
            cursor: pointer;
            width: 48%; /* Adjust the width to fit both buttons side by side */
        }

        button:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
        }
    </style>
    <script src="scriptLogin.js" defer></script>
</head>
<body>
<div class="login-container">
    <div class="left-column">
        <img src="logo.png" alt="Quizzer Big Logo">
        <h1>Welcome Back!</h1>
        <p>Please login/sign up to your account</p>
        
        <form action="login.php" method="post" id="login-form" onsubmit="return validateForm()">
            <input type="text" id="username" name="username" placeholder="Username" required><br>
            <input type="password" id="password" name="password" placeholder="Password" required><br>
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <div class="checkbox-container">
                <input type="checkbox" id="remember-me" name="remember-me">
                <label for="remember-me">Remember Me</label>
            </div>
            <a href="forgot_password.html">Forgot Password?</a><br>
            <div class="button-group">
                <button type="submit">Login</button>
                <button type="button" onclick="window.location.href='register.php'">Sign Up</button>
            </div>
        </form>
    </div>

    <div class="right-column">
        <img src="random_image.png" alt="Random Image">
    </div>
</div>

<?php include 'templates/footer.php'; ?>

</body>
</html>
