<?php
require_once 'config.php';

// Connexion sans DB d'abord pour la créer
try {
    $pdo = new PDO('mysql:host='.DB_HOST.';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `".DB_NAME."` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `".DB_NAME."`");
} catch (PDOException $e) {
    die('<p style="color:red;font-family:monospace">Erreur MySQL : ' . htmlspecialchars($e->getMessage()) . '<br>Vérifiez que XAMPP MySQL est démarré.</p>');
}

// ── Tables ──
$pdo->exec("
CREATE TABLE IF NOT EXISTS `categories` (
  `id`   INT AUTO_INCREMENT PRIMARY KEY,
  `nom`  VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `icone` VARCHAR(50) DEFAULT '📦'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS `produits` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `nom`         VARCHAR(200) NOT NULL,
  `description` TEXT,
  `prix`        DECIMAL(10,0) NOT NULL DEFAULT 0,
  `categorie_id` INT,
  `image_url`   VARCHAR(500),
  `stock`       INT DEFAULT 10,
  `vedette`     TINYINT(1) DEFAULT 0,
  `badge`       VARCHAR(50) DEFAULT NULL,
  `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`categorie_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS `contacts` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `nom`        VARCHAR(150),
  `email`      VARCHAR(200),
  `sujet`      VARCHAR(200),
  `message`    TEXT,
  `lu`         TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS `bannières` (
  `id`      INT AUTO_INCREMENT PRIMARY KEY,
  `titre`   VARCHAR(200),
  `sous_titre` VARCHAR(300),
  `image_url` VARCHAR(500),
  `lien`    VARCHAR(300),
  `actif`   TINYINT(1) DEFAULT 1,
  `ordre`   INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// ── Données de démo ──
$cats = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
if ($cats == 0) {
    $pdo->exec("INSERT INTO categories (nom, slug, icone) VALUES
      ('Ordinateurs',   'ordinateurs',  '💻'),
      ('Smartphones',   'smartphones',  '📱'),
      ('Audio',         'audio',        '🎧'),
      ('Moniteurs',     'moniteurs',    '🖥️'),
      ('Accessoires',   'accessoires',  '⌨️'),
      ('Caméras',       'cameras',      '📷')
    ");

    // Images Unsplash libres de droit, format fiable
    $produits = [
      // Ordinateurs
      ['MacBook Pro 16" M3 Max', 'Processeur M3 Max, 36 Go RAM unifiée, SSD 1To. L\'outil ultime pour les créatifs et professionnels exigeants.', 2850000, 1, 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&q=80', 8,  1, 'Nouveau'],
      ['Dell XPS 15 OLED',        'Écran OLED 3,5K, Intel Core i9, 32Go RAM, RTX 4060. Performances et élégance réunies.', 1950000, 1, 'https://images.unsplash.com/photo-1593642632559-0c6d3fc62b89?w=800&q=80', 12, 1, 'Populaire'],
      ['ASUS ROG Zephyrus G14',   'Gaming ultra-portable, AMD Ryzen 9, RTX 4070, écran 165Hz. Dominez chaque partie.', 1650000, 1, 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=800&q=80', 6,  0, NULL],
      // Smartphones
      ['iPhone 15 Pro Max',       'Puce A17 Pro, tripe caméra 48MP, Titane, USB-C. La référence absolue du smartphone premium.', 1350000, 2, 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&q=80', 20, 1, 'Nouveau'],
      ['Samsung Galaxy S24 Ultra','S Pen intégré, IA Galaxy, 200MP, 5000mAh. Le smartphone le plus puissant de Samsung.', 1250000, 2, 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=800&q=80', 15, 1, NULL],
      ['Google Pixel 8 Pro',      'IA Google Tensor G3, photos computationnelles, 7 ans de mises à jour garanties.',   950000, 2, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&q=80', 10, 0, NULL],
      // Audio
      ['Sony WH-1000XM5',         'Réduction de bruit leader du secteur, 30h autonomie, son Hi-Res. Le casque référence.',420000, 3, 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=800&q=80', 25, 1, 'Best-seller'],
      ['Apple AirPods Pro 2',     'Puce H2, ANC adaptatif, audio spatial, boîtier USB-C. L\'expérience audio Apple ultime.',380000, 3, 'https://images.unsplash.com/photo-1603351154351-5e2d0600bb77?w=800&q=80', 30, 0, NULL],
      ['Bose QuietComfort 45',    'Confort légendaire, ANC WorldClass, 24h d\'autonomie. Silence total, musique pure.',  350000, 3, 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=800&q=80', 18, 0, NULL],
      // Moniteurs
      ['LG UltraWide 34"',        'WQHD 3440x1440, 144Hz, 1ms, HDR600, compatible Thunderbolt 4. L\'espace de travail parfait.',680000, 4, 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=800&q=80', 7,  1, NULL],
      ['Samsung Odyssey OLED G8', 'OLED 4K, 240Hz, 0,03ms, Quantum Dot. L\'écran gaming ultime sans compromis.',       890000, 4, 'https://images.unsplash.com/photo-1593642634367-d91a135587b5?w=800&q=80', 5,  0, 'Pro'],
      // Accessoires
      ['Logitech MX Keys S',      'Clavier rétroéclairé intelligent, Multi-OS, Bluetooth, frappe silencieuse premium.',   95000, 5, 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=800&q=80', 40, 0, NULL],
      ['Logitech MX Master 3S',   'Souris ergonomique Magspeed, 8000 DPI, molette électromagnétique. Précision absolue.',  85000, 5, 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=800&q=80', 35, 0, NULL],
      // Caméras
      ['Sony Alpha A7 IV',        'Plein format 33MP, AF hybride 759 points, vidéo 4K 60fps. Photographie sans limites.', 1850000, 6, 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&q=80', 4,  1, NULL],
      ['Canon EOS R6 Mark II',    '40fps continus, AF par sujet/yeux, stabilisation 8 stops. Le hybride polyvalent par excellence.',1650000,6,'https://images.unsplash.com/photo-1502920917128-1aa500764cbd?w=800&q=80', 3,  0, NULL],
    ];

    $stmt = $pdo->prepare("INSERT INTO produits (nom, description, prix, categorie_id, image_url, stock, vedette, badge) VALUES (?,?,?,?,?,?,?,?)");
    foreach ($produits as $p) $stmt->execute($p);

    // Bannières hero
    $pdo->exec("INSERT INTO bannières (titre, sous_titre, image_url, lien, ordre) VALUES
      ('La Technologie du Futur, Aujourd''hui', 'Découvrez notre sélection d''équipements haut de gamme livrés partout au Togo.', 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1600&q=85', '/produits.php', 1),
      ('Audio Premium', 'Des sons qui transcendent l''ordinaire.', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=1600&q=85', '/produits.php?categorie=audio', 2)
    ");
}

echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Installation TechPro.tg</title>
<style>
  body{font-family:system-ui,sans-serif;background:#060810;color:#e8edf5;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}
  .box{background:#0f1420;border:1px solid rgba(0,200,255,.15);border-radius:16px;padding:40px;max-width:500px;text-align:center}
  h1{font-size:24px;margin-bottom:8px}span.ok{color:#00e5a0}
  p{color:#8899b0;margin:8px 0}
  a{display:inline-block;margin-top:20px;background:linear-gradient(135deg,#0066ff,#00c8ff);color:#fff;padding:12px 32px;border-radius:10px;text-decoration:none;font-weight:700}
</style></head><body>
<div class="box">
  <h1>✅ Installation <span class="ok">réussie !</span></h1>
  <p>Base de données <strong>techpro_db</strong> créée avec succès.</p>
  <p>15 produits de démonstration ajoutés.</p>
  <a href="' . APP_URL . '/">Visiter le site →</a>
  <br><br>
  <small style="color:#5a6680">Supprimez install.php après l\'installation.</small>
</div></body></html>';
?>
