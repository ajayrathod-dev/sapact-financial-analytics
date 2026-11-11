<?php
include 'connection.php';


// Get values from GET request safely
$bankname = $link->real_escape_string($_GET['bank_name'] ?? '');
$month    = $link->real_escape_string($_GET['month'] ?? '');

// Validate inputs
if (empty($bankname) || empty($month)) {
    http_response_code(400);
    die("Error: Bank name or month is missing.");
}

// Function to get amount from balance_sheet
function getBalanceAmount($link, $bankname, $month, $side, $category, $subcategory = '', $sub_subcategory = '') {
    $sql = "SELECT SUM(amount) AS total_amount 
            FROM balance_sheet 
            WHERE bank_name = '$bankname' 
            AND month = '$month' 
            AND side = '$side'";
    
    if (!empty($category)) {
        $sql .= " AND category = '$category'";
    }
    if (!empty($subcategory)) {
        $sql .= " AND subcategory = '$subcategory'";
    }
    if (!empty($sub_subcategory)) {
        $sql .= " AND sub_subcategory = '$sub_subcategory'";
    }
    
    $result = $link->query($sql);
    return $result && ($row = $result->fetch_assoc()) ? (float)$row['total_amount'] : 0.00;
}

// ============================================================================
// COMPLETE CALCULATIONS BASED ON EXCEL SHEET
// ============================================================================

// 1. NETWORTH COMPONENTS (Rows 1-14)
$share_capital = getBalanceAmount($link, $bankname, $month, 'Liabilities', 'SHARE CAPITAL', 'Paid up Share Capital');
$share_anamat = getBalanceAmount($link, $bankname, $month, 'Liabilities', 'SHARE CAPITAL', 'Shares Anamat');
$reserve_fund = getBalanceAmount($link, $bankname, $month, 'Liabilities', 'FUNDS AND RESERVES', 'RESERVE FUND');
$building_fund = getBalanceAmount($link, $bankname, $month, 'Liabilities', 'FUNDS AND RESERVES', 'BUILDING FUND');
$dividend_fluctuation_fund = getBalanceAmount($link, $bankname, $month, 'Liabilities', 'FUNDS AND RESERVES', 'DIVIDEND FLUCTUATION FUND');
$investment_fluctuation_fund = getBalanceAmount($link, $bankname, $month, 'Liabilities', 'FUNDS AND RESERVES', 'INVESTMENT FLUCTUATION FUND');
$profit_distribution = getBalanceAmount($link, $bankname, $month, 'Liabilities', 'FUNDS AND RESERVES', ' PROFIT DISTRUBUTION');
$standard_assets_fund = getBalanceAmount($link, $bankname, $month, 'Liabilities', 'FUNDS AND RESERVES', 'STANDARED ASSETS FUND');

// Profit Calculation with 8% adjustment
$previous_year_profit = getBalanceAmount($link, $bankname, $month, 'Liabilities', 'PROFIT AND LOSS', 'PREVIOUS YEAR PROFIT');
$current_year_profit = getBalanceAmount($link, $bankname, $month, 'Liabilities', 'PROFIT AND LOSS', 'CURRENT YEAR PROFIT');
$total_profit_before_adjustment = $previous_year_profit + $current_year_profit;
$profit_adjustment = $share_capital * 0.08;
$profit = $total_profit_before_adjustment - $profit_adjustment;
$reserve_on_profit_25 = $profit * 0.25;

//loss calculation
$loss = getBalanceAmount($link, $bankname, $month, 'Assets', 'LOSS');
// npa provisions
$sql = "SELECT SUM(amount) AS short_npa_provision
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='FUNDS AND RESERVES' AND subcategory='BUILDING FUND'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $short_npa_provision = $row["short_npa_provision"];
}
$sql = "SELECT SUM(amount) AS short_npa_int_provision
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='FUNDS AND RESERVES' AND subcategory='BUILDING FUND'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $short_npa_int_provision = $row["short_npa_int_provision"];
}

