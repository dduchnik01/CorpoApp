<?php
// =============================================
// views/layout.php — wspólny szablon HTML
// =============================================

// Wymuś kodowanie UTF-8 w przeglądarce
header('Content-Type: text/html; charset=UTF-8');

// Pobierz komunikaty z sesji
$sukces = $_SESSION['sukces'] ?? '';
$blad   = $_SESSION['blad']   ?? '';
unset($_SESSION['sukces'], $_SESSION['blad']);

$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
function isActive(string $path, string $current): string {
    return str_starts_with($current, $path) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'CorpoApp') ?> — System HR</title>
    <link rel="stylesheet" href="/style.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <a href="/" class="icon <?= isActive('/', $currentUri) && $currentUri === '/' ? 'active' : '' ?>" title="Dashboard">🏠</a>
    <a href="/pracownicy" class="icon <?= isActive('/pracownicy', $currentUri) ?>" title="Pracownicy">👥</a>
    <a href="/urlopy" class="icon <?= isActive('/urlopy', $currentUri) ?>" title="Urlopy">📅</a>
    <a href="/dzialy" class="icon <?= isActive('/dzialy', $currentUri) ?>" title="Działy">🏢</a>
</aside>

<main class="content">

    <?php if ($sukces): ?>
        <div class="alert alert-sukces">✅ <?= htmlspecialchars($sukces) ?></div>
    <?php endif; ?>
    <?php if ($blad): ?>
        <div class="alert alert-blad">❌ <?= $blad /* może zawierać <br> */ ?></div>
    <?php endif; ?>

    <?= $content ?? '' ?>

</main>

</body>
</html>
