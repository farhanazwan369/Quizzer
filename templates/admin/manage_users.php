<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit();
}

require_once '../../config/database.php';

// Function to get all users
function getUsers() {
    global $conn;
    $sql = "SELECT id, username, role_id FROM users";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

// Function to get role name by role ID
function getRoleName($role_id) {
    global $conn;
    $sql = "SELECT role_name FROM roles WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $stmt->bind_result($role_name);
    $stmt->fetch();
    return $role_name;
}

// Create new user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role_id = $_POST['role_id'];

    $sql = "INSERT INTO users (username, password, role_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $password, $role_id);

    if ($stmt->execute()) {
        $success_message = "User created successfully.";
    } else {
        $error_message = "Error creating user: " . $stmt->error;
    }
}

// Update user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $role_id = $_POST['role_id'];

    $sql = "UPDATE users SET username = ?, role_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $username, $role_id, $user_id);

    if ($stmt->execute()) {
        $success_message = "User updated successfully.";
    } else {
        $error_message = "Error updating user: " . $stmt->error;
    }
}

// Delete user
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        $success_message = "User deleted successfully.";
    } else {
        $error_message = "Error deleting user: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
		 body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #D6B5B5;
        }
	
		footer{
			text-align: center;
		}
		
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .user-table th, .user-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .user-table th {
            background-color: #f2f2f2;
        }
        .user-actions {
            display: flex;
            align-items: center;
        }
        .user-actions form {
            margin: 0;
        }
        .user-actions button {
            margin-left: 10px;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
		.back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            cursor: pointer;
        }
        .back-btn:hover {
            background-color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Users</h2>
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <h3>Create New User</h3>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="role_id">Role:</label>
            <select id="role_id" name="role_id" required>
                <option value="1">Admin</option>
                <option value="2">Teacher</option>
                <option value="3">Student</option>
            </select>
            <button type="submit" name="create">Create User</button>
        </form>

        <h3>Existing Users</h3>
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $users = getUsers();
                foreach ($users as $user):
                ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo getRoleName($user['role_id']); ?></td>
                    <td class="user-actions">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
                            <select name="role_id">
                                <option value="1" <?php echo ($user['role_id'] == 1) ? 'selected' : ''; ?>>Admin</option>
                                <option value="2" <?php echo ($user['role_id'] == 2) ? 'selected' : ''; ?>>Teacher</option>
                                <option value="3" <?php echo ($user['role_id'] == 3) ? 'selected' : ''; ?>>Student</option>
                            </select>
                            <button type="submit" name="update">Update</button>
                        </form>
                        <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <input type="hidden" name="delete" value="<?php echo $user['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
		<a href="../../dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
    <?php include '../../templates/footer.php'; ?>
</body>
</html>
