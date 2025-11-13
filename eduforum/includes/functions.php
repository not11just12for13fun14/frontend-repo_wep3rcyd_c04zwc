<?php
// EduForum - Helper functions (procedural, PHP 5.3.5+)

function sanitize($str) {
    return trim($str);
}

function password_hash_compat($password) {
    // For PHP 5.3.5 we don't have password_hash; use sha1 with salt
    // In production, upgrade PHP to use password_hash.
    $salt = 'eduforum_salt_2011';
    return sha1($salt . $password);
}

function ensure_logged_in() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function current_user($mysqli) {
    if (!isset($_SESSION['user_id'])) return null;
    $uid = intval($_SESSION['user_id']);
    $res = $mysqli->query("SELECT user_id, name, email, user_type, bio, profile_pic FROM users WHERE user_id = $uid LIMIT 1");
    if ($res && $res->num_rows) {
        return $res->fetch_assoc();
    }
    return null;
}

function user_avatar($user_or_row) {
    $pic = isset($user_or_row['profile_pic']) ? $user_or_row['profile_pic'] : '';
    if ($pic && file_exists(dirname(__FILE__) . '/../' . $pic)) {
        return $pic;
    }
    return 'assets/img/avatar.png';
}

function handle_upload($file, $allowed_exts) {
    if (!isset($file) || !isset($file['name']) || $file['error'] != UPLOAD_ERR_OK) {
        return array('ok' => false, 'error' => 'No file uploaded');
    }
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_exts)) {
        return array('ok' => false, 'error' => 'Invalid file type');
    }
    $safe = preg_replace('/[^A-Za-z0-9_\.-]/', '_', basename($filename));
    $newname = time() . '_' . $safe;
    $dest = UPLOAD_DIR . $newname;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return array('ok' => false, 'error' => 'Failed to move upload');
    }
    // Return web path relative to project root
    return array('ok' => true, 'path' => 'uploads/' . $newname);
}

function create_user($mysqli, $name, $email, $password, $user_type) {
    $name = $mysqli->real_escape_string($name);
    $email = $mysqli->real_escape_string($email);
    $pass = $mysqli->real_escape_string(password_hash_compat($password));
    $user_type = $mysqli->real_escape_string($user_type);

    $exist = $mysqli->query("SELECT user_id FROM users WHERE email='$email' LIMIT 1");
    if ($exist && $exist->num_rows) {
        return array('ok' => false, 'error' => 'Email already registered');
    }

    $sql = "INSERT INTO users(name,email,password,user_type,bio,profile_pic) VALUES('$name','$email','$pass','$user_type','','')";
    if ($mysqli->query($sql)) {
        return array('ok' => true, 'user_id' => $mysqli->insert_id);
    }
    return array('ok' => false, 'error' => 'Failed to create user');
}

function login_user($mysqli, $email, $password) {
    $email = $mysqli->real_escape_string($email);
    $pass = $mysqli->real_escape_string(password_hash_compat($password));
    $res = $mysqli->query("SELECT user_id FROM users WHERE email='$email' AND password='$pass' LIMIT 1");
    if ($res && $res->num_rows) {
        $row = $res->fetch_assoc();
        $_SESSION['user_id'] = intval($row['user_id']);
        return true;
    }
    return false;
}

function is_image($path) {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return in_array($ext, array('jpg','jpeg','png','gif'));
}

function time_ago($datetime) {
    $ts = is_numeric($datetime) ? intval($datetime) : strtotime($datetime);
    $diff = time() - $ts;
    if ($diff < 60) return $diff . 's ago';
    if ($diff < 3600) return floor($diff/60) . 'm ago';
    if ($diff < 86400) return floor($diff/3600) . 'h ago';
    return date('M d, Y', $ts);
}

function create_post($mysqli, $user_id, $caption, $file_path) {
    $user_id = intval($user_id);
    $caption = $mysqli->real_escape_string($caption);
    $file_path = $mysqli->real_escape_string($file_path);
    $sql = "INSERT INTO posts(user_id, caption, file_path, timestamp) VALUES($user_id,'$caption','$file_path',NOW())";
    if ($mysqli->query($sql)) {
        return $mysqli->insert_id;
    }
    return 0;
}

function get_posts($mysqli, $limit) {
    $limit = intval($limit);
    $sql = "SELECT p.*, u.name, u.user_type, u.profile_pic,
            (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.post_id) AS like_count,
            (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.post_id) AS comment_count
            FROM posts p INNER JOIN users u ON p.user_id = u.user_id
            ORDER BY p.post_id DESC LIMIT $limit";
    $rows = array();
    if ($res = $mysqli->query($sql)) {
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
    }
    return $rows;
}

function get_comments($mysqli, $post_id) {
    $post_id = intval($post_id);
    $sql = "SELECT c.*, u.name, u.profile_pic FROM comments c INNER JOIN users u ON c.user_id=u.user_id WHERE c.post_id=$post_id ORDER BY c.comment_id ASC";
    $rows = array();
    if ($res = $mysqli->query($sql)) {
        while ($row = $res->fetch_assoc()) $rows[] = $row;
    }
    return $rows;
}

function add_like($mysqli, $post_id, $user_id) {
    $post_id = intval($post_id);
    $user_id = intval($user_id);
    // toggle like
    $check = $mysqli->query("SELECT like_id FROM likes WHERE post_id=$post_id AND user_id=$user_id LIMIT 1");
    if ($check && $check->num_rows) {
        $row = $check->fetch_assoc();
        $mysqli->query("DELETE FROM likes WHERE like_id=" . intval($row['like_id']));
        return false;
    } else {
        $mysqli->query("INSERT INTO likes(post_id,user_id,timestamp) VALUES($post_id,$user_id,NOW())");
        return true;
    }
}

function add_comment($mysqli, $post_id, $user_id, $text) {
    $post_id = intval($post_id);
    $user_id = intval($user_id);
    $text = $mysqli->real_escape_string($text);
    return $mysqli->query("INSERT INTO comments(post_id,user_id,comment_text,timestamp) VALUES($post_id,$user_id,'$text',NOW())");
}

function create_notification($mysqli, $sender_id, $receiver_id, $message) {
    $sender_id = intval($sender_id);
    $receiver_id = intval($receiver_id);
    $message = $mysqli->real_escape_string($message);
    $mysqli->query("INSERT INTO notifications(sender_id,receiver_id,message,is_read,timestamp) VALUES($sender_id,$receiver_id,'$message',0,NOW())");
}
?>