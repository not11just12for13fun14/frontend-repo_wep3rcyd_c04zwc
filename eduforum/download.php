<?php
require_once __DIR__ . '/includes/db.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$rr = $mysqli->query("SELECT * FROM resources WHERE resource_id=$id LIMIT 1");
if ($rr && $rr->num_rows) {
    $r = $rr->fetch_assoc();
    $mysqli->query("UPDATE resources SET downloads = downloads + 1 WHERE resource_id=$id");
    header('Location: ' . $r['file_path']);
    exit;
}
header('Location: resource.php');
exit;