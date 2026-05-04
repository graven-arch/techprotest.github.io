<?php
define('ROOT', dirname(__DIR__));
$pageTitle = 'Catégories';
require_once __DIR__ . '/layout.php';

$db = getDB();
$ok = ''; $err = '';

// Ajout
if (!empty($_POST['action']) && $_POST['action'] === 'add') {
    $nom   = trim($_POST['nom'] ?? '');
    $slug  = trim($_POST['slug'] ?? '');
    $icone = trim($_POST['icone'] ?? '📦');
    if (!$nom || !$slug) { $err = 'Nom et slug requis.'; }
    else {
        try {
            $db->prepare("INSERT INTO categories (nom, slug, icone) VALUES (?,?,?)")->execute([$nom, $slug, $icone]);
            $ok = 'Catégorie ajoutée.';
        } catch (Exception $e) { $err = 'Ce slug existe déjà.'; }
    }
}
// Suppression
if (!empty($_GET['del']) && is_numeric($_GET['del'])) {
    $db->prepare("DELETE FROM categories WHERE id=?")->execute([(int)$_GET['del']]);
    header('Location: categories.php?ok=1'); exit;
}
if (!empty($_GET['ok'])) $ok = 'Catégorie supprimée.';

$cats = $db->query("SELECT c.*, COUNT(p.id) AS nb FROM categories c LEFT JOIN produits p ON p.categorie_id=c.id GROUP BY c.id ORDER BY c.nom")->fetchAll();
?>

<?php if ($ok): ?><div class="alert alert-success">✅ <?= htmlspecialchars($ok) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error">❌ <?= htmlspecialchars($err) ?></div><?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

  <!-- Liste -->
  <div class="admin-card">
    <div class="admin-card-head">
      <div class="admin-card-title">Catégories existantes</div>
      <span style="font-size:13px;color:var(--muted)"><?= count($cats) ?> catégorie(s)</span>
    </div>
    <table>
      <thead><tr><th>Icône</th><th>Nom</th><th>Slug</th><th>Produits</th><th>Action</th></tr></thead>
      <tbody>
      <?php foreach ($cats as $c): ?>
      <tr>
        <td style="font-size:24px;text-align:center"><?= $c['icone'] ?></td>
        <td style="font-weight:600"><?= htmlspecialchars($c['nom']) ?></td>
        <td style="font-family:monospace;font-size:12px;color:var(--muted)"><?= htmlspecialchars($c['slug']) ?></td>
        <td>
          <span style="background:#dbeafe;color:#1d4ed8;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:600"><?= $c['nb'] ?></span>
        </td>
        <td>
          <a href="?del=<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette catégorie ? Les produits ne seront pas supprimés.')">🗑️</a>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Formulaire ajout -->
  <div class="admin-card">
    <div class="admin-card-head"><div class="admin-card-title">Nouvelle catégorie</div></div>
    <div style="padding:20px">
      <form method="POST" class="form-grid">
        <input type="hidden" name="action" value="add">
        <div class="form-group">
          <label>Nom *</label>
          <input type="text" name="nom" placeholder="Ex: Imprimantes" required oninput="autoSlug(this.value)">
        </div>
        <div class="form-group">
          <label>Slug * <small style="color:var(--muted)">(URL, sans espace)</small></label>
          <input type="text" name="slug" id="slug" placeholder="ex: imprimantes" required pattern="[a-z0-9\-]+">
        </div>
        <div class="form-group">
          <label>Icône (emoji)</label>
          <input type="text" name="icone" value="📦" maxlength="4" style="font-size:20px;text-align:center">
        </div>
        <button type="submit" class="btn btn-primary">+ Ajouter</button>
      </form>
    </div>
  </div>

</div>

<script>
function autoSlug(v) {
  document.getElementById('slug').value = v.toLowerCase()
    .replace(/[àâä]/g,'a').replace(/[éèêë]/g,'e').replace(/[îï]/g,'i')
    .replace(/[ôö]/g,'o').replace(/[ùûü]/g,'u').replace(/ç/g,'c')
    .replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
}
</script>

</main></body></html>
