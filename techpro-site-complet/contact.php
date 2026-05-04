<?php
define('ROOT', __DIR__);
require_once ROOT . '/config.php';
require_once ROOT . '/includes/db.php';

$msg_ok = false;
$errors = [];
$produitPre = htmlspecialchars($_GET['produit'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom     = trim($_POST['nom']     ?? '');
    $email   = trim($_POST['email']   ?? '');
    $sujet   = trim($_POST['sujet']   ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$nom)                      $errors[] = 'Votre nom est requis.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
    if (!$message)                  $errors[] = 'Le message est requis.';

    if (!$errors) {
        try {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO contacts (nom, email, sujet, message) VALUES (?,?,?,?)");
            $stmt->execute([$nom, $email, $sujet, $message]);
            $msg_ok = true;
        } catch (Exception $e) { $errors[] = 'Erreur serveur. Veuillez réessayer.'; }
    }
}

$pageTitle = 'Contact';
require_once ROOT . '/includes/header.php';
?>

<!-- Hero -->
<section style="background:linear-gradient(135deg,var(--dark) 0%,#001440 100%);padding:60px 0;text-align:center">
  <div class="container">
    <h1 style="font-family:'Syne',sans-serif;font-size:clamp(28px,5vw,48px);font-weight:800;color:#fff;letter-spacing:-1px;margin-bottom:12px">
      Contactez-nous
    </h1>
    <p style="color:rgba(255,255,255,0.55);font-size:16px">Notre équipe vous répond sous 24h</p>
  </div>
</section>

<section style="padding:64px 0 80px;background:var(--bg2)">
  <div class="container">
    <div style="display:grid;grid-template-columns:1fr 2fr;gap:40px;align-items:start">

      <!-- Infos contact -->
      <div style="display:flex;flex-direction:column;gap:16px">
        <?php foreach ([
          ['📍','Adresse','Avenue de la Libération, Lomé, Togo'],
          ['📞','Téléphone','+228 90 00 00 00'],
          ['📧','Email','contact@techpro.tg'],
          ['🕐','Horaires','Lun–Sam : 8h00 – 18h00'],
        ] as [$ico,$t,$v]): ?>
        <div style="background:#fff;border:1px solid var(--border);border-radius:14px;padding:20px;display:flex;align-items:flex-start;gap:14px">
          <div style="font-size:24px;flex-shrink:0"><?= $ico ?></div>
          <div>
            <div style="font-weight:600;font-size:14px;margin-bottom:4px"><?= $t ?></div>
            <div style="font-size:13px;color:var(--muted)"><?= $v ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Formulaire -->
      <div style="background:#fff;border:1px solid var(--border);border-radius:16px;padding:36px">
        <?php if ($msg_ok): ?>
        <div style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;padding:20px;text-align:center;margin-bottom:24px">
          <div style="font-size:32px;margin-bottom:8px">✅</div>
          <h3 style="font-family:'Syne',sans-serif;font-size:18px;color:#065f46;margin-bottom:4px">Message envoyé !</h3>
          <p style="color:#047857;font-size:14px">Nous vous répondrons sous 24 heures.</p>
        </div>
        <?php endif; ?>

        <?php if ($errors): ?>
        <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:10px;padding:14px;margin-bottom:20px">
          <?php foreach ($errors as $e): ?><div style="font-size:13px;color:var(--red)">• <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST">
          <?php
          $fields = [
            ['text','nom','Votre nom complet *','Prénom Nom',true],
            ['email','email','Email *','votre@email.com',true],
          ];
          foreach ($fields as [$type,$name,$label,$ph,$req]):
          $val = htmlspecialchars($_POST[$name] ?? '');
          ?>
          <div style="margin-bottom:18px">
            <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);letter-spacing:1px;text-transform:uppercase;margin-bottom:7px"><?= $label ?></label>
            <input type="<?= $type ?>" name="<?= $name ?>" value="<?= $val ?>" placeholder="<?= $ph ?>"
                   style="width:100%;border:1px solid var(--border);border-radius:9px;padding:12px 14px;font-size:14px;font-family:inherit;outline:none;transition:border-color .2s"
                   onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'"
                   <?= $req ? 'required' : '' ?>>
          </div>
          <?php endforeach; ?>

          <div style="margin-bottom:18px">
            <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);letter-spacing:1px;text-transform:uppercase;margin-bottom:7px">Sujet</label>
            <input type="text" name="sujet" value="<?= $produitPre ? 'Demande : '.$produitPre : htmlspecialchars($_POST['sujet'] ?? '') ?>" placeholder="Ex: Demande de devis MacBook Pro..."
                   style="width:100%;border:1px solid var(--border);border-radius:9px;padding:12px 14px;font-size:14px;font-family:inherit;outline:none;transition:border-color .2s"
                   onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'">
          </div>

          <div style="margin-bottom:24px">
            <label style="display:block;font-size:12px;font-weight:600;color:var(--muted);letter-spacing:1px;text-transform:uppercase;margin-bottom:7px">Message *</label>
            <textarea name="message" rows="5" placeholder="Décrivez votre besoin..." required
                      style="width:100%;border:1px solid var(--border);border-radius:9px;padding:12px 14px;font-size:14px;font-family:inherit;outline:none;resize:vertical;transition:border-color .2s"
                      onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--border)'"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
          </div>

          <button type="submit" style="width:100%;background:linear-gradient(135deg,var(--accent),#00b4ff);color:#fff;border:none;border-radius:10px;padding:14px;font-size:15px;font-weight:700;cursor:pointer;font-family:'Syne',sans-serif;transition:opacity .2s" onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
            Envoyer le message →
          </button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once ROOT . '/includes/footer.php'; ?>
