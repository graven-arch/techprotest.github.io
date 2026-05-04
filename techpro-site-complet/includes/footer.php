<?php // includes/footer.php ?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="logo-text">Tech<span class="dot">Pro</span>.tg</div>
        <p>Votre partenaire de confiance pour les équipements technologiques haut de gamme au Togo. Authenticité garantie, service premium.</p>
        <div style="margin-top:16px;display:flex;gap:10px">
          <a href="#" style="width:34px;height:34px;background:rgba(255,255,255,0.08);border-radius:8px;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.7);font-size:14px;transition:background .2s" onmouseover="this.style.background='rgba(0,180,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">f</a>
          <a href="#" style="width:34px;height:34px;background:rgba(255,255,255,0.08);border-radius:8px;display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,0.7);font-size:14px;transition:background .2s" onmouseover="this.style.background='rgba(0,180,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">in</a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Produits</h4>
        <?php foreach ($navCats as $c): ?>
        <a href="<?= APP_URL ?>/produits.php?categorie=<?= urlencode($c['slug']) ?>"><?= htmlspecialchars($c['nom']) ?></a>
        <?php endforeach; ?>
      </div>
      <div class="footer-col">
        <h4>TechPro</h4>
        <a href="<?= APP_URL ?>/a-propos.php">À propos</a>
        <a href="<?= APP_URL ?>/contact.php">Contact</a>
        <a href="#">Livraison</a>
        <a href="#">Garantie</a>
      </div>
      <div class="footer-col">
        <h4>Contact</h4>
        <a href="mailto:contact@techpro.tg">contact@techpro.tg</a>
        <a href="tel:+22890000000">+228 90 00 00 00</a>
        <a href="#">Lomé, Togo</a>
        <a href="#">Lun–Sam 8h–18h</a>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; <?= date('Y') ?> TechPro.tg — Tous droits réservés</span>
      <a href="<?= ADMIN_URL ?>/login.php">Espace Admin</a>
    </div>
  </div>
</footer>
</body>
</html>
