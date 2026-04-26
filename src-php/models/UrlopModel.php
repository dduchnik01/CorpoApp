<?php
// =============================================
// models/UrlopModel.php
// =============================================

require_once __DIR__ . '/../config/db.php';

class UrlopModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Pobierz obecność dla danego miesiąca
    public function obecnosc(int $pracownikId, int $rok, int $miesiac): array {
        $stmt = $this->db->prepare(
            "SELECT DATE_FORMAT(data, '%Y-%m-%d') AS data, status
             FROM obecnosc
             WHERE id_pracownika = ?
               AND YEAR(data) = ?
               AND MONTH(data) = ?"
        );
        $stmt->execute([$pracownikId, $rok, $miesiac]);
        return $stmt->fetchAll();
    }

    // Podsumowanie urlopu
    public function podsumowanieUrlopu(int $pracownikId): array {
        $stmt = $this->db->prepare(
            "SELECT
                p.dni_urlopu AS przyslugujace,
                COALESCE(SUM(CASE WHEN u.status='zatwierdzony' THEN u.liczba_dni ELSE 0 END), 0) AS wykorzystane,
                p.dni_urlopu - COALESCE(SUM(CASE WHEN u.status='zatwierdzony' THEN u.liczba_dni ELSE 0 END), 0) AS pozostale
             FROM pracownicy p
             LEFT JOIN urlopy u ON p.id = u.id_pracownika
             WHERE p.id = ? AND p.aktywny = 1
             GROUP BY p.id, p.dni_urlopu"
        );
        $stmt->execute([$pracownikId]);
        return $stmt->fetch() ?: ['przyslugujace' => 26, 'wykorzystane' => 0, 'pozostale' => 26];
    }

    // Lista wniosków urlopowych — tylko aktywni pracownicy
    public function wnioski(int $pracownikId = 0): array {
        $sql = "SELECT u.*, CONCAT(p.imie, ' ', p.nazwisko) AS pracownik
                FROM urlopy u
                JOIN pracownicy p ON u.id_pracownika = p.id
                WHERE p.aktywny = 1";
        $params = [];
        if ($pracownikId > 0) {
            $sql .= " AND u.id_pracownika = ?";
            $params[] = $pracownikId;
        }
        $sql .= " ORDER BY u.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Dodaj wniosek urlopowy
    public function dodajWniosek(array $dane): bool|string {
        // Walidacja dat
        $od = new DateTime($dane['data_od']);
        $do = new DateTime($dane['data_do']);
        if ($od > $do) return 'Data końcowa musi być po dacie początkowej.';

        $dni = $od->diff($do)->days + 1;

        // Sprawdź czy pracownik jest aktywny
        $stmtAkt = $this->db->prepare("SELECT COUNT(*) FROM pracownicy WHERE id = ? AND aktywny = 1");
        $stmtAkt->execute([(int)$dane['id_pracownika']]);
        if ((int)$stmtAkt->fetchColumn() === 0) {
            return 'Wybrany pracownik nie istnieje lub jest nieaktywny.';
        }

        // Sprawdź czy nie ma nakładających się wniosków (oczekujących lub zatwierdzonych)
        $stmtNakladanie = $this->db->prepare(
            "SELECT COUNT(*) FROM urlopy
             WHERE id_pracownika = ?
               AND status IN ('oczekujacy', 'zatwierdzony')
               AND data_od <= ? AND data_do >= ?"
        );
        $stmtNakladanie->execute([(int)$dane['id_pracownika'], $dane['data_do'], $dane['data_od']]);
        if ((int)$stmtNakladanie->fetchColumn() > 0) {
            return 'Pracownik ma już złożony lub zatwierdzony wniosek urlopowy w tym terminie.';
        }

        // Sprawdź czy ma wystarczająco dni
        $podsumowanie = $this->podsumowanieUrlopu((int)$dane['id_pracownika']);
        if ($dni > $podsumowanie['pozostale']) {
            return "Niewystarczająca liczba dni urlopu. Pozostało: {$podsumowanie['pozostale']} dni.";
        }

        $stmt = $this->db->prepare(
            "INSERT INTO urlopy (id_pracownika, data_od, data_do, liczba_dni, uwagi)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            (int)$dane['id_pracownika'],
            $dane['data_od'],
            $dane['data_do'],
            $dni,
            htmlspecialchars($dane['uwagi'] ?? '', ENT_QUOTES)
        ]);
        return true;
    }

    // Zmień status wniosku
    public function zmienStatus(int $id, string $status): bool {
        $dozwolone = ['oczekujacy', 'zatwierdzony', 'odrzucony'];
        if (!in_array($status, $dozwolone)) return false;

        $stmt = $this->db->prepare("UPDATE urlopy SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    // Dodaj wpis obecności
    public function dodajObecnosc(array $dane): bool {
        $dozwolone = ['O', 'N', 'U', 'S', 'Z'];
        if (!in_array($dane['status'], $dozwolone)) return false;

        // Sprawdź czy pracownik jest aktywny
        $stmtAkt = $this->db->prepare("SELECT COUNT(*) FROM pracownicy WHERE id = ? AND aktywny = 1");
        $stmtAkt->execute([(int)$dane['id_pracownika']]);
        if ((int)$stmtAkt->fetchColumn() === 0) return false;

        $stmt = $this->db->prepare(
            "INSERT INTO obecnosc (id_pracownika, data, status, uwagi)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE status = VALUES(status), uwagi = VALUES(uwagi)"
        );
        return $stmt->execute([
            (int)$dane['id_pracownika'],
            $dane['data'],
            $dane['status'],
            htmlspecialchars($dane['uwagi'] ?? '', ENT_QUOTES)
        ]);
    }
}
