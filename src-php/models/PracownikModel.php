<?php
// =============================================
// models/PracownikModel.php — zapytania SQL
// Cała logika bazy danych w jednym miejscu (MVC)
// =============================================

require_once __DIR__ . '/../config/db.php';

class PracownikModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Pobierz wszystkich pracowników z działem i stanowiskiem
    public function wszyscy(string $szukaj = '', int $dzial = 0): array {
        $sql = "SELECT p.*, d.nazwa AS dzial, s.nazwa AS stanowisko
                FROM pracownicy p
                JOIN dzialy d ON p.id_dzialu = d.id
                JOIN stanowiska s ON p.id_stanowiska = s.id
                WHERE p.aktywny = 1";
        $params = [];

        if ($szukaj !== '') {
            $sql .= " AND (p.imie LIKE ? OR p.nazwisko LIKE ? OR p.email LIKE ?)";
            $like = "%$szukaj%";
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if ($dzial > 0) {
            $sql .= " AND p.id_dzialu = ?";
            $params[] = $dzial;
        }
        $sql .= " ORDER BY p.nazwisko, p.imie";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Pobierz jednego aktywnego pracownika
    public function jeden(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT p.*, d.nazwa AS dzial, s.nazwa AS stanowisko
             FROM pracownicy p
             JOIN dzialy d ON p.id_dzialu = d.id
             JOIN stanowiska s ON p.id_stanowiska = s.id
             WHERE p.id = ? AND p.aktywny = 1"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Sprawdź czy aktywny pracownik z tym emailem już istnieje
    public function emailIstnieje(string $email, int $wykluczId = 0): bool {
        $sql = "SELECT COUNT(*) FROM pracownicy WHERE email = ? AND aktywny = 1";
        $params = [$email];
        if ($wykluczId > 0) {
            $sql .= " AND id != ?";
            $params[] = $wykluczId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    // Sprawdź czy aktywny pracownik o takim imieniu i nazwisku już istnieje
    public function pracownikIstnieje(string $imie, string $nazwisko, int $wykluczId = 0): bool {
        $sql = "SELECT COUNT(*) FROM pracownicy WHERE imie = ? AND nazwisko = ? AND aktywny = 1";
        $params = [$imie, $nazwisko];
        if ($wykluczId > 0) {
            $sql .= " AND id != ?";
            $params[] = $wykluczId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    // Znajdź nieaktywnego pracownika po emailu (do reaktywacji)
    private function znajdzNieaktywnego(string $email): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM pracownicy WHERE email = ? AND aktywny = 0 LIMIT 1"
        );
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Dodaj pracownika — jeśli istnieje nieaktywny z tym emailem, reaktywuje go zamiast tworzyć duplikat
    public function dodaj(array $dane): bool {
        $email = filter_var($dane['email'], FILTER_SANITIZE_EMAIL);
        $haslo = password_hash($dane['haslo'], PASSWORD_BCRYPT);

        $nieaktywny = $this->znajdzNieaktywnego($email);
        if ($nieaktywny) {
            // Reaktywacja istniejącego rekordu z nowymi danymi
            $stmt = $this->db->prepare(
                "UPDATE pracownicy SET
                 imie = ?, nazwisko = ?, telefon = ?, data_zatrudnienia = ?,
                 id_dzialu = ?, id_stanowiska = ?, wynagrodzenie = ?,
                 dni_urlopu = ?, haslo = ?, aktywny = 1
                 WHERE id = ?"
            );
            return $stmt->execute([
                htmlspecialchars($dane['imie'], ENT_QUOTES),
                htmlspecialchars($dane['nazwisko'], ENT_QUOTES),
                htmlspecialchars($dane['telefon'] ?? '', ENT_QUOTES),
                $dane['data_zatrudnienia'],
                (int)$dane['id_dzialu'],
                (int)$dane['id_stanowiska'],
                (float)$dane['wynagrodzenie'],
                (int)($dane['dni_urlopu'] ?? 26),
                $haslo,
                $nieaktywny['id']
            ]);
        }

        $stmt = $this->db->prepare(
            "INSERT INTO pracownicy
             (imie, nazwisko, email, telefon, data_zatrudnienia, id_dzialu, id_stanowiska, wynagrodzenie, dni_urlopu, haslo)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            htmlspecialchars($dane['imie'], ENT_QUOTES),
            htmlspecialchars($dane['nazwisko'], ENT_QUOTES),
            $email,
            htmlspecialchars($dane['telefon'] ?? '', ENT_QUOTES),
            $dane['data_zatrudnienia'],
            (int)$dane['id_dzialu'],
            (int)$dane['id_stanowiska'],
            (float)$dane['wynagrodzenie'],
            (int)($dane['dni_urlopu'] ?? 26),
            $haslo
        ]);
    }

    // Edytuj pracownika
    public function edytuj(int $id, array $dane): bool {
        $stmt = $this->db->prepare(
            "UPDATE pracownicy SET
             imie = ?, nazwisko = ?, email = ?, telefon = ?,
             data_zatrudnienia = ?, id_dzialu = ?, id_stanowiska = ?,
             wynagrodzenie = ?, dni_urlopu = ?
             WHERE id = ?"
        );
        return $stmt->execute([
            htmlspecialchars($dane['imie'], ENT_QUOTES),
            htmlspecialchars($dane['nazwisko'], ENT_QUOTES),
            filter_var($dane['email'], FILTER_SANITIZE_EMAIL),
            htmlspecialchars($dane['telefon'] ?? '', ENT_QUOTES),
            $dane['data_zatrudnienia'],
            (int)$dane['id_dzialu'],
            (int)$dane['id_stanowiska'],
            (float)$dane['wynagrodzenie'],
            (int)$dane['dni_urlopu'],
            $id
        ]);
    }

    // Usuń (soft delete — zachowuje dane historyczne)
    public function usun(int $id): bool {
        $stmt = $this->db->prepare("UPDATE pracownicy SET aktywny = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Statystyki dla dashboardu — tylko aktywni pracownicy
    public function statystyki(): array {
        $stmt = $this->db->query(
            "SELECT
                COUNT(*) AS total,
                COUNT(*) AS aktywni,
                COUNT(DISTINCT id_dzialu) AS liczba_dzialow,
                AVG(wynagrodzenie) AS srednie_wynagrodzenie
             FROM pracownicy
             WHERE aktywny = 1"
        );
        return $stmt->fetch();
    }

    // Liczba pracowników per dział
    public function perDzial(): array {
        $stmt = $this->db->query(
            "SELECT d.nazwa, COUNT(p.id) AS liczba
             FROM dzialy d
             LEFT JOIN pracownicy p ON d.id = p.id_dzialu AND p.aktywny = 1
             GROUP BY d.id, d.nazwa
             ORDER BY liczba DESC"
        );
        return $stmt->fetchAll();
    }
}
