<?php
// =============================================
// models/DzialModel.php
// =============================================

require_once __DIR__ . '/../config/db.php';

class DzialModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function wszystkie(): array {
        return $this->db->query("SELECT * FROM dzialy ORDER BY nazwa")->fetchAll();
    }

    public function dodaj(array $dane): bool|string {
        // Sprawdź czy dział o tej nazwie już istnieje
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM dzialy WHERE nazwa = ?");
        $stmt->execute([trim($dane['nazwa'])]);
        if ((int)$stmt->fetchColumn() > 0) {
            return 'Dział o tej nazwie już istnieje.';
        }
        $stmt = $this->db->prepare("INSERT INTO dzialy (nazwa, lokalizacja, opis) VALUES (?, ?, ?)");
        return $stmt->execute([
            htmlspecialchars($dane['nazwa'], ENT_QUOTES),
            htmlspecialchars($dane['lokalizacja'] ?? '', ENT_QUOTES),
            htmlspecialchars($dane['opis'] ?? '', ENT_QUOTES)
        ]);
    }

    public function usun(int $id): bool|string {
        // Sprawdź czy ma pracowników
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM pracownicy WHERE id_dzialu = ? AND aktywny = 1");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            return 'Nie można usunąć działu z przypisanymi pracownikami.';
        }
        $stmt = $this->db->prepare("DELETE FROM dzialy WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

class StanowiskoModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function wszystkie(): array {
        return $this->db->query("SELECT * FROM stanowiska ORDER BY nazwa")->fetchAll();
    }

    public function dodaj(array $dane): bool|string {
        // Sprawdź czy stanowisko o tej nazwie już istnieje
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM stanowiska WHERE nazwa = ?");
        $stmt->execute([trim($dane['nazwa'])]);
        if ((int)$stmt->fetchColumn() > 0) {
            return 'Stanowisko o tej nazwie już istnieje.';
        }
        $stmt = $this->db->prepare("INSERT INTO stanowiska (nazwa, stawka_min, stawka_max) VALUES (?, ?, ?)");
        return $stmt->execute([
            htmlspecialchars($dane['nazwa'], ENT_QUOTES),
            (float)$dane['stawka_min'],
            (float)$dane['stawka_max']
        ]);
    }

    public function usun(int $id): bool|string {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM pracownicy WHERE id_stanowiska = ? AND aktywny = 1");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            return 'Nie można usunąć stanowiska przypisanego pracownikom.';
        }
        $stmt = $this->db->prepare("DELETE FROM stanowiska WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
