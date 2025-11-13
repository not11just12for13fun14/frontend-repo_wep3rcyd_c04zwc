<?php
// EduForum - Database connection (PHP 5.3.5+ compatible)
// Update credentials as needed for your WAMP/XAMPP setup
if (session_id() == '') {
    session_start();
}

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'eduforum';

$mysqli = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    die('Failed to connect to MySQL: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8');

// Base URL autoguess; adjust if folder name differs
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$basePath = rtrim(str_replace(basename($script), '', $script), '/');
if (strpos($basePath, '/eduforum') === false) {
    $basePath .= '/eduforum';
}
if (!defined('BASE_URL')) {
    define('BASE_URL', $protocol . $host . $basePath);
}

if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', dirname(__FILE__) . '/../uploads/');
}
if (!file_exists(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0755, true);
}
?>