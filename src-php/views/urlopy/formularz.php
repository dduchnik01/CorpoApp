<?php
// views/urlopy/formularz.php
$pageTitle = 'Nowy wniosek urlopowy';
ob_start();
?>

<div style="max-width:500px;">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:30px;">
        <a href="/urlopy" style="color:#94a3b8; text-decoration:none;">← Wróć</a>
        <h1>Nowy wniosek urlopowy</h1>
    </div>

    <form method="POST" action="/urlopy/zapisz" class="formularz">

        <div class="form-group">
            <label for="id_pracownika">Pracownik *</label>
            <select id="id_pracownika" name="id_pracownika" required
                    style="width:100%; padding:12px; border-radius:12px; border:none; background:#1e293b; color:white;">
                <option value="">— wybierz pracownika —</option>
                <?php foreach ($pracownicy as $p): ?>
                    <option value="<?= $p['id'] ?>" <?= (int)($_POST['id_pracownika'] ?? 0) === (int)$p['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['imie'] . ' ' . $p['nazwisko']) ?> (<?= htmlspecialchars($p['dzial']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="data_od">Data od *</label>
                <input type="date" id="data_od" name="data_od" required
                       class="date-picker" value="<?= htmlspecialchars($_POST['data_od'] ?? date('Y-m-d')) ?>">
            </div>
            <div class="form-group">
                <label for="data_do">Data do *</label>
                <input type="date" id="data_do" name="data_do" required
                       class="date-picker" value="<?= htmlspecialchars($_POST['data_do'] ?? date('Y-m-d')) ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="uwagi">Uwagi</label>
            <input type="text" id="uwagi" name="uwagi"
                   placeholder="Powód urlopu (opcjonalnie)"
                   value="<?= htmlspecialchars($_POST['uwagi'] ?? '') ?>">
        </div>

        <div style="display:flex; gap:12px; margin-top:24px;">
            <button type="submit" style="flex:1;">📅 Złóż wniosek</button>
            <a href="/urlopy" style="flex:1; text-align:center; padding:10px; border-radius:12px; background:#334155; color:white; text-decoration:none;">Anuluj</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
