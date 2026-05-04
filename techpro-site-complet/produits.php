<?php
define('ROOT', __DIR__);
require_once ROOT . '/config.php';
require_once ROOT . '/includes/db.php';

$db = getDB();

// Filtres
$catSlug = trim($_GET['categorie'] ?? '');
$search  = trim($_GET['q'] ?? '');
$sort    = $_GET['tri'] ?? 'recents';

$catInfo = null;
$catId   = null;
if ($catSlug) {
    $catInfo = $db->prepare("SELECT * FROM categories WHERE slug=?")->execute([$catSlug]) ? $db->prepare("SELECT * FROM categories WHERE slug=?")->execute([$catSlug]) : null;
    $stmt = $db->prepare("SELECT * FROM categories WHERE slug=?");
    $stmt->execute([$catSlug]);
    $catInfo = $stmt->fetch();
    if ($catInfo) $catId = $catInfo['id'];
}

// Construction requête
$where = []; $params = [];
if ($catId)   { $where[] = 'p.categorie_id=?'; $params[] = $catId; }
if ($search)  { $where[] = '(p.nom LIKE ? OR p.description LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$orderSQL = match($sort) {
    'prix_asc'  => 'p.prix ASC',
    'prix_desc' => 'p.prix DESC',
    'nom'       => 'p.nom ASC',
    default     => 'p.id DESC',
};

$stmt = $db->prepare("SELECT p.*, c.nom AS cat_nom, c.slug AS cat_slug FROM produits p LEFT JOIN categories c ON p.categorie_id=c.id $whereSQL ORDER BY $orderSQL");
$stmt->execute($params);
$produits = $stmt->fetchAll();

$categories = $db->query("SELECT c.*, COUNT(p.id) AS nb FROM categories c LEFT JOIN produits p ON p.categorie_id=c.id GROUP BY c.id ORDER BY c.nom")->fetchAll();

$pageTitle = $catInfo ? $catInfo['nom'] : 'Tous les produits';
require_once ROOT . '/includes/header.php';

function formatPrice($p) { return number_format($p, 0, ',', ' '); }
?>

<!-- Breadcrumb -->
<div style="background:var(--bg2);padding:14px 0;border-bottom:1px solid var(--border)">
  <div class="container" style="font-size:13px;color:var(--muted)">
    <a href="<?= APP_URL ?>/" style="color:var(--accent)">Accueil</a>
    <span style="margin:0 8px">›</span>
    <?php if ($catInfo): ?>
    <a href="<?= APP_URL ?>/produits.php" style="color:var(--accent)">Produits</a>
    <span style="margin:0 8px">›</span>
    <span style="color:var(--text)"><?= htmlspecialchars($catInfo['nom']) ?></span>
    <?php else: ?>
    <span style="color:var(--text)">Tous les produits</span>
    <?php endif; ?>
  </div>
</div>

