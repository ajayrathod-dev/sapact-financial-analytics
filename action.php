<?php
include 'connection.php';

// Get values from GET request safely
$bankname = $link->real_escape_string($_GET['bank_name'] ?? '');
$month    = $link->real_escape_string($_GET['month'] ?? '');

// Initialize variables with default values
$self_fund = 0;
$real_state_n_movable_property = 0;
$share_inv = 0;
$reserve_fund = 0;
$net_worth = 0;
$total_debts = 0;
$total_deposits = 0;

// Get self fund
$sql = "SELECT * FROM networth_camales WHERE bank_name='$bankname' AND month_year='$month'";
$result = $link->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $self_fund = $row["total_profit"] ?? 0;
}

// Real estate and movable
$sql = "SELECT SUM(amount) AS realmoveableassets 
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='OWN ASSETS'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $real_state_n_movable_property = $row["realmoveableassets"] ?? 0;
}

// shares investment start
$fund1 = 0;
$fund2 = 0;
$fund3 = 0;

$sql = "SELECT SUM(amount) AS share_invs 
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='INVESTMENTS' AND subcategory='Bank Shares'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $fund1 = $row["share_invs"] ?? 0;
}

$sql = "SELECT SUM(amount) AS share_invs 
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='INVESTMENTS' AND subcategory='Other Shares'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $fund2 = $row["share_invs"] ?? 0;
}

$sql = "SELECT SUM(amount) AS share_invs
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='INVESTMENTS' AND subcategory='Federation Shares'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $fund3 = $row["share_invs"] ?? 0;
}

$share_inv = $fund1 + $fund2 + $fund3;

// Reserve fund start 
$sql = "SELECT SUM(amount) AS reserve_fund
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='FUNDS AND RESERVES' AND subcategory='RESERVE FUND' ";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $reserve_fund = $row["reserve_fund"] ?? 0;
}

// Net worth start 
$net_worth = $reserve_fund + $self_fund + $real_state_n_movable_property + $share_inv;

// total debts start
$sql = "SELECT SUM(amount) AS debts
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='LOANS & ADVANCES'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $total_debts = $row["debts"] ?? 0;
}

// total deposits begins
$sql = "SELECT SUM(amount) AS deposits
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='DEPOSITS'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $total_deposits = $row["deposits"] ?? 0;
}
 
// cd ratio begins 
$cd_ratio = $total_deposits > 0 ? ($total_debts/$total_deposits*100) : 0;

// average cd ratio begins
$avg_cd_ratio = $cd_ratio/12;

// Insert into cd_ratio with correct column order
$sql = "INSERT INTO `cd_ratio`(
    `bank_name`, 
    `month`, 
    `self_fund`, 
    `real_state_n_movable_property`,
    `investment_in_shares`, 
    `reserve_fund`,
    `net_worth`, 
    `total_debt`, 
    `total_deposits`, 
    `cd_ratio`, 
    `avg_cd_ratio`,
    `created_at`
) VALUES (
    '$bankname', 
    '$month', 
    '$self_fund', 
    '$real_state_n_movable_property', 
    '$share_inv', 
    '$reserve_fund', 
    '$net_worth', 
    '$total_debts', 
    '$total_deposits',
    '$cd_ratio',
    '$avg_cd_ratio',
    NOW()
)";

if ($link->query($sql) === TRUE) {
    header("Location: CD_Ratio.php");
    exit();
} else {
    echo "Error: " . $link->error;
}