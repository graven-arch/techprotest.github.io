<?php
define('ROOT', __DIR__);
$pageTitle = 'Accueil';
require_once ROOT . '/includes/header.php';

$db = getDB();
$featured  = $db->query("SELECT p.*, c.nom AS cat_nom FROM produits p LEFT JOIN categories c ON p.categorie_id=c.id WHERE p.vedette=1 ORDER BY p.id DESC LIMIT 6")->fetchAll();
$categories = $db->query("SELECT c.*, COUNT(p.id) AS nb FROM categories c LEFT JOIN produits p ON p.categorie_id=c.id GROUP BY c.id ORDER BY nb DESC")->fetchAll();
$nouveaux  = $db->query("SELECT p.*, c.nom AS cat_nom FROM produits p LEFT JOIN categories c ON p.categorie_id=c.id ORDER BY p.created_at DESC LIMIT 4")->fetchAll();

function formatPrice($p) { return number_format($p, 0, ',', ' ') . ' FCFA'; }
?>

<!-- ══ HERO ══ -->
<section style="position:relative;background:var(--dark);min-height:90vh;display:flex;align-items:center;overflow:hidden">
  <div style="position:absolute;inset:0">
    <img src="https://images.unsplash.com/photo-1518770660439-4636190af475?w=1600&q=85"
         alt="TechPro hero" style="width:100%;height:100%;object-fit:cover;opacity:0.35">
    <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(6,8,16,0.95) 40%,rgba(0,87,255,0.2))"></div>
  </div>

  <!-- Grille décorative -->
  <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(0,200,255,0.04) 1px,transparent 1px),linear-gradient(90deg,rgba(0,200,255,0.04) 1px,transparent 1px);background-size:60px 60px;pointer-events:none"></div>

  <div class="container" style="position:relative;z-index:1;padding-top:80px;padding-bottom:80px">
    <div style="max-width:680px">
      <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(0,180,255,0.12);border:1px solid rgba(0,180,255,0.2);border-radius:20px;padding:6px 16px;margin-bottom:24px">
        <span style="width:6px;height:6px;border-radius:50%;background:#00c8ff;box-shadow:0 0 8px #00c8ff;display:inline-block"></span>
        <span style="font-size:12px;color:#00c8ff;letter-spacing:1.5px;text-transform:uppercase">Équipements Premium</span>
      </div>
      <h1 style="font-family:'Syne',sans-serif;font-size:clamp(36px,6vw,72px);font-weight:800;color:#fff;line-height:1.05;letter-spacing:-2px;margin-bottom:24px">
        La Technologie<br><span style="background:linear-gradient(90deg,#0057ff,#00c8ff);-webkit-background-clip:text;-webkit-text-fill-color:transparent">du Futur</span>, Aujourd'hui.
      </h1>
      <p style="font-size:18px;color:rgba(255,255,255,0.65);max-width:500px;margin-bottom:36px;line-height:1.7">
        Découvrez notre sélection d'équipements technologiques haut de gamme. Authenticité garantie, livraison partout au Togo.
      </p>
      <div style="display:flex;gap:14px;flex-wrap:wrap">
        <a href="produits.php" style="background:linear-gradient(135deg,#0057ff,#00b4ff);color:#fff;padding:14px 32px;border-radius:10px;font-weight:700;font-size:15px;transition:opacity .2s,transform .2s" onmouseover="this.style.opacity='.88';this.style.transform='translateY(-2px)'" onmouseout="this.style.opacity='1';this.style.transform='none'">
          Explorer les produits →
        </a>
        <a href="contact.php" style="background:rgba(255,255,255,0.08);color:#fff;border:1px solid rgba(255,255,255,0.15);padding:14px 32px;border-radius:10px;font-weight:600;font-size:15px;transition:background .2s" onmouseover="this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">
          Nous contacter
        </a>
      </div>

      <!-- Stats -->
      <div style="display:flex;gap:40px;margin-top:56px;padding-top:40px;border-top:1px solid rgba(255,255,255,0.08)">
        <?php foreach ([['500+','Produits en stock'],['5 ans','D\'expertise'],['1000+','Clients satisfaits']] as [$v,$l]): ?>
        <div>
          <div style="font-family:'Syne',sans-serif;font-size:28px;font-weight:800;color:#fff"><?= $v ?></div>
          <div style="font-size:12px;color:rgba(255,255,255,0.45);margin-top:2px"><?= $l ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Image produit flottante -->
  <div style="position:absolute;right:6%;bottom:0;width:420px;z-index:1;pointer-events:none;display:none" id="heroImg">
    <img src="https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=600&q=80" alt="" style="border-radius:20px 20px 0 0;box-shadow:0 -20px 80px rgba(0,87,255,0.3)">
  </div>
