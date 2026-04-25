<?php
// views/dzialy/lista.php
$pageTitle = 'Działy i stanowiska';
ob_start();
?>

<h1 style="margin-bottom:30px;">Działy i stanowiska</h1>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:30px;">

<!-- DZIAŁY -->
<div>
    <h2 style="margin-bottom:16px;">🏢 Działy</h2>

    <!-- Formularz dodaj dział -->
    <form method="POST" action="/dzialy/dodaj" class="formularz" style="margin-bottom:20px;">
        <div class="form-group">
            <label>Nazwa działu *</label>
            <input type="text" name="nazwa" required placeholder="np. Marketing">
        </div>
        <div class="form-group">
            <label>Lokalizacja</label>
            <input type="text" name="lokalizacja" placeholder="np. Warszawa">
        </div>
        <button type="submit" style="width:100%;">+ Dodaj dział</button>
    </form>

    <!-- Lista działów -->
    <?php foreach ($dzialy as $d): ?>
    <div style="background:#1e293b; border-radius:12px; padding:16px; margin-bottom:10px;
                display:flex; justify-content:space-between; align-items:center;">
        <div>
            <strong><?= htmlspecialchars($d['nazwa']) ?></strong>
            <?php if ($d['lokalizacja']): ?>
                <span style="color:#94a3b8; margin-left:8px;">📍 <?= htmlspecialchars($d['lokalizacja']) ?></span>
            <?php endif; ?>
        </div>
        <form method="POST" action="/dzialy/usun/<?= $d['id'] ?>"
              onsubmit="return confirm('Usunąć dział <?= htmlspecialchars(addslashes($d['nazwa'])) ?>?');">
            <button type="submit" class="btn-akcja btn-usun">🗑️</button>
        </form>
    </div>
    <?php endforeach; ?>
</div>

<!-- STANOWISKA -->
<div>
    <h2 style="margin-bottom:16px;">💼 Stanowiska</h2>

    <!-- Formularz dodaj stanowisko -->
    <form method="POST" action="/stanowiska/dodaj" class="formularz" style="margin-bottom:20px;">
        <div class="form-group">
            <label>Nazwa stanowiska *</label>
            <input type="text" name="nazwa" required placeholder="np. Analityk Danych">
        </div>
        <!--<div class="form-row">
            <div class="form-group">
                <label>Stawka min (zł)</label>
                <input type="number" name="stawka_min" min="0" step="100" placeholder="5000">
            </div>
            <div class="form-group">
                <label>Stawka max (zł)</label>
                <input type="number" name="stawka_max" min="0" step="100" placeholder="10000">
            </div>
        </div> -->
        <button type="submit" style="width:100%;">+ Dodaj stanowisko</button>
    </form>

    <!-- Lista stanowisk -->
    <?php foreach ($stanowiska as $s): ?>
    <div style="background:#1e293b; border-radius:12px; padding:16px; margin-bottom:10px;
                display:flex; justify-content:space-between; align-items:center;">
        <div>
            <strong><?= htmlspecialchars($s['nazwa']) ?></strong>
            <!--<span style="color:#34d399; margin-left:8px; font-size:0.85rem;"> -->
             <!--   <?= number_format($s['stawka_min'],0,',',' ') ?>–<?= number_format($s['stawka_max'],0,',',' ') ?> zł -->
            <!--</span> -->
        </div>
        <form method="POST" action="/stanowiska/usun/<?= $s['id'] ?>"
              onsubmit="return confirm('Usunąć stanowisko <?= htmlspecialchars(addslashes($s['nazwa'])) ?>?');">
            <button type="submit" class="btn-akcja btn-usun">🗑️</button>
        </form>
    </div>
    <?php endforeach; ?>
</div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
