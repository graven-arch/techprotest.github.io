<?php
define('ROOT', dirname(__DIR__));
$pageTitle = 'Ajouter un produit';
require_once __DIR__ . '/layout.php';

$db   = getDB();
$cats = $db->query("SELECT * FROM categories ORDER BY nom")->fetchAll();
$errors = []; $ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom   = trim($_POST['nom']   ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $prix  = (int)($_POST['prix'] ?? 0);
    $cat   = (int)($_POST['categorie_id'] ?? 0);
    $img   = trim($_POST['image_url'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);
    $ved   = !empty($_POST['vedette']) ? 1 : 0;
    $badge = trim($_POST['badge'] ?? '');

    if (!$nom)   $errors[] = 'Le nom est requis.';
    if ($prix<=0) $errors[] = 'Le prix doit être supérieur à 0.';

    if (!$errors) {
        $stmt = $db->prepare("INSERT INTO produits (nom, description, prix, categorie_id, image_url, stock, vedette, badge) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$nom,$desc,$prix,$cat?$cat:null,$img,$stock,$ved,$badge?:null]);
        header('Location: produits.php?ok=1'); exit;
    }
}
?>

<?php if ($errors): ?>
<div class="alert alert-error"><?= implode(' · ', array_map('htmlspecialchars', $errors)) ?></div>
<?php endif; ?>

<div class="admin-card" style="max-width:800px">
  <div class="admin-card-head">
    <div class="admin-card-title">Nouveau produit</div>
    <a href="produits.php" class="btn btn-secondary btn-sm">← Retour</a>
  </div>
  <div style="padding:28px">
    <form method="POST" class="form-grid">
      <div class="form-grid form-grid-2">
        <div class="form-group">
          <label>Nom du produit *</label>
          <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom']??'') ?>" placeholder="Ex: MacBook Pro 16 M3" required>
        </div>
        <div class="form-group">
          <label>Catégorie</label>
          <select name="categorie_id">
            <option value="">— Sélectionner —</option>
            <?php foreach ($cats as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($_POST['categorie_id']??'')==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['icone'].' '.$c['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" rows="4" placeholder="Description complète du produit..."><?= htmlspecialchars($_POST['description']??'') ?></textarea>
      </div>
      <div class="form-grid form-grid-2">
        <div class="form-group">
          <label>Prix (FCFA) *</label>
          <input type="number" name="prix" value="<?= htmlspecialchars($_POST['prix']??'') ?>" placeholder="Ex: 850000" min="1" required>
        </div>
        <div class="form-group">
          <label>Stock</label>
          <input type="number" name="stock" value="<?= htmlspecialchars($_POST['stock']??'10') ?>" min="0">
        </div>
      </div>
      <div class="form-group">
        <label>URL de l'image</label>
        <input type="url" name="image_url" id="imgUrl" value="<?= htmlspecialchars($_POST['image_url']??'') ?>" placeholder="https://images.unsplash.com/photo-..." oninput="document.getElementById('imgPrev').src=this.value">
        <div style="margin-top:10px;width:100%;max-height:200px;border-radius:10px;overflow:hidden;background:var(--bg);border:1px solid var(--border)">
          <img id="imgPrev" src="<?= htmlspecialchars($_POST['image_url']??'') ?>" alt="Aperçu" style="width:100%;max-height:200px;object-fit:cover;<?= empty($_POST['image_url'])?'display:none':'' ?>">
        </div>
        <p style="font-size:11px;color:var(--muted);margin-top:6px">💡 Utilisez des images de <a href="https://unsplash.com" target="_blank" style="color:var(--accent)">Unsplash</a> (libres de droits). Format : https://images.unsplash.com/photo-XXXXX?w=800&q=80</p>
      </div>
      <div class="form-grid form-grid-2">
        <div class="form-group">
          <label>Badge (optionnel)</label>
          <select name="badge">
            <option value="">— Aucun badge —</option>
            <?php foreach (['Nouveau','Populaire','Best-seller','Pro','Promo'] as $b): ?>
            <option value="<?= $b ?>" <?= ($_POST['badge']??'')===$b?'selected':'' ?>><?= $b ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:4px">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer;text-transform:none;font-size:14px;font-weight:500;letter-spacing:0">
            <input type="checkbox" name="vedette" value="1" <?= !empty($_POST['vedette'])?'checked':'' ?> style="width:18px;height:18px;border-radius:5px;cursor:pointer;accent-color:var(--accent)">
            ⭐ Mettre en vedette (page d'accueil)
          </label>
        </div>
      </div>
      <div style="display:flex;gap:10px;padding-top:8px;border-top:1px solid var(--border)">
        <button type="submit" class="btn btn-primary">✅ Enregistrer le produit</button>
        <a href="produits.php" class="btn btn-secondary">Annuler</a>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById('imgUrl').addEventListener('input', function() {
  var img = document.getElementById('imgPrev');
  img.style.display = this.value ? 'block' : 'none';
  img.src = this.value;
});
</script>

</main></body></html>
