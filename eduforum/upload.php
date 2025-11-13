<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
ensure_logged_in();
$user = current_user($mysqli);

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = isset($_POST['caption']) ? trim($_POST['caption']) : '';
    $file_path = '';
    if (!empty($_FILES['file']['name'])) {
        $up = handle_upload($_FILES['file'], array('jpg','jpeg','png','gif','pdf','ppt','pptx','doc','docx'));
        if ($up['ok']) $file_path = $up['path'];
    }
    create_post($mysqli, $user['user_id'], $caption, $file_path);
    $msg = 'Uploaded successfully';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduForum • Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/partials/navbar.php'; ?>
<div class="container py-4">
    <?php if ($msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <div class="card shadow-sm rounded-4">
        <div class="card-body">
            <h6 class="mb-3">Create a new post</h6>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Caption</label>
                    <textarea class="form-control" name="caption" rows="3" placeholder="Say something..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Attach file (image or document)</label>
                    <input type="file" name="file" class="form-control">
                </div>
                <button class="btn btn-primary rounded-pill" type="submit">Upload</button>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>