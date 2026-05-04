<?php
define('ROOT', dirname(__DIR__));
$pageTitle = 'Messages reçus';
require_once __DIR__ . '/layout.php';

$db = getDB();

// Marquer comme lu
if (!empty($_GET['lu']) && is_numeric($_GET['lu'])) {
    $db->prepare("UPDATE contacts SET lu=1 WHERE id=?")->execute([(int)$_GET['lu']]);
    header('Location: contacts.php'); exit;
}
// Supprimer
if (!empty($_GET['del']) && is_numeric($_GET['del'])) {
    $db->prepare("DELETE FROM contacts WHERE id=?")->execute([(int)$_GET['del']]);
    header('Location: contacts.php?ok=1'); exit;
}
// Tout marquer lu
if (!empty($_GET['all_lu'])) {
    $db->exec("UPDATE contacts SET lu=1");
    header('Location: contacts.php'); exit;
}

$msgs = $db->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
$non_lus = array_filter($msgs, fn($m) => !$m['lu']);
?>

<?php if (!empty($_GET['ok'])): ?><div class="alert alert-success">✅ Message supprimé.</div><?php endif; ?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
  <div style="font-size:13px;color:var(--muted)">
    <?= count($msgs) ?> message(s) ·
    <strong style="color:var(--red)"><?= count($non_lus) ?> non lu(s)</strong>
  </div>
  <?php if ($non_lus): ?>
  <a href="?all_lu=1" class="btn btn-secondary btn-sm">✅ Tout marquer lu</a>
  <?php endif; ?>
</div>

<div class="admin-card">
  <table>
    <thead>
      <tr>
        <th style="width:28px"></th>
        <th>Expéditeur</th>
        <th>Sujet</th>
        <th>Message</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($msgs as $m): ?>
    <tr style="<?= !$m['lu'] ? 'background:#f0f4ff;font-weight:500' : '' ?>">
      <td>
        <?php if (!$m['lu']): ?>
        <div style="width:8px;height:8px;border-radius:50%;background:var(--accent);box-shadow:0 0 6px var(--accent)"></div>
        <?php endif; ?>
      </td>
      <td>
        <div style="font-weight:600;font-size:13px"><?= htmlspecialchars($m['nom']) ?></div>
        <a href="mailto:<?= htmlspecialchars($m['email']) ?>" style="font-size:11px;color:var(--accent)"><?= htmlspecialchars($m['email']) ?></a>
      </td>
      <td style="font-size:13px;max-width:160px">
        <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($m['sujet'] ?: '(sans sujet)') ?></div>
      </td>
      <td style="max-width:260px">
        <div style="font-size:12px;color:var(--muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars(mb_substr($m['message'],0,80)) ?><?= mb_strlen($m['message'])>80?'…':'' ?></div>
        <?php if (mb_strlen($m['message']) > 10): ?>
        <details style="margin-top:4px">
          <summary style="font-size:11px;color:var(--accent);cursor:pointer;list-style:none">Lire tout ▾</summary>
          <div style="font-size:12px;color:var(--text);margin-top:6px;padding:10px;background:var(--bg);border-radius:8px;line-height:1.7;white-space:pre-wrap"><?= htmlspecialchars($m['message']) ?></div>
        </details>
        <?php endif; ?>
      </td>
      <td style="font-size:12px;color:var(--muted);white-space:nowrap"><?= date('d/m/Y H:i', strtotime($m['created_at'])) ?></td>
      <td>
        <div style="display:flex;gap:5px">
          <?php if (!$m['lu']): ?>
          <a href="?lu=<?= $m['id'] ?>" class="btn btn-secondary btn-sm" title="Marquer lu">✓</a>
          <?php endif; ?>
          <a href="mailto:<?= htmlspecialchars($m['email']) ?>?subject=Re:+<?= urlencode($m['sujet']??'') ?>" class="btn btn-secondary btn-sm">📧</a>
          <a href="?del=<?= $m['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce message ?')">🗑️</a>
        </div>
      </td>
    </tr>
    <?php endforeach; ?>
    <?php if (!$msgs): ?>
    <tr><td colspan="6" style="text-align:center;padding:48px;color:var(--muted)">
      <div style="font-size:40px;margin-bottom:12px">📭</div>
      Aucun message reçu pour l'instant.
    </td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>

</main></body></html>
