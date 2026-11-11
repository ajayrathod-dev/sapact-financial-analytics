<?php 
include('connection.php');  
$id    = $_GET['bank_name'] ?? '';
$month = $_GET['month'] ?? '';

function getSum($link, $bankId, $month, $where) {
    $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$bankId' AND month='$month' AND $where";
    $result = $link->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        return $row['total'] ?? 0;
    }
    return 0;
}

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

// Check if record already exists
$sql422 = "SELECT * FROM networth WHERE bank_name = '$id' AND month_year='$month'";
$result422 = $link->query($sql422);
$record_exists = ($result422->num_rows > 0);

if ($id && $month) {
    // Get all sums
    $pu_exp        = getSum($link, $id, $month, "category='SHARE CAPITAL'");
    $res_fund      = getSum($link, $id, $month, "subcategory='RESERVE FUND'");
    $res_build1    = getSum($link, $id, $month, "subcategory='BUILDING FUND'");
    $res_free1     = getSum($link, $id, $month, "category='FREE RESERVES'");
    $res_std1      = getSum($link, $id, $month, "subcategory='Standard Assets Provision'");
    $res_div1      = getSum($link, $id, $month, "subcategory='DIVIDEND FIUCTUATION FUND'");
    $res_pro_loss1 = getSum($link, $id, $month, "category='PROFIT AND LOSS'");
    $res_loss1     = getSum($link, $id, $month, "category='LOSS'");
 
    // Calculations
    $res_pro_loss1 = ($res_pro_loss1 / 100) * 25;
    $tot_a = $pu_exp + $res_fund + $res_build1 + $res_free1 + $res_std1 + $res_pro_loss1;
    $tot_b = $res_loss1;
    $tot_net = $tot_a - $tot_b;
    
    $currentDateTime = date('Y-m-d H:i:s');
    
    if (!$record_exists) {
        // INSERT
        $sql = "
            INSERT INTO `networth` 
            (`share_capital`, `reserve_fund`, `building_fund`, `free_reserve`, `standard_assets`,
             `dividand_fund`, `gross_profit`, `gross_loss`, `bank_name`, `month_year`, `by`, `total_a`, `total_b`, `networth`, `created_at`)
            VALUES 
            ('$pu_exp', '$res_fund', '$res_build1', '$res_free1', '$res_std1',
             '$res_div1', '$res_pro_loss1', '$res_loss1', '$id', '$month', '$u_uname', '$tot_a', '$tot_b', '$tot_net', '$currentDateTime')
        ";

        if ($link->query($sql) === TRUE) {
            header("Location: networth_form.php?bank_name=$id&month=$month&success=1");
            exit;
        } else {
            $error = "Data Not Saved: " . $link->error;
            header("Location: networth_form.php?bank_name=$id&month=$month&error=" . urlencode($error));
            exit;
        }
    } else {
        // UPDATE
        $sql = "UPDATE `networth` SET 
                `share_capital` = '$pu_exp',
                `reserve_fund` = '$res_fund',
                `building_fund` = '$res_build1',
                `free_reserve` = '$res_free1',
                `standard_assets` = '$res_std1',
                `dividand_fund` = '$res_div1',
                `gross_profit` = '$res_pro_loss1',
                `gross_loss` = '$res_loss1',
                `by` = '$u_uname',
                `total_a` = '$tot_a',
                `total_b` = '$tot_b',
                `networth` = '$tot_net',
                `created_at` = '$currentDateTime'
                WHERE bank_name = '$id' AND month_year = '$month'";

        if ($link->query($sql) === TRUE) {
            header("Location: networth_form.php?bank_name=$id&month=$month&success=1");
            exit;
        } else {
            $error = "Data Not Saved: " . $link->error;
            header("Location: networth_form.php?bank_name=$id&month=$month&error=" . urlencode($error));
            exit;
        }
    }
} else {
    header("Location: networth_form.php?error=" . urlencode("Bank name and month are required"));
    exit;
}
?>