<?php
define('ROOT', dirname(__DIR__));
$pageTitle = 'Modifier un produit';
require_once __DIR__ . '/layout.php';

$db   = getDB();
$id   = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: produits.php'); exit; }

$p = $db->prepare("SELECT * FROM produits WHERE id=?");
$p->execute([$id]); $prod = $p->fetch();
if (!$prod) { header('Location: produits.php'); exit; }

$cats   = $db->query("SELECT * FROM categories ORDER BY nom")->fetchAll();
$errors = []; $ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom   = trim($_POST['nom']         ?? '');
    $desc  = trim($_POST['description'] ?? '');
    $prix  = (int)($_POST['prix']       ?? 0);
    $cat   = (int)($_POST['categorie_id'] ?? 0);
    $img   = trim($_POST['image_url']   ?? '');
    $stock = (int)($_POST['stock']      ?? 0);
    $ved   = !empty($_POST['vedette'])  ? 1 : 0;
    $badge = trim($_POST['badge']       ?? '');

    if (!$nom)    $errors[] = 'Le nom est requis.';
    if ($prix<=0) $errors[] = 'Prix invalide.';

    if (!$errors) {
        $stmt = $db->prepare("UPDATE produits SET nom=?,description=?,prix=?,categorie_id=?,image_url=?,stock=?,vedette=?,badge=? WHERE id=?");
        $stmt->execute([$nom,$desc,$prix,$cat?$cat:null,$img,$stock,$ved,$badge?:null,$id]);
        // Recharger les données
        $p->execute([$id]); $prod = $p->fetch();
        $ok = true;
    }
}
// Pré-remplissage depuis POST ou DB
$v = $_SERVER['REQUEST_METHOD']==='POST' ? $_POST : $prod;
?>

<?php if ($ok): ?><div class="alert alert-success">✅ Produit mis à jour avec succès.</div><?php endif; ?>
<?php if ($errors): ?><div class="alert alert-error"><?= implode(' · ', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>

<div class="admin-card" style="max-width:800px">
  <div class="admin-card-head">
    <div class="admin-card-title">Modifier : <?= htmlspecialchars(mb_substr($prod['nom'],0,40)) ?></div>
    <a href="produits.php" class="btn btn-secondary btn-sm">← Retour</a>
  </div>
  <div style="padding:28px">
    <form method="POST" class="form-grid">
      <div class="form-grid form-grid-2">
        <div class="form-group">
          <label>Nom du produit *</label>
          <input type="text" name="nom" value="<?= htmlspecialchars($v['nom']??'') ?>" required>
        </div>
        <div class="form-group">
          <label>Catégorie</label>
          <select name="categorie_id">
            <option value="">— Aucune —</option>
            <?php foreach ($cats as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($v['categorie_id']??'')==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['icone'].' '.$c['nom']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" rows="4"><?= htmlspecialchars($v['description']??'') ?></textarea>
      </div>
      <div class="form-grid form-grid-2">
        <div class="form-group">
          <label>Prix (FCFA) *</label>
          <input type="number" name="prix" value="<?= htmlspecialchars($v['prix']??'') ?>" min="1" required>
        </div>
        <div class="form-group">
          <label>Stock</label>
          <input type="number" name="stock" value="<?= htmlspecialchars($v['stock']??'0') ?>" min="0">
        </div>
      </div>
      <div class="form-group">
        <label>URL de l'image</label>
        <input type="url" name="image_url" id="imgUrl" value="<?= htmlspecialchars($v['image_url']??'') ?>" oninput="pr()">
        <div style="margin-top:10px;border-radius:10px;overflow:hidden;background:var(--bg);border:1px solid var(--border)">
          <img id="imgPrev" src="<?= htmlspecialchars($v['image_url']??'') ?>" alt="Aperçu" style="width:100%;max-height:200px;object-fit:cover">
        </div>
      </div>
      <div class="form-grid form-grid-2">
        <div class="form-group">
          <label>Badge</label>
          <select name="badge">
            <option value="">— Aucun —</option>
            <?php foreach (['Nouveau','Populaire','Best-seller','Pro','Promo'] as $b): ?>
            <option value="<?= $b ?>" <?= ($v['badge']??'')===$b?'selected':'' ?>><?= $b ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:4px">
          <label style="display:flex;align-items:center;gap:10px;cursor:pointer;text-transform:none;font-size:14px;font-weight:500;letter-spacing:0">
            <input type="checkbox" name="vedette" value="1" <?= !empty($v['vedette'])?'checked':'' ?> style="width:18px;height:18px;accent-color:var(--accent)">
            ⭐ En vedette
          </label>
        </div>
      </div>
      <div style="display:flex;gap:10px;padding-top:8px;border-top:1px solid var(--border)">
        <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
        <a href="produits.php" class="btn btn-secondary">Annuler</a>
        <a href="<?= APP_URL ?>/produit.php?id=<?= $id ?>" target="_blank" class="btn btn-secondary" style="margin-left:auto">👁 Voir sur le site</a>
      </div>
    </form>
  </div>
</div>
<script>function pr(){var i=document.getElementById('imgPrev');i.src=document.getElementById('imgUrl').value}</script>

</main></body></html>
