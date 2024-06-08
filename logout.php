<?php
session_start();
session_destroy(); // Destroy all sessions

header("Location: login.php");
exit();
?>
