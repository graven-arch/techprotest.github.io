<?php
define('ROOT', dirname(__DIR__));
require_once ROOT . '/config.php';
require_once ROOT . '/includes/auth.php';

if (!empty($_SESSION['admin'])) {
    header('Location: ' . ADMIN_URL . '/');
    exit;
}

$error   = '';
$timeout = !empty($_GET['timeout']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (empty($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf'] ?? '')) {
        $error = 'Requête invalide.';
    } else {
        $user = authenticateAD($username, $password);
        if ($user) {
            session_regenerate_id(true);
            $_SESSION['admin']      = $user;
            $_SESSION['admin_time'] = time();
            unset($_SESSION['csrf']);
            header('Location: ' . ADMIN_URL . '/');
            exit;
        } else {
            $error = 'Identifiants AD incorrects ou accès refusé.';
        }
    }
}
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Administration — TechPro.tg</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{--bg:#060810;--panel:#0f1420;--border:rgba(0,200,255,0.12);--accent:#0057ff;--accent2:#00c8ff;--text:#e8edf5;--muted:#5a6680;--error:#ef4444;--green:#10b981}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center}
.bg-grid{position:fixed;inset:0;background-image:linear-gradient(rgba(0,200,255,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(0,200,255,0.04) 1px,transparent 1px);background-size:56px 56px;pointer-events:none}
.orb{position:fixed;border-radius:50%;filter:blur(80px);pointer-events:none}
.o1{width:500px;height:500px;background:radial-gradient(circle,rgba(0,87,255,0.2),transparent 70%);top:-150px;left:-100px}
.o2{width:350px;height:350px;background:radial-gradient(circle,rgba(0,200,255,0.15),transparent 70%);bottom:-80px;right:-60px}
.card{position:relative;z-index:1;background:var(--panel);border:1px solid var(--border);border-radius:20px;padding:44px 40px;width:100%;max-width:420px;box-shadow:0 0 60px rgba(0,200,255,0.08),0 24px 80px rgba(0,0,0,0.5)}
.logo{text-align:center;margin-bottom:32px}
.logo-icon{display:inline-flex;align-items:center;justify-content:center;width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,var(--accent),var(--accent2));box-shadow:0 0 28px rgba(0,87,255,0.4);margin-bottom:12px}
.logo-icon svg{width:26px;height:26px;fill:#fff}
.logo-name{font-family:'Syne',sans-serif;font-size:20px;font-weight:800}
.logo-name .dot{color:var(--accent2)}
.logo-sub{font-size:11px;color:var(--muted);letter-spacing:2px;text-transform:uppercase;margin-top:4px}
.sep{height:1px;background:linear-gradient(90deg,transparent,var(--border),transparent);margin-bottom:24px}
.banner{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:18px}
.banner-err{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:var(--error)}
.banner-warn{background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);color:#f59e0b}
label{display:block;font-size:11px;font-weight:600;color:var(--muted);letter-spacing:1.5px;text-transform:uppercase;margin-bottom:7px}
.inp-wrap{position:relative;margin-bottom:18px}
.inp-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--muted)}
.inp-icon svg{width:16px;height:16px}
input[type=text],input[type=password]{width:100%;background:#0c0f1a;border:1px solid rgba(0,200,255,0.12);border-radius:10px;padding:12px 14px 12px 42px;color:var(--text);font-family:'Inter',sans-serif;font-size:14px;outline:none;transition:border-color .2s,box-shadow .2s}
input:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(0,87,255,0.15)}
input::placeholder{color:var(--muted)}
.hint{font-size:11px;color:var(--muted);margin-top:-12px;margin-bottom:14px;padding-left:2px}
.hint span{color:var(--accent2)}
.toggle{position:absolute;right:13px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--muted);transition:color .2s;line-height:0}
.toggle:hover{color:var(--accent2)}
.toggle svg{width:16px;height:16px}
.btn-submit{width:100%;padding:13px;background:linear-gradient(135deg,var(--accent),var(--accent2));border:none;border-radius:10px;color:#fff;font-family:'Syne',sans-serif;font-size:15px;font-weight:700;cursor:pointer;transition:opacity .2s,transform .15s;box-shadow:0 4px 24px rgba(0,87,255,0.35)}
.btn-submit:hover{opacity:.88;transform:translateY(-1px)}
.footer{margin-top:24px;text-align:center;font-size:12px;color:var(--muted)}
.footer a{color:var(--accent2)}
</style>
</head>
<body>
<div class="bg-grid"></div>
<div class="orb o1"></div>
<div class="orb o2"></div>

<div class="card">
  <div class="logo">
    <div class="logo-icon"><svg viewBox="0 0 24 24"><path d="M13 3L4 14h7l-2 7 9-11h-7l2-7z"/></svg></div>
    <div class="logo-name">Tech<span class="dot">Pro</span>.tg</div>
    <div class="logo-sub">Administration</div>
  </div>
  <div class="sep"></div>

  <?php if ($timeout): ?>
  <div class="banner banner-warn">⏱ Session expirée. Reconnectez-vous.</div>
  <?php endif; ?>
  <?php if ($error): ?>
  <div class="banner banner-err">✕ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

    <label for="u">Identifiant Active Directory</label>
    <div class="inp-wrap">
      <span class="inp-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
      <input type="text" id="u" name="username" placeholder="ex: jean.dupont" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
    </div>
    <div class="hint">Domaine : <span><?= LDAP_DOMAIN ?></span> · Serveur AD : <span><?= LDAP_HOST ?></span></div>

    <label for="p">Mot de passe</label>
    <div class="inp-wrap">
      <span class="inp-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
      <input type="password" id="p" name="password" placeholder="Mot de passe AD" required>
      <button type="button" class="toggle" onclick="var i=document.getElementById('p');i.type=i.type==='password'?'text':'password'">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
      </button>
    </div>

    <button type="submit" class="btn-submit">Connexion Admin →</button>
  </form>

  <div class="footer">
    <a href="<?= APP_URL ?>/">← Retour au site</a>
  </div>
</div>
</body>
</html>
