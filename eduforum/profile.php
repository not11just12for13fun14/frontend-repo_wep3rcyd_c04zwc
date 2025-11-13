<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
ensure_logged_in();
$user = current_user($mysqli);

// Update profile
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $mysqli->real_escape_string(trim($_POST['name']));
    $bio = $mysqli->real_escape_string(trim($_POST['bio']));

    $pic_path = $user['profile_pic'];
    if (!empty($_FILES['profile_pic']['name'])) {
        $up = handle_upload($_FILES['profile_pic'], array('jpg','jpeg','png','gif'));
        if ($up['ok']) { $pic_path = $up['path']; }
    }

    $mysqli->query("UPDATE users SET name='$name', bio='$bio', profile_pic='".$mysqli->real_escape_string($pic_path)."' WHERE user_id=".intval($user['user_id']));
    $msg = 'Profile updated';
    $user = current_user($mysqli); // refresh
}

// User posts
$uid = intval($user['user_id']);
$posts = array();
$res = $mysqli->query("SELECT * FROM posts WHERE user_id=$uid ORDER BY post_id DESC");
if ($res) { while ($row = $res->fetch_assoc()) $posts[] = $row; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduForum • Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/partials/navbar.php'; ?>
<div class="container py-4">
    <div class="profile-header p-4 mb-4">
        <div class="d-flex align-items-center">
            <img src="<?=htmlspecialchars(user_avatar($user))?>" class="avatar-xl me-3" alt="avatar">
            <div>
                <h4 class="mb-1"><?=htmlspecialchars($user['name'])?></h4>
                <div class="text-white-50 small"><?=htmlspecialchars($user['email'])?> • <?=htmlspecialchars(ucfirst($user['user_type']))?></div>
            </div>
        </div>
        <p class="mt-3 mb-0">
            <?=nl2br(htmlspecialchars($user['bio']))?>
        </p>
    </div>

    <?php if ($msg): ?><div class="alert alert-success"><?=htmlspecialchars($msg)?></div><?php endif; ?>

    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <h6 class="mb-3">Edit Profile</h6>
            <form method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?=htmlspecialchars($user['name'])?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" name="profile_pic" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Bio</label>
                        <textarea class="form-control" name="bio" rows="3"><?=htmlspecialchars($user['bio'])?></textarea>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary rounded-pill" type="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3">
        <?php foreach ($posts as $p): ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="ratio ratio-1x1 bg-light rounded-4 overflow-hidden">
                <?php if ($p['file_path'] && preg_match('/\.(jpg|jpeg|png|gif)$/i', $p['file_path'])): ?>
                    <img src="<?=htmlspecialchars($p['file_path'])?>" class="w-100 h-100 object-fit-cover" alt="post">
                <?php else: ?>
                    <div class="d-flex align-items-center justify-content-center text-muted">No preview</div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>