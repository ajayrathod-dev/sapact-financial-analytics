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
} else {
    header("location:logout.php");
    exit;
}

if ($id && $month) {
    // START: Fetch total_fixed_dead_stock sum from balance_sheet
    $sql_total_fixed_dead_stock = "SELECT SUM(amount) AS total_fixed_dead_stock FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Assets' AND category='OWN ASSETS'";
    $result_total_fixed_dead_stock = $link->query($sql_total_fixed_dead_stock);
    $total_fixed_dead_stock = 0;
    if ($result_total_fixed_dead_stock && $row_total_fixed_dead_stock = $result_total_fixed_dead_stock->fetch_assoc()) {
        $total_fixed_dead_stock = $row_total_fixed_dead_stock['total_fixed_dead_stock'] ?? 0;
    }
    // END: Fetch total_fixed_dead_stock sum from balance_sheet

    // START: Fetch total_investment sum from balance_sheet
    $sql_total_investment = "SELECT SUM(amount) AS total_investment FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Assets' AND category='INVESTMENTS'";
    $result_total_investment = $link->query($sql_total_investment);
    $total_investment = 0;
    if ($result_total_investment && $row_total_investment = $result_total_investment->fetch_assoc()) {
        $total_investment = $row_total_investment['total_investment'] ?? 0;
    }
    // END: Fetch total_investment sum from balance_sheet

    // START: Fetch total_deposits sum from balance_sheet
    $sql_total_deposits = "SELECT SUM(amount) AS total_deposits FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Liabilities' AND category='DEPOSITS'";
    $result_total_deposits = $link->query($sql_total_deposits);
    $total_deposits = 0;
    if ($result_total_deposits && $row_total_deposits = $result_total_deposits->fetch_assoc()) {
        $total_deposits = $row_total_deposits['total_deposits'] ?? 0;
    }
    // END: Fetch total_deposits sum from balance_sheet

    // START: Fetch CRR data from crr_report
    $sql_crr = "SELECT total_crr_assets, last_qtr_deposit_total, crr_percent, min_req_percent FROM crr_report WHERE bank_name='$id' AND month_year='$month' LIMIT 1";
    $result_crr = $link->query($sql_crr);
    $actual_crr = 0;
    $last_qtr_deposit_total = $total_deposits; // Fallback to current if not available
    $crr_percentage = 0;
    $min_req_percent = 1.00;
    if ($result_crr && $row_crr = $result_crr->fetch_assoc()) {
        $actual_crr = $row_crr['total_crr_assets'] ?? 0;
        $last_qtr_deposit_total = $row_crr['last_qtr_deposit_total'] ?? $total_deposits;
        $crr_percentage = $row_crr['crr_percent'] ?? 0;
        $min_req_percent = $row_crr['min_req_percent'] ?? 1.00;
    }
    // END: Fetch CRR data from crr_report

    $required_crr = ($last_qtr_deposit_total * $min_req_percent / 100);

    // START: Fetch SLR data from slr_report
    $sql_slr = "SELECT amt_total AS actual_slr, last_qtr_deposit_total, slr_proof AS slr_percentage, min_req_percent FROM slr_report WHERE bank_name='$id' AND month_year='$month' LIMIT 1";
    $result_slr = $link->query($sql_slr);
    $actual_slr = $total_investment; // Fallback
    $slr_percentage = 0;
    $min_req_slr_percent = 25.00;
    $slr_last_qtr_deposit = $total_deposits; // Fallback
    if ($result_slr && $row_slr = $result_slr->fetch_assoc()) {
        $actual_slr = $row_slr['actual_slr'] ?? $total_investment;
        $slr_last_qtr_deposit = $row_slr['last_qtr_deposit_total'] ?? $total_deposits;
        $slr_percentage = $row_slr['slr_percentage'] ?? 0;
        $min_req_slr_percent = $row_slr['min_req_percent'] ?? 25.00;
    }
    // END: Fetch SLR data from slr_report

    $required_slr = ($slr_last_qtr_deposit * $min_req_slr_percent / 100);

    // Placeholder values (extend if data available)
    $npa_investment_crr = 0;
    $npa_investment_slr = 0;

    // START: Fetch interest_receivable sum from balance_sheet
    $sql_interest_receivable = "SELECT SUM(amount) AS interest_receivable FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Assets' AND category='INT.RECEIVABLE ON INVESTMENT & ADVANCES'";
    $result_interest_receivable = $link->query($sql_interest_receivable);
    $interest_receivable = 0;
    if ($result_interest_receivable && $row_interest_receivable = $result_interest_receivable->fetch_assoc()) {
        $interest_receivable = $row_interest_receivable['interest_receivable'] ?? 0;
    }
    // END: Fetch interest_receivable sum from balance_sheet

    // START: Fetch thirty_percent_investment_limit sum from balance_sheet
    $sql_thirty_percent_investment_limit = "SELECT SUM(amount) AS thirty_percent_investment_limit FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND (subcategory LIKE '%Shares%' OR subcategory LIKE '%Investment%')";
    $result_thirty_percent_investment_limit = $link->query($sql_thirty_percent_investment_limit);
    $thirty_percent_investment_limit = 0;
    if ($result_thirty_percent_investment_limit && $row_thirty_percent_investment_limit = $result_thirty_percent_investment_limit->fetch_assoc()) {
        $thirty_percent_investment_limit = $row_thirty_percent_investment_limit['thirty_percent_investment_limit'] ?? 0;
    }
    // END: Fetch thirty_percent_investment_limit sum from balance_sheet

    // Always INSERT new record
    $sql = "INSERT INTO investment_fix_dead_stock (
                bank_name, month, total_fixed_dead_stock, total_investment, 
                required_crr, actual_crr, crr_percentage, 
                required_slr, actual_slr, slr_percentage, 
                npa_investment_crr, npa_investment_slr, interest_receivable, 
                thirty_percent_investment_limit, created_at
            ) VALUES (
                '$id', '$month', '$total_fixed_dead_stock', '$total_investment', 
                '$required_crr', '$actual_crr', '$crr_percentage', 
                '$required_slr', '$actual_slr', '$slr_percentage', 
                '$npa_investment_crr', '$npa_investment_slr', '$interest_receivable', 
                '$thirty_percent_investment_limit', NOW()
            )";

    if ($link->query($sql) === TRUE) {
        $p = "Investment & Dead Stock Data Saved Successfully";
        header("Location: Investment_dead.php?bank_name=$id&month=$month&success=" . urlencode($p));
        exit;
    } else {
        $p = "Data Not Saved: " . $link->error;
        header("Location: Investment_dead.php?bank_name=$id&month=$month&error=" . urlencode($p));
        exit;
    }
}
?>