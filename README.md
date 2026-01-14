
# Secure PHP–MySQL Registration System (Implementation Guide)

This repository contains a **Docker-based, procedural PHP implementation** of a secure user registration system.

Conceptual explanations, security theory, and design rationale are documented separately in `REPORT.md`.

This README focuses **only on implementation, setup, and execution**.

---

## Repository Structure

```text
.
├── architecture.png          # High-level architecture diagram
├── docker-compose.yml        # Docker services definition
├── mysql/
│   └── init.sql              # Database schema initialization
├── php/
│   ├── Dockerfile            # PHP + Apache image definition
│   └── php.ini               # PHP runtime security configuration
├── REPORT.md                 # Conceptual and theoretical report
└── src/
    ├── app.log               # Application log file (runtime)
    ├── config.php            # Global configuration
    ├── logger.php            # Centralized logging
    ├── db.php                # Single database access function
    ├── csrf.php              # CSRF protection utilities
    ├── register.php          # User registration page
    ├── test.php              # PHP sanity test
    ├── test_logger.php       # Logger test
    ├── test_db.php           # Database connectivity test
    └── test_csrf.php         # CSRF token test

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
sudo chown -R $USER:$USER src
sudo chown 33:33 src/app.log
sudo chmod 664 src/app.log

```

* **Explanation:** Source files are owned by the developer, while `app.log` must be writable by the Apache process (`www-data`, UID 33).

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

---

## Stopping the Application

```bash
docker-compose down

```

---

