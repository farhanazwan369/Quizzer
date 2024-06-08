<?php
require '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;

// Fetch quiz questions and options
$stmt = $conn->prepare("SELECT q.id as question_id, q.question_text, o.id as option_id, o.option_text 
                        FROM questions q 
                        JOIN options o ON q.id = o.question_id 
                        WHERE q.quiz_id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[$row['question_id']]['question_text'] = $row['question_text'];
    $questions[$row['question_id']]['options'][] = [
        'option_id' => $row['option_id'],
        'option_text' => $row['option_text']
    ];
}

$stmt->free_result(); // Free the result set to avoid the 'Commands out of sync' error

// Handle quiz submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $score = 0;
    foreach ($_POST['answers'] as $question_id => $option_id) {
        // Check if the selected option is correct
        $stmt = $conn->prepare("SELECT is_correct FROM options WHERE id = ?");
        $stmt->bind_param("i", $option_id);
        $stmt->execute();
        $stmt->bind_result($is_correct);
        $stmt->fetch();

        if ($is_correct) {
            $score++;
        }
        $stmt->free_result();
    }

    // Insert user's quiz result
    $stmt = $conn->prepare("INSERT INTO user_quizzes (user_id, quiz_id, score) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $quiz_id, $score);
    if ($stmt->execute()) {
        $success_message = "Quiz submitted successfully! Your score is $score.";
    } else {
        $error_message = "Error submitting quiz: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz</title>
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
        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Take Quiz</h2>
        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <?php foreach ($questions as $question_id => $question): ?>
                <div class="section">
                    <h3><?php echo htmlspecialchars($question['question_text']); ?></h3>
                    <?php foreach ($question['options'] as $option): ?>
                        <div>
                            <input type="radio" name="answers[<?php echo $question_id; ?>]" value="<?php echo $option['option_id']; ?>" required>
                            <label><?php echo htmlspecialchars($option['option_text']); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <div class="btn-container">
                <button type="submit" class="btn">Submit Quiz</button>
            </div>
        </form>
        <div class="btn-container">
            <a href="../../dashboard.php" class="btn">Back to Dashboard</a>
        </div>
    </div>
    <?php include '../../templates/footer.php'; ?>
</body>
</html>