</section>
<script>if(window.innerWidth>1100)document.getElementById('heroImg').style.display='block'</script>

<!-- ══ CATÉGORIES ══ -->
<section style="padding:80px 0;background:var(--bg2)">
  <div class="container">
    <div class="section-header">
      <span class="section-eyebrow">Parcourir</span>
      <h2 class="section-title">Nos Catégories</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px">
      <?php foreach ($categories as $cat): ?>
      <a href="produits.php?categorie=<?= urlencode($cat['slug']) ?>"
         style="background:#fff;border:1px solid var(--border);border-radius:14px;padding:24px 16px;text-align:center;transition:all .2s;display:block"
         onmouseover="this.style.borderColor='var(--accent)';this.style.boxShadow='0 8px 24px rgba(0,87,255,0.1)';this.style.transform='translateY(-3px)'"
         onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow='none';this.style.transform='none'">
        <div style="font-size:32px;margin-bottom:10px"><?= $cat['icone'] ?></div>
        <div style="font-family:'Syne',sans-serif;font-weight:700;font-size:14px"><?= htmlspecialchars($cat['nom']) ?></div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px"><?= $cat['nb'] ?> produit<?= $cat['nb']>1?'s':'' ?></div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ PRODUITS VEDETTES ══ -->
<section style="padding:80px 0">
  <div class="container">
    <div class="section-header">
      <span class="section-eyebrow">Sélection</span>
      <h2 class="section-title">Produits Phares</h2>
      <p class="section-sub">Notre sélection des meilleurs équipements du moment</p>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px">
      <?php foreach ($featured as $p): ?>
      <a href="produit.php?id=<?= $p['id'] ?>" class="product-card">
        <div class="product-img-wrap">
          <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>" loading="lazy">
          <?php if ($p['badge']): ?>
          <span class="product-badge <?= $p['badge']==='Best-seller'?'gold':($p['badge']==='Nouveau'?'':'') ?>"><?= htmlspecialchars($p['badge']) ?></span>
          <?php endif; ?>
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
    <div style="text-align:center;margin-top:40px">
      <a href="produits.php" style="border:2px solid var(--accent);color:var(--accent);padding:12px 32px;border-radius:10px;font-weight:700;font-size:15px;transition:all .2s;display:inline-block" onmouseover="this.style.background='var(--accent)';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='var(--accent)'">
        Voir tous les produits
      </a>
    </div>
  </div>
</section>

<!-- ══ BANNIÈRE INTERMÉDIAIRE ══ -->
<section style="margin:0;background:linear-gradient(135deg,var(--dark) 0%,#001a66 100%);position:relative;overflow:hidden;padding:80px 0">
  <div style="position:absolute;inset:0;opacity:0.15">
    <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1400&q=80" alt="" style="width:100%;height:100%;object-fit:cover">
  </div>
  <div class="container" style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:24px">
    <div>
      <h2 style="font-family:'Syne',sans-serif;font-size:clamp(24px,4vw,40px);font-weight:800;color:#fff;letter-spacing:-1px;margin-bottom:12px">
        Audio Premium.<br><span style="color:#00c8ff">Un son qui transcende.</span>
      </h2>
      <p style="color:rgba(255,255,255,0.6);max-width:400px">
        Casques, écouteurs et enceintes des plus grandes marques. Vivez la musique autrement.
      </p>
    </div>
    <a href="produits.php?categorie=audio" style="background:#fff;color:var(--dark);padding:14px 32px;border-radius:10px;font-weight:700;font-size:15px;white-space:nowrap;transition:opacity .2s" onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
      Voir l'audio →
    </a>
  </div>
