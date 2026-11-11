<?php
include 'connection.php';

// Get values from GET request safely
$bankname = $link->real_escape_string($_GET['bank_name'] ?? '');
$month    = $link->real_escape_string($_GET['month'] ?? '');

// Helper: Calculate previous quarter month (e.g., 2025-03 -> 2024-12)
$prev_quarter_month = date('Y-m', strtotime($month . ' -3 months'));

if ($bankname && $month) {
    // START: Fetch last quarter total deposits from balance_sheet (CRR base)
    $sql_last_qtr_deposits = "SELECT SUM(amount) AS total_deposits FROM balance_sheet WHERE bank_name='$bankname' AND month='$prev_quarter_month' AND side='Liabilities' AND category='DEPOSITS'";
    $result_last_qtr_deposits = $link->query($sql_last_qtr_deposits);
    $last_qtr_deposit_total = 0;
    if ($result_last_qtr_deposits && $row_last_qtr_deposits = $result_last_qtr_deposits->fetch_assoc()) {
        $last_qtr_deposit_total = $row_last_qtr_deposits['total_deposits'] ?? 0;
    }
    // END: Fetch last quarter total deposits from balance_sheet (CRR base)
    
    // START: Fetch Cash in Hand sum from balance_sheet (full sum, no cap)
    $sql_cash_in_hand = "SELECT SUM(amount) AS cash_in_hand FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='CASH IN HAND'";
    $result_cash_in_hand = $link->query($sql_cash_in_hand);
    $cash_in_hand = 0;
    if ($result_cash_in_hand && $row_cash_in_hand = $result_cash_in_hand->fetch_assoc()) {
        $cash_in_hand = $row_cash_in_hand['cash_in_hand'] ?? 0;
    }
    // END: Fetch Cash in Hand sum from balance_sheet (full sum, no cap)
    
    // START: Fetch Cash at Bank sum from balance_sheet
    $sql_cash_at_bank = "SELECT SUM(amount) AS cash_at_bank FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='CASH AT BANK'";
    $result_cash_at_bank = $link->query($sql_cash_at_bank);
    $cash_at_bank = 0;
    if ($result_cash_at_bank && $row_cash_at_bank = $result_cash_at_bank->fetch_assoc()) {
        $cash_at_bank = $row_cash_at_bank['cash_at_bank'] ?? 0;
    }
    // END: Fetch Cash at Bank sum from balance_sheet
    
    // START: Fetch OD sum from balance_sheet (under BORROWINGS as proxy)
    $sql_od = "SELECT SUM(amount) AS od FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Liabilities' AND category='BORROWINGS'";
    $result_od = $link->query($sql_od);
    $od = 0;
    if ($result_od && $row_od = $result_od->fetch_assoc()) {
        $od = $row_od['od'] ?? 0;
    }
    // ISSUE: If OD not under BORROWINGS, adjust category/subcategory (e.g., to 'OD')
    // END: Fetch OD sum from balance_sheet
    
    $cash_at_bank_od = $cash_at_bank + $od;
    
    // Editable: Short Term Deposits (up to 15 days) - Manual override if query returns 0
    $short_term_deposits = 0; // Set manually if needed (e.g., $short_term_deposits = 12345.67;)
    // START: Fetch Short Term Deposits sum from balance_sheet (optional query for reference)
    $sql_short_term_deposits = "SELECT SUM(amount) AS short_term_deposits FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND (subcategory LIKE '%Short Term%' OR subcategory LIKE '%15 days%')";
    $result_short_term_deposits = $link->query($sql_short_term_deposits);
    if ($result_short_term_deposits && $row_short_term_deposits = $result_short_term_deposits->fetch_assoc()) {
        $short_term_deposits = $row_short_term_deposits['short_term_deposits'] ?? $short_term_deposits; // Use query or manual
    }
    // END: Fetch Short Term Deposits sum from balance_sheet (editable)
    
    // Editable: Excess Liquid Fund Investment - Manual override if query returns 0
    $excess_liquid_fund_invest = 0; // Set manually if needed (e.g., $excess_liquid_fund_invest = 98765.43;)
    // START: Fetch Excess Liquid Fund Investment sum from balance_sheet (optional query for reference)
    $sql_excess_liquid_fund_invest = "SELECT SUM(amount) AS excess_liquid_fund_invest FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND (subcategory LIKE '%Excess Liquid%' OR subcategory LIKE '%25% liquid fund%')";
    $result_excess_liquid_fund_invest = $link->query($sql_excess_liquid_fund_invest);
    if ($result_excess_liquid_fund_invest && $row_excess_liquid_fund_invest = $result_excess_liquid_fund_invest->fetch_assoc()) {
        $excess_liquid_fund_invest = $row_excess_liquid_fund_invest['excess_liquid_fund_invest'] ?? $excess_liquid_fund_invest; // Use query or manual
    }
    // END: Fetch Excess Liquid Fund Investment sum from balance_sheet (editable)
    
    // START: Fetch Other Bank Investment sum from balance_sheet (for Private/NPA/Other)
    $sql_fund1 = "SELECT SUM(amount) AS share_invs FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND subcategory='Other Bank'";
    $result_fund1 = $link->query($sql_fund1);
    $fund1 = 0;
    if ($result_fund1 && $row_fund1 = $result_fund1->fetch_assoc()) {
        $fund1 = $row_fund1['share_invs'] ?? 0;
    }
    // END: Fetch Other Bank Investment sum from balance_sheet
    
    // START: Fetch NPA Bank Investment sum from balance_sheet
    $sql_fund2 = "SELECT SUM(amount) AS share_invs FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND subcategory='NPA Bank'";
    $result_fund2 = $link->query($sql_fund2);
    $fund2 = 0;
    if ($result_fund2 && $row_fund2 = $result_fund2->fetch_assoc()) {
        $fund2 = $row_fund2['share_invs'] ?? 0;
    }
    // END: Fetch NPA Bank Investment sum from balance_sheet
    
    // START: Fetch Private Bank Investment sum from balance_sheet
    $sql_fund3 = "SELECT SUM(amount) AS share_invs FROM balance_sheet WHERE bank_name='$bankname' AND month='$month' AND side='Assets' AND category='INVESTMENTS' AND subcategory='Private Bank Investment'";
    $result_fund3 = $link->query($sql_fund3);
    $fund3 = 0;
    if ($result_fund3 && $row_fund3 = $result_fund3->fetch_assoc()) {
        $fund3 = $row_fund3['share_invs'] ?? 0;
    }
    // ISSUE: Sheet implies Saving/Current under CASH AT BANK; if wrong, adjust to category='CASH AT BANK' AND subcategory LIKE '%Private%'
    // END: Fetch Private Bank Investment sum from balance_sheet
    
    $private_bank_saving_current = $fund1 + $fund2 + $fund3;
    
    // Calculations (simple arithmetic post-fetch; Deficit/Surplus Amt = Last Qtr * (Deficit % / 100))
    $total_crr_assets = $cash_in_hand + $cash_at_bank_od + $short_term_deposits + $excess_liquid_fund_invest + $private_bank_saving_current;
    $min_req_percent = 1.00;
    $crr_percent = ($last_qtr_deposit_total > 0) ? ($total_crr_assets / $last_qtr_deposit_total) * 100 : 0;
    $deficit_surplus_percent = $crr_percent - $min_req_percent;
    $deficit_surplus_amt = ($last_qtr_deposit_total * $deficit_surplus_percent) / 100; // Fixed: Direct % calc on base
    $crr_percent = round($crr_percent, 2);
    $deficit_surplus_percent = round($deficit_surplus_percent, 2);
    $deficit_surplus_amt = round($deficit_surplus_amt, 2);
    
    // Always INSERT for logging Including CRR (no UPDATE)
    $sql = "INSERT INTO crr_report_incl_nagri (
                bank_name, month_year, cash_in_hand, cash_at_bank_od, 
                short_term_deposits, excess_liquid_fund_invest, private_bank_saving_current,
                total_crr_assets, last_qtr_deposit_total, crr_percent, min_req_percent,
                deficit_surplus_percent, deficit_surplus_amt
            ) VALUES (
                '$bankname', '$month', '$cash_in_hand', '$cash_at_bank_od',
                '$short_term_deposits', '$excess_liquid_fund_invest', '$private_bank_saving_current',
                '$total_crr_assets', '$last_qtr_deposit_total', '$crr_percent', '$min_req_percent',
                '$deficit_surplus_percent', '$deficit_surplus_amt'
            )";

    if ($link->query($sql) === TRUE) {
        $p = "CRR Including Nagri Report Generated Successfully";
        header("Location: crr_report_incl_nagri.php?bank_name=$bankname&month=$month&success=" . urlencode($p));
        exit;
    } else {
        $p = "Error Generating CRR Including Nagri Report: " . $link->error;
        header("Location: crr_report_incl_nagri.php?bank_name=$bankname&month=$month&error=" . urlencode($p));
        exit;
    }
}
$link->close();
?>