$sql = "SELECT SUM(amount) AS new_baylows_say_profit_not_add
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='FUNDS AND RESERVES' AND subcategory='BUILDING FUND'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $new_baylows_say_profit_not_add = $row["new_baylows_say_profit_not_add"];
}

$sql = "SELECT SUM(amount) AS short_exp_provision
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='FUNDS AND RESERVES' AND subcategory='BUILDING FUND'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $short_exp_provision = $row["short_exp_provision"];
}
// // $short_npa_provision = 0.00;
// $short_npa_int_provision = 0.00;
// $short_exp_provision = 0.00;
// $new_baylows_say_profit_not_add = 0.00;

// Total Networth (Row 20)
$total_networth = $share_capital + $share_anamat + $reserve_fund + $building_fund + $dividend_fluctuation_fund + 
                  $investment_fluctuation_fund + $profit_distribution + $standard_assets_fund + 
                  $profit + $reserve_on_profit_25 - $loss - $short_npa_provision - 
                  $short_npa_int_provision - $short_exp_provision;

// 2. NETWORTH INCREASES & RATIOS (Rows 21-29)
// $increase_in_nw = 0.00; // Would need previous month data
$sql = "SELECT SUM(amount) AS increase_in_nw
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='FUNDS AND RESERVES' AND subcategory='BUILDING FUND'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $increase_in_nw = $row["increase_in_nw"];
}
$profit_to_networth = ($total_networth > 0) ? ($profit / $total_networth * 100) : 0.00;
$working_capital = getBalanceAmount($link, $bankname, $month, 'Liabilities', 'WORKING CAPITAL');
$networth_percentage = ($working_capital > 0) ? ($total_networth / $working_capital * 100) : 0.00;
$nw_increase_percentage = 0.00;
$total_deposits = getBalanceAmount($link, $bankname, $month, 'Liabilities', 'DEPOSITS');

// Law 35 Calculation
$core_funds = $share_capital + $reserve_fund + $building_fund;
$total_core_funds = $core_funds;
$law_35 = ($core_funds > 0) ? ($total_deposits / $core_funds) : 0.00;

$new_law_self_15 = $total_networth * 0.15;
$new_law_group_20 = $total_networth * 0.20;

// Cost of Share Capital
$no_of_shares = $share_capital / 100;
$cost_of_share_capital = ($no_of_shares > 0) ? ($share_capital / $no_of_shares) : 0.00;

// 3. LAW 35 SECTION (Rows 37-42)
$total_deposits_law_35 = $total_deposits;
$divided_by_5 = $total_core_funds / 5;
$new_law_networth_15_self = $total_networth * 0.15;
$new_law_networth_20_group = $total_networth * 0.20;

// 4. CD RATIO SECTION (Rows 69-76)
$assets_total = getBalanceAmount($link, $bankname, $month, 'Assets', '');
$investments_in_shares = getBalanceAmount($link, $bankname, $month, 'Assets', 'INVESTMENTS');
$funds_available_for_loan = $total_networth - $assets_total - $investments_in_shares - $reserve_fund;
$total_loans = getBalanceAmount($link, $bankname, $month, 'Assets', 'LOANS & ADVANCES');
$gross_npa = 3531519.00; // Should be dynamic
$deposits_for_cd = $total_deposits;
$cd_ratio = ($deposits_for_cd > 0) ? (($total_loans - $funds_available_for_loan) / $deposits_for_cd * 100) : 0.00;
$earning_cd_ratio = ($deposits_for_cd > 0) ? (($total_loans - $funds_available_for_loan - $gross_npa) / $deposits_for_cd * 100) : 0.00;

// 5. CRAR SECTION (Rows 81-86)
$rwa_proxy = $total_loans + $investments_in_shares;
$crar = ($rwa_proxy > 0) ? ($total_networth / $rwa_proxy * 100) : 0.00;
$target_crar = 9.00;
$crar_increase_needed = $target_crar - $crar;
$crar_increase_rupees = ($crar_increase_needed > 0) ? ($crar_increase_needed * $rwa_proxy / 100) : 0.00;
$calculation_after_crar_increase = ($rwa_proxy > 0) ? (($total_networth + $crar_increase_rupees) / $rwa_proxy * 100) : 0.00;

