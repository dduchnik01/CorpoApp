<?php
// views/pracownicy/edytuj.php
// $pracownik, $dzialy, $stanowiska dostępne z kontrolera
$pageTitle = 'Edytuj pracownika';
ob_start();
?>

<div style="max-width:600px;">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:30px;">
        <a href="/pracownicy" style="color:#94a3b8; text-decoration:none;">← Wróć</a>
        <h1>Edytuj: <?= htmlspecialchars($pracownik['imie'] . ' ' . $pracownik['nazwisko']) ?></h1>
    </div>

    <form method="POST" action="/pracownicy/aktualizuj/<?= $pracownik['id'] ?>" class="formularz">

        <div class="form-row">
            <div class="form-group">
                <label for="imie">Imię *</label>
                <input type="text" id="imie" name="imie" required minlength="2"
                       value="<?= htmlspecialchars($pracownik['imie']) ?>">
            </div>
            <div class="form-group">
                <label for="nazwisko">Nazwisko *</label>
                <input type="text" id="nazwisko" name="nazwisko" required minlength="2"
                       value="<?= htmlspecialchars($pracownik['nazwisko']) ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($pracownik['email']) ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="telefon">Telefon</label>
                <input type="tel" id="telefon" name="telefon"
                       value="<?= htmlspecialchars($pracownik['telefon'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="data_zatrudnienia">Data zatrudnienia *</label>
                <input type="date" id="data_zatrudnienia" name="data_zatrudnienia" required
                       value="<?= htmlspecialchars($pracownik['data_zatrudnienia']) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="id_dzialu">Dział *</label>
                <select id="id_dzialu" name="id_dzialu" required>
                    <?php foreach ($dzialy as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= (int)$pracownik['id_dzialu'] === (int)$d['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['nazwa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_stanowiska">Stanowisko *</label>
                <select id="id_stanowiska" name="id_stanowiska" required>
                    <?php foreach ($stanowiska as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= (int)$pracownik['id_stanowiska'] === (int)$s['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nazwa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="wynagrodzenie">Wynagrodzenie (zł) *</label>
                <input type="number" id="wynagrodzenie" name="wynagrodzenie" required
                       min="3000" step="100" value="<?= htmlspecialchars($pracownik['wynagrodzenie']) ?>">
            </div>
            <div class="form-group">
                <label for="dni_urlopu">Dni urlopu rocznie</label>
                <input type="number" id="dni_urlopu" name="dni_urlopu"
                       min="20" max="40" value="<?= htmlspecialchars($pracownik['dni_urlopu']) ?>">
            </div>
        </div>

        <div style="display:flex; gap:12px; margin-top:24px;">
            <button type="submit" style="flex:1; background:#10b981;">💾 Zapisz zmiany</button>
            <a href="/pracownicy" style="flex:1; text-align:center; padding:10px; border-radius:12px; background:#334155; color:white; text-decoration:none;">Anuluj</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
