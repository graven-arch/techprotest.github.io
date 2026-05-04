<?php
define('ROOT', dirname(__DIR__));
$pageTitle = 'Gérer les produits';
require_once __DIR__ . '/layout.php';

$db  = getDB();
$msg = '';

// Suppression
if (!empty($_GET['del']) && is_numeric($_GET['del'])) {
    $db->prepare("DELETE FROM produits WHERE id=?")->execute([(int)$_GET['del']]);
    $msg = 'success:Produit supprimé.';
    header('Location: produits.php?ok=1'); exit;
}
if (!empty($_GET['ok'])) $msg = 'success:Produit supprimé avec succès.';

// Toggle vedette
if (!empty($_GET['vedette']) && is_numeric($_GET['vedette'])) {
    $db->prepare("UPDATE produits SET vedette = 1-vedette WHERE id=?")->execute([(int)$_GET['vedette']]);
    header('Location: produits.php'); exit;
}

$catFilter = (int)($_GET['cat'] ?? 0);
$sql  = "SELECT p.*, c.nom AS cat_nom FROM produits p LEFT JOIN categories c ON p.categorie_id=c.id";
$args = [];
if ($catFilter) { $sql .= " WHERE p.categorie_id=?"; $args[] = $catFilter; }
$sql .= " ORDER BY p.id DESC";
$stmt = $db->prepare($sql); $stmt->execute($args);
$produits = $stmt->fetchAll();
$cats = $db->query("SELECT * FROM categories ORDER BY nom")->fetchAll();
?>

<?php if ($msg): [$type,$text] = explode(':',$msg,2); ?>
<div class="alert alert-<?= $type === 'success' ? 'success' : 'error' ?>"><?= htmlspecialchars($text) ?></div>
<?php endif; ?>

<!-- Filtres + actions -->
<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap">
  <form method="GET" style="display:flex;gap:8px">
    <select name="cat" onchange="this.form.submit()" style="border:1px solid var(--border);border-radius:8px;padding:9px 13px;font-size:13px;font-family:inherit;background:#fff">
      <option value="">Toutes les catégories</option>
      <?php foreach ($cats as $c): ?>
      <option value="<?= $c['id'] ?>" <?= $catFilter==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['nom']) ?></option>
      <?php endforeach; ?>
    </select>
  </form>
  <span style="font-size:13px;color:var(--muted)"><?= count($produits) ?> produit(s)</span>
  <a href="ajouter.php" class="btn btn-primary" style="margin-left:auto">+ Nouveau produit</a>
</div>

<div class="admin-card">
  <table>
    <thead>
      <tr>
        <th style="width:60px">Image</th>
        <th>Produit</th>
        <th>Catégorie</th>
        <th>Prix (FCFA)</th>
        <th>Stock</th>
        <th>Vedette</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($produits as $p): ?>
    <tr>
      <td>
        <div style="width:50px;height:40px;border-radius:7px;overflow:hidden;background:var(--bg)">
          <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="" style="width:100%;height:100%;object-fit:cover">
        </div>
      </td>
      <td>
        <div style="font-weight:600;font-size:13px"><?= htmlspecialchars(mb_substr($p['nom'],0,40)) ?><?= mb_strlen($p['nom'])>40?'…':'' ?></div>
        <?php if ($p['badge']): ?><span style="font-size:10px;background:#dbeafe;color:#1d4ed8;padding:1px 8px;border-radius:20px;font-weight:600"><?= htmlspecialchars($p['badge']) ?></span><?php endif; ?>
      </td>
      <td style="font-size:13px;color:var(--muted)"><?= htmlspecialchars($p['cat_nom'] ?? '—') ?></td>
      <td style="font-weight:700;color:var(--accent);font-size:13px"><?= number_format($p['prix'],0,',',' ') ?></td>
      <td>
        <span style="font-size:12px;padding:2px 10px;border-radius:20px;<?= $p['stock']>5?'background:#d1fae5;color:#065f46':($p['stock']>0?'background:#fef9c3;color:#92400e':'background:#fee2e2;color:#991b1b') ?>">
          <?= $p['stock'] ?>
        </span>
      </td>
      <td>
        <a href="?vedette=<?= $p['id'] ?>" title="Basculer vedette" style="font-size:20px;text-decoration:none">
          <?= $p['vedette'] ? '⭐' : '☆' ?>
        </a>
      </td>
      <td>
        <div style="display:flex;gap:6px">
          <a href="modifier.php?id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm">✏️ Modifier</a>
          <a href="?del=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce produit ?')">🗑️</a>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$produits): ?>
    <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">Aucun produit. <a href="ajouter.php" style="color:var(--accent)">Ajouter le premier</a></td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

</main></body></html>
