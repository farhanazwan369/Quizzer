<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

require 'config/database.php';

// Fetch user's completed quizzes and their scores
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT q.id, t.name as topic_name, q.created_at, uq.score 
                        FROM user_quizzes uq 
                        JOIN quizzes q ON uq.quiz_id = q.id 
                        JOIN topics t ON q.topic_id = t.id 
                        WHERE uq.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$completed_quizzes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $completed_quizzes[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #D6B5B5;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .section {
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Student Dashboard</h2>
        <div class="section">
            <h3>Actions</h3>
            <a href="templates/student/select_topic.php" class="btn">Select Topic</a>
            <a href="templates/student/search_topics.php" class="btn">Search Topics</a>
        </div>
        <div class="section">
            <h3>Completed Quizzes</h3>
            <?php if (count($completed_quizzes) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Quiz ID</th>
                            <th>Topic</th>
                            <th>Date Taken</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($completed_quizzes as $quiz): ?>
                            <tr>
                                <td><?php echo $quiz['id']; ?></td>
                                <td><?php echo $quiz['topic_name']; ?></td>
                                <td><?php echo $quiz['created_at']; ?></td>
                                <td><?php echo $quiz['score']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No quizzes completed yet.</p>
            <?php endif; ?>
        </div>
        <div class="btn-container">
            <a href="../../dashboard.php" class="btn">Back to Main Dashboard</a>
        </div>
    </div>
    
</body>
</html>
