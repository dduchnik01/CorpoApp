<?php
// views/pracownicy/lista.php
// Zmienne dostępne z kontrolera: $pracownicy, $dzialy, $statystyki
$pageTitle = 'Pracownicy';
ob_start();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
    <div>
        <h1 style="margin-bottom:4px;">Pracownicy</h1>
        <p style="color:#94a3b8;">Zarządzanie personelem firmy</p>
    </div>
    <a href="/pracownicy/dodaj" style="background:#3b82f6; color:white; padding:12px 20px; border-radius:12px; text-decoration:none; font-weight:600;">+ Dodaj pracownika</a>
</div>

<!-- Filtrowanie -->
<form method="GET" action="/pracownicy" style="display:flex; gap:12px; margin-bottom:24px; flex-wrap:wrap;">
    <input type="text" name="szukaj" value="<?= htmlspecialchars($_GET['szukaj'] ?? '') ?>"
           placeholder="Szukaj (imię, nazwisko, email)..." style="flex:1; min-width:200px;">
    <select name="dzial" style="padding:12px; border-radius:12px; border:none; background:#1e293b; color:white;">
        <option value="0">Wszystkie działy</option>
        <?php foreach ($dzialy as $d): ?>
            <option value="<?= $d['id'] ?>" <?= (int)($_GET['dzial'] ?? 0) === (int)$d['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($d['nazwa']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">🔍 Szukaj</button>
    <?php if (!empty($_GET['szukaj']) || !empty($_GET['dzial'])): ?>
        <a href="/pracownicy" style="padding:12px; border-radius:12px; background:#334155; color:white; text-decoration:none;">✕ Wyczyść</a>
    <?php endif; ?>
</form>

<!-- Tabela pracowników -->
<?php if (empty($pracownicy)): ?>
    <div class="card" style="text-align:center; color:#94a3b8; padding:40px;">
        Brak pracowników spełniających kryteria wyszukiwania.
    </div>
<?php else: ?>
<div style="overflow-x:auto;">
<table class="tabela">
    <thead>
        <tr>
            <th>Pracownik</th>
            <th>Email</th>
            <th>Dział</th>
            <th>Stanowisko</th>
            <th>Wynagrodzenie</th>
            <th>Data zatrudnienia</th>
            <th>Akcje</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pracownicy as $p): ?>
        <tr>
            <td>
                <strong><?= htmlspecialchars($p['imie'] . ' ' . $p['nazwisko']) ?></strong>
                <?php if ($p['telefon']): ?>
                    <br><small style="color:#94a3b8;"><?= htmlspecialchars($p['telefon']) ?></small>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($p['email']) ?></td>
            <td><span class="badge badge-blue"><?= htmlspecialchars($p['dzial']) ?></span></td>
            <td><?= htmlspecialchars($p['stanowisko']) ?></td>
            <td style="font-weight:600; color:#34d399;"><?= number_format((float)$p['wynagrodzenie'], 0, ',', ' ') ?> zł</td>
            <td><?= htmlspecialchars($p['data_zatrudnienia']) ?></td>
            <td>
                <div style="display:flex; gap:6px; flex-wrap:wrap;">
                    <a href="/pracownicy/edytuj/<?= $p['id'] ?>" class="btn-akcja btn-edytuj">✏️ Edytuj</a>
                    <a href="/urlopy/kalendarz/<?= $p['id'] ?>" class="btn-akcja btn-info">📅 Obecność</a>
                    <form method="POST" action="/pracownicy/usun/<?= $p['id'] ?>"
                          onsubmit="return confirm('Usunąć pracownika <?= htmlspecialchars(addslashes($p['imie'] . ' ' . $p['nazwisko'])) ?>?');">
                        <button type="submit" class="btn-akcja btn-usun">🗑️ Usuń</button>
                    </form>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<p style="color:#94a3b8; margin-top:12px; font-size:0.85rem;">Znaleziono: <?= count($pracownicy) ?> pracowników</p>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
