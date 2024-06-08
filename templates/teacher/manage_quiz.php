<?php
require '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch quizzes created by the teacher
$quizzes_stmt = $conn->prepare("SELECT q.id, t.name as topic_name FROM quizzes q JOIN topics t ON q.topic_id = t.id WHERE t.created_by = ?");
$quizzes_stmt->bind_param("i", $user_id);
$quizzes_stmt->execute();
$quizzes_result = $quizzes_stmt->get_result();

// Handle delete and update requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_quiz'])) {
        $quiz_id = $_POST['quiz_id'];

        // Delete quiz
        $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
        $stmt->bind_param("i", $quiz_id);
        $stmt->execute();
        header("Location: manage_quiz.php");
        exit();
    }

    if (isset($_POST['update_quiz'])) {
        $quiz_id = $_POST['quiz_id'];
        $quiz_name = $_POST['quiz_name'];

        // Update quiz
        $stmt = $conn->prepare("UPDATE quizzes SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $quiz_name, $quiz_id);
        $stmt->execute();
        header("Location: manage_quiz.php");
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
        <h2>Manage Quizzes</h2>
        <div class="section">
            <h3>Quizzes</h3>
            <table>
                <thead>
                    <tr>
                        <th>Quiz ID</th>
                        <th>Topic</th>
                        <th>Actions</th>
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
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="quiz_id" value="<?php echo $quiz['id']; ?>">
                                    <input type="text" name="quiz_name" value="<?php echo htmlspecialchars($quiz['topic_name']); ?>" required>
                                    <button type="submit" name="update_quiz" class="btn">Update</button>
                                </form>
                                <a href="manage_questions.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn">Manage Questions</a>
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
