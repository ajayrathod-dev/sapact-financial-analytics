<?php
include('connection.php');  

$id    = $_GET['bank_name'] ?? '';
$month = $_GET['month'] ?? '';

$u_uname = '';
if (isset($_COOKIE['uuid'])) {
    $u_uname = $link->real_escape_string($_COOKIE['uuid']);
}

$sql4 = "SELECT * FROM ods WHERE uuid='$u_uname' AND status='verified'";
$result4 = $link->query($sql4);

if ($result4->num_rows > 0) {
    $row4 = $result4->fetch_assoc();
    $u_uname = $link->real_escape_string($row4['user_id']);
    $br_id = $link->real_escape_string($row4['user_id']);
} else {
    header("location:logout.php");
    exit;
}

// START: Fetch cd_ratio from cd_ratio
$sql_cd_ratio = "SELECT cd_ratio FROM cd_ratio WHERE bank_name='$id' AND month='$month' ORDER BY id DESC LIMIT 1";
$result_cd_ratio = $link->query($sql_cd_ratio);
$cd_ratio = 0;
if ($result_cd_ratio && $row_cd_ratio = $result_cd_ratio->fetch_assoc()) {
    $cd_ratio = $row_cd_ratio['cd_ratio'] ?? 0;
}
// END: Fetch cd_ratio from cd_ratio

// TODO: Review overdue_percent - hardcoded to 0, no query in original
$overdue_percent = 0;

// START: Fetch total deposits last month from balance_sheet
$last_month = date('Y-m', strtotime($month . ' -1 month'));
$sql_total_deposits_last_month = "SELECT SUM(amount) AS total_deposits FROM balance_sheet WHERE bank_name='$id' AND month='$last_month' AND side='Liabilities' AND category='DEPOSITS'";
$result_total_deposits_last_month = $link->query($sql_total_deposits_last_month);
$total_deposits_last_month = 0;
if ($result_total_deposits_last_month && $row_total_deposits_last_month = $result_total_deposits_last_month->fetch_assoc()) {
    $total_deposits_last_month = $row_total_deposits_last_month['total_deposits'] ?? 0;
}
// END: Fetch total deposits last month from balance_sheet

// START: Fetch total deposits last quarter from balance_sheet
$month_num = date('n', strtotime($month));
$year = date('Y', strtotime($month));

if ($month_num >= 1 && $month_num <= 3) {
    $last_quarter_month = ($year - 1) . '-12';
} elseif ($month_num >= 4 && $month_num <= 6) {
    $last_quarter_month = $year . '-03';
} elseif ($month_num >= 7 && $month_num <= 9) {
    $last_quarter_month = $year . '-06';
} else {
    $last_quarter_month = $year . '-09';
}

$sql_total_deposits_last_quarter = "SELECT SUM(amount) AS total_deposits FROM balance_sheet WHERE bank_name='$id' AND month='$last_quarter_month' AND side='Liabilities' AND category='DEPOSITS'";
$result_total_deposits_last_quarter = $link->query($sql_total_deposits_last_quarter);
$total_deposits_last_quarter = 0;
if ($result_total_deposits_last_quarter && $row_total_deposits_last_quarter = $result_total_deposits_last_quarter->fetch_assoc()) {
    $total_deposits_last_quarter = $row_total_deposits_last_quarter['total_deposits'] ?? 0;
}
// END: Fetch total deposits last quarter from balance_sheet

// START: Fetch last year December deposits from balance_sheet for cash_reserve_required
$last_year = $year - 1;
$last_year_dec = $last_year . '-12';
$sql_last_year_dec_deposits = "SELECT SUM(amount) AS total_deposits FROM balance_sheet WHERE bank_name='$id' AND month='$last_year_dec' AND side='Liabilities' AND category='DEPOSITS'";
$result_last_year_dec_deposits = $link->query($sql_last_year_dec_deposits);
$last_year_dec_deposits = 0;
if ($result_last_year_dec_deposits && $row_last_year_dec_deposits = $result_last_year_dec_deposits->fetch_assoc()) {
    $last_year_dec_deposits = $row_last_year_dec_deposits['total_deposits'] ?? 0;
}
$cash_reserve_required = $last_year_dec_deposits * 0.01;
// END: Fetch last year December deposits from balance_sheet for cash_reserve_required

// START: Fetch CASH AT BANK sum from balance_sheet
$sql_inbank = "SELECT SUM(amount) AS cash_reserve FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Assets' AND category='CASH AT BANK'";
$result_inbank = $link->query($sql_inbank);
$inbank = 0;
if ($result_inbank && $row_inbank = $result_inbank->fetch_assoc()) {
    $inbank = $row_inbank['cash_reserve'] ?? 0;
}
// END: Fetch CASH AT BANK sum from balance_sheet

