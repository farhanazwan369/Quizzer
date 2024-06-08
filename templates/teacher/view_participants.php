<?php
require '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../../login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

// Fetch quizzes created by the logged-in teacher by joining quizzes and topics
$stmt = $conn->prepare("SELECT q.id, t.name 
                        FROM quizzes q 
                        JOIN topics t ON q.topic_id = t.id 
                        WHERE t.created_by = ?");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$quizzes = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
    }
}
$stmt->free_result();

// Fetch participants for a selected quiz
$participants = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['quiz_id'])) {
    $quiz_id = $_POST['quiz_id'];

    $stmt = $conn->prepare("SELECT uq.user_id, u.username, uq.score 
                            FROM user_quizzes uq
                            JOIN users u ON uq.user_id = u.id
                            WHERE uq.quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $participants[] = $row;
    }
    $stmt->free_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Participants</title>
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
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
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
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>View Participants</h2>
        <form method="POST">
            <div class="form-group">
                <label for="quiz_id">Select Quiz:</label>
                <select id="quiz_id" name="quiz_id" required>
                    <option value="">Select a quiz</option>
                    <?php foreach ($quizzes as $quiz): ?>
                        <option value="<?php echo $quiz['id']; ?>"><?php echo htmlspecialchars($quiz['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="btn-container">
                <button type="submit" class="btn">View Participants</button>
				<br />
				<br />
            </div>
        </form>

        <?php if (!empty($participants)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participants as $participant): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($participant['username']); ?></td>
                            <td><?php echo htmlspecialchars($participant['score']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <p>No participants found for the selected quiz.</p>
        <?php endif; ?>
		<br />
		<br />
        <div class="btn-container">
            <a href="../../dashboard.php" class="btn">Back to Dashboard</a>
        </div>
    </div>
    <?php include '../../templates/footer.php'; ?>
</body>
</html>
