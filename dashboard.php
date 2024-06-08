<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #D6B5B5;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        
        .header {
            width: 100%;
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
        }
		
        .section {
            margin-bottom: 20px;
        }
		
        .main {
            display: flex;
            flex: 1;
        }

        .sidebar {
            width: 200px;
            background-color: #555;
            padding: 20px;
            color: #fff;
            height: 100%;
            position: fixed;
            top: 88px; /* Aligns the sidebar below the header */
            left: 0;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            margin: 10px 0;
        }

        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px;
            background-color: #666;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sidebar ul li a:hover {
            background-color: #777;
        }

        .content {
            margin-left: 220px; /* Space for the sidebar */
            padding: 20px;
            flex-grow: 1;
        }

        .logout-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #555;
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
    <div class="header">
        <h2>Dashboard</h2>
    </div>
    <div class="main">
        <?php if ($_SESSION['role_id'] == 1): ?>
            <div class="sidebar">
                <h3>Admin Dashboard</h3>
                <ul>
                    <li><a href="templates/admin/manage_users.php">Manage Users</a></li>
                    <li><a href="templates/admin/manage_quizzes.php">Manage Quizzes</a></li>
                </ul>
            </div>
        <?php elseif ($_SESSION['role_id'] == 2): ?>
            <div class="sidebar">
                <h3>Teacher Dashboard</h3>
                <ul>
                    <li><a href="templates/teacher/create_topic.php">Create Topic</a></li>
                    <li><a href="templates/teacher/create_quiz.php">Create Quiz</a></li>
                    <li><a href="templates/teacher/view_participants.php">View Participants</a></li>
                    <li><a href="templates/teacher/manage_topic.php">Manage Topic</a></li>
                    <li><a href="templates/teacher/manage_quiz.php">Manage Quiz</a></li>
                    <li><a href="templates/teacher/manage_questions.php">Manage Questions</a></li>
                </ul>
            </div>
        <?php else: ?>
            <div class="sidebar">
                <h3>Student Dashboard</h3>
				<ul>
                <li><a href="templates/student/select_topic.php">Select Topic</a></li>
                <li><a href="templates/student/search_topics.php">Search Topics</a></li>
				</ul>
            </div>
			<div class="container">
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
		</div>
        <?php endif; ?>
        <div class="content">
            <h3>Welcome to your Dashboard</h3>
            <p>Select an option from the sidebar to manage your tasks.</p>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
</body>
</html>
