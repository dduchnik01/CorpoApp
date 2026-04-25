<?php
$pageTitle = '404 — Nie znaleziono';
ob_start();
?>
<div style="text-align:center; padding:80px 20px;">
    <div style="font-size:5rem; margin-bottom:20px;">🔍</div>
    <h1 style="font-size:2rem; margin-bottom:12px;">Strona nie istnieje</h1>
    <p style="color:#94a3b8; margin-bottom:30px;">Sprawdź adres URL lub wróć do dashboardu.</p>
    <a href="/" style="background:#3b82f6; color:white; padding:12px 24px; border-radius:12px; text-decoration:none;">Wróć do dashboardu</a>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
