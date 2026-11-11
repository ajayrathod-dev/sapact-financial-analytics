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
    
    // START: Fetch FD in DCCB (DCC/Co-op/Nationalise) sum from balance_sheet
    $sql_fd_in_dccb = "SELECT SUM(amount) AS amount FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND (subcategory='DCC Bank Investment' OR subcategory='Co-operative Bank Investment' OR subcategory='Nationalise Bank Investment')";
    $result_fd_in_dccb = $link->query($sql_fd_in_dccb);
    $fd_in_dccb = 0;
    if ($result_fd_in_dccb && $row_fd_in_dccb = $result_fd_in_dccb->fetch_assoc()) {
        $fd_in_dccb = $row_fd_in_dccb['amount'] ?? 0;
    }
    // END: Fetch FD in DCCB (DCC/Co-op/Nationalise) sum from balance_sheet
    
    // START: Fetch Urban Cooperative Bank Investments sum from balance_sheet
    $sql_amt_urbanc = "SELECT SUM(amount) AS amount FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND subcategory LIKE '%Urban Co-operative%'";
    $result_amt_urbanc = $link->query($sql_amt_urbanc);
    $amt_urbanc = 0;
    if ($result_amt_urbanc && $row_amt_urbanc = $result_amt_urbanc->fetch_assoc()) {
        $amt_urbanc = $row_amt_urbanc['amount'] ?? 0;
    }
    // END: Fetch Urban Cooperative Bank Investments sum from balance_sheet
    
    // START: Fetch Private Bank Investment sum from balance_sheet
    $sql_fund1 = "SELECT SUM(amount) AS share_invs FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND subcategory='Private Bank'";
    $result_fund1 = $link->query($sql_fund1);
    $fund1 = 0;
    if ($result_fund1 && $row_fund1 = $result_fund1->fetch_assoc()) {
        $fund1 = $row_fund1['share_invs'] ?? 0;
    }
    // END: Fetch Private Bank Investment sum from balance_sheet
    
    // START: Fetch NPA Bank Investment sum from balance_sheet
    $sql_fund2 = "SELECT SUM(amount) AS share_invs FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND subcategory='NPA Bank'";
    $result_fund2 = $link->query($sql_fund2);
    $fund2 = 0;
    if ($result_fund2 && $row_fund2 = $result_fund2->fetch_assoc()) {
        $fund2 = $row_fund2['share_invs'] ?? 0;
    }
    // END: Fetch NPA Bank Investment sum from balance_sheet
    
    // START: Fetch Other Bank Investment sum from balance_sheet
    $sql_fund3 = "SELECT SUM(amount) AS share_invs FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND subcategory='Other Bank'";
    $result_fund3 = $link->query($sql_fund3);
    $fund3 = 0;
    if ($result_fund3 && $row_fund3 = $result_fund3->fetch_assoc()) {
        $fund3 = $row_fund3['share_invs'] ?? 0;
    }
    // END: Fetch Other Bank Investment sum from balance_sheet
    
    $amt_term_dp_nat = $fund1 + $fund2 + $fund3;
    
    // START: Fetch Government General Ordinance Investments sum from balance_sheet
    $sql_invest_govt_gen = "SELECT SUM(amount) AS amount FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND (subcategory LIKE '%Government%' OR subcategory LIKE '%Ordinance%')";
    $result_invest_govt_gen = $link->query($sql_invest_govt_gen);
    $invest_govt_gen = 0;
    if ($result_invest_govt_gen && $row_invest_govt_gen = $result_invest_govt_gen->fetch_assoc()) {
        $invest_govt_gen = $row_invest_govt_gen['amount'] ?? 0;
    }
    // END: Fetch Government General Ordinance Investments sum from balance_sheet
    
    // START: Fetch Multistate and Nagari Patsanstha Investments sum from balance_sheet
    $sql_fd_multistate = "SELECT SUM(amount) AS amount FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND (subcategory LIKE '%other%' OR subcategory LIKE '%Private Bank Investment%' OR subcategory LIKE '%Other Investment%' OR subcategory LIKE '%Small Finance Bank Investment%' OR subcategory LIKE '%Mutual Fund Investment%' OR subcategory LIKE '%Nagari & Multistate Society Investment%' OR subcategory LIKE '%Other Investment%')";
    $result_fd_multistate = $link->query($sql_fd_multistate);
    $fd_multistate = 0;
    if ($result_fd_multistate && $row_fd_multistate = $result_fd_multistate->fetch_assoc()) {
        $fd_multistate = $row_fd_multistate['amount'] ?? 0;
    }
    // END: Fetch Multistate and Nagari Patsanstha Investments sum from balance_sheet
    
    // START: Fetch OD Amount (Borrowings) sum from balance_sheet
    $sql_od_amount = "SELECT SUM(amount) AS od_amount FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Liabilities' AND category='BORROWINGS'";
    $result_od_amount = $link->query($sql_od_amount);
    $od_amount = 0;
    if ($result_od_amount && $row_od_amount = $result_od_amount->fetch_assoc()) {
        $od_amount = $row_od_amount['od_amount'] ?? 0;
    }
    // END: Fetch OD Amount (Borrowings) sum from balance_sheet
    
    // Calculations (simple arithmetic post-fetch)
    $amt_total = $fd_in_scb + $fd_in_dccb + $amt_urbanc + $amt_term_dp_nat + $invest_govt_gen + $fd_multistate;
    $total_after_od = $amt_total - $od_amount;
    $min_req_percent = 25.00;
    $min_req_qty = $last_qtr_deposit_total * ($min_req_percent / 100);
    $slr_proof = ($last_qtr_deposit_total > 0) ? ($amt_total / $last_qtr_deposit_total) * 100 : 0;
    $slr_proof_after_od = ($last_qtr_deposit_total > 0) ? ($total_after_od / $last_qtr_deposit_total) * 100 : 0;
    $deficit_surplus = $amt_total - $min_req_qty;
    $deficit_surplus_after_od = $total_after_od - $min_req_qty;
    $deficit_surplus_percent = ($min_req_qty > 0) ? ($deficit_surplus / $min_req_qty) * 100 : 0;
    $deficit_surplus_after_od_percent = ($min_req_qty > 0) ? ($deficit_surplus_after_od / $min_req_qty) * 100 : 0;
    $slr_proof = round($slr_proof, 2);
    $slr_proof_after_od = round($slr_proof_after_od, 2);
    $deficit_surplus_percent = round($deficit_surplus_percent, 2);
    $deficit_surplus_after_od_percent = round($deficit_surplus_after_od_percent, 2);
    
    // Always INSERT for logging (no UPDATE to preserve history)
    $sql = "INSERT INTO slr_report_multistate (
                bank_name, month_year, fd_in_scb, fd_in_dccb, amt_urbanc, 
                amt_term_dp_nat, invest_govt_gen, fd_multistate, amt_total, 
                last_qtr_deposit_total, slr_proof, min_req_percent, min_req_qty, 
                deficit_surplus_percent, deficit_surplus, od_amount, total_after_od,
                slr_proof_after_od, deficit_surplus_after_od_percent, deficit_surplus_after_od
            ) VALUES (
                '$bankname', '$month', '$fd_in_scb', '$fd_in_dccb', '$amt_urbanc',
                '$amt_term_dp_nat', '$invest_govt_gen', '$fd_multistate', '$amt_total',
                '$last_qtr_deposit_total', '$slr_proof', '$min_req_percent', '$min_req_qty',
                '$deficit_surplus_percent', '$deficit_surplus', '$od_amount', '$total_after_od',
                '$slr_proof_after_od', '$deficit_surplus_after_od_percent', '$deficit_surplus_after_od'
            )";

    if ($link->query($sql) === TRUE) {
        $p = "SLR Multistate Report Generated Successfully";
        header("Location: slr_multistate_report.php?bank_name=$bankname&month=$month&success=" . urlencode($p));
        exit;
    } else {
        $p = "Error Generating SLR Multistate Report: " . $link->error;
        header("Location: slr_multistate_report.php?bank_name=$bankname&month=$month&error=" . urlencode($p));
        exit;
    }
}
$link->close();
?>