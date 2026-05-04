<?php
// admin/layout.php — included at top of every admin page
// $pageTitle must be set before including
if (!defined('ROOT')) define('ROOT', dirname(__DIR__));
require_once ROOT . '/config.php';
require_once ROOT . '/includes/db.php';
require_once ROOT . '/includes/auth.php';
requireAdmin();

$admin = $_SESSION['admin'];
$initials = strtoupper(substr($admin['firstname'] ?? $admin['username'], 0, 1) . substr($admin['lastname'] ?? '', 0, 1));
if (strlen($initials) < 1) $initials = strtoupper(substr($admin['username'], 0, 2));

try {
    $db = getDB();
    $nb_produits = $db->query("SELECT COUNT(*) FROM produits")->fetchColumn();
    $nb_contacts = $db->query("SELECT COUNT(*) FROM contacts WHERE lu=0")->fetchColumn();
    $nb_cats     = $db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
} catch (Exception $e) { $nb_produits = $nb_contacts = $nb_cats = '?'; }

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — Admin TechPro.tg</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#f7f8fc;--bg2:#eef0f6;--white:#ffffff;
  --dark:#060810;--dark2:#0f1420;
  --accent:#0057ff;--accent2:#00b4ff;
  --text:#1a1d2e;--muted:#6b7280;--border:#e5e7eb;
  --red:#ef4444;--green:#10b981;--gold:#f59e0b;
  --sidebar:220px;--shadow:0 4px 20px rgba(0,0,0,0.07);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);display:flex;min-height:100vh}

