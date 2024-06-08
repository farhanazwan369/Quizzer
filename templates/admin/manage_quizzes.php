<?php
require '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../../login.php");
    exit();
}

// Fetch all topics
$topics_stmt = $conn->prepare("SELECT * FROM topics");
$topics_stmt->execute();
$topics_result = $topics_stmt->get_result();

// Fetch all quizzes
$quizzes_stmt = $conn->prepare("SELECT q.id, t.name as topic_name FROM quizzes q JOIN topics t ON q.topic_id = t.id");
$quizzes_stmt->execute();
$quizzes_result = $quizzes_stmt->get_result();

// Handle delete request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_topic'])) {
        $topic_id = $_POST['topic_id'];

        // Delete topic
        $stmt = $conn->prepare("DELETE FROM topics WHERE id = ?");
        $stmt->bind_param("i", $topic_id);
        $stmt->execute();
        header("Location: manage_quizzes.php");
        exit();
    }

    if (isset($_POST['delete_quiz'])) {
        $quiz_id = $_POST['quiz_id'];

        // Delete quiz
        $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
        $stmt->bind_param("i", $quiz_id);
        $stmt->execute();
        header("Location: manage_quizzes.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Quizzes</title>
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
            display: flex;
            flex-direction: column;
            min-height: 100vh;
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
        .btn-container {
            margin-top: auto;
        }
        footer {
            text-align: center;
            padding: 10px 0;
            background-color: #f1f1f1;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Quizzes</h2>
        <div class="section">
            <h3>Topics</h3>
            <table>
                <thead>
                    <tr>
                        <th>Topic ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($topic = $topics_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($topic['id']); ?></td>
                            <td><?php echo htmlspecialchars($topic['name']); ?></td>
                            <td><?php echo htmlspecialchars($topic['description']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="topic_id" value="<?php echo $topic['id']; ?>">
                                    <button type="submit" name="delete_topic" class="btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="section">
            <h3>Quizzes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Quiz ID</th>
                        <th>Topic</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($quiz = $quizzes_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($quiz['id']); ?></td>
                            <td><?php echo htmlspecialchars($quiz['topic_name']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                                    <button type="submit" name="delete_quiz" class="btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div class="btn-container">
            <a href="../../dashboard.php" class="btn">Back to Main Dashboard</a>
        </div>
    </div>
    <?php include '../../templates/footer.php'; ?>
</body>
</html>
