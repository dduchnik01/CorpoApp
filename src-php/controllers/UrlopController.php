<?php
// =============================================
// controllers/UrlopController.php
// =============================================

require_once __DIR__ . '/../models/UrlopModel.php';
require_once __DIR__ . '/../models/PracownikModel.php';

class UrlopController {
    private UrlopModel $model;
    private PracownikModel $pracownikModel;

    public function __construct() {
        $this->model          = new UrlopModel();
        $this->pracownikModel = new PracownikModel();
    }

    // Widok kalendarza obecności
    public function kalendarz(int $pracownikId): void {
        $pracownik = $this->pracownikModel->jeden($pracownikId);
        if (!$pracownik) {
            $_SESSION['blad'] = 'Nie znaleziono pracownika.';
            header('Location: /pracownicy');
            exit;
        }

        $rok     = (int)($_GET['rok']    ?? date('Y'));
        $miesiac = (int)($_GET['miesiac'] ?? date('n'));

        $obecnosc    = $this->model->obecnosc($pracownikId, $rok, $miesiac);
        $podsumowanie = $this->model->podsumowanieUrlopu($pracownikId);
        $wnioski     = $this->model->wnioski($pracownikId);

        require __DIR__ . '/../views/urlopy/kalendarz.php';
    }

    // API JSON
    public function apiObecnosc(int $pracownikId, int $rok, int $miesiac): void {
        header('Content-Type: application/json');
        $dane = $this->model->obecnosc($pracownikId, $rok, $miesiac);
        echo json_encode($dane);
        exit;
    }

    // API JSON
    public function apiPodsumowanie(int $pracownikId): void {
        header('Content-Type: application/json');
        echo json_encode($this->model->podsumowanieUrlopu($pracownikId));
        exit;
    }

    // Lista wniosków (admin)
    public function wnioski(): void {
        $wnioski    = $this->model->wnioski();
        $pracownicy = $this->pracownikModel->wszyscy();
        require __DIR__ . '/../views/urlopy/wnioski.php';
    }

    // Formularz nowego wniosku
    public function formularz(): void {
        $pracownicy = $this->pracownikModel->wszyscy();
        require __DIR__ . '/../views/urlopy/formularz.php';
    }

    // Zapisz wniosek
    public function zapisz(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /urlopy');
            exit;
        }

        $wynik = $this->model->dodajWniosek($_POST);
        if ($wynik === true) {
            $_SESSION['sukces'] = 'Wniosek urlopowy złożony.';
            header('Location: /urlopy');
        } else {
            $_SESSION['blad'] = is_string($wynik) ? $wynik : 'Błąd zapisu wniosku.';
            header('Location: /urlopy/dodaj');
        }
        exit;
    }

    // Zmień status wniosku
    public function zmienStatus(int $id): void {
        $status = $_POST['status'] ?? '';
        if ($this->model->zmienStatus($id, $status)) {
            $_SESSION['sukces'] = 'Status wniosku zmieniony.';
        } else {
            $_SESSION['blad'] = 'Błąd zmiany statusu.';
        }
        header('Location: /urlopy');
        exit;
    }

    // Dodaj wpis obecności
    public function zapiszObecnosc(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /pracownicy');
            exit;
        }

        $id = (int)($_POST['id_pracownika'] ?? 0);

        // Walidacja pracownika
        $pracownik = $this->pracownikModel->jeden($id);
        if (!$pracownik) {
            $_SESSION['blad'] = 'Nie znaleziono pracownika.';
            header('Location: /pracownicy');
            exit;
        }

        // Walidacja daty
        $data = $_POST['data'] ?? '';
        if (empty($data) || !strtotime($data)) {
            $_SESSION['blad'] = 'Nieprawidłowa data.';
            header("Location: /urlopy/kalendarz/$id");
            exit;
        }

        $this->model->dodajObecnosc($_POST);
        header("Location: /urlopy/kalendarz/$id");
        exit;
    }
}
