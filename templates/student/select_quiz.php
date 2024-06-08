<?php
require '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$topic_id = $_GET['topic_id'];

// Fetch quizzes for the selected topic from the database
$stmt = $conn->prepare("SELECT id, created_at FROM quizzes WHERE topic_id = ?");
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$result = $stmt->get_result();
$quizzes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Quiz</title>
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
        .quiz {
            margin-bottom: 20px;
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
        <h2>Select Quiz</h2>
        <?php foreach ($quizzes as $quiz): ?>
            <div class="quiz">
                <p>Quiz ID: <?php echo $quiz['id']; ?></p>
                <p>Created At: <?php echo $quiz['created_at']; ?></p>
                <a href="take_quiz.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn">Take Quiz</a>
            </div>
        <?php endforeach; ?>
		<br />
        <div class="btn-container">
            <a href="select_topic.php" class="btn">Back to Topics</a>
        </div>
    </div>
    <?php include '../../templates/footer.php'; ?>
</body>
</html>
