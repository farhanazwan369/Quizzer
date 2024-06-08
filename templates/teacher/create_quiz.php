<?php
require '../../config/database.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Fetch topics from the database
$sql = "SELECT id, name FROM topics";
$result = $conn->query($sql);
$topics = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $topics[] = $row;
    }
}

// Insert quiz into the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $topic_id = $_POST['topic_id'];
    $questions = $_POST['questions'];

    // Insert quiz details into the database
    $stmt = $conn->prepare("INSERT INTO quizzes (topic_id) VALUES (?)");
    $stmt->bind_param("i", $topic_id);

    if ($stmt->execute()) {
        $quiz_id = $stmt->insert_id;

        // Insert questions and options into the database
        foreach ($questions as $question) {
            $question_text = $question['question_text'];
            $options = $question['options'];

            // Insert question into the database
            $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
            $stmt->bind_param("is", $quiz_id, $question_text);
            if ($stmt->execute()) {
                $question_id = $stmt->insert_id;

                // Insert options into the database
                foreach ($options as $option) {
                    $option_text = $option['option_text'];
                    $is_correct = isset($option['is_correct']) ? 1 : 0;

                    $stmt = $conn->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
                    $stmt->bind_param("isi", $question_id, $option_text, $is_correct);
                    $stmt->execute();
                }
            } else {
                $error_message = "Error inserting question: " . $stmt->error;
                break;
            }
        }

        if (!isset($error_message)) {
            $success_message = "Quiz created successfully!";
        }
    } else {
        $error_message = "Error creating quiz: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #D6B5B5;
        }
        footer {
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
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group select,
        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group textarea {
            height: 150px;
            resize: vertical;
        }
        .btn-container {
            text-align: right;
        }
        .btn-container .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .btn-container .btn:hover {
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
        <h2>Create Quiz</h2>
        <?php if (isset($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="topic_id">Select Topic:</label>
                <select id="topic_id" name="topic_id" required>
                    <option value="">Select a topic</option>
                    <?php foreach ($topics as $topic): ?>
                        <option value="<?php echo $topic['id']; ?>"><?php echo $topic['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Questions:</label>
                <div id="questions-container">
                    <div class="question">
                        <textarea name="questions[0][question_text]" placeholder="Enter question" required></textarea>
                        <br><br>
                        <input type="text" name="questions[0][options][0][option_text]" placeholder="Option 1" required>
                        <input type="checkbox" name="questions[0][options][0][is_correct]" value="1"> Correct
                        <br><br>
                        <input type="text" name="questions[0][options][1][option_text]" placeholder="Option 2" required>
                        <input type="checkbox" name="questions[0][options][1][is_correct]" value="1"> Correct
                        <br><br>
                        <input type="text" name="questions[0][options][2][option_text]" placeholder="Option 3" required>
                        <input type="checkbox" name="questions[0][options][2][is_correct]" value="1"> Correct
                    </div>
                </div>
                <button type="button" id="add-question">Add Question</button>
            </div>
            <div class="btn-container">
                <button type="submit" class="btn">Create Quiz</button>
            </div>
        </form>
        <br><br>
        <div class="btn-container">
            <a href="../../dashboard.php" class="btn">Back to Dashboard</a>
        </div>
    </div>

    <script>
        document.getElementById('add-question').addEventListener('click', function() {
            var questionsContainer = document.getElementById('questions-container');
            var questionIndex = document.querySelectorAll('.question').length;
            var questionDiv = document.createElement('div');
            questionDiv.classList.add('question');
            questionDiv.innerHTML = `
                <textarea name="questions[${questionIndex}][question_text]" placeholder="Enter question" required></textarea>
                <br><br>
                <input type="text" name="questions[${questionIndex}][options][0][option_text]" placeholder="Option 1" required>
                <input type="checkbox" name="questions[${questionIndex}][options][0][is_correct]" value="1"> Correct
                <br><br>
                <input type="text" name="questions[${questionIndex}][options][1][option_text]" placeholder="Option 2" required>
                <input type="checkbox" name="questions[${questionIndex}][options][1][is_correct]" value="1"> Correct
                <br><br>
                <input type="text" name="questions[${questionIndex}][options][2][option_text]" placeholder="Option 3" required>
                <input type="checkbox" name="questions[${questionIndex}][options][2][is_correct]" value="1"> Correct
            `;
            questionsContainer.appendChild(questionDiv);
        });
    </script>
    <?php include '../../templates/footer.php'; ?>
</body>
</html>
