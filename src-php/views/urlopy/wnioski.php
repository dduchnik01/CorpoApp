<?php
// views/urlopy/wnioski.php
$pageTitle = 'Wnioski urlopowe';
ob_start();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
    <div>
        <h1 style="margin-bottom:4px;">Wnioski urlopowe</h1>
        <p style="color:#94a3b8;">Zarządzanie urlopami pracowników</p>
    </div>
    <a href="/urlopy/dodaj" style="background:#3b82f6; color:white; padding:12px 20px; border-radius:12px; text-decoration:none; font-weight:600;">+ Nowy wniosek</a>
</div>

<?php if (empty($wnioski)): ?>
    <div class="card" style="text-align:center; color:#94a3b8; padding:40px;">Brak wniosków urlopowych.</div>
<?php else: ?>
<div style="overflow-x:auto;">
<table class="tabela">
    <thead>
        <tr>
            <th>Pracownik</th>
            <th>Od</th>
            <th>Do</th>
            <th>Dni</th>
            <th>Uwagi</th>
            <th>Status</th>
            <th>Akcje</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($wnioski as $w): ?>
        <tr>
            <td><strong><?= htmlspecialchars($w['pracownik']) ?></strong></td>
            <td><?= htmlspecialchars($w['data_od']) ?></td>
            <td><?= htmlspecialchars($w['data_do']) ?></td>
            <td style="text-align:center;"><?= $w['liczba_dni'] ?></td>
            <td style="color:#94a3b8;"><?= htmlspecialchars($w['uwagi'] ?? '—') ?></td>
            <td>
                <span class="badge <?= $w['status'] === 'zatwierdzony' ? 'badge-green' : ($w['status'] === 'odrzucony' ? 'badge-red' : 'badge-yellow') ?>">
                    <?= htmlspecialchars($w['status']) ?>
                </span>
            </td>
            <td>
                <div style="display:flex; gap:6px;">
                    <?php if ($w['status'] === 'oczekujacy'): ?>
                    <form method="POST" action="/urlopy/status/<?= $w['id'] ?>">
                        <input type="hidden" name="status" value="zatwierdzony">
                        <button type="submit" class="btn-akcja btn-info">✅ Zatwierdź</button>
                    </form>
                    <form method="POST" action="/urlopy/status/<?= $w['id'] ?>">
                        <input type="hidden" name="status" value="odrzucony">
                        <button type="submit" class="btn-akcja btn-usun">❌ Odrzuć</button>
                    </form>
                    <?php else: ?>
                    <form method="POST" action="/urlopy/status/<?= $w['id'] ?>">
                        <input type="hidden" name="status" value="oczekujacy">
                        <button type="submit" class="btn-akcja" style="background:#334155;">↩️ Resetuj</button>
                    </form>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
