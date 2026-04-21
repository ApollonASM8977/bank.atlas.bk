# Atlas Money ðŸ¦

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=flat-square&logo=mysql)
![License](https://img.shields.io/badge/License-All%20Rights%20Reserved-red?style=flat-square)

> Atlas Money â€” a mobile money web application inspired by wave/orange money.  
> Users can create accounts, send money to other users, view transaction history, and manage their balance.  
> Admin panel for fee management and user overview.

---

## Features

- ðŸ” **Authentication** â€” Login with phone number + password, session management
- ðŸ›¡ï¸ **Brute-force protection** â€” Account lockout after 3 failed attempts (1-hour suspension)
- ðŸ“ **Registration** â€” New account creation with duplicate phone/email detection
- ðŸ’¸ **Send Money** â€” Transfer funds to any registered phone number
- ðŸ’° **Transaction Fees** â€” 1% fee per transaction, credited to admin account
- ðŸ“Š **Transaction History** â€” Full history with sender/receiver details and transaction IDs
- ðŸ”‘ **Forgot Password** â€” Password reset functionality
- ðŸ› ï¸ **Admin Panel** â€” Fee management and user overview
- ðŸ“ž **Assistance** â€” Contact/support page

---

## Security

| Feature | Status |
|---------|--------|
| SQL Injection protection (prepared statements) | âœ… `login.php`, `server.php` |
| XSS protection (htmlspecialchars) | âœ… All user-facing outputs |
| Session-based authentication | âœ… |
| Login rate limiting (3 attempts / 1 hour) | âœ… |
| CSRF protection | âš ï¸ Not yet implemented |
| Password hashing (bcrypt) | âš ï¸ Planned â€” currently plain text (dev/demo only) |
| HTTPS enforcement | âš ï¸ Configure via server (Apache/Nginx) |

> **Note:** This project was built for educational purposes. For production deployment, enable password hashing with `password_hash()` / `password_verify()` and enforce HTTPS.

---

## Project Structure

```
bank.atlas.bk/
â”œâ”€â”€ index.php           # Login page
â”œâ”€â”€ register.php        # Registration form
â”œâ”€â”€ server.php          # Registration handler (prepared statements)
â”œâ”€â”€ login.php           # Login handler (prepared statements)
â”œâ”€â”€ logout.php          # Session destroy
â”œâ”€â”€ atlasmoney.php      # Main dashboard (balance + recent transactions)
â”œâ”€â”€ SendMoney.php       # Money transfer
â”œâ”€â”€ transactionDetail.php # Full transaction history
â”œâ”€â”€ forgetPassword.php  # Password reset
â”œâ”€â”€ AdminPage.php       # Admin dashboard
â”œâ”€â”€ AdminFees.php       # Fee management
â”œâ”€â”€ Assistance.php      # Support page
â”œâ”€â”€ db_connect.php      # Database connection
â”œâ”€â”€ atlasmoney.sql      # Database schema + seed
â”œâ”€â”€ atlas.css           # Main styles
â”œâ”€â”€ atlas.js            # Client-side scripts
â””â”€â”€ .github/workflows/  # CI pipeline
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

**Aboubacar Sidick Meite** â€” [@ApollonASM8977](https://github.com/ApollonASM8977)

---

Â© 2026 Aboubacar Sidick Meite (ApollonASM8977) â€” All Rights Reserved

