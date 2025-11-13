<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
ensure_logged_in();
$user = current_user($mysqli);
if (!$user || $user['user_type'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$stats = array('users'=>0,'posts'=>0,'downloads'=>0,'reports'=>0);
$r = $mysqli->query("SELECT COUNT(*) c FROM users"); if ($r && $row=$r->fetch_assoc()) $stats['users']=intval($row['c']);
$r = $mysqli->query("SELECT COUNT(*) c FROM posts"); if ($r && $row=$r->fetch_assoc()) $stats['posts']=intval($row['c']);
$r = $mysqli->query("SELECT SUM(downloads) c FROM resources"); if ($r && $row=$r->fetch_assoc()) $stats['downloads']=intval($row['c']);

$users = array();
$ur = $mysqli->query("SELECT user_id,name,email,user_type FROM users ORDER BY user_id DESC LIMIT 50");
if ($ur) { while ($row=$ur->fetch_assoc()) $users[]=$row; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin • EduForum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <h4 class="mb-4">Admin Dashboard</h4>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4"><div class="card-body"><div class="small text-muted">Users</div><div class="h3 mb-0"><?=$stats['users']?></div></div></div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4"><div class="card-body"><div class="small text-muted">Posts</div><div class="h3 mb-0"><?=$stats['posts']?></div></div></div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4"><div class="card-body"><div class="small text-muted">Downloads</div><div class="h3 mb-0"><?=$stats['downloads']?></div></div></div>
        </div>
    </div>

    <div class="card shadow-sm rounded-4">
        <div class="card-body">
            <h6 class="mb-3">Manage Users</h6>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?=$u['user_id']?></td>
                            <td><?=htmlspecialchars($u['name'])?></td>
                            <td><?=htmlspecialchars($u['email'])?></td>
                            <td><?=htmlspecialchars($u['user_type'])?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-danger" href="user_delete.php?id=<?=$u['user_id']?>" onclick="return confirm('Delete this user?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>