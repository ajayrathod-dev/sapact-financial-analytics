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
    // START: Fetch cd_ratio actual from cd_ratio
    $sql_cd = "SELECT cd_ratio FROM cd_ratio WHERE bank_name='$id' AND month='$month' LIMIT 1";
    $result_cd = $link->query($sql_cd);
    $cd_ratio_actual = 0;
    if ($result_cd && $row_cd = $result_cd->fetch_assoc()) {
        $cd_ratio_actual = floatval($row_cd['cd_ratio'] ?? 0);
    }
    // END: Fetch cd_ratio actual from cd_ratio

    // CD Ratio marks (simple arithmetic post-fetch)
    if ($cd_ratio_actual > 80 || $cd_ratio_actual < 55) {
        $cd_ratio_marks = 0;
    } elseif ($cd_ratio_actual > 70) {
        $cd_ratio_marks = 10;
    } elseif ($cd_ratio_actual > 60) {
        $cd_ratio_marks = 40;
    } elseif ($cd_ratio_actual >= 55) {
        $cd_ratio_marks = 10;
    } else {
        $cd_ratio_marks = 0;
    }

    // START: Fetch total loans sum from balance_sheet
    $sql_loans = "SELECT SUM(amount) AS total_loans FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES'";
    $result_loans = $link->query($sql_loans);
    $total_loans = 0;
    if ($result_loans && $row_loans = $result_loans->fetch_assoc()) {
        $total_loans = floatval($row_loans['total_loans'] ?? 0);
    }
    // END: Fetch total loans sum from balance_sheet

    // START: Fetch overdue/NPA amount sum from balance_sheet
    $sql_npa = "SELECT SUM(amount) AS overdue_amount FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND (category LIKE '%NPA%' OR subcategory LIKE '%NPA%' OR sub_subcategory LIKE '%NPA%')";
    $result_npa = $link->query($sql_npa);
    $overdue_amount = 0;
    if ($result_npa && $row_npa = $result_npa->fetch_assoc()) {
        $overdue_amount = floatval($row_npa['overdue_amount'] ?? 0);
    }
    // END: Fetch overdue/NPA amount sum from balance_sheet

    // Overdue actual (simple arithmetic post-fetch)
    if ($total_loans > 0) {
        $overdue_actual = ($overdue_amount / $total_loans) * 100;
    } else {
        // START: Fetch total deposits sum from balance_sheet (fallback)
        $sql_dep = "SELECT SUM(amount) AS tot_dep FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Liabilities' AND category='DEPOSITS'";
        $result_dep = $link->query($sql_dep);
        $tot_dep = 0;
        if ($result_dep && $row_dep = $result_dep->fetch_assoc()) {
            $tot_dep = floatval($row_dep['tot_dep'] ?? 0);
        }
        // END: Fetch total deposits sum from balance_sheet (fallback)
        $overdue_actual = ($tot_dep > 0) ? ($overdue_amount / $tot_dep) * 100 : 0;
    }

    // Overdue marks (simple arithmetic post-fetch)
    if ($overdue_actual > 15) {
        $overdue_marks = 0;
    } elseif ($overdue_actual > 10) {
        $overdue_marks = 10;
    } elseif ($overdue_actual >= 5) {
        $overdue_marks = 20;
    } else {
        $overdue_marks = 30;
    }

    // START: Fetch cash_reserve sum from balance_sheet for CRR
    $sql_cash_reserve = "SELECT SUM(amount) FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND (category='CASH IN HAND' OR category='CASH AT BANK')";
    $result_cash_reserve = $link->query($sql_cash_reserve);
    $cash_reserve = 0;
    if ($result_cash_reserve && $row_cash_reserve = $result_cash_reserve->fetch_assoc()) {
        $cash_reserve = floatval($row_cash_reserve['SUM(amount)'] ?? 0);
    }
    // END: Fetch cash_reserve sum from balance_sheet for CRR

    // START: Fetch total_deposits sum from balance_sheet for CRR
    $sql_total_deposits_crr = "SELECT SUM(amount) FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Liabilities' AND category='DEPOSITS'";
    $result_total_deposits_crr = $link->query($sql_total_deposits_crr);
    $total_deposits_crr = 0;
    if ($result_total_deposits_crr && $row_total_deposits_crr = $result_total_deposits_crr->fetch_assoc()) {
        $total_deposits_crr = floatval($row_total_deposits_crr['SUM(amount)'] ?? 0);
    }
    // END: Fetch total_deposits sum from balance_sheet for CRR

    $crr_actual = ($total_deposits_crr > 0) ? ($cash_reserve / $total_deposits_crr) * 100 : 0;

    // CRR marks (simple arithmetic post-fetch)
    if ($crr_actual < 1) {
        $crr_marks = 0;
    } elseif ($crr_actual == 1) {
        $crr_marks = 5;
    } elseif ($crr_actual > 1 && $crr_actual <= 2) {
        $crr_marks = 4;
    } elseif ($crr_actual > 2 && $crr_actual <= 3) {
        $crr_marks = 3;
    } elseif ($crr_actual > 3 && $crr_actual <= 4) {
        $crr_marks = 2;
    } elseif ($crr_actual > 4 && $crr_actual <= 5) {
        $crr_marks = 1;
    } else {
        $crr_marks = 0;
    }

    // START: Fetch slr_investments sum from balance_sheet for SLR
    $sql_slr_investments = "SELECT SUM(amount) FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND category='INVESTMENTS'";
    $result_slr_investments = $link->query($sql_slr_investments);
    $slr_investments = 0;
    if ($result_slr_investments && $row_slr_investments = $result_slr_investments->fetch_assoc()) {
        $slr_investments = floatval($row_slr_investments['SUM(amount)'] ?? 0);
    }
    // END: Fetch slr_investments sum from balance_sheet for SLR

    // START: Fetch total_deposits sum from balance_sheet for SLR
    $sql_total_deposits_slr = "SELECT SUM(amount) FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Liabilities' AND category='DEPOSITS'";
    $result_total_deposits_slr = $link->query($sql_total_deposits_slr);
    $total_deposits_slr = 0;
    if ($result_total_deposits_slr && $row_total_deposits_slr = $result_total_deposits_slr->fetch_assoc()) {
        $total_deposits_slr = floatval($row_total_deposits_slr['SUM(amount)'] ?? 0);
    }
    // END: Fetch total_deposits sum from balance_sheet for SLR

    $slr_actual = ($total_deposits_slr > 0) ? ($slr_investments / $total_deposits_slr) * 100 : 0;

    // SLR marks (simple arithmetic post-fetch)
    if ($slr_actual < 25 || $slr_actual > 40) {
        $slr_marks = 0;
    } elseif ($slr_actual == 25) {
        $slr_marks = 20;
    } elseif ($slr_actual > 25 && $slr_actual <= 30) {
        $slr_marks = 15;
    } elseif ($slr_actual > 30 && $slr_actual <= 35) {
        $slr_marks = 10;
    } elseif ($slr_actual > 35 && $slr_actual <= 40) {
        $slr_marks = 5;
    } else {
        $slr_marks = 0;
    }

    // START: Fetch profit sum from balance_sheet
    $sql_profit = "SELECT SUM(amount) AS profit FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Liabilities' AND category='PROFIT AND LOSS' AND amount > 0";
    $result_profit = $link->query($sql_profit);
    $profit_actual = 0;
    if ($result_profit && $row_profit = $result_profit->fetch_assoc()) {
        $profit_actual = floatval($row_profit['profit'] ?? 0);
    }
    $profit_marks = ($profit_actual > 0) ? 5 : 0;
    // END: Fetch profit sum from balance_sheet

    // START: Fetch loss sum from balance_sheet
    $sql_loss = "SELECT SUM(amount) AS loss FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND side='Assets' AND category='LOSS'";
    $result_loss = $link->query($sql_loss);
    $loss_actual = 0;
    if ($result_loss && $row_loss = $result_loss->fetch_assoc()) {
        $loss_actual = floatval($row_loss['loss'] ?? 0);
    }
    $loss_marks = 0;
    // END: Fetch loss sum from balance_sheet
    // ISSUE: No 'LOSS' category in sample data

    // Total marks and classification (simple arithmetic post-fetch)
    $total_marks = $cd_ratio_marks + $overdue_marks + $crr_marks + $slr_marks + $profit_marks + $loss_marks;

    if ($total_marks > 75) {
        $classification = 'अ';
    } elseif ($total_marks > 50) {
        $classification = 'ब';
    } elseif ($total_marks > 25) {
        $classification = 'क';
    } else {
        $classification = 'ड';
    }

    // Always INSERT new record
    $sql = "INSERT INTO mis_classification (
                bank_name, month, cd_ratio_actual, cd_ratio_marks, overdue_actual, overdue_marks,
                crr_actual, crr_marks, slr_actual, slr_marks, profit_actual, profit_marks,
                loss_actual, loss_marks, total_marks, classification, created_at
            ) VALUES (
                '$id', '$month', '$cd_ratio_actual', '$cd_ratio_marks', '$overdue_actual', '$overdue_marks',
                '$crr_actual', '$crr_marks', '$slr_actual', '$slr_marks', '$profit_actual', '$profit_marks',
                '$loss_actual', '$loss_marks', '$total_marks', '$classification', NOW()
            )";

    if ($link->query($sql) === TRUE) {
        $p = "MIS Classification Data Saved Successfully";
        header("Location: mis_classification.php?bank_name=$id&month=$month&success=" . urlencode($p));
        exit;
    } else {
        $p = "Data Not Saved: " . $link->error;
        header("Location: mis_classification.php?bank_name=$id&month=$month&error=" . urlencode($p));
        exit;
    }
}
?>