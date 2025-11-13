<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
ensure_logged_in();

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$posts = array();
$users = array();
if ($q !== '') {
    $q_esc = $mysqli->real_escape_string($q);
    $pr = $mysqli->query("SELECT p.*, u.name, u.user_type, u.profile_pic,
        (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.post_id) AS like_count,
        (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.post_id) AS comment_count
        FROM posts p INNER JOIN users u ON p.user_id=u.user_id
        WHERE p.caption LIKE '%$q_esc%'
        ORDER BY p.post_id DESC LIMIT 30");
    if ($pr) { while ($row=$pr->fetch_assoc()) $posts[]=$row; }

    $ur = $mysqli->query("SELECT user_id, name, email, user_type, profile_pic FROM users WHERE name LIKE '%$q_esc%' OR email LIKE '%$q_esc%' ORDER BY user_id DESC LIMIT 20");
    if ($ur) { while ($row=$ur->fetch_assoc()) $users[]=$row; }
} else {
    $posts = get_posts($mysqli, 30);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduForum • Explore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/partials/navbar.php'; ?>
<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="row g-3">
                <?php foreach ($posts as $p): ?>
                <div class="col-6 col-md-4">
                    <div class="card shadow-sm rounded-4 overflow-hidden">
                        <?php if (!empty($p['file_path']) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $p['file_path'])): ?>
                            <img src="<?=htmlspecialchars($p['file_path'])?>" class="w-100" alt="">
                        <?php else: ?>
                            <div class="ratio ratio-1x1 bg-light d-flex align-items-center justify-content-center text-muted">No preview</div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <img src="<?=htmlspecialchars(user_avatar($p))?>" class="rounded-circle me-2" width="28" height="28" alt="">
                                <div class="small"><strong><?=htmlspecialchars($p['name'])?></strong> <span class="text-muted">• <?=time_ago($p['timestamp'])?></span></div>
                            </div>
                            <div class="small text-muted">❤ <?=$p['like_count']?> • 💬 <?=$p['comment_count']?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <h6 class="mb-3">People</h6>
                    <?php if ($q !== ''): ?>
                        <?php foreach ($users as $u): ?>
                        <div class="d-flex align-items-center mb-2">
                            <img src="<?=htmlspecialchars(user_avatar($u))?>" width="32" height="32" class="rounded-circle me-2" alt="">
                            <div class="small">
                                <div class="fw-semibold"><?=htmlspecialchars($u['name'])?></div>
                                <div class="text-muted"><?=htmlspecialchars($u['email'])?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted small">Use the search bar to find users or topics.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>