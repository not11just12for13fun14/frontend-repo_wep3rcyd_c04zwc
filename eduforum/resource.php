<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
ensure_logged_in();
$user = current_user($mysqli);

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : 'Notes';
    $file_path = '';
    if (!empty($_FILES['file']['name'])) {
        $up = handle_upload($_FILES['file'], array('pdf','ppt','pptx','doc','docx','jpg','jpeg','png','gif'));
        if ($up['ok']) $file_path = $up['path'];
    }
    if ($title && $file_path) {
        $uid = intval($user['user_id']);
        $title_ = $mysqli->real_escape_string($title);
        $cat_ = $mysqli->real_escape_string($category);
        $file_ = $mysqli->real_escape_string($file_path);
        $mysqli->query("INSERT INTO resources(uploader_id,title,category,file_path,downloads,timestamp) VALUES($uid,'$title_','$cat_','$file_',0,NOW())");
        $msg = 'Resource shared successfully';
    } else {
        $msg = 'Please add a title and file';
    }
}

// Latest resources
$resources = array();
$rr = $mysqli->query("SELECT r.*, u.name FROM resources r INNER JOIN users u ON r.uploader_id=u.user_id ORDER BY r.resource_id DESC LIMIT 30");
if ($rr) { while ($row=$rr->fetch_assoc()) $resources[]=$row; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduForum • Resources</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/partials/navbar.php'; ?>
<div class="container py-4">
    <?php if ($msg): ?><div class="alert alert-info"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <h6 class="mb-3">Share a Resource</h6>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input class="form-control" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" name="category">
                                <option>Notes</option>
                                <option>Assignments</option>
                                <option>Research</option>
                                <option>Books</option>
                                <option>Others</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">File (PDF/PPT/DOC/Image)</label>
                            <input type="file" class="form-control" name="file" required>
                        </div>
                        <button class="btn btn-primary rounded-pill" type="submit">Upload</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="row g-3">
                <?php foreach ($resources as $r): ?>
                <div class="col-md-6">
                    <div class="card shadow-sm rounded-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge badge-category me-2"><?=htmlspecialchars($r['category'])?></span>
                                <strong class="flex-grow-1"><?=htmlspecialchars($r['title'])?></strong>
                            </div>
                            <div class="small text-muted mb-2">By <?=htmlspecialchars($r['name'])?> • Downloads: <?=$r['downloads']?></div>
                            <div class="d-flex gap-2">
                                <a class="btn btn-sm btn-outline-primary rounded-pill" href="download.php?id=<?=$r['resource_id']}"><i class="fa-solid fa-download me-1"></i> Download</a>
                                <a class="btn btn-sm btn-outline-secondary rounded-pill" href="<?=htmlspecialchars($r['file_path'])?>" target="_blank"><i class="fa-regular fa-eye me-1"></i> View</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>