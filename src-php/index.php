<?php
// =============================================
// index.php — główny router aplikacji (MVC)
// =============================================

session_start();

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/PracownikController.php';
require_once __DIR__ . '/controllers/UrlopController.php';
require_once __DIR__ . '/models/DzialModel.php';

// Parsuj URL
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Obsługa metody DELETE przez formularz (_method)
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

$pracownikCtrl = new PracownikController();
$urlopCtrl     = new UrlopController();

// =============================================
// ROUTING
// =============================================

// Dashboard
if ($uri === '/' || $uri === '/index.php') {
    require __DIR__ . '/views/dashboard.php';
    exit;
}

// Pracownicy
if ($uri === '/pracownicy' && $method === 'GET') {
    $pracownikCtrl->lista();
    exit;
}
if ($uri === '/pracownicy/dodaj' && $method === 'GET') {
    $pracownikCtrl->formularzDodaj();
    exit;
}
if ($uri === '/pracownicy/zapisz' && $method === 'POST') {
    $pracownikCtrl->zapisz();
    exit;
}
if (preg_match('#^/pracownicy/edytuj/(\d+)$#', $uri, $m) && $method === 'GET') {
    $pracownikCtrl->formularzEdytuj((int)$m[1]);
    exit;
}
if (preg_match('#^/pracownicy/aktualizuj/(\d+)$#', $uri, $m) && $method === 'POST') {
    $pracownikCtrl->aktualizuj((int)$m[1]);
    exit;
}
if (preg_match('#^/pracownicy/usun/(\d+)$#', $uri, $m) && $method === 'POST') {
    $pracownikCtrl->usun((int)$m[1]);
    exit;
}

// Urlopy / Obecność
if ($uri === '/urlopy' && $method === 'GET') {
    $urlopCtrl->wnioski();
    exit;
}
if ($uri === '/urlopy/dodaj' && $method === 'GET') {
    $urlopCtrl->formularz();
    exit;
}
if ($uri === '/urlopy/zapisz' && $method === 'POST') {
    $urlopCtrl->zapisz();
    exit;
}
if (preg_match('#^/urlopy/status/(\d+)$#', $uri, $m) && $method === 'POST') {
    $urlopCtrl->zmienStatus((int)$m[1]);
    exit;
}
if (preg_match('#^/urlopy/kalendarz/(\d+)$#', $uri, $m) && $method === 'GET') {
    $urlopCtrl->kalendarz((int)$m[1]);
    exit;
}
if ($uri === '/urlopy/obecnosc' && $method === 'POST') {
    $urlopCtrl->zapiszObecnosc();
    exit;
}

// API JSON (jak endpointy w server.js kolegi)
if (preg_match('#^/api/obecnosc/(\d+)/(\d+)/(\d+)$#', $uri, $m)) {
    $urlopCtrl->apiObecnosc((int)$m[1], (int)$m[2], (int)$m[3]);
    exit;
}
if (preg_match('#^/api/urlop/(\d+)$#', $uri, $m)) {
    $urlopCtrl->apiPodsumowanie((int)$m[1]);
    exit;
}

// Działy — CRUD
if ($uri === '/dzialy' && $method === 'GET') {
    $model  = new DzialModel();
    $sm     = new StanowiskoModel();
    $dzialy = $model->wszystkie();
    $stanowiska = $sm->wszystkie();
    require __DIR__ . '/views/dzialy/lista.php';
    exit;
}
if ($uri === '/dzialy/dodaj' && $method === 'POST') {
    $model = new DzialModel();
    $model->dodaj($_POST);
    $_SESSION['sukces'] = 'Dział dodany.';
    header('Location: /dzialy');
    exit;
}
if (preg_match('#^/dzialy/usun/(\d+)$#', $uri, $m) && $method === 'POST') {
    $model = new DzialModel();
    $wynik = $model->usun((int)$m[1]);
    if (is_string($wynik)) $_SESSION['blad'] = $wynik;
    else $_SESSION['sukces'] = 'Dział usunięty.';
    header('Location: /dzialy');
    exit;
}
if ($uri === '/stanowiska/dodaj' && $method === 'POST') {
    $model = new StanowiskoModel();
    $model->dodaj($_POST);
    $_SESSION['sukces'] = 'Stanowisko dodane.';
    header('Location: /dzialy');
    exit;
}
if (preg_match('#^/stanowiska/usun/(\d+)$#', $uri, $m) && $method === 'POST') {
    $model = new StanowiskoModel();
    $wynik = $model->usun((int)$m[1]);
    if (is_string($wynik)) $_SESSION['blad'] = $wynik;
    else $_SESSION['sukces'] = 'Stanowisko usunięte.';
    header('Location: /dzialy');
    exit;
}

// 404
http_response_code(404);
require __DIR__ . '/views/404.php';
