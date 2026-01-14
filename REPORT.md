

# Technical Project Report: Secure User Registration System

**System Architecture: Dockerized PHP & MySQL (Deep Dive)**

---

## 1. Executive Summary

This project demonstrates a production-ready, secure user registration system developed using a **procedural PHP** architecture. The primary objective was to implement "Defense in Depth" while maintaining a clean, containerized deployment environment using **Docker**.

The final implementation specifically addresses common pitfalls in PHP development, such as **MySQL reserved keyword conflicts**, **session fixation**, and **circular dependency management** in logging systems.

---

## 2. System Architecture & Network Topology

The application follows a modular procedural design where core concerns are isolated into separate includes.

### Infrastructure Highlights:

* **Network Isolation:** The MySQL database is not exposed to the host machine. It communicates with the PHP container via a private Docker bridge network, mitigating external brute-force attempts on the database port.
* **Environment Parity:** Docker ensures the development environment exactly matches the production environment, eliminating "it works on my machine" issues.
* **Persistence:** A Docker Volume is utilized for MySQL data to ensure user accounts persist across container restarts.

---

## 3. Database Schema Design

The system uses a normalized relational structure. A significant update was made to the `app_logs` table to ensure compatibility with MySQL 8.0's reserved words.

### 3.1 User Table (`users`)

Stores credential and profile data.

| Field | Type | Constraint | Purpose |
| --- | --- | --- | --- |
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique Identifier |
| `roll` | VARCHAR(50) | UNIQUE, NOT NULL | Academic/User Identifier |
| `webmail` | VARCHAR(100) | UNIQUE, NOT NULL | Official Communication Email |
| `password` | VARCHAR(255) | NOT NULL | Hashed Credential (Bcrypt) |

### 3.2 Audit Table (`app_logs`)

Captures application telemetry and security events.

| Field | Type | Purpose |
| --- | --- | --- |
| `level` | ENUM | Categorizes logs as INFO, WARNING, ERROR, or SECURITY |
| ``function`` | VARCHAR(100) | Captures the calling function (Uses backticks for reserved keyword) |
| `ip_address` | VARCHAR(45) | Captures the remote user IP for forensic auditing |
| `trace` | JSON | Stores the full PHP stack trace for debugging |

---

## 4. Security Implementation Matrix

### 4.1 SQL Injection (SQLi) Prevention

The system enforces a **"No Raw Queries"** policy. All interactions occur through a centralized `db_query()` function that utilizes **MySQLi Prepared Statements**.

* **Mechanism:** User input is never concatenated into SQL strings. Instead, placeholders (`?`) are used, and values are bound separately.
* **Refinement:** The `db_query` function includes recursion protection to ensure the logger doesn't trigger an infinite loop when logging a database event.

### 4.2 Cross-Site Request Forgery (CSRF) Defense

Every POST request is validated against a unique, cryptographically secure token stored in the user's session.

* **Mechanism:** Tokens are generated using `random_bytes(32)` and compared using `hash_equals()` to prevent timing attacks.

### 4.3 Password Security & Hashing

The system uses the **Blowfish (Bcrypt)** algorithm via PHP’s `password_hash()`.

* **Salt:** Automatically managed by the API, ensuring unique hashes for identical passwords.
* **Cost Factor:** Set to 10, balancing CPU load with protection against offline brute-force attacks.

### 4.4 Hardened PHP Environment (`php.ini`)

The following runtime protections are enabled:

* `session.cookie_httponly = 1`: Prevents JavaScript from accessing session cookies (Mitigates XSS).
* `display_errors = Off`: Prevents leaking sensitive system paths or SQL logic to the end user.
* `session.use_strict_mode = 1`: Prevents session fixation attacks.

---

## 5. Logical Workflow

The registration process follows a strict validation pipeline:

1. **Request Arrival:** Check if the method is `POST`.
2. **CSRF Verification:** Validate the hidden token against `$_SESSION`.
3. **Sanitization:** Trim and filter email formats.
4. **Credential Hashing:** Generate Bcrypt hash for the password.
5. **Database Transaction:** Execute the prepared `INSERT` query.
6. **Audit Logging:** Record the successful registration or failure trace.

---

## 6. Challenges & Resolutions

### 6.1 Reserved Keyword Conflict

**Issue:** Initial SQL scripts failed because `function` is a reserved keyword in MySQL 8.0.
**Resolution:** Updated the schema and the PHP `logger.php` file to wrap the column name in backticks (``function``) to allow valid SQL execution.

### 6.2 Circular Logger Dependencies

**Issue:** `db.php` calls `logger.php` on success, but `logger.php` uses `db.php` to write logs, causing an infinite loop.
**Resolution:** Implemented a `static $in_logger` flag in the logging function to short-circuit any recursive calls during the logging process.

---

## 7. Conclusion

This project successfully implements a secure, scalable, and auditable registration system. By leveraging Docker and modern PHP security practices, the system remains resilient against common OWASP Top 10 vulnerabilities while providing a lightweight footprint suitable for production-like environments.

---
