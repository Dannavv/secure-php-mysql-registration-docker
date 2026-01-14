
# Secure PHP–MySQL Registration System (Implementation Guide)

This repository contains a **Docker-based, procedural PHP implementation** of a secure user registration system.

Conceptual explanations, security theory, and design rationale are documented separately in `REPORT.md`.

This README focuses **only on implementation, setup, and execution**.

---

## Repository Structure

```text
.
├── architecture.png          # System architecture diagram
├── docker-compose.yml        # Orchestration of PHP & MySQL services
├── mysql/
│   └── init.sql              # Database schema (includes app_logs fix)
├── php/
│   ├── Dockerfile            # PHP 8.2 + Apache environment
│   └── php.ini               # Hardened security configurations
└── src/                      # Application Source
    ├── config.php            # Security constants & Session handling
    ├── db.php                # Database wrapper & connection object
    ├── logger.php            # DB-backed audit logger (fixes 'function' keyword)
    ├── csrf.php              # Anti-CSRF token utilities
    ├── register.php          # Main registration interface & logic
    ├── test_logger.php       # Verification utility for the logging system
    └── ...                   # Additional connectivity tests

```

## Prerequisites (Pre-Setup)

Ensure the following are installed on the host system:

* **Docker**
* **Docker Compose**
* **Linux environment** (Ubuntu preferred)

**Verify installation:**

```bash
docker --version
docker-compose --version

```

---

## Setup Instructions

### 1. Clone Repository

```bash
git clone <repository-url>
cd secure_registration

```

### 2. File Permissions (Important)

Ensure correct permissions for runtime logging:

```bash
sudo chown -R $USER:$USER src/
sudo chmod -R 755 src/

```


---

## Docker Configuration Overview

### docker-compose.yml

* Defines **php** (8.2 + Apache) and **mysql** (8.0) services.
* MySQL is isolated (internal network only); PHP is exposed on port **8080**.

### php/Dockerfile

* Base: `php:8.2-apache`
* Installs `mysqli` extension and enables Apache `rewrite` module.

### mysql/init.sql

* Automatically initializes the `users` table on the first container startup.

---

## Running the Application

### 1. Build and Start Containers

```bash
docker-compose up -d --build

```

Verify with `docker ps`. You should see `php_app` and `mysql_db` running.

### 2. Access Application

* **Registration page:** `http://localhost:8080/register.php`

---

## Testing Utilities

The `src/` directory includes specific scripts to verify the environment:

* **PHP Runtime:** `http://localhost:8080/test.php`
* **Logger:** `http://localhost:8080/test_logger.php`
* **DB Connectivity:** `http://localhost:8080/test_db.php`
* **CSRF Token:** `http://localhost:8080/test_csrf.php`

---

## Core Implementation Files

| File | Purpose |
| --- | --- |
| **config.php** | Database credentials, secure session start, and log definitions. |
| **logger.php** | Centralized JSON logging with stack traces and SQL query capture. |
| **db.php** | Single entry point for DB access using **prepared statements only**. |
| **csrf.php** | Token generation and validation for POST request security. |
| **register.php** | Registration logic: Hashing (Bcrypt), XSS handling, and UI state control. |


To view the internal application logs in real-time, use:

```bash
docker logs -f php_app

```

---

## 🛡️ Security Features

* **Prepared Statements:** All database interactions in `db.php` use `mysqli_prepare` to eliminate SQL Injection risks.
* **Bcrypt Hashing:** User passwords are encrypted using `PASSWORD_BCRYPT` with a default cost factor of 10.
* **CSRF Protection:** Every POST request is validated against a unique, session-bound token to prevent Cross-Site Request Forgery.
* **Centralized Logging:** System events, security warnings, and errors are captured in the `app_logs` table.
* **Reserved Keyword Handling:** The logging system uses backticks (``function``) to support MySQL 8.0+ reserved word compatibility.

## Stopping the Application

```bash
docker-compose down

```

---

