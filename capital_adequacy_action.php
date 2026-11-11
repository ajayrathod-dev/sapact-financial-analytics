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
    // START: Fetch total_deposits, share_capital, reserve_fund, building_fund, loss from balance_sheet
    $sql = "SELECT 
              SUM(CASE WHEN side='Liabilities' AND category='DEPOSITS' THEN amount ELSE 0 END) AS total_deposits,
              SUM(CASE WHEN category='SHARE CAPITAL' THEN amount ELSE 0 END) AS share_capital,
              SUM(CASE WHEN category='FUNDS AND RESERVES' AND subcategory='RESERVE FUND' THEN amount ELSE 0 END) AS reserve_fund,
              SUM(CASE WHEN category='FUNDS AND RESERVES' AND subcategory='BUILDING FUND' THEN amount ELSE 0 END) AS building_fund,
              SUM(CASE WHEN category='LOSS' THEN amount ELSE 0 END) AS loss
            FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month'";
    $result = $link->query($sql);
    $row = $result ? $result->fetch_assoc() : [];

    $total_deposits = floatval($row['total_deposits'] ?? 0);
    $share_capital  = floatval($row['share_capital'] ?? 0);
    $reserve_fund   = floatval($row['reserve_fund'] ?? 0);
    $building_fund  = floatval($row['building_fund'] ?? 0);
    $loss           = floatval($row['loss'] ?? 0);
    // END: Fetch totals from balance_sheet

    // Calculations (simple arithmetic post-fetch) - Rule 35
    $denominator     = $share_capital + $reserve_fund + $building_fund - $loss;
    $rule_35_value   = $denominator > 0 ? $total_deposits / $denominator : 0;
    $rule_35_required = $rule_35_value > 10 ? ($total_deposits / 10) - $denominator : 0;

    // START: Fetch networth from networth table
    $sql_networth = "SELECT networth FROM networth WHERE bank_name='$id' AND month_year='$month' LIMIT 1";
    $result_networth = $link->query($sql_networth);
    $networth = 0;
    if ($result_networth && $row_networth = $result_networth->fetch_assoc()) {
        $networth = floatval($row_networth['networth'] ?? 0);
    }
    // END: Fetch networth

    // START: Fetch total risk weighted assets from crar_report_log
    $sql_rwa = "SELECT SUM(jokhim) AS total_rwa FROM crar_report_log WHERE bank_name='$id' AND month='$month'";
    $result_rwa = $link->query($sql_rwa);
    $total_rwa = 1; // avoid division by zero
    if ($result_rwa && $row_rwa = $result_rwa->fetch_assoc()) {
        $total_rwa = floatval($row_rwa['total_rwa'] ?? 1);
    }
    // END: Fetch total_rwa

    $crar_percentage = $total_rwa > 0 ? ($networth / $total_rwa) * 100 : 0;
    $crar_required   = $crar_percentage < 9 ? 9 - $crar_percentage : 0;

    // START: Fetch share count (no_of_accounts) from balance_sheet
    $sql_share_count = "SELECT no_of_accounts FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND category='SHARE CAPITAL' AND subcategory='Paid up Share Capital' LIMIT 1";
    $result_share_count = $link->query($sql_share_count);
    $share_count = 1;
    if ($result_share_count && $row_share_count = $result_share_count->fetch_assoc()) {
        $share_count = floatval($row_share_count['no_of_accounts'] ?? 1);
    }
    // END: Fetch share count

    $per_share_value    = $share_count > 0 ? $networth / $share_count : 0;
    $per_share_required = $per_share_value < 500 ? 500 - $per_share_value : 0;

    // Calculations (simple arithmetic post-fetch)
    $self_exposure_limit  = $networth * 0.15;
    $group_exposure_limit = $networth * 0.20;

    // START: Fetch management expenses from profit_loss
    $sql_mgmt_exp = "SELECT SUM(amount) AS management_expenses 
                     FROM profit_loss 
                     WHERE bank_name='$id' AND month='$month' 
                       AND (category='MANAGEMENT EXPENSES' OR category='ALL MANAGEMENT EXPENCES')";
    $result_mgmt_exp = $link->query($sql_mgmt_exp);
    $management_expenses = 0;
    if ($result_mgmt_exp && $row_mgmt = $result_mgmt_exp->fetch_assoc()) {
        $management_expenses = floatval($row_mgmt['management_expenses'] ?? 0);
    }
    // END: Fetch management expenses

    // START: Fetch total members from society_info
    $sql_members = "SELECT holder_member FROM society_info WHERE id='$id'";
    $result_members = $link->query($sql_members);
    $total_members = 1;
    if ($result_members && $row_members = $result_members->fetch_assoc()) {
        $total_members = intval($row_members['holder_member'] ?? 1);
    }
    // END: Fetch total members

    $coca_capital = $total_members > 0 ? $management_expenses / $total_members : 0;

    // Calculations (simple arithmetic post-fetch)
    $market_capitalization = $per_share_value * ($share_capital / 10);

    // START: Fetch working capital from balance_sheet
    $sql_wc = "SELECT SUM(amount) AS wcapital FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Liabilities' AND category='WORKING CAPITAL'";
    $result_wc = $link->query($sql_wc);
    $working_capital = 0;
    if ($result_wc && $row_wc = $result_wc->fetch_assoc()) {
        $working_capital = floatval($row_wc['wcapital'] ?? 0);
    }
    // END: Fetch working capital

    // START: Fetch deposit interest paid from profit_loss
    $sql_dep_int = "SELECT SUM(amount) AS deposit_interest FROM profit_loss WHERE bank_name='$id' AND month='$month' AND category LIKE 'INTEREST PAID ON DEPOSIT%'";
    $result_dep_int = $link->query($sql_dep_int);
    $deposit_interest = 0;
    if ($result_dep_int && $row_dep_int = $result_dep_int->fetch_assoc()) {
        $deposit_interest = floatval($row_dep_int['deposit_interest'] ?? 0);
    }
    // END: Fetch deposit interest

    // Calculations (simple arithmetic post-fetch)
    $interest_paid_share = $share_capital * 0.08;
    $total_interest_paid = $interest_paid_share + $deposit_interest;
    $cost_of_funds = $working_capital > 0 ? ($total_interest_paid / $working_capital) * 100 : 0;

    // START: Fetch total interest received from profit_loss
    $sql_int_rec = "SELECT SUM(amount) AS interest_received FROM profit_loss WHERE bank_name='$id' AND month='$month' AND category LIKE 'INTEREST RECEIVED%'";
    $result_int_rec = $link->query($sql_int_rec);
    $interest_received = 0;
    if ($result_int_rec && $row_int_rec = $result_int_rec->fetch_assoc()) {
        $interest_received = floatval($row_int_rec['interest_received'] ?? 0);
    }
    // END: Fetch interest received

    // Calculations (simple arithmetic post-fetch) - Yield, Management Cost, Margin
    $yield_of_funds   = $working_capital > 0 ? ($interest_received / $working_capital) * 100 : 0;
    $management_cost  = $working_capital > 0 ? ($management_expenses / $working_capital) * 100 : 0;
    $margin           = $yield_of_funds - $cost_of_funds - $management_cost;

    // Always INSERT new record (preserve history)
    $sql_insert = "INSERT INTO capital_adequacy (
                    bank_name, month, rule_35_value, rule_35_required, crar_percentage, crar_required,
                    per_share_value, per_share_required, self_exposure_limit, group_exposure_limit,
                    coca_capital, market_capitalization, cost_of_funds, yield_of_funds, management_cost, margin
                   ) VALUES (
                    '$id', '$month', '$rule_35_value', '$rule_35_required', '$crar_percentage', '$crar_required',
                    '$per_share_value', '$per_share_required', '$self_exposure_limit', '$group_exposure_limit',
                    '$coca_capital', '$market_capitalization', '$cost_of_funds', '$yield_of_funds', '$management_cost', '$margin'
                   )";

    if ($link->query($sql_insert) === TRUE) {
        $p = "Capital Adequacy Data Saved Successfully";
        header("Location: capital_adequacy.php?bank_name=$id&month=$month&success=" . urlencode($p));
        exit;
    } else {
        $p = "Data Not Saved: " . $link->error;
        header("Location: capital_adequacy.php?bank_name=$id&month=$month&error=" . urlencode($p));
        exit;
    }
}
?>