</section>

<!-- ══ NOUVEAUTÉS ══ -->
<section style="padding:80px 0;background:var(--bg2)">
  <div class="container">
    <div class="section-header">
      <span class="section-eyebrow">Fraîchement arrivés</span>
      <h2 class="section-title">Dernières Nouveautés</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:20px">
      <?php foreach ($nouveaux as $p): ?>
      <a href="produit.php?id=<?= $p['id'] ?>" class="product-card">
        <div class="product-img-wrap" style="aspect-ratio:3/2">
          <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>" loading="lazy">
          <?php if ($p['badge']): ?><span class="product-badge"><?= htmlspecialchars($p['badge']) ?></span><?php endif; ?>
        </div>
        <div class="product-body">
          <div class="product-cat"><?= htmlspecialchars($p['cat_nom']) ?></div>
          <div class="product-name" style="font-size:15px"><?= htmlspecialchars($p['nom']) ?></div>
          <div class="product-footer" style="margin-top:10px">
            <div class="product-price" style="font-size:16px"><?= formatPrice($p['prix']) ?></div>
            <span class="btn-detail" style="font-size:12px">Voir</span>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ POURQUOI NOUS ══ -->
<section style="padding:80px 0">
  <div class="container">
    <div class="section-header">
      <span class="section-eyebrow">Notre engagement</span>
      <h2 class="section-title">Pourquoi TechPro.tg ?</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:24px">
      <?php
      $atouts = [
        ['🔒','Authenticité garantie','Tous nos produits sont neufs, scellés et 100% authentiques. Garantie constructeur incluse.'],
        ['🚀','Livraison rapide','Livraison à Lomé sous 24h. Partout au Togo en 48–72h.'],
        ['💬','Support expert','Une équipe technique disponible 6j/7 pour vous conseiller et vous assister.'],
        ['🛡️','Garantie SAV','Service après-vente réactif. Échanges et remboursements simplifiés.'],
      ];
      foreach ($atouts as [$ico,$titre,$desc]):
      ?>
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:16px;padding:28px 24px;transition:box-shadow .2s" onmouseover="this.style.boxShadow='var(--shadow2)'" onmouseout="this.style.boxShadow='none'">
        <div style="font-size:36px;margin-bottom:16px"><?= $ico ?></div>
        <h3 style="font-family:'Syne',sans-serif;font-size:17px;font-weight:700;margin-bottom:8px"><?= $titre ?></h3>
        <p style="font-size:13px;color:var(--muted);line-height:1.7"><?= $desc ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ══ CTA ══ -->
<section style="padding:80px 0;background:var(--dark)">
  <div class="container" style="text-align:center">
    <h2 style="font-family:'Syne',sans-serif;font-size:clamp(28px,5vw,48px);font-weight:800;color:#fff;letter-spacing:-1px;margin-bottom:16px">
      Un projet ? Une question ?
    </h2>
    <p style="color:rgba(255,255,255,0.55);font-size:16px;margin-bottom:32px">
      Notre équipe est à votre disposition pour vous accompagner.
    </p>
    <a href="contact.php" style="background:linear-gradient(135deg,#0057ff,#00b4ff);color:#fff;padding:16px 40px;border-radius:12px;font-weight:700;font-size:16px;display:inline-block;transition:opacity .2s,transform .2s" onmouseover="this.style.opacity='.88';this.style.transform='translateY(-2px)'" onmouseout="this.style.opacity='1';this.style.transform='none'">
      Contactez-nous
    </a>
  </div>
</section>

<?php require_once ROOT . '/includes/footer.php'; ?>
