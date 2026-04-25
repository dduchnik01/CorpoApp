<?php
// views/dashboard.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/PracownikModel.php';

$model      = new PracownikModel();
$statystyki = $model->statystyki();
$perDzial   = $model->perDzial();
$pageTitle  = 'Dashboard';

ob_start();
?>

<h1 style="margin-bottom:8px;">Dashboard</h1>
<p style="color:#94a3b8; margin-bottom:30px;">System Zarządzania Pracownikami — CorpoApp</p>

<!-- Karty statystyk -->
<div class="grid">
    <div class="card">
        <div style="font-size:2.5rem; font-weight:800; color:#38bdf8;"><?= (int)$statystyki['aktywni'] ?></div>
        <div style="color:#94a3b8; margin-top:8px;">Aktywnych pracowników</div>
    </div>
    <div class="card">
        <div style="font-size:2.5rem; font-weight:800; color:#34d399;"><?= (int)$statystyki['liczba_dzialow'] ?></div>
        <div style="color:#94a3b8; margin-top:8px;">Działów</div>
    </div>
    <div class="card">
        <div style="font-size:2.5rem; font-weight:800; color:#facc15;"><?= number_format((float)$statystyki['srednie_wynagrodzenie'], 0, ',', ' ') ?> zł</div>
        <div style="color:#94a3b8; margin-top:8px;">Średnie wynagrodzenie</div>
    </div>
</div>

<!-- Pracownicy per dział -->
<h2 style="margin:40px 0 16px;">Pracownicy wg działów</h2>
<div style="display:flex; flex-direction:column; gap:12px;">
    <?php foreach ($perDzial as $d): 
        $max = max(array_column($perDzial, 'liczba'));
        $pct = $max > 0 ? ($d['liczba'] / $max * 100) : 0;
    ?>
    <div style="background:#1e293b; border-radius:12px; padding:16px 20px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
            <span><?= htmlspecialchars($d['nazwa']) ?></span>
            <span style="color:#38bdf8; font-weight:700;"><?= $d['liczba'] ?> os.</span>
        </div>
        <div style="background:#0f172a; border-radius:99px; height:8px;">
            <div style="background:#38bdf8; width:<?= $pct ?>%; height:8px; border-radius:99px; transition:.3s;"></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div style="margin-top:30px; display:flex; gap:12px; flex-wrap:wrap;">
    <a href="/pracownicy" style="background:#3b82f6; color:white; padding:12px 24px; border-radius:12px; text-decoration:none; font-weight:600;">👥 Zarządzaj pracownikami</a>
    <a href="/urlopy" style="background:#10b981; color:white; padding:12px 24px; border-radius:12px; text-decoration:none; font-weight:600;">📅 Wnioski urlopowe</a>
    <a href="/dzialy" style="background:#8b5cf6; color:white; padding:12px 24px; border-radius:12px; text-decoration:none; font-weight:600;">🏢 Działy i stanowiska</a>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