<div style="padding:48px 0 80px">
  <div class="container" style="display:grid;grid-template-columns:240px 1fr;gap:32px;align-items:start">

    <!-- ── SIDEBAR FILTRES ── -->
    <aside>
      <div style="background:#fff;border:1px solid var(--border);border-radius:14px;padding:20px;position:sticky;top:88px">
        <h3 style="font-family:'Syne',sans-serif;font-size:15px;font-weight:700;margin-bottom:16px">Catégories</h3>
        <a href="produits.php" style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-radius:8px;font-size:13px;<?= !$catId?'background:#f0f4ff;color:var(--accent);font-weight:600':'color:var(--muted)' ?>;transition:background .15s" onmouseover="if(!this.classList.contains('act'))this.style.background='#f7f8fc'" onmouseout="if(!this.classList.contains('act'))this.style.background='transparent'">
          Toutes les catégories
        </a>
        <?php foreach ($categories as $c): ?>
        <a href="produits.php?categorie=<?= urlencode($c['slug']) ?><?= $search ? '&q='.urlencode($search) : '' ?>"
           style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;border-radius:8px;font-size:13px;<?= ($catId==$c['id'])?'background:#f0f4ff;color:var(--accent);font-weight:600':'color:var(--text)' ?>;transition:background .15s"
           onmouseover="this.style.background='#f7f8fc'" onmouseout="this.style.background='<?= ($catId==$c['id'])?'#f0f4ff':'transparent' ?>'">
          <span><?= $c['icone'] ?> <?= htmlspecialchars($c['nom']) ?></span>
          <span style="font-size:11px;color:var(--muted)"><?= $c['nb'] ?></span>
        </a>
        <?php endforeach; ?>
      </div>
    </aside>

    <!-- ── CONTENU PRINCIPAL ── -->
    <div>
      <!-- Barre de recherche + tri -->
      <div style="display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap">
        <form method="GET" style="flex:1;display:flex;gap:8px;min-width:200px">
          <?php if ($catSlug): ?><input type="hidden" name="categorie" value="<?= htmlspecialchars($catSlug) ?>"><?php endif; ?>
          <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                 placeholder="Rechercher un produit..."
                 style="flex:1;border:1px solid var(--border);border-radius:8px;padding:10px 14px;font-size:14px;outline:none;font-family:inherit"
                 onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          <button type="submit" style="background:var(--accent);color:#fff;border:none;border-radius:8px;padding:0 16px;cursor:pointer;font-size:13px;font-weight:600">Chercher</button>
          <?php if ($search): ?><a href="produits.php<?= $catSlug?'?categorie='.$catSlug:'' ?>" style="background:#fee2e2;color:var(--red);border-radius:8px;padding:0 14px;display:flex;align-items:center;font-size:13px">✕</a><?php endif; ?>
        </form>
        <select onchange="location.href=this.value"
                style="border:1px solid var(--border);border-radius:8px;padding:10px 14px;font-size:13px;cursor:pointer;background:#fff;font-family:inherit">
          <?php foreach (['recents'=>'Plus récents','prix_asc'=>'Prix croissant','prix_desc'=>'Prix décroissant','nom'=>'A→Z'] as $k=>$l): ?>
          <option value="produits.php?<?= http_build_query(array_merge($_GET, ['tri'=>$k])) ?>" <?= $sort===$k?'selected':'' ?>><?= $l ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Titre + compteur -->
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
        <div>
          <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:800">
            <?= $catInfo ? htmlspecialchars($catInfo['icone'].' '.$catInfo['nom']) : 'Tous les produits' ?>
          </h1>
          <?php if ($search): ?><p style="font-size:13px;color:var(--muted);margin-top:4px">Résultats pour «&nbsp;<?= htmlspecialchars($search) ?>&nbsp;»</p><?php endif; ?>
        </div>
        <span style="font-size:13px;color:var(--muted)"><?= count($produits) ?> produit<?= count($produits)>1?'s':'' ?></span>
      </div>

      <!-- Grille produits -->
      <?php if (empty($produits)): ?>
      <div style="text-align:center;padding:80px 0;color:var(--muted)">
        <div style="font-size:48px;margin-bottom:16px">🔍</div>
        <h3 style="font-family:'Syne',sans-serif;font-size:20px;margin-bottom:8px">Aucun produit trouvé</h3>
        <a href="produits.php" style="color:var(--accent);font-size:14px">Voir tous les produits</a>
      </div>
      <?php else: ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px">
        <?php foreach ($produits as $p): ?>
        <a href="produit.php?id=<?= $p['id'] ?>" class="product-card">
          <div class="product-img-wrap">
            <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>" loading="lazy">
            <?php if ($p['badge']): ?><span class="product-badge"><?= htmlspecialchars($p['badge']) ?></span><?php endif; ?>
            <?php if ($p['stock'] <= 3 && $p['stock'] > 0): ?><span class="product-badge" style="background:var(--red);top:auto;bottom:12px">Derniers <?= $p['stock'] ?></span><?php endif; ?>
          </div>
          <div class="product-body">
            <div class="product-cat"><?= htmlspecialchars($p['cat_nom']) ?></div>
            <div class="product-name"><?= htmlspecialchars($p['nom']) ?></div>
            <div class="product-desc"><?= htmlspecialchars($p['description']) ?></div>
            <div class="product-footer">
              <div class="product-price"><?= formatPrice($p['prix']) ?> <small>FCFA</small></div>
              <span class="btn-detail">Voir →</span>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<?php require_once ROOT . '/includes/footer.php'; ?>
