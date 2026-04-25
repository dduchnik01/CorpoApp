<?php
// views/urlopy/kalendarz.php
// Odpowiednik index.html kolegi — kalendarz obecności
// $pracownik, $obecnosc, $podsumowanie, $wnioski, $rok, $miesiac dostępne z kontrolera

$pageTitle = 'Kalendarz — ' . $pracownik['imie'] . ' ' . $pracownik['nazwisko'];

// Przygotuj mapę dat → status
$mapa = [];
foreach ($obecnosc as $o) {
    $mapa[$o['data']] = $o['status'];
}

// Oblicz dni w miesiącu
$liczbaDni    = cal_days_in_month(CAL_GREGORIAN, $miesiac, $rok);
$pierwszyDzien = (int)date('N', mktime(0,0,0,$miesiac,1,$rok)); // 1=Pon ... 7=Nie

$nazwyMiesiecy = ['','Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec',
                  'Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'];

// Poprzedni / następny miesiąc
$poprzedniMiesiac = $miesiac - 1;
$poprzedniRok     = $rok;
if ($poprzedniMiesiac < 1) { $poprzedniMiesiac = 12; $poprzedniRok--; }
$nastepnyMiesiac = $miesiac + 1;
$nastepnyRok     = $rok;
if ($nastepnyMiesiac > 12) { $nastepnyMiesiac = 1; $nastepnyRok++; }

ob_start();
?>

<div style="display:flex; align-items:center; gap:12px; margin-bottom:24px;">
    <a href="/pracownicy" style="color:#94a3b8; text-decoration:none;">← Wróć</a>
    <h1>Obecność: <?= htmlspecialchars($pracownik['imie'] . ' ' . $pracownik['nazwisko']) ?></h1>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; flex-wrap:wrap;">

<!-- KALENDARZ — jak .calendar-widget z pliku CSS kolegi -->
<div>
    <div class="calendar-widget">
        <h1 style="font-size:1.2rem;"><?= $nazwyMiesiecy[$miesiac] ?> <?= $rok ?></h1>

        <!-- Nawigacja miesiącami -->
        <div style="display:flex; justify-content:space-between; margin-bottom:16px;">
            <a href="/urlopy/kalendarz/<?= $pracownik['id'] ?>?rok=<?= $poprzedniRok ?>&miesiac=<?= $poprzedniMiesiac ?>"
               style="color:#94a3b8; text-decoration:none; padding:6px 12px; background:#0f172a; border-radius:8px;">← Poprzedni</a>
            <a href="/urlopy/kalendarz/<?= $pracownik['id'] ?>?rok=<?= $nastepnyRok ?>&miesiac=<?= $nastepnyMiesiac ?>"
               style="color:#94a3b8; text-decoration:none; padding:6px 12px; background:#0f172a; border-radius:8px;">Następny →</a>
        </div>

        <!-- Siatka kalendarza — identyczna struktura jak index.html kolegi -->
        <div class="grid-days">
            <div class="weekday">Pn</div>
            <div class="weekday">Wt</div>
            <div class="weekday">Śr</div>
            <div class="weekday">Cz</div>
            <div class="weekday">Pt</div>
            <div class="weekday">Sb</div>
            <div class="weekday">Nd</div>

            <!-- Puste komórki przed 1. dniem -->
            <?php for ($i = 1; $i < $pierwszyDzien; $i++): ?>
                <div class="empty"></div>
            <?php endfor; ?>

            <!-- Dni miesiąca -->
            <?php for ($d = 1; $d <= $liczbaDni; $d++):
                $data   = sprintf('%04d-%02d-%02d', $rok, $miesiac, $d);
                $status = $mapa[$data] ?? '';
                $klasa  = $status ? "day $status" : "day";
                $dzien  = (int)date('N', mktime(0,0,0,$miesiac,$d,$rok));
                if ($dzien >= 6) $klasa .= ' weekend';
            ?>
                <div class="<?= $klasa ?>" title="<?= $data ?> <?= $status ?>">
                    <?= $d ?>
                </div>
            <?php endfor; ?>
        </div>

        <!-- Legenda statusów — jak w style.css kolegi (O,N,S,Z) + U=Urlop -->
        <div style="margin-top:20px; display:flex; flex-wrap:wrap; gap:8px; font-size:0.8rem;">
            <span class="day O" style="width:auto; border-radius:6px; padding:4px 8px; height:auto;">O — Obecny</span>
            <span class="day N" style="width:auto; border-radius:6px; padding:4px 8px; height:auto;">N — Nieobecny</span>
            <span class="day S" style="width:auto; border-radius:6px; padding:4px 8px; height:auto;">S — Spóźnienie</span>
            <span class="day Z" style="width:auto; border-radius:6px; padding:4px 8px; height:auto;">Z — Zdalne</span>
            <span style="background:#8b5cf6; color:white; border-radius:6px; padding:4px 8px;">U — Urlop</span>
        </div>
    </div>

    <!-- Formularz dodawania obecności -->
    <div class="card" style="margin-top:20px;">
        <h3 style="margin-bottom:16px;">Dodaj wpis obecności</h3>
        <form method="POST" action="/urlopy/obecnosc">
            <input type="hidden" name="id_pracownika" value="<?= $pracownik['id'] ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Data</label>
                    <input type="date" name="data" required
                           value="<?= date('Y-m-d') ?>" class="date-picker">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" style="padding:12px; border-radius:12px; border:none; background:#0f172a; color:white; width:100%;">
                        <option value="O">O — Obecny</option>
                        <option value="N">N — Nieobecny</option>
                        <option value="S">S — Spóźnienie</option>
                        <option value="Z">Z — Zdalne</option>
                        <option value="U">U — Urlop</option>
                    </select>
                </div>
            </div>
            <button type="submit" style="width:100%; margin-top:8px;">💾 Zapisz obecność</button>
        </form>
    </div>
