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

if ($id && $month) {
    // START: Fetch Paid up Share Capital sum from balance_sheet
    $sql_pu_exp = "SELECT SUM(amount) AS total FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND category='SHARE CAPITAL' AND subcategory='Paid up Share Capital'";
    $result_pu_exp = $link->query($sql_pu_exp);
    $pu_exp = 0;
    if ($result_pu_exp && $row_pu_exp = $result_pu_exp->fetch_assoc()) {
        $pu_exp = $row_pu_exp['total'] ?? 0;
    }
    // END: Fetch Paid up Share Capital sum from balance_sheet
    
    // START: Fetch RESERVE FUND sum from balance_sheet
    $sql_res_fund = "SELECT SUM(amount) AS total FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND category='FUNDS AND RESERVES' AND subcategory='RESERVE FUND'";
    $result_res_fund = $link->query($sql_res_fund);
    $res_fund = 0;
    if ($result_res_fund && $row_res_fund = $result_res_fund->fetch_assoc()) {
        $res_fund = $row_res_fund['total'] ?? 0;
    }
    // END: Fetch RESERVE FUND sum from balance_sheet
    
    // START: Fetch BUILDING FUND sum from balance_sheet
    $sql_res_build1 = "SELECT SUM(amount) AS total FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND category='FUNDS AND RESERVES' AND subcategory='BUILDING FUND'";
    $result_res_build1 = $link->query($sql_res_build1);
    $res_build1 = 0;
    if ($result_res_build1 && $row_res_build1 = $result_res_build1->fetch_assoc()) {
        $res_build1 = $row_res_build1['total'] ?? 0;
    }
    // END: Fetch BUILDING FUND sum from balance_sheet
    
    // START: Fetch DIVIDEND FLUCTUATION FUND sum from balance_sheet
    $sql_res_free1 = "SELECT SUM(amount) AS total FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND category='FUNDS AND RESERVES' AND subcategory='DIVIDEND FLUCTUATION FUND'";
    $result_res_free1 = $link->query($sql_res_free1);
    $res_free1 = 0;
    if ($result_res_free1 && $row_res_free1 = $result_res_free1->fetch_assoc()) {
        $res_free1 = $row_res_free1['total'] ?? 0;
    }
    // END: Fetch DIVIDEND FLUCTUATION FUND sum from balance_sheet
    
    // START: Fetch INVESTMENT FLUCTUATION FUND sum from balance_sheet
    $sql_res_inv = "SELECT SUM(amount) AS total FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND category='FUNDS AND RESERVES' AND subcategory='INVESTMENT FLUCTUATION FUND'";
    $result_res_inv = $link->query($sql_res_inv);
    $res_inv = 0;
    if ($result_res_inv && $row_res_inv = $result_res_inv->fetch_assoc()) {
        $res_inv = $row_res_inv['total'] ?? 0;
    }
    // END: Fetch INVESTMENT FLUCTUATION FUND sum from balance_sheet
    
    // START: Fetch Bad and Doubtful Debts Provision sum from balance_sheet
    $sql_bad_dbt = "SELECT SUM(amount) AS total FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND category='FUNDS AND RESERVES' AND subcategory='Bad and Doubtful Debts Provision'";
    $result_bad_dbt = $link->query($sql_bad_dbt);
    $bad_dbt = 0;
    if ($result_bad_dbt && $row_bad_dbt = $result_bad_dbt->fetch_assoc()) {
        $bad_dbt = $row_bad_dbt['total'] ?? 0;
    }
    // END: Fetch Bad and Doubtful Debts Provision sum from balance_sheet
    
    // START: Fetch PREVIOUS YEAR PROFIT sum from balance_sheet
    $sql_res_pro_loss = "SELECT SUM(amount) AS total FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND category='PROFIT AND LOSS' AND subcategory='PREVIOUS YEAR PROFIT'";
    $result_res_pro_loss = $link->query($sql_res_pro_loss);
    $res_pro_loss = 0;
    if ($result_res_pro_loss && $row_res_pro_loss = $result_res_pro_loss->fetch_assoc()) {
        $res_pro_loss = $row_res_pro_loss['total'] ?? 0;
    }
    // END: Fetch PREVIOUS YEAR PROFIT sum from balance_sheet
    
    // START: Fetch CURRENT YEAR PROFIT sum from balance_sheet
    $sql_res_pro_loss1 = "SELECT SUM(amount) AS total FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND category='PROFIT AND LOSS' AND subcategory='CURRENT YEAR PROFIT'";
    $result_res_pro_loss1 = $link->query($sql_res_pro_loss1);
    $res_pro_loss1 = 0;
    if ($result_res_pro_loss1 && $row_res_pro_loss1 = $result_res_pro_loss1->fetch_assoc()) {
        $res_pro_loss1 = $row_res_pro_loss1['total'] ?? 0;
    }
    // END: Fetch CURRENT YEAR PROFIT sum from balance_sheet
    
    // START: Fetch LAST YEAR LOSS sum from balance_sheet
    $sql_res_loss = "SELECT SUM(amount) AS total FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND category='LOSS' AND subcategory='LAST YEAR'";
    $result_res_loss = $link->query($sql_res_loss);
    $res_loss = 0;
    if ($result_res_loss && $row_res_loss = $result_res_loss->fetch_assoc()) {
        $res_loss = $row_res_loss['total'] ?? 0;
    }
    // END: Fetch LAST YEAR LOSS sum from balance_sheet
    
    // START: Fetch CURRENT YEAR LOSS sum from balance_sheet
    $sql_res_loss1 = "SELECT SUM(amount) AS total FROM balance_sheet WHERE bank_name='$id' AND month='$month' AND category='LOSS' AND subcategory='CURRENT YEAR'";
    $result_res_loss1 = $link->query($sql_res_loss1);
    $res_loss1 = 0;
    if ($result_res_loss1 && $row_res_loss1 = $result_res_loss1->fetch_assoc()) {
        $res_loss1 = $row_res_loss1['total'] ?? 0;
    }
    // END: Fetch CURRENT YEAR LOSS sum from balance_sheet
    
    // Calculations (simple arithmetic post-fetch; cannot push to SELECT/SUM without aggregation across unrelated sums)
    $share_tot = (8/100) * $pu_exp;
    $profit_minus = $res_pro_loss + $res_pro_loss1 - $share_tot;
    $total = $pu_exp + $res_fund + $res_build1 + $res_free1 + $res_inv + $bad_dbt + $profit_minus;
    $tot_loss = $res_loss + $res_loss1;
    $networth = $total - $tot_loss;
    
    // Always INSERT for logging (no UPDATE to preserve history)
    $sql = "INSERT INTO `networth`(`share_capital`, `reserve_fund`, `building_fund`, `free_reserve`, `standard_assets`, `dividand_fund`, `gross_profit`, `gross_loss`, `bank_name`, `month_year`, `by`, `total_a`, `total_b`, `networth`) 
            VALUES ('$pu_exp', '$res_fund', '$res_build1', '$res_free1', '$res_inv', '$bad_dbt', '$profit_minus', '$tot_loss', '$id', '$month', '$u_uname', '$total', '$tot_loss', '$networth')";

    if ($link->query($sql) === TRUE) {
        $p = "Data Saved Successfully";
        header("Location: camales_networth_form.php?bank_name=$id&month=$month&success=" . urlencode($p));
        exit;
    } else {
        $p = "Data Not Saved: " . $link->error;
        header("Location: camales_networth_form.php?bank_name=$id&month=$month&error=" . urlencode($p));
        exit;
    }
} 
?>