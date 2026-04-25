-- =============================================
-- System Zarządzania Pracownikami
-- Schemat bazy danych (3NF)
-- =============================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET character_set_connection=utf8mb4;

CREATE DATABASE IF NOT EXISTS pracownicy_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pracownicy_db;

-- Tabela działów
CREATE TABLE dzialy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nazwa VARCHAR(100) NOT NULL,
    lokalizacja VARCHAR(100),
    opis TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela stanowisk
CREATE TABLE stanowiska (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nazwa VARCHAR(100) NOT NULL,
    stawka_min DECIMAL(10,2) NOT NULL DEFAULT 0,
    stawka_max DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela pracowników (klucze obce do działów i stanowisk)
CREATE TABLE pracownicy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    imie VARCHAR(50) NOT NULL,
    nazwisko VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefon VARCHAR(20),
    data_zatrudnienia DATE NOT NULL,
    id_dzialu INT NOT NULL,
    id_stanowiska INT NOT NULL,
    wynagrodzenie DECIMAL(10,2) NOT NULL,
    dni_urlopu INT NOT NULL DEFAULT 26,
    aktywny TINYINT(1) NOT NULL DEFAULT 1,
    haslo VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_dzialu)
        REFERENCES dzialy(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    FOREIGN KEY (id_stanowiska)
        REFERENCES stanowiska(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- Tabela obecności
CREATE TABLE obecnosc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pracownika INT NOT NULL,
    data DATE NOT NULL,
    status ENUM('O','N','U','S','Z') NOT NULL COMMENT 'O=Obecny, N=Nieobecny, U=Urlop, S=Spóźnienie, Z=Zdalne',
    uwagi VARCHAR(255),
    UNIQUE KEY uniq_pracownik_data (id_pracownika, data),
    FOREIGN KEY (id_pracownika) REFERENCES pracownicy(id) ON DELETE CASCADE
);

-- Tabela wniosków urlopowych
CREATE TABLE urlopy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pracownika INT NOT NULL,
    data_od DATE NOT NULL,
    data_do DATE NOT NULL,
    liczba_dni INT NOT NULL,
    status ENUM('oczekujacy','zatwierdzony','odrzucony') NOT NULL DEFAULT 'oczekujacy',
    uwagi VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pracownika) REFERENCES pracownicy(id) ON DELETE CASCADE
);

-- Indeksy dla wydajności
CREATE INDEX idx_obecnosc_data ON obecnosc(data);
CREATE INDEX idx_obecnosc_pracownik ON obecnosc(id_pracownika);
CREATE INDEX idx_urlopy_pracownik ON urlopy(id_pracownika);
CREATE INDEX idx_pracownicy_dzial ON pracownicy(id_dzialu);

-- =============================================
-- Dane przykładowe
-- =============================================

INSERT INTO dzialy (nazwa, lokalizacja, opis) VALUES
('IT', 'Warszawa', 'Dział informatyczny'),
('HR', 'Warszawa', 'Zasoby ludzkie'),
('Sprzedaż', 'Kraków', 'Dział handlowy'),
('Finanse', 'Warszawa', 'Księgowość i finanse');

INSERT INTO stanowiska (nazwa, stawka_min, stawka_max) VALUES
('Programista Junior', 5000, 8000),
('Programista Senior', 10000, 18000),
('Kierownik Projektu', 12000, 20000),
('Specjalista HR', 6000, 10000),
('Analityk Finansowy', 7000, 12000),
('Handlowiec', 5500, 9000);

-- Hasło dla wszystkich: Admin1234 (zahashowane password_hash)
INSERT INTO pracownicy (imie, nazwisko, email, telefon, data_zatrudnienia, id_dzialu, id_stanowiska, wynagrodzenie, dni_urlopu, haslo) VALUES
('Admin', 'System', 'admin@firma.pl', '500000000', '2020-01-01', 1, 3, 15000, 26, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Anna', 'Kowalska', 'anna.kowalska@firma.pl', '501111111', '2021-03-15', 1, 1, 6500, 26, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Piotr', 'Nowak', 'piotr.nowak@firma.pl', '502222222', '2019-07-01', 1, 2, 14000, 26, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Maria', 'Wiśniewska', 'maria.w@firma.pl', '503333333', '2022-01-10', 2, 4, 7500, 26, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Tomasz', 'Wójcik', 'tomasz.w@firma.pl', '504444444', '2020-09-01', 3, 6, 6000, 26, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Katarzyna', 'Zając', 'katarzyna.z@firma.pl', '505555555', '2021-11-20', 4, 5, 9000, 26, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Przykładowe dni obecności (kwiecień 2026)
INSERT INTO obecnosc (id_pracownika, data, status) VALUES
(2, '2026-04-01', 'O'), (2, '2026-04-02', 'O'), (2, '2026-04-03', 'S'),
(2, '2026-04-06', 'O'), (2, '2026-04-07', 'U'), (2, '2026-04-08', 'U'),
(2, '2026-04-09', 'O'), (2, '2026-04-10', 'Z'), (2, '2026-04-13', 'O'),
(2, '2026-04-14', 'O'), (2, '2026-04-15', 'N'), (2, '2026-04-16', 'O'),
(3, '2026-04-01', 'O'), (3, '2026-04-02', 'Z'), (3, '2026-04-03', 'O'),
(3, '2026-04-06', 'O'), (3, '2026-04-07', 'O'), (3, '2026-04-08', 'O'),
(3, '2026-04-09', 'N'), (3, '2026-04-10', 'O');

-- Przykładowe urlopy
INSERT INTO urlopy (id_pracownika, data_od, data_do, liczba_dni, status, uwagi) VALUES
(2, '2026-04-07', '2026-04-08', 2, 'zatwierdzony', 'Urlop wypoczynkowy'),
(3, '2026-05-04', '2026-05-15', 10, 'oczekujacy', 'Wyjazd wakacyjny'),
(4, '2026-04-20', '2026-04-24', 5, 'zatwierdzony', 'Sprawy rodzinne'),
(5, '2026-06-01', '2026-06-05', 5, 'odrzucony', 'Sezon sprzedażowy');
