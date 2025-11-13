<?php
// EduForum - Home (News Feed)
// Pure PHP + MySQLi (PHP 5.3.5+ compatible)
// This file serves as the main feed page
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Require login
ensure_logged_in();
$user = current_user($mysqli);

// Handle new post submission
$post_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_post') {
    $caption = isset($_POST['caption']) ? trim($_POST['caption']) : '';
    $file_path = '';

    if (!empty($_FILES['file']['name'])) {
        $upload = handle_upload($_FILES['file'], array('jpg','jpeg','png','gif','pdf','ppt','pptx','doc','docx')); // images + docs
        if ($upload['ok']) {
            $file_path = $upload['path'];
        } else {
            $post_error = $upload['error'];
        }
    }

    if ($post_error === '') {
        create_post($mysqli, $user['user_id'], $caption, $file_path);
        header('Location: index.php');
        exit;
    }
}

// Fetch feed posts (self + following + public). For simplicity, all public posts by recency
$posts = get_posts($mysqli, 50);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduForum • Feed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-light">
<?php include __DIR__ . '/partials/navbar.php'; ?>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="create_post">
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?=htmlspecialchars(user_avatar($user))?>" class="rounded-circle me-2" width="44" height="44" alt="avatar">
                            <input class="form-control rounded-pill" type="text" name="caption" placeholder="Share an update, resource, or thought...">
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>
                                <label class="btn btn-outline-primary btn-sm me-2">
                                    <i class="fa-solid fa-paperclip me-1"></i> Attach file
                                    <input type="file" name="file" hidden>
                                </label>
                            </div>
                            <button class="btn btn-primary btn-sm rounded-pill" type="submit">Post</button>
                        </div>
                        <?php if ($post_error): ?>
                            <div class="alert alert-danger mt-3 mb-0"><?php echo htmlspecialchars($post_error); ?></div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <?php foreach ($posts as $p): ?>
            <div class="card shadow-sm rounded-4 mb-4 post-card" data-post-id="<?=$p['post_id']?>">
                <div class="card-header bg-white border-0 d-flex align-items-center">
                    <img src="<?=htmlspecialchars(user_avatar($p))?>" class="rounded-circle me-2" width="36" height="36" alt="avatar">
                    <div>
                        <div class="fw-semibold"><?=htmlspecialchars($p['name'])?> <span class="badge bg-light text-dark text-capitalize ms-2"><?=htmlspecialchars($p['user_type'])?></span></div>
                        <small class="text-muted"><?=time_ago($p['timestamp'])?></small>
                    </div>
                </div>
                <?php if (!empty($p['file_path'])): ?>
                <div class="ratio ratio-1x1 bg-black overflow-hidden">
                    <?php if (is_image($p['file_path'])): ?>
                        <img src="<?=htmlspecialchars($p['file_path'])?>" class="w-100 h-100 object-fit-cover" alt="post image">
                    <?php else: ?>
                        <div class="d-flex flex-column align-items-center justify-content-center text-white h-100">
                            <i class="fa-regular fa-file-lines fa-2x mb-2"></i>
                            <a href="<?=htmlspecialchars($p['file_path'])?>" class="btn btn-outline-light btn-sm" target="_blank">Open file</a>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <p class="mb-2"><?=nl2br(htmlspecialchars($p['caption']))?></p>
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <button class="btn btn-like btn-sm btn-light rounded-pill" data-post-id="<?=$p['post_id']?>">
                            <i class="fa-regular fa-heart me-1"></i> <span class="like-count"><?=$p['like_count']?></span>
                        </button>
                        <button class="btn btn-comment-toggle btn-sm btn-light rounded-pill" data-post-id="<?=$p['post_id']?>">
                            <i class="fa-regular fa-comment me-1"></i> <span class="comment-count"><?=$p['comment_count']?></span>
                        </button>
                        <a class="btn btn-sm btn-light rounded-pill" href="share.php?post_id=<?=$p['post_id']?>"><i class="fa-solid fa-share me-1"></i> Share</a>
                    </div>
                    <div class="comments" id="comments-<?=$p['post_id']?>" style="display:none"></div>
                    <div class="input-group input-group-sm mt-2">
                        <input type="text" class="form-control comment-input" placeholder="Add a comment...">
                        <button class="btn btn-primary btn-comment-submit" data-post-id="<?=$p['post_id']?>">Post</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm rounded-4 mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="<?=htmlspecialchars(user_avatar($user))?>" class="rounded-circle me-3" width="56" height="56" alt="avatar">
                        <div>
                            <div class="fw-semibold"><?=htmlspecialchars($user['name'])?></div>
                            <small class="text-muted"><?=htmlspecialchars($user['email'])?></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <h6 class="mb-3">Upload Resource</h6>
                    <a href="resource.php" class="btn btn-outline-primary w-100 rounded-pill">Share materials</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>