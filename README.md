# Atlas Money ðŸ¦

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=flat-square&logo=mysql)
![License](https://img.shields.io/badge/License-All%20Rights%20Reserved-red?style=flat-square)

> Atlas Money — a mobile money web application inspired by wave/orange money.  
> Users can create accounts, send money to other users, view transaction history, and manage their balance.  
> Admin panel for fee management and user overview.

---

## Features

- ðŸ” **Authentication** — Login with phone number + password, session management
- ðŸ›¡ï¸ **Brute-force protection** — Account lockout after 3 failed attempts (1-hour suspension)
- ðŸ“ **Registration** — New account creation with duplicate phone/email detection
- ðŸ’¸ **Send Money** — Transfer funds to any registered phone number
- 💰 **Transaction Fees** — 1% fee per transaction, credited to admin account
- ðŸ“Š **Transaction History** — Full history with sender/receiver details and transaction IDs
- ðŸ”‘ **Forgot Password** — Password reset functionality
- ðŸ› ï¸ **Admin Panel** — Fee management and user overview
- ðŸ“ž **Assistance** — Contact/support page

---

## Security

| Feature | Status |
|---------|--------|
| SQL Injection protection (prepared statements) | âœ… `login.php`, `server.php` |
| XSS protection (htmlspecialchars) | âœ… All user-facing outputs |
| Session-based authentication | âœ… |
| Login rate limiting (3 attempts / 1 hour) | âœ… |
| CSRF protection | ⚠ï¸ Not yet implemented |
| Password hashing (bcrypt) | ⚠ï¸ Planned — currently plain text (dev/demo only) |
| HTTPS enforcement | ⚠ï¸ Configure via server (Apache/Nginx) |

> **Note:** This project was built for educational purposes. For production deployment, enable password hashing with `password_hash()` / `password_verify()` and enforce HTTPS.

---

## Project Structure

```
bank.atlas.bk/
├── index.php           # Login page
├── register.php        # Registration form
├── server.php          # Registration handler (prepared statements)
├── login.php           # Login handler (prepared statements)
├── logout.php          # Session destroy
├── atlasmoney.php      # Main dashboard (balance + recent transactions)
├── SendMoney.php       # Money transfer
├── transactionDetail.php # Full transaction history
├── forgetPassword.php  # Password reset
├── AdminPage.php       # Admin dashboard
├── AdminFees.php       # Fee management
├── Assistance.php      # Support page
├── db_connect.php      # Database connection
├── atlasmoney.sql      # Database schema + seed
├── atlas.css           # Main styles
├── atlas.js            # Client-side scripts
└── .github/workflows/  # CI pipeline
```

---

## Database Schema

```sql
-- Main users table
CREATE TABLE atlasin (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100),
    email      VARCHAR(100) UNIQUE,
    phone      VARCHAR(15)  UNIQUE,
    password   VARCHAR(255),
    balance    DECIMAL(15,2) DEFAULT 0.00
);

-- Transactions table
CREATE TABLE transaction (
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    sender             INT,
    receiver           INT,
    amount             DECIMAL(15,2),
    fees               DECIMAL(15,2),
    transaction_number VARCHAR(50),
    timestamp          TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## Setup

### Requirements
- PHP 8.x
- MySQL 8.x
- Apache / Nginx (XAMPP / WAMP for local dev)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/ApollonASM8977/bank.atlas.bk.git

# 2. Import the database
# Open phpMyAdmin or MySQL CLI:
mysql -u root -p < atlasmoney.sql

# 3. Configure the connection
# Edit db_connect.php if needed:
$sname    = "localhost";
$usname   = "root";
$password = "";
$db_name  = "atlasmoney";

# 4. Place in your web server root (e.g. htdocs/ for XAMPP)
# 5. Open http://localhost/bank.atlas.bk/
```

### Default Admin Account
```
Phone    : 0101010101
Password : 1234
```

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | PHP 8.x |
| Database | MySQL 8.x |
| Frontend | HTML5, CSS3, JavaScript |
| Server | Apache (XAMPP/WAMP) |

---

## Author

**Aboubacar Sidick Meite** — [@ApollonASM8977](https://github.com/ApollonASM8977)

---

© 2026 Aboubacar Sidick Meite (ApollonASM8977) — All Rights Reserved

