<?php
include 'connection.php';

// Get values from GET request safely
$bankname = $link->real_escape_string($_GET['bank_name'] ?? '');
$month    = $link->real_escape_string($_GET['month'] ?? '');

// Helper: Calculate previous quarter month (e.g., 2025-03 -> 2024-12)
$prev_quarter_month = date('Y-m', strtotime($month . ' -3 months'));

if ($bankname && $month) {
    // START: Fetch current total deposits from balance_sheet
    $sql_total_deposits = "SELECT SUM(amount) AS total_deposits FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Liabilities' AND category='DEPOSITS'";
    $result_total_deposits = $link->query($sql_total_deposits);
    $total_deposits = 0;
    if ($result_total_deposits && $row_total_deposits = $result_total_deposits->fetch_assoc()) {
        $total_deposits = $row_total_deposits['total_deposits'] ?? 0;
    }
    // END: Fetch current total deposits from balance_sheet
    
    // START: Fetch last quarter total deposits from balance_sheet
    $sql_last_qtr_deposits = "SELECT SUM(amount) AS total_deposits FROM balance_sheet WHERE bank_name='$bankname' AND month='$prev_quarter_month' AND side='Liabilities' AND category='DEPOSITS'";
    $result_last_qtr_deposits = $link->query($sql_last_qtr_deposits);
    $last_qtr_deposit_total = 0;
    if ($result_last_qtr_deposits && $row_last_qtr_deposits = $result_last_qtr_deposits->fetch_assoc()) {
        $last_qtr_deposit_total = $row_last_qtr_deposits['total_deposits'] ?? 0;
    }
    // END: Fetch last quarter total deposits from balance_sheet
    
    // START: Fetch FD in SCB sum from balance_sheet
    $sql_fd_in_scb = "SELECT SUM(amount) AS fd_in_scb FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND subcategory=' State Co-operative Bank Investment'";
    $result_fd_in_scb = $link->query($sql_fd_in_scb);
    $fd_in_scb = 0;
    if ($result_fd_in_scb && $row_fd_in_scb = $result_fd_in_scb->fetch_assoc()) {
        $fd_in_scb = $row_fd_in_scb['fd_in_scb'] ?? 0;
    }
    // END: Fetch FD in SCB sum from balance_sheet
    
    // START: Fetch DCC Bank Investment sum from balance_sheet
    $sql_fund1 = "SELECT SUM(amount) AS amount FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND subcategory='DCC Bank Investment'";
    $result_fund1 = $link->query($sql_fund1);
    $fund1 = 0;
    if ($result_fund1 && $row_fund1 = $result_fund1->fetch_assoc()) {
        $fund1 = $row_fund1['amount'] ?? 0;
    }
    // END: Fetch DCC Bank Investment sum from balance_sheet
    
    // START: Fetch Co-operative Bank Investment sum from balance_sheet
    $sql_fund2 = "SELECT SUM(amount) AS amount FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND subcategory='Co-operative Bank Investment'";
    $result_fund2 = $link->query($sql_fund2);
    $fund2 = 0;
    if ($result_fund2 && $row_fund2 = $result_fund2->fetch_assoc()) {
        $fund2 = $row_fund2['amount'] ?? 0;
    }
    // END: Fetch Co-operative Bank Investment sum from balance_sheet
    
    // START: Fetch Nationalise Bank Investment sum from balance_sheet
    $sql_fund3 = "SELECT SUM(amount) AS amount FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND subcategory='Nationalise Bank Investment'";
    $result_fund3 = $link->query($sql_fund3);
    $fund3 = 0;
    if ($result_fund3 && $row_fund3 = $result_fund3->fetch_assoc()) {
        $fund3 = $row_fund3['amount'] ?? 0;
    }
    // END: Fetch Nationalise Bank Investment sum from balance_sheet
    
    $fd_in_dccb = $fund1 + $fund2 + $fund3;
    
    // START: Fetch Urban Cooperative Bank Investments sum from balance_sheet
    $sql_amt_urbanc_ = "SELECT SUM(amount) AS amount FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND (subcategory LIKE '%Urban Co-operative%' OR subcategory LIKE '%Urban Cooperative%')";
    $result_amt_urbanc_ = $link->query($sql_amt_urbanc_);
    $amt_urbanc_ = 0;
    if ($result_amt_urbanc_ && $row_amt_urbanc_ = $result_amt_urbanc_->fetch_assoc()) {
        $amt_urbanc_ = $row_amt_urbanc_['amount'] ?? 0;
    }
    // END: Fetch Urban Cooperative Bank Investments sum from balance_sheet
    
    // START: Fetch Term Deposits in Nationalized Banks sum from balance_sheet
    $sql_amt_term_dp_nat = "SELECT SUM(amount) AS amount FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND (subcategory LIKE '%Term Deposit%' OR subcategory LIKE '%Nationalized Bank%')";
    $result_amt_term_dp_nat = $link->query($sql_amt_term_dp_nat);
    $amt_term_dp_nat = 0;
    if ($result_amt_term_dp_nat && $row_amt_term_dp_nat = $result_amt_term_dp_nat->fetch_assoc()) {
        $amt_term_dp_nat = $row_amt_term_dp_nat['amount'] ?? 0;
    }
    // END: Fetch Term Deposits in Nationalized Banks sum from balance_sheet
    
    // START: Fetch Government General Ordinance Investments sum from balance_sheet
    $sql_invest_govt_gen = "SELECT SUM(amount) AS amount FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND (subcategory LIKE '%Government%' OR subcategory LIKE '%Ordinance%' OR subcategory LIKE '%Govt%')";
    $result_invest_govt_gen = $link->query($sql_invest_govt_gen);
    $invest_govt_gen = 0;
    if ($result_invest_govt_gen && $row_invest_govt_gen = $result_invest_govt_gen->fetch_assoc()) {
        $invest_govt_gen = $row_invest_govt_gen['amount'] ?? 0;
    }
    // END: Fetch Government General Ordinance Investments sum from balance_sheet
    
    // Calculations (simple arithmetic post-fetch)
    $amt_total = $fd_in_scb + $fd_in_dccb + $amt_urbanc_ + $amt_term_dp_nat + $invest_govt_gen;
    $min_req_percent = 25.00;
    $slr_proof = ($total_deposits > 0) ? ($amt_total / $total_deposits) * 100 : 0;
    $deficit_surplus_percent = $slr_proof - $min_req_percent;
    $deficit_surplus_amt = ($total_deposits > 0) ? ($total_deposits * $deficit_surplus_percent) / 100 : 0;
    $slr_proof = round($slr_proof, 2);
    $deficit_surplus_percent = round($deficit_surplus_percent, 2);
    $deficit_surplus_amt = round($deficit_surplus_amt, 2);
    
    // Always INSERT for logging (no UPDATE to preserve history)
    $sql = "INSERT INTO slr_report (
                bank_name, month_year, last_qtr_deposit_total, fd_in_scb, 
                fd_in_dccb, amt_urbanc_, amt_term_dp_nat, invest_govt_gen_ord, 
                amt_total, total_deposits, slr_proof, min_req_percent,
                deficit_surplus_percent, deficit_surplus
            ) VALUES (
                '$bankname', '$month', '$last_qtr_deposit_total', '$fd_in_scb', 
                '$fd_in_dccb', '$amt_urbanc_', '$amt_term_dp_nat', '$invest_govt_gen',
                '$amt_total', '$total_deposits', '$slr_proof', '$min_req_percent',
                '$deficit_surplus_percent', '$deficit_surplus_amt'
            )";

    if ($link->query($sql) === TRUE) {
        $p = "SLR Report Generated Successfully";
        header("Location: slr_report.php?success=" . urlencode($p));
        exit;
    } else {
        $p = "Error Generating SLR Report: " . $link->error;
        header("Location: slr_report.php?error=" . urlencode($p));
        exit;
    }
}
?>