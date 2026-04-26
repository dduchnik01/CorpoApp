# CorpoApp

## 🚀 Jak uruchomić projekt?

1. Sklonuj repozytorium i wejdź do folderu:
```bash
git clone <link>
cd CorpoApp

docker-compose up  -d --build
```

## 🌐 Dostępne usługi

Po poprawnym zbudowaniu kontenerów, aplikacje znajdziesz pod adresami:

- Frontend / Klasyczny CMS (PHP): http://localhost:8000

- Zarządzanie bazą (phpMyAdmin): http://localhost:8080 (login: root, hasło: root)

🛑 Jak zatrzymać serwer?
Aby wyłączyć środowisko, wpisz w terminalu:
```bash
docker compose down
```