/* ── SIDEBAR ── */
.aside{width:var(--sidebar);flex-shrink:0;background:var(--dark);display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:100}
.a-logo{padding:22px 18px;border-bottom:1px solid rgba(255,255,255,0.07)}
.a-logo-row{display:flex;align-items:center;gap:10px}
.a-logo-icon{width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center}
.a-logo-icon svg{width:18px;height:18px;fill:#fff}
.a-logo-text{font-family:'Syne',sans-serif;font-size:16px;font-weight:800;color:#fff}
.a-logo-text span{color:var(--accent2)}
.a-logo-sub{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.3);margin-top:2px}
.a-nav{flex:1;padding:14px 10px;overflow-y:auto}
.a-nav-label{font-size:9px;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,0.25);padding:0 8px;margin:16px 0 6px}
.a-nav a{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;color:rgba(255,255,255,0.55);font-size:13px;text-decoration:none;transition:background .15s,color .15s;position:relative}
.a-nav a svg{width:16px;height:16px;flex-shrink:0}
.a-nav a:hover,.a-nav a.act{background:rgba(0,200,255,0.08);color:#fff}
.a-nav a.act::before{content:'';position:absolute;left:0;top:20%;bottom:20%;width:3px;border-radius:3px;background:var(--accent2)}
.badge-n{margin-left:auto;background:var(--red);color:#fff;font-size:10px;padding:1px 6px;border-radius:20px;line-height:16px}

.a-user{padding:14px;border-top:1px solid rgba(255,255,255,0.07);display:flex;align-items:center;gap:10px}
.a-avatar{width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-family:'Syne',sans-serif;font-weight:700;font-size:13px;color:#fff;flex-shrink:0}
.a-user-info{flex:1;min-width:0}
.a-user-name{font-size:12px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.a-user-role{font-size:10px;color:rgba(255,255,255,0.35)}
.a-logout{background:none;border:none;cursor:pointer;color:rgba(255,255,255,0.35);transition:color .2s;padding:4px;line-height:0}
.a-logout:hover{color:var(--red)}
.a-logout svg{width:15px;height:15px}

/* ── MAIN ── */
.main-admin{margin-left:var(--sidebar);flex:1;padding:32px 28px;min-height:100vh}

/* ── TOPBAR ── */
.admin-topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px}
.admin-title{font-family:'Syne',sans-serif;font-size:22px;font-weight:800;letter-spacing:-0.5px}
.admin-title span{color:var(--accent)}
.back-site{display:flex;align-items:center;gap:6px;font-size:13px;color:var(--muted);background:var(--white);border:1px solid var(--border);border-radius:8px;padding:7px 14px;text-decoration:none;transition:color .15s}
.back-site:hover{color:var(--accent)}
.back-site svg{width:14px;height:14px}

/* ── CARDS & TABLES ── */
.admin-card{background:var(--white);border:1px solid var(--border);border-radius:14px;overflow:hidden;box-shadow:var(--shadow)}
.admin-card-head{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.admin-card-title{font-family:'Syne',sans-serif;font-size:15px;font-weight:700}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:opacity .2s,transform .15s;font-family:'Inter',sans-serif}
.btn:hover{opacity:.88;transform:translateY(-1px)}
.btn-primary{background:linear-gradient(135deg,var(--accent),var(--accent2));color:#fff}
.btn-secondary{background:var(--bg);border:1px solid var(--border);color:var(--text)}
.btn-danger{background:#fee2e2;color:var(--red)}
.btn-sm{padding:5px 12px;font-size:12px}
table{width:100%;border-collapse:collapse;font-size:13px}
th{padding:10px 16px;text-align:left;font-size:11px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--muted);border-bottom:1px solid var(--border);background:var(--bg)}
td{padding:14px 16px;border-bottom:1px solid var(--border)}
tr:last-child td{border-bottom:none}
tr:hover td{background:var(--bg)}

/* Forms */
.form-grid{display:grid;gap:20px}
.form-grid-2{grid-template-columns:1fr 1fr}
.form-group label{display:block;font-size:12px;font-weight:600;color:var(--muted);letter-spacing:1px;text-transform:uppercase;margin-bottom:7px}
.form-group input,.form-group select,.form-group textarea{width:100%;border:1px solid var(--border);border-radius:9px;padding:11px 13px;font-size:14px;font-family:'Inter',sans-serif;outline:none;transition:border-color .2s;background:var(--white)}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--accent);box-shadow:0 0 0 3px rgba(0,87,255,0.08)}
.form-group textarea{resize:vertical;min-height:100px}

/* Alerts */
.alert{padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:20px;display:flex;align-items:center;gap:8px}
.alert-success{background:#d1fae5;border:1px solid #6ee7b7;color:#065f46}
.alert-error{background:#fee2e2;border:1px solid #fca5a5;color:var(--red)}

/* ── STATS MINI ── */
.stat-mini{background:var(--white);border:1px solid var(--border);border-radius:12px;padding:20px;display:flex;align-items:center;gap:14px}
.stat-mini-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}

::-webkit-scrollbar{width:5px}::-webkit-scrollbar-track{background:transparent}::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}
</style>
</head>
<body>

<aside class="aside">
  <div class="a-logo">
    <div class="a-logo-row">
      <div class="a-logo-icon"><svg viewBox="0 0 24 24"><path d="M13 3L4 14h7l-2 7 9-11h-7l2-7z"/></svg></div>
      <div>
        <div class="a-logo-text">Tech<span>Pro</span>.tg</div>
        <div class="a-logo-sub">Admin Panel</div>
      </div>
    </div>
  </div>

  <nav class="a-nav">
    <div class="a-nav-label">Principal</div>
    <a href="<?= ADMIN_URL ?>/" class="<?= in_array($currentPage,['index.php','']) ? 'act' : '' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Tableau de bord
    </a>
    <a href="produits.php" class="<?= $currentPage==='produits.php'?'act':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
      Produits
      <span style="margin-left:auto;font-size:11px;color:rgba(255,255,255,0.3)"><?= $nb_produits ?></span>
    </a>
    <a href="ajouter.php" class="<?= $currentPage==='ajouter.php'?'act':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
      Ajouter un produit
    </a>
    <a href="categories.php" class="<?= $currentPage==='categories.php'?'act':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
      Catégories
      <span style="margin-left:auto;font-size:11px;color:rgba(255,255,255,0.3)"><?= $nb_cats ?></span>
    </a>

    <div class="a-nav-label">Communication</div>
    <a href="contacts.php" class="<?= $currentPage==='contacts.php'?'act':'' ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      Messages
      <?php if ($nb_contacts > 0): ?><span class="badge-n"><?= $nb_contacts ?></span><?php endif; ?>
    </a>
  </nav>

  <div class="a-user">
    <div class="a-avatar"><?= htmlspecialchars($initials) ?></div>
    <div class="a-user-info">
      <div class="a-user-name"><?= htmlspecialchars($admin['fullname']) ?></div>
      <div class="a-user-role">Admin AD</div>
    </div>
    <form method="POST" action="logout.php">
      <button type="submit" class="a-logout" title="Déconnexion">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      </button>
    </form>
  </div>
</aside>

<main class="main-admin">
  <div class="admin-topbar">
    <div class="admin-title"><?= htmlspecialchars($pageTitle ?? 'Admin') ?></div>
    <a href="<?= APP_URL ?>/" class="back-site" target="_blank">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
      Voir le site
    </a>
  </div>