// START: Fetch CASH IN HAND sum from balance_sheet
$sql_inhand = "SELECT SUM(amount) AS cash_reserve FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Assets' AND category='CASH IN HAND'";
$result_inhand = $link->query($sql_inhand);
$inhand = 0;
if ($result_inhand && $row_inhand = $result_inhand->fetch_assoc()) {
    $inhand = $row_inhand['cash_reserve'] ?? 0;
}
// END: Fetch CASH IN HAND sum from balance_sheet

$cash_reserve_maintained = $inbank + $inhand;
$cash_reserve_percent = ($cash_reserve_required > 0) ? ($cash_reserve_maintained / $cash_reserve_required) * 100 : 0;

// START: Fetch last year December deposits from balance_sheet for statutory_liquid_required
// (Reusing the earlier query for last_year_dec_deposits)
$statutory_liquid_required = $last_year_dec_deposits * 0.25;
// END: Fetch last year December deposits from balance_sheet for statutory_liquid_required

// START: Fetch Co-operative Bank Investment sum from balance_sheet
$sql_inv1 = "SELECT SUM(amount) AS statutory_liquid FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND subcategory='Co-operative Bank Investment'";
$result_inv1 = $link->query($sql_inv1);
$inv1 = 0;
if ($result_inv1 && $row_inv1 = $result_inv1->fetch_assoc()) {
    $inv1 = $row_inv1['statutory_liquid'] ?? 0;
}
// END: Fetch Co-operative Bank Investment sum from balance_sheet

// START: Fetch DCC Bank Investment sum from balance_sheet
$sql_inv2 = "SELECT SUM(amount) AS statutory_liquid FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND subcategory='DCC Bank Investment'";
$result_inv2 = $link->query($sql_inv2);
$inv2 = 0;
if ($result_inv2 && $row_inv2 = $result_inv2->fetch_assoc()) {
    $inv2 = $row_inv2['statutory_liquid'] ?? 0;
}
// END: Fetch DCC Bank Investment sum from balance_sheet

$statutory_liquid_maintained = $inv1 + $inv2;
$statutory_liquid_percent = ($statutory_liquid_required > 0) ? ($statutory_liquid_maintained / $statutory_liquid_required) * 100 : 0;

// START: Fetch CURRENT YEAR PROFIT sum from balance_sheet
$sql_profit = "SELECT SUM(amount) AS profit FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Liabilities' AND category='PROFIT AND LOSS' AND subcategory='CURRENT YEAR PROFIT'";
$result_profit = $link->query($sql_profit);
$profit = 0;
if ($result_profit && $row_profit = $result_profit->fetch_assoc()) {
    $profit = $row_profit['profit'] ?? 0;
}
// END: Fetch CURRENT YEAR PROFIT sum from balance_sheet

// START: Fetch CURRENT YEAR loss sum from balance_sheet
$sql_loss = "SELECT SUM(amount) AS loss FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Assets' AND category='LOSS' AND subcategory='CURRENT YEAR'";
$result_loss = $link->query($sql_loss);
$loss = 0;
if ($result_loss && $row_loss = $result_loss->fetch_assoc()) {
    $loss = $row_loss['loss'] ?? 0;
}
// END: Fetch CURRENT YEAR loss sum from balance_sheet
// ISSUE: No 'LOSS' category in sample balance_sheet data

// Calculations (simple arithmetic post-fetch)
$cd_ratio = round($cd_ratio, 2);
$overdue_percent = round($overdue_percent, 2);
$cash_reserve_percent = round($cash_reserve_percent, 2);
$statutory_liquid_percent = round($statutory_liquid_percent, 2);

$sql = "INSERT INTO mis_monthly_report(
            bank_name, month_year, cd_ratio, overdue_percent, 
            total_deposits_last_month, total_deposits_last_quarter,
            cash_reserve_required, cash_reserve_maintained, cash_reserve_percent,
            statutory_liquid_required, statutory_liquid_maintained, statutory_liquid_percent,
            profit, loss
        ) VALUES (
            '$id', '$month', '$cd_ratio', '$overdue_percent',
            '$total_deposits_last_month', '$total_deposits_last_quarter',
            '$cash_reserve_required', '$cash_reserve_maintained', '$cash_reserve_percent',
            '$statutory_liquid_required', '$statutory_liquid_maintained', '$statutory_liquid_percent',
            '$profit', '$loss'
        )";

if ($link->query($sql) === TRUE) {
    $p = "Data Saved Successfully";
    header("Location: mis_monthly.php?bank_name=$id&month=$month&success=" . urlencode($p));
    exit;
} else {
    $p = "Data Not Saved: " . $link->error;
    header("Location: mis_monthly.php?bank_name=$id&month=$month&error=" . urlencode($p));
    exit;
}
?>