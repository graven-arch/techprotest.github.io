<?php
// ══════════════════════════════════════════════════
//  TECHPRO.TG — Configuration centrale
// ══════════════════════════════════════════════════

define('DB_HOST',    'localhost');
define('DB_NAME',    'techpro_db');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

define('LDAP_HOST',   '10.0.10.3');
define('LDAP_PORT',    389);
define('LDAP_DOMAIN', 'techpro.tg');
define('LDAP_BASEDN', 'DC=techpro,DC=tg');

define('SITE_NAME',     'TechPro.tg');
define('SITE_TAGLINE',  "L'excellence technologique au Togo");
define('CONTACT_EMAIL', 'contact@techpro.tg');
define('SESSION_LIFETIME', 7200);

// Détection automatique de l'URL de base
if (!defined('APP_URL')) {
    $proto   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir     = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
    $baseDir = str_ends_with($dir, '/admin') ? dirname($dir) : $dir;
    define('APP_URL',   $proto . '://' . $host . $baseDir);
    define('ADMIN_URL', APP_URL . '/admin');
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime'=>SESSION_LIFETIME,'path'=>'/','httponly'=>true,'samesite'=>'Lax']);
    session_start();
}
?>
