<?php
// views/pracownicy/formularz.php
// $dzialy, $stanowiska dostępne z kontrolera
$pageTitle = 'Dodaj pracownika';
ob_start();
?>

<div style="max-width:600px;">
    <div style="display:flex; align-items:center; gap:12px; margin-bottom:30px;">
        <a href="/pracownicy" style="color:#94a3b8; text-decoration:none;">← Wróć</a>
        <h1>Dodaj pracownika</h1>
    </div>

    <form method="POST" action="/pracownicy/zapisz" class="formularz">

        <div class="form-row">
            <div class="form-group">
                <label for="imie">Imię *</label>
                <input type="text" id="imie" name="imie" required minlength="2"
                       value="<?= htmlspecialchars($_POST['imie'] ?? '') ?>"
                       placeholder="Anna">
            </div>
            <div class="form-group">
                <label for="nazwisko">Nazwisko *</label>
                <input type="text" id="nazwisko" name="nazwisko" required minlength="2"
                       value="<?= htmlspecialchars($_POST['nazwisko'] ?? '') ?>"
                       placeholder="Kowalska">
            </div>
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   placeholder="anna.kowalska@firma.pl">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="telefon">Telefon</label>
                <input type="tel" id="telefon" name="telefon"
                       value="<?= htmlspecialchars($_POST['telefon'] ?? '') ?>"
                       placeholder="500 000 000">
            </div>
            <div class="form-group">
                <label for="data_zatrudnienia">Data zatrudnienia *</label>
                <input type="date" id="data_zatrudnienia" name="data_zatrudnienia" required
                       value="<?= htmlspecialchars($_POST['data_zatrudnienia'] ?? date('Y-m-d')) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="id_dzialu">Dział *</label>
                <select id="id_dzialu" name="id_dzialu" required>
                    <option value="">— wybierz —</option>
                    <?php foreach ($dzialy as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= (int)($_POST['id_dzialu'] ?? 0) === (int)$d['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['nazwa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_stanowiska">Stanowisko *</label>
                <select id="id_stanowiska" name="id_stanowiska" required>
                    <option value="">— wybierz —</option>
                    <?php foreach ($stanowiska as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= (int)($_POST['id_stanowiska'] ?? 0) === (int)$s['id'] ? 'selected' : '' ?>>
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
                       min="3000" step="100"
                       value="<?= htmlspecialchars($_POST['wynagrodzenie'] ?? '') ?>"
                       placeholder="5000">
            </div>
            <div class="form-group">
                <label for="dni_urlopu">Dni urlopu rocznie</label>
                <input type="number" id="dni_urlopu" name="dni_urlopu"
                       min="20" max="40" value="<?= htmlspecialchars($_POST['dni_urlopu'] ?? '26') ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="haslo">Hasło (min. 6 znaków) *</label>
            <input type="password" id="haslo" name="haslo" required minlength="6"
                   placeholder="••••••••">
        </div>

        <div style="display:flex; gap:12px; margin-top:24px;">
            <button type="submit" style="flex:1;">✅ Dodaj pracownika</button>
            <a href="/pracownicy" style="flex:1; text-align:center; padding:10px; border-radius:12px; background:#334155; color:white; text-decoration:none;">Anuluj</a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
