<?php
require '../../config/database.php';
session_start();

$search = $_GET['search'] ?? '';
$stmt = $conn->prepare("SELECT * FROM topics WHERE name LIKE ?");
$search_param = "%" . $search . "%";
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Topics</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h2>Search Topics</h2>
        <form method="GET">
            <input type="text" name="search" placeholder="Search topics" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li><?= htmlspecialchars($row['name']) ?></li>
            <?php endwhile; ?>
        </ul>
    </div>
    <?php include '../../templates/footer.php'; ?>
</body>
</html>
