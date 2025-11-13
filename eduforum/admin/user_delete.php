<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
ensure_logged_in();
$user = current_user($mysqli);
if (!$user || $user['user_type'] !== 'admin') { header('Location: ../index.php'); exit; }
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id) {
    $mysqli->query("DELETE FROM users WHERE user_id=$id");
}
header('Location: dashboard.php');
exit;