</div>

<!-- PANEL URLOPOWY — jak /leave/summary z server.js kolegi -->
<div>
    <!-- Podsumowanie urlopu -->
    <div class="card" style="margin-bottom:20px;">
        <h3 style="margin-bottom:20px;">📊 Urlop <?= $rok ?></h3>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px; text-align:center; margin-bottom:20px;">
            <div style="background:#0f172a; border-radius:12px; padding:16px;">
                <div style="font-size:1.8rem; font-weight:800; color:#38bdf8;"><?= $podsumowanie['przyslugujace'] ?></div>
                <div style="color:#94a3b8; font-size:0.8rem; margin-top:4px;">Przysługuje</div>
            </div>
            <div style="background:#0f172a; border-radius:12px; padding:16px;">
                <div style="font-size:1.8rem; font-weight:800; color:#f87171;"><?= $podsumowanie['wykorzystane'] ?></div>
                <div style="color:#94a3b8; font-size:0.8rem; margin-top:4px;">Wykorzystano</div>
            </div>
            <div style="background:#0f172a; border-radius:12px; padding:16px;">
                <div style="font-size:1.8rem; font-weight:800; color:#34d399;"><?= $podsumowanie['pozostale'] ?></div>
                <div style="color:#94a3b8; font-size:0.8rem; margin-top:4px;">Pozostało</div>
            </div>
        </div>

        <!-- Pasek postępu -->
        <?php $pct = $podsumowanie['przyslugujace'] > 0
            ? round($podsumowanie['wykorzystane'] / $podsumowanie['przyslugujace'] * 100) : 0; ?>
        <div style="background:#0f172a; border-radius:99px; height:12px;">
            <div style="background: linear-gradient(90deg, #34d399, #f87171);
                        width:<?= $pct ?>%; height:12px; border-radius:99px; transition:.3s;"></div>
        </div>
        <p style="color:#94a3b8; font-size:0.8rem; margin-top:8px;">Wykorzystano <?= $pct ?>% rocznego urlopu</p>
    </div>

    <!-- Lista wniosków urlopowych -->
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3>Wnioski urlopowe</h3>
            <a href="/urlopy/dodaj" style="background:#3b82f6; color:white; padding:8px 14px; border-radius:8px; text-decoration:none; font-size:0.85rem;">+ Nowy wniosek</a>
        </div>

        <?php if (empty($wnioski)): ?>
            <p style="color:#94a3b8;">Brak wniosków urlopowych.</p>
        <?php else: ?>
            <?php foreach ($wnioski as $w): ?>
            <div style="background:#0f172a; border-radius:10px; padding:12px; margin-bottom:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <span style="font-weight:600;"><?= htmlspecialchars($w['data_od']) ?> → <?= htmlspecialchars($w['data_do']) ?></span>
                        <span style="color:#94a3b8; margin-left:8px;">(<?= $w['liczba_dni'] ?> dni)</span>
                    </div>
                    <span class="badge <?= $w['status'] === 'zatwierdzony' ? 'badge-green' : ($w['status'] === 'odrzucony' ? 'badge-red' : 'badge-yellow') ?>">
                        <?= htmlspecialchars($w['status']) ?>
                    </span>
                </div>
                <?php if ($w['uwagi']): ?>
                    <p style="color:#94a3b8; font-size:0.8rem; margin-top:6px;"><?= htmlspecialchars($w['uwagi']) ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
