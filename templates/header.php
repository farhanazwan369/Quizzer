<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizzer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #D6B5B5;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
		
		footer{
		text-align: center;
		}
		

        .logo img {
            width: 160px;
			height: 100px;
			margin-top: 10px;
			margin-left: 20px;
        }

        .navigation a {
            color: #fff;
            text-decoration: none;
            margin-right: 20px;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .login-btn {
            background-color: #007bff;
            border: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .login-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <img src="logo_header.png" alt="Quizzer Logo">
    </div>
    <div class="navigation">
        <a href="contact.html">About Us</a>
        <a href="tutorial.html">How It Works</a>
        <a href="login.php" class="login-btn">Login</a>
    </div>
</header>
