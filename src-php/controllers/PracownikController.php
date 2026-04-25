<?php
// =============================================
// controllers/PracownikController.php
// Obsługa żądań HTTP dla pracowników
// =============================================

require_once __DIR__ . '/../models/PracownikModel.php';
require_once __DIR__ . '/../models/DzialModel.php';

class PracownikController {
    private PracownikModel $model;
    private DzialModel $dzialModel;
    private StanowiskoModel $stanowiskoModel;

    public function __construct() {
        $this->model           = new PracownikModel();
        $this->dzialModel      = new DzialModel();
        $this->stanowiskoModel = new StanowiskoModel();
    }

    public function lista(): void {
        $szukaj = htmlspecialchars($_GET['szukaj'] ?? '', ENT_QUOTES);
        $dzial  = (int)($_GET['dzial'] ?? 0);

        $pracownicy  = $this->model->wszyscy($szukaj, $dzial);
        $dzialy      = $this->dzialModel->wszystkie();
        $statystyki  = $this->model->statystyki();
        $perDzial    = $this->model->perDzial();

        require __DIR__ . '/../views/pracownicy/lista.php';
    }

    public function formularzDodaj(): void {
        $dzialy     = $this->dzialModel->wszystkie();
        $stanowiska = $this->stanowiskoModel->wszystkie();
        $blad = $_GET['blad'] ?? '';
        require __DIR__ . '/../views/pracownicy/formularz.php';
    }

    public function zapisz(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /pracownicy');
            exit;
        }

        // Walidacja
        $bledy = [];
        if (empty($_POST['imie']))     $bledy[] = 'Imię jest wymagane.';
        if (empty($_POST['nazwisko'])) $bledy[] = 'Nazwisko jest wymagane.';
        if (!filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $bledy[] = 'Nieprawidłowy adres email.';
        }
        if (empty($_POST['data_zatrudnienia'])) $bledy[] = 'Data zatrudnienia jest wymagana.';
        if ((float)($_POST['wynagrodzenie'] ?? 0) <= 0) $bledy[] = 'Wynagrodzenie musi być większe od 0.';
        if (empty($_POST['haslo']) || strlen($_POST['haslo']) < 6) $bledy[] = 'Hasło min. 6 znaków.';
        if (empty($_POST['id_dzialu'])) $bledy[] = 'Wybór działu jest wymagany.';
        if (empty($_POST['id_stanowiska'])) $bledy[] = 'Wybór stanowiska jest wymagany.';

        // Sprawdzenie duplikatów
        if (empty($bledy)) {
            if ($this->model->emailIstnieje(trim($_POST['email']))) {
                $bledy[] = 'Pracownik z tym adresem email już istnieje.';
            }
            if ($this->model->pracownikIstnieje(trim($_POST['imie']), trim($_POST['nazwisko']))) {
                $bledy[] = 'Pracownik o tym imieniu i nazwisku już istnieje w systemie.';
            }
        }

        if ($bledy) {
            $_SESSION['blad'] = implode('<br>', $bledy);
            header('Location: /pracownicy/dodaj');
            exit;
        }

        if ($this->model->dodaj($_POST)) {
            $_SESSION['sukces'] = 'Pracownik został dodany.';
            header('Location: /pracownicy');
        } else {
            $_SESSION['blad'] = 'Wystąpił błąd podczas dodawania pracownika.';
            header('Location: /pracownicy/dodaj');
        }
        exit;
    }

    public function formularzEdytuj(int $id): void {
        $pracownik  = $this->model->jeden($id);
        if (!$pracownik) {
            $_SESSION['blad'] = 'Nie znaleziono pracownika.';
            header('Location: /pracownicy');
            exit;
        }
        $dzialy     = $this->dzialModel->wszystkie();
        $stanowiska = $this->stanowiskoModel->wszystkie();
        require __DIR__ . '/../views/pracownicy/edytuj.php';
    }

    public function aktualizuj(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /pracownicy');
            exit;
        }

        $bledy = [];
        if (empty($_POST['imie']))     $bledy[] = 'Imię jest wymagane.';
        if (empty($_POST['nazwisko'])) $bledy[] = 'Nazwisko jest wymagane.';
        if (!filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $bledy[] = 'Nieprawidłowy adres email.';
        }
        if ((float)($_POST['wynagrodzenie'] ?? 0) <= 0) $bledy[] = 'Wynagrodzenie musi być > 0.';
        if (empty($_POST['id_dzialu'])) $bledy[] = 'Wybór działu jest wymagany.';
        if (empty($_POST['id_stanowiska'])) $bledy[] = 'Wybór stanowiska jest wymagany.';

        // Sprawdzenie duplikatów (z wykluczeniem edytowanego pracownika)
        if (empty($bledy)) {
            if ($this->model->emailIstnieje(trim($_POST['email']), $id)) {
                $bledy[] = 'Inny pracownik z tym adresem email już istnieje.';
            }
            if ($this->model->pracownikIstnieje(trim($_POST['imie']), trim($_POST['nazwisko']), $id)) {
                $bledy[] = 'Inny pracownik o tym imieniu i nazwisku już istnieje w systemie.';
            }
        }

        if ($bledy) {
            $_SESSION['blad'] = implode('<br>', $bledy);
            header("Location: /pracownicy/edytuj/$id");
            exit;
        }

        if ($this->model->edytuj($id, $_POST)) {
            $_SESSION['sukces'] = 'Dane pracownika zaktualizowane.';
        } else {
            $_SESSION['blad'] = 'Błąd aktualizacji danych.';
        }
        header('Location: /pracownicy');
        exit;
    }

    public function usun(int $id): void {
        if ($this->model->usun($id)) {
            $_SESSION['sukces'] = 'Pracownik został usunięty.';
        } else {
            $_SESSION['blad'] = 'Błąd usuwania pracownika.';
        }
        header('Location: /pracownicy');
        exit;
    }
}
