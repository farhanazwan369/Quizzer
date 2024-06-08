<?php
require 'config/database.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role_id = $_POST['role_id']; // 1: Admin, 2: Teacher, 3: Student

    $stmt = $conn->prepare("INSERT INTO users (username, password, role_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $username, $password, $role_id);

    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Quizzer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #D6B5B5;
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
        select,
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
    <script src="scriptRegister.js" defer></script>
</head>
<body>
<div class="login-container">
    <div class="left-column">
        <img src="logo.png" alt="Quizzer Big Logo">
        <h1>Register Now!</h1>
        <p>Create your account to get started</p>
        
        <form action="register.php" method="post" id="register-form" onsubmit="return validateForm()">
            <input type="text" id="username" name="username" placeholder="Username" required><br>
            <input type="password" id="password" name="password" placeholder="Password" required><br>
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <select name="role_id" required>
                <option value="1">Admin</option>
                <option value="2">Teacher</option>
                <option value="3">Student</option>
            </select>
            <div class="button-group">
                <button type="submit">Register</button>
                <button type="button" onclick="window.location.href='login.php'">Login</button>
            </div>
        </form>
    </div>

    <div class="right-column">
        <img src="random_image.png" alt="Random Image">
    </div>
</div>

</body>
</html>
