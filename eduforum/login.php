<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? sanitize($_POST['email']) : '';
    $password = isset($_POST['password']) ? sanitize($_POST['password']) : '';
    if (login_user($mysqli, $email, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduForum • Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <img src="assets/img/logo.png" width="56" alt="logo">
                        <h4 class="mt-2">EduForum</h4>
                        <p class="text-muted">Welcome back</p>
                    </div>
                    <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary rounded-pill" type="submit">Login</button>
                        </div>
                    </form>
                    <div class="d-flex justify-content-between mt-3">
                        <a href="signup.php">Create account</a>
                        <a href="forgot.php">Forgot password?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>