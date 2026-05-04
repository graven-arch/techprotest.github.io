<?php
define('ROOT', dirname(__DIR__));
$pageTitle = 'Tableau de bord';
require_once __DIR__ . '/layout.php';

$db = getDB();
$total_produits  = $db->query("SELECT COUNT(*) FROM produits")->fetchColumn();
$total_vedettes  = $db->query("SELECT COUNT(*) FROM produits WHERE vedette=1")->fetchColumn();
$total_contacts  = $db->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$non_lus         = $db->query("SELECT COUNT(*) FROM contacts WHERE lu=0")->fetchColumn();
$total_cats      = $db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$derniers_msgs   = $db->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5")->fetchAll();
$derniers_prods  = $db->query("SELECT p.*, c.nom AS cat_nom FROM produits p LEFT JOIN categories c ON p.categorie_id=c.id ORDER BY p.created_at DESC LIMIT 5")->fetchAll();
?>

<!-- Stats -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px">
  <?php
  $stats = [
    ['📦','Produits',     $total_produits, 'Catalogue total',     '#dbeafe','#1d4ed8'],
    ['⭐','En vedette',   $total_vedettes, 'Sur la page d\'accueil','#fef9c3','#92400e'],
    ['📩','Messages',     $total_contacts, 'Formulaires reçus',   '#f0fdf4','#065f46'],
    ['🔴','Non lus',      $non_lus,        'À traiter',           '#fee2e2','#991b1b'],
  ];
  foreach ($stats as [$ico,$label,$val,$sub,$bg,$col]):
  ?>
  <div class="stat-mini">
    <div class="stat-mini-icon" style="background:<?= $bg ?>"><?= $ico ?></div>
    <div>
      <div style="font-family:'Syne',sans-serif;font-size:26px;font-weight:800;color:<?= $col ?>"><?= $val ?></div>
      <div style="font-size:13px;font-weight:600"><?= $label ?></div>
      <div style="font-size:11px;color:var(--muted)"><?= $sub ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Bienvenue -->
<div style="background:linear-gradient(135deg,var(--dark),#001a66);border-radius:14px;padding:24px 28px;margin-bottom:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px">
  <div>
    <h2 style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#fff;margin-bottom:4px">
      Bienvenue, <?= htmlspecialchars($admin['firstname'] ?: $admin['username']) ?> 👋
    </h2>
    <p style="color:rgba(255,255,255,0.5);font-size:13px">Connecté via Active Directory · <?= LDAP_DOMAIN ?> · <?= date('d/m/Y H:i') ?></p>
  </div>
  <div style="display:flex;gap:10px">
    <a href="ajouter.php" class="btn btn-primary">+ Ajouter un produit</a>
    <a href="contacts.php" class="btn" style="background:rgba(255,255,255,0.1);color:#fff;border:1px solid rgba(255,255,255,0.15)">
      Voir les messages <?php if($non_lus>0): ?><span style="background:var(--red);border-radius:20px;padding:1px 7px;font-size:11px"><?= $non_lus ?></span><?php endif; ?>
    </a>
  </div>
</div>

<!-- Grille basse -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

  <!-- Derniers produits -->
  <div class="admin-card">
    <div class="admin-card-head">
      <div class="admin-card-title">Derniers produits ajoutés</div>
      <a href="ajouter.php" class="btn btn-primary btn-sm">+ Ajouter</a>
    </div>
    <table>
      <thead><tr><th>Produit</th><th>Prix</th><th>Action</th></tr></thead>
      <tbody>
      <?php foreach ($derniers_prods as $p): ?>
      <tr>
        <td>
          <div style="font-weight:600;font-size:13px"><?= htmlspecialchars(mb_substr($p['nom'],0,32)) ?><?= mb_strlen($p['nom'])>32?'…':'' ?></div>
          <div style="font-size:11px;color:var(--muted)"><?= htmlspecialchars($p['cat_nom']) ?></div>
        </td>
        <td style="font-weight:600;color:var(--accent);white-space:nowrap"><?= number_format($p['prix'],0,',',' ') ?> F</td>
        <td><a href="produits.php" class="btn btn-secondary btn-sm">Gérer</a></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Derniers messages -->
  <div class="admin-card">
    <div class="admin-card-head">
      <div class="admin-card-title">Derniers messages</div>
      <a href="contacts.php" class="btn btn-secondary btn-sm">Tout voir</a>
    </div>
    <table>
      <thead><tr><th>Expéditeur</th><th>Sujet</th><th>Date</th></tr></thead>
      <tbody>
      <?php foreach ($derniers_msgs as $m): ?>
      <tr style="<?= !$m['lu'] ? 'background:#f0f4ff' : '' ?>">
        <td>
          <div style="font-weight:<?= !$m['lu']?'600':'400' ?>;font-size:13px"><?= htmlspecialchars($m['nom']) ?></div>
          <div style="font-size:11px;color:var(--muted)"><?= htmlspecialchars($m['email']) ?></div>
        </td>
        <td style="font-size:12px;color:var(--muted)"><?= htmlspecialchars(mb_substr($m['sujet']??'(sans sujet)',0,28)) ?></td>
        <td style="font-size:11px;color:var(--muted);white-space:nowrap"><?= date('d/m H:i', strtotime($m['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$derniers_msgs): ?>
      <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:24px">Aucun message reçu.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>

</main></body></html>
