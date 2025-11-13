<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$msg = '';$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $email = trim($_POST['email']);
  if($email){
    $res = $mysqli->query("SELECT user_id FROM users WHERE email='".$mysqli->real_escape_string($email)."' LIMIT 1");
    if($res && $res->num_rows){
      // Simple tokenless reset: set temp password and display it (since no SMTP). For production, integrate SMTP.
      $temp = substr(md5(uniqid('', true)),0,8);
      $mysqli->query("UPDATE users SET password='".$mysqli->real_escape_string(password_hash_compat($temp))."' WHERE email='".$mysqli->real_escape_string($email)."'");
      $msg = 'Temporary password: ' . $temp . ' (please login and change it)';
    } else {
      $err = 'Email not found';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EduForum • Forgot Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh;">
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm rounded-4">
        <div class="card-body p-4">
          <h4 class="mb-3">Forgot Password</h4>
          <?php if($msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
          <?php if($err): ?><div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
          <form method="post">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <button class="btn btn-primary rounded-pill" type="submit">Reset</button>
            <a class="btn btn-link" href="login.php">Back to login</a>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>