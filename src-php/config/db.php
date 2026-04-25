<?php
// =============================================
// config/db.php — połączenie z bazą (PDO)
// Odpowiednik db.js kolegi, ale w PHP + MySQL
// =============================================

define('DB_HOST', 'db');        // nazwa serwisu z docker-compose
define('DB_NAME', 'pracownicy_db');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
                self::$instance->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
            } catch (PDOException $e) {
                // Nie pokazujemy surowego błędu użytkownikowi
                error_log("Błąd połączenia z bazą: " . $e->getMessage());
                die(json_encode(['error' => 'Błąd połączenia z bazą danych.']));
            }
        }
        return self::$instance;
    }
}
