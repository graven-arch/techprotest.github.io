<?php
// includes/header.php
// Usage: require_once ROOT.'/includes/header.php';
// Expects $pageTitle to be set before inclusion
if (!defined('ROOT')) define('ROOT', dirname(__DIR__));
require_once ROOT . '/config.php';
require_once ROOT . '/includes/db.php';
$_pageTitle = ($pageTitle ?? SITE_NAME) . ' — ' . SITE_NAME;

// Récupérer les catégories pour le menu
try {
    $navCats = getDB()->query("SELECT nom, slug FROM categories ORDER BY nom")->fetchAll();
} catch (Exception $e) { $navCats = []; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($_pageTitle) ?></title>
<meta name="description" content="TechPro.tg — Équipements technologiques haut de gamme au Togo.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
/* ══════════════════════════════════
   TECHPRO.TG — Global Stylesheet
══════════════════════════════════ */
:root {
  --bg:      #ffffff;
  --bg2:     #f7f8fc;
  --dark:    #060810;
  --dark2:   #0f1420;
  --accent:  #0057ff;
  --accent2: #00b4ff;
  --text:    #1a1d2e;
  --muted:   #6b7280;
  --border:  #e5e7eb;
  --red:     #ef4444;
  --green:   #10b981;
  --gold:    #f59e0b;
  --radius:  12px;
  --shadow:  0 4px 24px rgba(0,0,0,0.08);
  --shadow2: 0 16px 48px rgba(0,0,0,0.14);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body {
  font-family: 'Inter', sans-serif;
  background: var(--bg);
  color: var(--text);
  line-height: 1.6;
}
img { max-width: 100%; display: block; }
a  { color: inherit; text-decoration: none; }

/* ── TOPBAR ── */
.topbar {
  background: var(--dark);
  color: rgba(255,255,255,0.65);
  font-size: 12px;
  padding: 6px 0;
  text-align: center;
  letter-spacing: 0.3px;
}
.topbar span { color: var(--accent2); }

/* ── NAVBAR ── */
.navbar {
  position: sticky; top: 0; z-index: 900;
  background: rgba(255,255,255,0.96);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--border);
  padding: 0 40px;
  display: flex; align-items: center;
  height: 68px; gap: 32px;
}
.nav-logo {
  display: flex; align-items: center; gap: 10px;
  font-family: 'Syne', sans-serif; font-weight: 800;
  font-size: 20px; letter-spacing: -0.5px; flex-shrink: 0;
}
.nav-logo .logo-icon {
  width: 36px; height: 36px; border-radius: 9px;
  background: linear-gradient(135deg, var(--accent), var(--accent2));
  display: flex; align-items: center; justify-content: center;
}
.nav-logo .logo-icon svg { width: 18px; height: 18px; fill: #fff; }
.nav-logo .dot { color: var(--accent); }

.nav-links {
  display: flex; align-items: center; gap: 4px;
  list-style: none; flex: 1;
}
.nav-links a {
  padding: 7px 14px; border-radius: 8px;
  font-size: 14px; font-weight: 500; color: var(--muted);
  transition: background .15s, color .15s;
  white-space: nowrap;
}
.nav-links a:hover, .nav-links a.active { background: #f0f4ff; color: var(--accent); }

/* Dropdown catégories */
.nav-dropdown { position: relative; }
.nav-dropdown > a::after { content: ' ▾'; font-size: 10px; }
.dropdown-menu {
  position: absolute; top: calc(100% + 8px); left: 0;
  background: #fff; border: 1px solid var(--border);
  border-radius: var(--radius); box-shadow: var(--shadow2);
  padding: 8px; min-width: 200px; z-index: 999;
  opacity: 0; visibility: hidden; transform: translateY(6px);
  transition: all .2s;
}
.nav-dropdown:hover .dropdown-menu { opacity: 1; visibility: visible; transform: none; }
.dropdown-menu a {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 12px; border-radius: 8px; color: var(--text);
  font-size: 13px;
}
.dropdown-menu a:hover { background: #f0f4ff; color: var(--accent); }

.nav-right { margin-left: auto; display: flex; align-items: center; gap: 12px; }
.btn-contact {
  background: var(--accent);
  color: #fff; padding: 8px 20px;
  border-radius: 8px; font-size: 13px; font-weight: 600;
  transition: opacity .2s, transform .15s;
}
.btn-contact:hover { opacity: .88; transform: translateY(-1px); }

/* ── CONTAINER ── */
.container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }

/* ── SECTION TITLE ── */
.section-header { text-align: center; margin-bottom: 48px; }
.section-eyebrow {
  display: inline-block; font-size: 11px; font-weight: 600;
  letter-spacing: 2px; text-transform: uppercase;
  color: var(--accent); margin-bottom: 12px;
}
.section-title {
  font-family: 'Syne', sans-serif; font-size: clamp(26px, 4vw, 38px);
  font-weight: 800; letter-spacing: -0.8px; line-height: 1.15;
  color: var(--dark);
}
.section-sub { color: var(--muted); margin-top: 12px; font-size: 16px; }

/* ── PRODUCT CARD ── */
.product-card {
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 16px; overflow: hidden;
  transition: box-shadow .25s, transform .25s;
  display: flex; flex-direction: column;
}
.product-card:hover {
  box-shadow: var(--shadow2);
  transform: translateY(-4px);
}
.product-img-wrap {
  position: relative; overflow: hidden;
  aspect-ratio: 4/3; background: var(--bg2);
}
.product-img-wrap img {
  width: 100%; height: 100%; object-fit: cover;
  transition: transform .4s ease;
}
.product-card:hover .product-img-wrap img { transform: scale(1.05); }
.product-badge {
  position: absolute; top: 12px; left: 12px;
  background: var(--accent); color: #fff;
  font-size: 11px; font-weight: 700;
  padding: 3px 10px; border-radius: 20px;
  letter-spacing: 0.5px;
}
.product-badge.gold { background: var(--gold); }
.product-badge.green { background: var(--green); }
.product-body { padding: 16px; flex: 1; display: flex; flex-direction: column; }
.product-cat { font-size: 11px; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
.product-name { font-family: 'Syne', sans-serif; font-size: 16px; font-weight: 700; margin-bottom: 6px; line-height: 1.3; }
.product-desc { font-size: 13px; color: var(--muted); flex: 1; margin-bottom: 14px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.product-footer { display: flex; align-items: center; justify-content: space-between; }
.product-price { font-family: 'Syne', sans-serif; font-size: 18px; font-weight: 800; color: var(--accent); }
.product-price small { font-size: 12px; font-weight: 400; color: var(--muted); }
.btn-detail {
  background: #f0f4ff; color: var(--accent);
  padding: 7px 16px; border-radius: 8px;
  font-size: 13px; font-weight: 600;
  transition: background .15s;
}
.btn-detail:hover { background: var(--accent); color: #fff; }

/* ── FOOTER ── */
.site-footer {
  background: var(--dark); color: rgba(255,255,255,0.7);
  padding: 64px 0 0;
}
.footer-grid {
  display: grid; grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 40px; margin-bottom: 48px;
}
.footer-brand .logo-text { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 800; color: #fff; }
.footer-brand .logo-text .dot { color: var(--accent2); }
.footer-brand p { margin-top: 12px; font-size: 13px; line-height: 1.8; max-width: 280px; }
.footer-col h4 { color: #fff; font-size: 13px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 16px; }
.footer-col a { display: block; font-size: 13px; margin-bottom: 8px; transition: color .15s; }
.footer-col a:hover { color: var(--accent2); }
.footer-bottom {
  border-top: 1px solid rgba(255,255,255,0.08);
  padding: 20px 0; display: flex; align-items: center;
  justify-content: space-between; font-size: 12px;
}
.footer-bottom a { color: var(--accent2); }

/* ── UTILS ── */
.badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-blue  { background: #dbeafe; color: #1d4ed8; }
.badge-green { background: #d1fae5; color: #065f46; }

/* ── SCROLLBAR ── */
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: transparent; }
::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
  .navbar { padding: 0 16px; gap: 12px; }
  .nav-links { display: none; }
  .footer-grid { grid-template-columns: 1fr 1fr; }
}
</style>
</head>
<body>

<div class="topbar">
  Livraison partout au Togo · <span>support@techpro.tg</span> · Lun–Sam 8h–18h
</div>

<nav class="navbar">
  <a href="<?= APP_URL ?>/" class="nav-logo">
    <div class="logo-icon">
      <svg viewBox="0 0 24 24"><path d="M13 3L4 14h7l-2 7 9-11h-7l2-7z"/></svg>
    </div>
    Tech<span class="dot">Pro</span>.tg
  </a>

  <ul class="nav-links">
    <li><a href="<?= APP_URL ?>/" class="<?= (basename($_SERVER['PHP_SELF'])=='index.php'?'active':'') ?>">Accueil</a></li>
    <li class="nav-dropdown">
      <a href="<?= APP_URL ?>/produits.php">Produits</a>
      <div class="dropdown-menu">
        <a href="<?= APP_URL ?>/produits.php">Tout voir</a>
        <?php foreach ($navCats as $c): ?>
        <a href="<?= APP_URL ?>/produits.php?categorie=<?= urlencode($c['slug']) ?>">
          <?= htmlspecialchars($c['nom']) ?>
        </a>
        <?php endforeach; ?>
      </div>
    </li>
    <li><a href="<?= APP_URL ?>/a-propos.php">À propos</a></li>
    <li><a href="<?= APP_URL ?>/contact.php">Contact</a></li>
  </ul>

  <div class="nav-right">
    <a href="<?= APP_URL ?>/contact.php" class="btn-contact">Nous contacter</a>
  </div>
</nav>
