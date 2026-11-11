# SAPACT - Co-operative Bank & Society Financial Compliance System

**SAPACT** (Statutory Analysis & Performance Audit Compliance Tool) is an enterprise web application designed for **Urban Co-operative Banks, Credit Co-operative Societies (Nagari Patsanstha), and Multi-State Co-operative Societies**.

It streamlines the ingestion of financial statements (Balance Sheets, Profit & Loss Accounts, NPA portfolios) and automates the calculation of statutory ratios, regulatory compliance reports, and audit metrics.

---

## 🌟 Key Features

### 1. Financial Statement Ingestion & Management
- **Balance Sheet Upload & Processing**: Asset and liability categorizations with subcategories.
- **Profit & Loss Account**: Detailed ledger mapping and operational expense tracking.
- **NPA Portfolio Management**: Classification of sub-standard, doubtful, and loss assets.

### 2. Statutory & Regulatory Reports (30+ Reports)
- **CRAR (Capital to Risk-Weighted Assets Ratio)**: Tier-I and Tier-II capital adequacy calculations.
- **CAMELS Rating & Networth**: Capital adequacy, Asset quality, Management, Earnings, Liquidity, and Sensitivity analysis.
- **SLR (Statutory Liquidity Ratio)**: Standard SLR, Multistate after Overdraft (OD), and SLR including Nagari Patsanstha investments.
- **CRR (Cash Reserve Ratio)**: Standard CRR and CRR including Nagari savings and current deposits.
- **CD Ratio**: Credit-to-Deposit ratio tracking monthly and year-end trends.
- **ABR & ALR (Section 89A)**: Average Borrowing Rate (ABR) and Average Lending Rate (ALR) compliance.
- **Cost Analysis**: Cost of funds, yield on advances, and net interest margins.
- **NPA Tracking**: Overdue recovery, old NPA movement, and recovery trend reports.
- **Statutory Compliance**: Section 144 compliance, sick and weak society evaluation, investment exposure limits, and mudat thev (term deposits).

### 3. Executive Dashboard & Analytics
- Visual breakdowns of deposits and loan disbursements.
- Liquid asset ratios, CRR/SLR thresholds, and net worth overview.
- Society and branch-level performance indicators.

---

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+ / 8.x
- **Database**: MySQL / MariaDB (via MySQLi & PDO)
- **Frontend**: Bootstrap 5, MetisMenu, Pace.js, SimpleBar, Lobibox
- **Charts & Visualizations**: Chart.js / ApexCharts
- **Server Environment**: Apache / XAMPP / WampServer / Linux LAMP

---

## 🚀 Installation & Setup

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) or any Apache + MySQL + PHP stack
- PHP 7.4 or higher with `mysqli` and `pdo_mysql` extensions enabled
- MySQL 5.7+ or MariaDB 10.3+

### Steps

1. **Clone the Repository**
   ```bash
   git clone <YOUR_REPOSITORY_URL>
   cd sapact
   ```

2. **Move to Web Directory**
   Move the project folder to your web server document root:
   - XAMPP (Windows): `C:\xampp\htdocs\sapact`
   - LAMP (Linux): `/var/www/html/sapact`

3. **Database Configuration**
   - Create a new MySQL database named `sapact` (or import your existing database dump).
   - Configure database credentials in `connection.php`:
     ```php
     $DB_SERVER = 'localhost';
     $DB_NAME   = 'sapact';
     $username  = 'root';
     $password  = '';
     ```

4. **Launch Application**
   - Start Apache and MySQL services in XAMPP.
   - Open your browser and navigate to:
     ```text
     http://localhost/sapact/login.php
     ```

---

## 🔒 Security Best Practices
- Never commit live production database credentials or passwords to version control.
- Ensure error reporting is disabled in production environments (`display_errors = 0`).
- Use `.gitignore` to prevent sensitive credentials and error logs from being pushed.

---

## 📄 License
This project is proprietary and intended for authorized banking and cooperative institutional audits.
