<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
ensure_logged_in();

$post_id = isset($_REQUEST['post_id']) ? intval($_REQUEST['post_id']) : 0;
$comments = get_comments($mysqli, $post_id);
?>
<?php foreach ($comments as $c): ?>
<div class="d-flex align-items-start mb-2">
    <img src="<?=htmlspecialchars(user_avatar($c))?>" width="28" height="28" class="rounded-circle me-2" alt="avatar">
    <div>
        <div class="small"><strong><?=htmlspecialchars($c['name'])?></strong> • <span class="text-muted"><?=time_ago($c['timestamp'])?></span></div>
        <div><?=nl2br(htmlspecialchars($c['comment_text']))?></div>
    </div>
</div>
<?php endforeach; ?>