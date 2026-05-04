<?php
define('ROOT', __DIR__);
require_once ROOT . '/config.php';
require_once ROOT . '/includes/db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: produits.php'); exit; }

$db = getDB();
$stmt = $db->prepare("SELECT p.*, c.nom AS cat_nom, c.slug AS cat_slug, c.icone AS cat_icone FROM produits p LEFT JOIN categories c ON p.categorie_id=c.id WHERE p.id=?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { header('Location: produits.php'); exit; }

// Produits similaires
$sim = $db->prepare("SELECT p.*, c.nom AS cat_nom FROM produits p LEFT JOIN categories c ON p.categorie_id=c.id WHERE p.categorie_id=? AND p.id!=? LIMIT 3");
$sim->execute([$p['categorie_id'], $id]);
$similaires = $sim->fetchAll();

$pageTitle = $p['nom'];
require_once ROOT . '/includes/header.php';

function formatPrice($v) { return number_format($v, 0, ',', ' '); }
?>

<!-- Breadcrumb -->
<div style="background:var(--bg2);padding:14px 0;border-bottom:1px solid var(--border)">
  <div class="container" style="font-size:13px;color:var(--muted)">
    <a href="<?= APP_URL ?>/" style="color:var(--accent)">Accueil</a> <span style="margin:0 8px">›</span>
    <a href="<?= APP_URL ?>/produits.php" style="color:var(--accent)">Produits</a> <span style="margin:0 8px">›</span>
    <a href="<?= APP_URL ?>/produits.php?categorie=<?= urlencode($p['cat_slug']) ?>" style="color:var(--accent)"><?= htmlspecialchars($p['cat_nom']) ?></a>
    <span style="margin:0 8px">›</span>
    <span style="color:var(--text)"><?= htmlspecialchars($p['nom']) ?></span>
  </div>
</div>

<div style="padding:48px 0 80px">
  <div class="container">

    <!-- PRODUIT PRINCIPAL -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:56px;margin-bottom:80px;align-items:start">

      <!-- Image -->
      <div style="position:sticky;top:88px">
        <div style="border-radius:20px;overflow:hidden;border:1px solid var(--border);background:var(--bg2);aspect-ratio:4/3;position:relative">
          <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>" style="width:100%;height:100%;object-fit:cover">
          <?php if ($p['badge']): ?>
          <span class="product-badge" style="font-size:13px;padding:5px 14px"><?= htmlspecialchars($p['badge']) ?></span>
          <?php endif; ?>
        </div>
      </div>

      <!-- Infos -->
      <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px">
          <a href="produits.php?categorie=<?= urlencode($p['cat_slug']) ?>" style="font-size:12px;color:var(--accent);background:#f0f4ff;padding:4px 12px;border-radius:20px;font-weight:600">
            <?= htmlspecialchars($p['cat_icone'].' '.$p['cat_nom']) ?>
          </a>
          <?php if ($p['stock'] > 0): ?>
          <span style="font-size:12px;color:var(--green);background:#d1fae5;padding:4px 12px;border-radius:20px;font-weight:600">✓ En stock (<?= $p['stock'] ?>)</span>
          <?php else: ?>
          <span style="font-size:12px;color:var(--red);background:#fee2e2;padding:4px 12px;border-radius:20px;font-weight:600">Rupture de stock</span>
          <?php endif; ?>
        </div>

        <h1 style="font-family:'Syne',sans-serif;font-size:clamp(22px,3.5vw,32px);font-weight:800;letter-spacing:-0.8px;line-height:1.2;margin-bottom:16px">
          <?= htmlspecialchars($p['nom']) ?>
        </h1>

        <div style="font-size:36px;font-family:'Syne',sans-serif;font-weight:800;color:var(--accent);margin-bottom:24px">
          <?= formatPrice($p['prix']) ?> <span style="font-size:18px;font-weight:400;color:var(--muted)">FCFA</span>
        </div>

        <div style="background:var(--bg2);border-radius:12px;padding:20px;margin-bottom:24px;font-size:14px;color:var(--muted);line-height:1.8">
          <?= nl2br(htmlspecialchars($p['description'])) ?>
        </div>

        <!-- CTA -->
        <div style="display:flex;flex-direction:column;gap:12px">
          <a href="contact.php?produit=<?= urlencode($p['nom']) ?>"
             style="display:flex;align-items:center;justify-content:center;gap:8px;background:linear-gradient(135deg,var(--accent),#00b4ff);color:#fff;padding:16px;border-radius:12px;font-weight:700;font-size:16px;transition:opacity .2s,transform .2s"
             onmouseover="this.style.opacity='.88';this.style.transform='translateY(-1px)'"
             onmouseout="this.style.opacity='1';this.style.transform='none'">
            📩 Demander ce produit
          </a>
          <a href="contact.php"
             style="display:flex;align-items:center;justify-content:center;gap:8px;border:2px solid var(--border);color:var(--text);padding:14px;border-radius:12px;font-weight:600;font-size:14px;transition:all .2s"
             onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'"
             onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text)'">
            💬 Nous contacter
          </a>
        </div>

        <!-- Garanties -->
        <div style="display:flex;gap:16px;margin-top:24px;flex-wrap:wrap">
          <?php foreach (['🔒 Authentique','🚚 Livraison rapide','🛡️ Garantie SAV'] as $g): ?>
          <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted)"><?= $g ?></div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- PRODUITS SIMILAIRES -->
    <?php if ($similaires): ?>
    <div>
      <h2 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800;margin-bottom:24px">Produits similaires</h2>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px">
        <?php foreach ($similaires as $s): ?>
        <a href="produit.php?id=<?= $s['id'] ?>" class="product-card">
          <div class="product-img-wrap">
            <img src="<?= htmlspecialchars($s['image_url']) ?>" alt="<?= htmlspecialchars($s['nom']) ?>" loading="lazy">
            <?php if ($s['badge']): ?><span class="product-badge"><?= htmlspecialchars($s['badge']) ?></span><?php endif; ?>
          </div>
          <div class="product-body">
            <div class="product-cat"><?= htmlspecialchars($s['cat_nom']) ?></div>
            <div class="product-name" style="font-size:15px"><?= htmlspecialchars($s['nom']) ?></div>
            <div class="product-footer" style="margin-top:10px">
              <div class="product-price" style="font-size:16px"><?= formatPrice($s['prix']) ?> <small>FCFA</small></div>
              <span class="btn-detail" style="font-size:12px">Voir</span>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>

<?php require_once ROOT . '/includes/footer.php'; ?>
