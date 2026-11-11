<?php
include 'connection.php';

date_default_timezone_set('Asia/Kolkata');

// Get values from GET request safely
$bank_name = mysqli_real_escape_string($link, $_GET['bank_name'] ?? '');
$month = mysqli_real_escape_string($link, $_GET['month'] ?? '');

if (empty($bank_name) || empty($month)) {
    die("Bank name and month are required.");
}

// First, let's create the table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS cd_ratio_end (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    bank_name VARCHAR(255) NOT NULL,
    month_year VARCHAR(255) NOT NULL,
    networth_b DECIMAL(15,2) DEFAULT 0.00,
    a DECIMAL(15,2) DEFAULT 0.00,
    assets DECIMAL(15,2) DEFAULT 0.00,
    invest_in_shares DECIMAL(15,2) DEFAULT 0.00,
    reserve_fund DECIMAL(15,2) DEFAULT 0.00,
    b DECIMAL(15,2) DEFAULT 0.00,
    a_b DECIMAL(15,2) DEFAULT 0.00,
    total_loan DECIMAL(15,2) DEFAULT 0.00,
    total_deposit DECIMAL(15,2) DEFAULT 0.00,
    cd_ratio DECIMAL(10,2) DEFAULT 0.00,
    cd_high_1_perc DECIMAL(15,2) DEFAULT 0.00,
    cd_ratio_minus_65 DECIMAL(10,2) DEFAULT 0.00,
    collected_deposit DECIMAL(15,2) DEFAULT 0.00,
    cd_low_1perc DECIMAL(15,2) DEFAULT 0.00,
    betwn_65_70 DECIMAL(10,2) DEFAULT 0.00,
    loan_destribution DECIMAL(15,2) DEFAULT 0.00,
    earning_cd DECIMAL(10,2) DEFAULT 0.00,
    average_cd DECIMAL(10,2) DEFAULT 0.00,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($link, $create_table_sql)) {
    die("Error creating table: " . mysqli_error($link));
}

// Initialize variables
$networth_b = $a = $assets = $invest_in_shares = $reserve_fund = $b = $a_b = 0;
$total_loan = $total_deposit = $cd_ratio = $cd_high_1_perc = $cd_ratio_minus_65 = 0;
$collected_deposit = $cd_low_1perc = $betwn_65_70 = $loan_destribution = $earning_cd = $average_cd = 0;

// 1. TOTAL NET WORTH - Get from networth_camales table
$sql_networth = "SELECT total_profit FROM networth_camales WHERE bank_name='$bank_name' AND month_year='$month'";
$result_networth = mysqli_query($link, $sql_networth);
if ($result_networth && $row = mysqli_fetch_assoc($result_networth)) {
    $networth_b = $row['total_profit'] ?? 0;
}
$a = $networth_b; // A = TOTAL NET WORTH

// 2. ASSETS - Sum of OWN ASSETS categories
$sql_assets = "SELECT SUM(amount) as total_assets FROM balance_sheet 
               WHERE bank_name='$bank_name' AND month='$month' 
               AND side='Assets' AND category='OWN ASSETS'";
$result_assets = mysqli_query($link, $sql_assets);
if ($result_assets && $row = mysqli_fetch_assoc($result_assets)) {
    $assets = $row['total_assets'] ?? 0;
}

// 3. INVESTMENT IN SHARE - Sum of all share investments
$sql_shares = "SELECT SUM(amount) as total_shares FROM balance_sheet 
               WHERE bank_name='$bank_name' AND month='$month' 
               AND side='Assets' AND category='INVESTMENTS' 
               AND subcategory IN ('Bank Shares', 'Federation Shares', 'Other Shares')";
$result_shares = mysqli_query($link, $sql_shares);
if ($result_shares && $row = mysqli_fetch_assoc($result_shares)) {
    $invest_in_shares = $row['total_shares'] ?? 0;
}

// 4. RESERVE FUND
$sql_reserve = "SELECT SUM(amount) as total_reserve FROM balance_sheet 
                WHERE bank_name='$bank_name' AND month='$month' 
                AND side='Liabilities' AND category='FUNDS AND RESERVES' 
                AND subcategory='RESERVE FUND'";
$result_reserve = mysqli_query($link, $sql_reserve);
if ($result_reserve && $row = mysqli_fetch_assoc($result_reserve)) {
    $reserve_fund = $row['total_reserve'] ?? 0;
}

// 5. B = ASSETS + INVESTMENT IN SHARE + RESERVE FUND
$b = $assets + $invest_in_shares + $reserve_fund;

