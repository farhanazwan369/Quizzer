<?php
require '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch topics from the database
$sql = "SELECT id, name, description FROM topics";
$result = $conn->query($sql);
$topics = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $topics[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Topic</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #D6B5B5;
        }
		footer{
			text-align: center;
		}
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .topic {
            margin-bottom: 20px;
        }
        .topic h3 {
            margin: 0;
        }
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Select Topic</h2>
        <?php foreach ($topics as $topic): ?>
            <div class="topic">
                <h3><?php echo $topic['name']; ?></h3>
                <p><?php echo $topic['description']; ?></p>
                <a href="select_quiz.php?topic_id=<?php echo $topic['id']; ?>" class="btn">View Quizzes</a>
            </div>
        <?php endforeach; ?>
		<br />
        <div class="btn-container">
            <a href="../../dashboard.php" class="btn">Back to Dashboard</a>
        </div>
    </div>
    <?php include '../../templates/footer.php'; ?>
</body>
</html>
