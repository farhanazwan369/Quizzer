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

// Handle update request for questions and options
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_question'])) {
        $question_id = $_POST['question_id'];
        $question_text = $_POST['question_text'];

        // Update question
        $stmt = $conn->prepare("UPDATE questions SET question_text = ? WHERE id = ?");
        $stmt->bind_param("si", $question_text, $question_id);
        $stmt->execute();
        header("Location: manage_questions.php");
        exit();
    }

    if (isset($_POST['update_option'])) {
        $option_id = $_POST['option_id'];
        $option_text = $_POST['option_text'];
        $is_correct = isset($_POST['is_correct']) ? 1 : 0;

        // Update option
        $stmt = $conn->prepare("UPDATE options SET option_text = ?, is_correct = ? WHERE id = ?");
        $stmt->bind_param("sii", $option_text, $is_correct, $option_id);
        $stmt->execute();
        header("Location: manage_questions.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions</title>
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
        <h2>Manage Questions</h2>
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
                                <a href="manage_questions.php?quiz_id=<?php echo $quiz['id']; ?>" class="btn">Manage Questions</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php if (isset($_GET['quiz_id'])): ?>
        <?php
            $quiz_id = $_GET['quiz_id'];

            // Fetch questions for the selected quiz
            $questions_stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
            $questions_stmt->bind_param("i", $quiz_id);
            $questions_stmt->execute();
            $questions_result = $questions_stmt->get_result();
        ?>
        <div class="section">
            <h3>Questions for Quiz ID: <?php echo $quiz_id; ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>Question ID</th>
                        <th>Question Text</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($question = $questions_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($question['id']); ?></td>
                            <td><?php echo htmlspecialchars($question['question_text']); ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                                    <input type="text" name="question_text" value="<?php echo htmlspecialchars($question['question_text']); ?>" required>
                                    <button type="submit" name="update_question" class="btn">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php
                            // Fetch options for the current question
                            $options_stmt = $conn->prepare("SELECT * FROM options WHERE question_id = ?");
                            $options_stmt->bind_param("i", $question['id']);
                            $options_stmt->execute();
                            $options_result = $options_stmt->get_result();
                        ?>
                        <?php while ($option = $options_result->fetch_assoc()): ?>
                            <tr>
                                <td colspan="2"><?php echo htmlspecialchars($option['option_text']); ?> (Correct: <?php echo $option['is_correct'] ? 'Yes' : 'No'; ?>)</td>
                                <td>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="option_id" value="<?php echo $option['id']; ?>">
                                        <input type="text" name="option_text" value="<?php echo htmlspecialchars($option['option_text']); ?>" required>
                                        <input type="checkbox" name="is_correct" <?php echo $option['is_correct'] ? 'checked' : ''; ?>>
                                        <button type="submit" name="update_option" class="btn">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        <div class="btn-container">
            <a href="../../dashboard.php" class="btn">Back to Main Dashboard</a>
        </div>
    </div>
    <?php include '../../templates/footer.php'; ?>
</body>
</html>
