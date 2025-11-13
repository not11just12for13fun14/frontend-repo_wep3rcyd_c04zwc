<?php
if (!isset($user)) {
    $user = isset($user) ? $user : (isset($mysqli) ? current_user($mysqli) : null);
}
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="assets/img/logo.png" width="28" class="me-2" alt="logo"> <strong>EduForum</strong>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <form class="ms-auto me-3 d-none d-lg-flex" action="explore.php" method="get">
        <div class="input-group">
            <input type="text" name="q" class="form-control rounded-start-pill" placeholder="Search users, topics, #tags">
            <button class="btn btn-outline-primary rounded-end-pill" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
      </form>
      <ul class="navbar-nav align-items-center gap-2">
        <li class="nav-item"><a class="nav-link" href="explore.php"><i class="fa-regular fa-compass"></i></a></li>
        <li class="nav-item"><a class="nav-link" href="resource.php"><i class="fa-regular fa-folder-open"></i></a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
            <img src="<?=htmlspecialchars(user_avatar($user))?>" class="rounded-circle me-2" width="28" height="28"> <span class="d-none d-lg-inline"><?=htmlspecialchars($user['name'])?></span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
            <li><a class="dropdown-item" href="upload.php">Upload</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>