// 6. SHARE VALUES SECTION (Rows 90-96)
$per_share_value = ($no_of_shares > 0) ? ($total_networth / $no_of_shares) : 0.00;
$target_per_share = 500.00;
$value_500 = $target_per_share - $per_share_value;
$deficit_in_rupees = ($value_500 > 0) ? ($value_500 * $no_of_shares) : 0.00;
$calculation_after_share_increase = ($no_of_shares > 0) ? (($total_networth + $deficit_in_rupees) / $no_of_shares) : 0.00;
$market_capitalization = $per_share_value * $no_of_shares;

// ============================================================================
// INSERT DATA WITH ALL COLUMNS
// ============================================================================

$sql = "INSERT INTO networth96 (
    bank_name, month, 
    share_capital, share_anamat, reserve_fund, building_fund, dividend_fluctuation_fund, 
    investment_fluctuation_fund, profit_distribution, standard_assets_fund, 
    profit, profit_adjustment, total_profit_before_adjustment, reserve_on_profit_25, 
    loss, short_npa_provision, short_npa_int_provision, short_exp_provision, 
    new_baylows_say_profit_not_add, total_networth, increase_in_nw, profit_to_networth, 
    working_capital, networth_percentage, nw_increase_percentage, total_deposits, 
    law_35, core_funds, new_law_self_15, new_law_group_20, cost_of_share_capital, 
    no_of_shares, funds_available_for_loan, total_loans, gross_npa, deposits_for_cd, 
    cd_ratio, earning_cd_ratio, crar, crar_increase_needed, crar_increase_rupees, 
    rwa_proxy, target_crar, calculation_after_crar_increase, per_share_value, 
    value_500, deficit_in_rupees, target_per_share, calculation_after_share_increase, 
    market_capitalization, previous_year_profit, current_year_profit, total_deposits_law_35, 
    total_core_funds, divided_by_5, new_law_networth_15_self, new_law_networth_20_group, 
    assets_total, investments_in_shares
) VALUES (
    '$bankname', '$month', 
    $share_capital, $share_anamat, $reserve_fund, $building_fund, $dividend_fluctuation_fund, 
    $investment_fluctuation_fund, $profit_distribution, $standard_assets_fund, 
    $profit, $profit_adjustment, $total_profit_before_adjustment, $reserve_on_profit_25, 
    $loss, $short_npa_provision, $short_npa_int_provision, $short_exp_provision, 
    $new_baylows_say_profit_not_add, $total_networth, $increase_in_nw, $profit_to_networth, 
    $working_capital, $networth_percentage, $nw_increase_percentage, $total_deposits, 
    $law_35, $core_funds, $new_law_self_15, $new_law_group_20, $cost_of_share_capital, 
    $no_of_shares, $funds_available_for_loan, $total_loans, $gross_npa, $deposits_for_cd, 
    $cd_ratio, $earning_cd_ratio, $crar, $crar_increase_needed, $crar_increase_rupees, 
    $rwa_proxy, $target_crar, $calculation_after_crar_increase, $per_share_value, 
    $value_500, $deficit_in_rupees, $target_per_share, $calculation_after_share_increase, 
    $market_capitalization, $previous_year_profit, $current_year_profit, $total_deposits_law_35, 
    $total_core_funds, $divided_by_5, $new_law_networth_15_self, $new_law_networth_20_group, 
    $assets_total, $investments_in_shares
)";

if ($link->query($sql) === TRUE) {
    $link->close();
    header("Location: networth_rep.php?bank_name=" . urlencode($bankname) . "&month=" . urlencode($month));
    exit();
} else {
    $error = "Error inserting data: " . $link->error;
    error_log($error);
    error_log("SQL: " . $sql);
    http_response_code(500);
    echo $error;
    $link->close();
}
?>