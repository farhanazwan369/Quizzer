<?php
require '../../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch topics created by the teacher
$topics_stmt = $conn->prepare("SELECT * FROM topics WHERE created_by = ?");
$topics_stmt->bind_param("i", $user_id);
$topics_stmt->execute();
$topics_result = $topics_stmt->get_result();

// Handle delete and update requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_topic'])) {
        $topic_id = $_POST['topic_id'];

        // Delete topic
        $stmt = $conn->prepare("DELETE FROM topics WHERE id = ?");
        $stmt->bind_param("i", $topic_id);
        $stmt->execute();
        header("Location: manage_topic.php");
        exit();
    }

    if (isset($_POST['update_topic'])) {
        $topic_id = $_POST['topic_id'];
        $topic_name = $_POST['topic_name'];
        $topic_description = $_POST['topic_description'];

        // Update topic
        $stmt = $conn->prepare("UPDATE topics SET name = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $topic_name, $topic_description, $topic_id);
        $stmt->execute();
        header("Location: manage_topic.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Topics</title>
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
        <h2>Manage Topics</h2>
        <div class="section">
            <h3>Topics</h3>
            <table>
                <thead>
                    <tr>
                        <th>Topic ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
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
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="topic_id" value="<?php echo $topic['id']; ?>">
                                    <input type="text" name="topic_name" value="<?php echo htmlspecialchars($topic['name']); ?>" required>
                                    <input type="text" name="topic_description" value="<?php echo htmlspecialchars($topic['description']); ?>" required>
                                    <button type="submit" name="update_topic" class="btn">Update</button>
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
