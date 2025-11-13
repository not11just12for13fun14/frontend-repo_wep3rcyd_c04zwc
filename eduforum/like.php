<?php
header('Content-Type: application/json');
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
ensure_logged_in();

$user = current_user($mysqli);
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

add_like($mysqli, $post_id, $user['user_id']);

// Return like count
$res = $mysqli->query("SELECT COUNT(*) AS c FROM likes WHERE post_id=$post_id");
$count = 0; if ($res && $row=$res->fetch_assoc()) $count=intval($row['c']);

echo json_encode(array('like_count'=>$count));
