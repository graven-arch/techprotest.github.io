<?php
define('ROOT', __DIR__);
$pageTitle = 'À propos';
require_once ROOT . '/includes/header.php';
?>

<section style="background:linear-gradient(135deg,var(--dark) 0%,#001440 100%);padding:80px 0;position:relative;overflow:hidden">
  <div style="position:absolute;inset:0;opacity:0.12">
    <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=1400&q=80" alt="" style="width:100%;height:100%;object-fit:cover">
  </div>
  <div class="container" style="position:relative;z-index:1;text-align:center">
    <h1 style="font-family:'Syne',sans-serif;font-size:clamp(32px,6vw,60px);font-weight:800;color:#fff;letter-spacing:-2px;margin-bottom:16px">
      À propos de <span style="color:#00c8ff">TechPro.tg</span>
    </h1>
    <p style="color:rgba(255,255,255,0.6);font-size:18px;max-width:560px;margin:0 auto">
      Votre partenaire technologique de confiance au Togo depuis 2020.
    </p>
  </div>
</section>

<section style="padding:80px 0">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;margin-bottom:80px">
      <div>
        <span class="section-eyebrow">Notre histoire</span>
        <h2 style="font-family:'Syne',sans-serif;font-size:clamp(24px,4vw,36px);font-weight:800;letter-spacing:-0.8px;margin-bottom:20px">
          L'excellence technologique, au service du Togo
        </h2>
        <p style="color:var(--muted);line-height:1.9;margin-bottom:16px">
          Fondée en 2020 à Lomé, TechPro.tg est née d'une conviction simple : chaque professionnel et particulier au Togo mérite d'accéder aux meilleures technologies mondiales, avec la même qualité de service qu'ailleurs.
        </p>
        <p style="color:var(--muted);line-height:1.9">
          Nous sommes distributeurs agréés des plus grandes marques — Apple, Samsung, Sony, Dell, Logitech — et nous garantissons l'authenticité de chaque produit vendu.
        </p>
      </div>
      <div style="border-radius:20px;overflow:hidden;border:1px solid var(--border)">
        <img src="https://images.unsplash.com/photo-1497366811353-6870744d04b2?w=800&q=80" alt="TechPro bureau" style="width:100%;height:350px;object-fit:cover">
      </div>
    </div>

    <!-- Valeurs -->
    <div style="text-align:center;margin-bottom:48px">
      <span class="section-eyebrow">Ce qui nous guide</span>
      <h2 class="section-title">Nos valeurs</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:80px">
      <?php foreach ([
        ['💡','Innovation','Nous sélectionnons les technologies les plus avancées pour vous proposer ce qui se fait de mieux.'],
        ['🤝','Confiance','Authenticité garantie, tarifs transparents, engagement tenu. La confiance est notre fondation.'],
        ['🌍','Local d\'abord','Employer local, livrer local, servir local. Nous sommes fiers d\'être togolais.'],
      ] as [$ico,$t,$d]): ?>
      <div style="background:var(--bg2);border:1px solid var(--border);border-radius:16px;padding:32px;text-align:center">
        <div style="font-size:44px;margin-bottom:16px"><?= $ico ?></div>
        <h3 style="font-family:'Syne',sans-serif;font-size:19px;font-weight:700;margin-bottom:10px"><?= $t ?></h3>
        <p style="color:var(--muted);font-size:14px;line-height:1.8"><?= $d ?></p>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Chiffres -->
    <div style="background:linear-gradient(135deg,var(--dark),#001a66);border-radius:20px;padding:60px;display:grid;grid-template-columns:repeat(4,1fr);gap:20px;text-align:center">
      <?php foreach ([['2020','Année de création'],['500+','Produits référencés'],['1000+','Clients servis'],['100%','Produits authentiques']] as [$v,$l]): ?>
      <div>
        <div style="font-family:'Syne',sans-serif;font-size:40px;font-weight:800;color:#fff;letter-spacing:-1px"><?= $v ?></div>
        <div style="font-size:13px;color:rgba(255,255,255,0.45);margin-top:6px"><?= $l ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section style="padding:80px 0;background:var(--bg2)">
  <div class="container" style="text-align:center">
    <h2 style="font-family:'Syne',sans-serif;font-size:32px;font-weight:800;letter-spacing:-0.8px;margin-bottom:16px">Prêt à équiper votre quotidien ?</h2>
    <p style="color:var(--muted);margin-bottom:32px">Explorez notre catalogue ou contactez notre équipe.</p>
    <div style="display:flex;gap:14px;justify-content:center">
      <a href="produits.php" style="background:var(--accent);color:#fff;padding:14px 32px;border-radius:10px;font-weight:700;font-size:15px;transition:opacity .2s" onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">Voir les produits</a>
      <a href="contact.php" style="border:2px solid var(--border);color:var(--text);padding:14px 32px;border-radius:10px;font-weight:600;font-size:15px;transition:all .2s" onmouseover="this.style.borderColor='var(--accent)';this.style.color='var(--accent)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text)'">Nous contacter</a>
    </div>
  </div>
</section>

<?php require_once ROOT . '/includes/footer.php'; ?>