// 6. A - B
$a_b = $a - $b;

// 7. TOTAL LOAN
$sql_loan = "SELECT SUM(amount) as total_loan FROM balance_sheet 
             WHERE bank_name='$bank_name' AND month='$month' 
             AND side='Assets' AND category='LOANS & ADVANCES'";
$result_loan = mysqli_query($link, $sql_loan);
if ($result_loan && $row = mysqli_fetch_assoc($result_loan)) {
    $total_loan = $row['total_loan'] ?? 0;
}

// 8. TOTAL DEPOSIT
$sql_deposit = "SELECT SUM(amount) as total_deposit FROM balance_sheet 
                WHERE bank_name='$bank_name' AND month='$month' 
                AND side='Liabilities' AND category='DEPOSITS'";
$result_deposit = mysqli_query($link, $sql_deposit);
if ($result_deposit && $row = mysqli_fetch_assoc($result_deposit)) {
    $total_deposit = $row['total_deposit'] ?? 0;
}

// 9. C.D. RATIO = (TOTAL LOAN - (A-B)) * 100 / TOTAL DEPOSIT
if ($total_deposit > 0) {
    $cd_ratio = (($total_loan - $a_b) * 100) / $total_deposit;
}

// 10. If C.D.Ratio High Calculate For 1 % = (A + TOTAL LOAN + (A-B)) / CD_RATIO
if ($cd_ratio > 0) {
    $cd_high_1_perc = ($a + $total_loan + $a_b) / $cd_ratio;
}

// 11. Between 65 to 70 % = CD_RATIO - 65
$cd_ratio_minus_65 = $cd_ratio - 65;

// 12. How Many Deposit COLLECTED = (Between 65 to 70 %) * (If C.D.Ratio High Calculate For 1 %)
$collected_deposit = $cd_ratio_minus_65 * $cd_high_1_perc;

// 13. If C.D.Ratio Low Calculate For 1 % = (A + (A-B) + TOTAL LOAN) / CD_RATIO
if ($cd_ratio > 0) {
    $cd_low_1perc = ($a + $a_b + $total_loan) / $cd_ratio;
}

// 14. Between 65 to 70 % (for low) = 65 - CD_RATIO
$betwn_65_70 = 65 - $cd_ratio;

// 15. How Many Loan Distribution = (Between 65 to 70 %) * (If C.D.Ratio Low Calculate For 1 %)
$loan_destribution = $betwn_65_70 * $cd_low_1perc;

// 16. Earning C.D.Ratio - Using same as CD Ratio for now (as per Excel logic)
$earning_cd = $cd_ratio;

// 17. Average CD Ratio - Calculate average of current and previous months
$sql_avg = "SELECT AVG(cd_ratio) as avg_cd FROM cd_ratio_end 
            WHERE bank_name='$bank_name' 
            AND month_year <= '$month' 
            ORDER BY month_year DESC LIMIT 12";
$result_avg = mysqli_query($link, $sql_avg);
if ($result_avg && $row = mysqli_fetch_assoc($result_avg)) {
    $average_cd = $row['avg_cd'] ?? $cd_ratio;
} else {
    $average_cd = $cd_ratio;
}

// Insert into cd_ratio_end table
$currentDateTime = date('Y-m-d H:i:s');

$sql_insert = "INSERT INTO cd_ratio_end (
    bank_name, month_year, networth_b, a, assets, invest_in_shares, 
    reserve_fund, b, a_b, total_loan, total_deposit, cd_ratio, 
    cd_high_1_perc, cd_ratio_minus_65, collected_deposit, 
    cd_low_1perc, betwn_65_70, loan_destribution, earning_cd, 
    average_cd, created_at
) VALUES (
    '$bank_name', '$month', '$networth_b', '$a', '$assets', 
    '$invest_in_shares', '$reserve_fund', '$b', '$a_b', 
    '$total_loan', '$total_deposit', '$cd_ratio', 
    '$cd_high_1_perc', '$cd_ratio_minus_65', '$collected_deposit', 
    '$cd_low_1perc', '$betwn_65_70', '$loan_destribution', 
    '$earning_cd', '$average_cd', '$currentDateTime'
)";

if (mysqli_query($link, $sql_insert)) {
    // Close connection before redirect
    mysqli_close($link);
    header("Location: cd_month.php");
    exit();
} else {
    echo "Error inserting data: " . mysqli_error($link);
}

// Close connection
mysqli_close($link);
?>