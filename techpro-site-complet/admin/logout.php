<?php
define('ROOT', dirname(__DIR__));
require_once ROOT . '/config.php';
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();
header('Location: ' . ADMIN_URL . '/login.php');
exit;
?>
