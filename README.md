# TechAssist – Simple PHP Ticketing System

Lightweight IT helpdesk/ticketing built in **vanilla PHP 8** (no framework) with **MariaDB/MySQL**, containerized via **Docker Compose**.  
Originally a class project, rebuilt to serve as a **clean portfolio piece** and a **maintenance-friendly codebase**.

## Why this matters
- Demonstrates **backend fundamentals** without hiding behind a framework: routing, controllers, models, views, validation.
- Shows **security & data integrity** awareness (authZ/authN, CSRF, input validation, prepared statements).
- Runs anywhere with Docker; onboarding is **one command** away.

---

## ✨ Features
- **Ticket CRUD**: create, view, update, close.
- **Comments / Activity thread** per ticket.
- **Status workflow**: Open → In‑Progress → Closed (timestamps).
- **Search / Filter / Pagination** to navigate large lists.
- **Flash messages & validation** for user actions.
- **Roles & authorization**: Admin vs standard users.
- **Assignment**: assign tickets to users (admin only).
- **Audit Log**: every change stamped with actor + time.
- **Demo data seeder** to explore quickly.
- **Dockerized env**: PHP‑FPM, Nginx, MariaDB, phpMyAdmin.

> Screenshots available in `docs/images/` (login, dashboard, ticket flows, audit).

---

## 🧰 Tech Stack
- **PHP 8.x** (no framework, readable vanilla PHP)
- **MariaDB 10.x** (MySQL compatible)
- **Nginx** as web server
- **Docker Compose** for local deployment
- **phpMyAdmin** for DB admin

---

## 🚀 Quick Start

### 1) Clone
```bash
git clone https://github.com/MederickBernier/TechAssist.git
cd TechAssist
```

### 2) Environment
```bash
cp .env.example .env
# edit .env as needed (DB creds, app host/ports)
```

### 3) Up (Docker)
```bash
docker compose up -d --build
```

### 4) Initialize DB
```bash
docker compose exec -T db mariadb -utechassist -ptechassist techassist < ./sql/init/01_schema.sql
docker compose exec -T db mariadb -utechassist -ptechassist techassist < ./sql/init/02_features.sql
docker compose exec -T db mariadb -utechassist -ptechassist techassist < ./sql/init/03_seed_data.sql
```

### 5) Access
- **App:** http://localhost:8080  
- **phpMyAdmin:** http://localhost:8081 (server: `db`, user: `techassist`, pass: `techassist`)

### Demo accounts
| Role  | Username | Password |
|-------|----------|----------|
| Admin | admin    | password |
| User  | demo     | password |
| User  | alice    | password |
| User  | bob      | password |

---

## 🧱 Project Structure
```
.
├── docker/                 # Docker config
├── public/                 # Web root
├── sql/init/               # Schema + seed scripts
├── src/                    # App code
│   ├── Controllers/        # Ticket & Auth controllers
│   ├── Models/             # Data access
│   ├── Views/              # Templates
│   └── bootstrap.php       # App bootstrap
├── .env.example
├── docker-compose.yml
└── README.md
```

---

## 🔒 Security & Data Practices
- CSRF tokens on state‑changing requests.
- Input validation + prepared statements to avoid SQLi.
- Role checks on admin routes.
- Passwords stored with modern hashing (if applicable in your fork).
- `.env` for secrets (excluded from VCS).

> This is a portfolio app; review & harden before internet exposure (HTTPS, headers, rate limits, logs).

---

## 🏎️ Performance Notes
- Query indexes added where relevant.
- Pagination for list endpoints.
- Simple caching opportunities noted as TODOs.

---

## 🧪 Testing (optional)
Light smoke tests can be added with PHP‑CLI scripts or Pest/PHPUnit.  
Example layout (not included by default):
```
tests/
  TicketFlowTest.php
  AuthTest.php
```

---

## 📍 Roadmap
- Password reset & email notifications
- File attachments on tickets
- Export (CSV) for audit logs
- Basic API endpoints for integration
- Add minimal test suite (Pest/PHPUnit)

---

## 📜 License
MIT — use freely.

## 🙋 Author
**Mederick Bernier** — GitHub: https://github.com/MederickBernier
