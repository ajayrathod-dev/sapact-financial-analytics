<?php 

include('connection.php');  
$id    = $_GET['bank_name'] ?? '';
$month = $_GET['month'] ?? '';

function getSum($link, $bankId, $month, $where) {
    $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name = ? AND month = ? AND $where";
    $stmt = $link->prepare($sql);
    $stmt->bind_param("ss", $bankId, $month);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        return $row['total'] ?? 0;
    }
    return 0;
}

$u_uname = '';
if (isset($_COOKIE['uuid'])) {
    $uuid = $_COOKIE['uuid'];
    // We will use prepared statement for the ods query
    $sql4 = "SELECT * FROM ods WHERE uuid = ? AND status='verified'";
    $stmt4 = $link->prepare($sql4);
    $stmt4->bind_param("s", $uuid);
    $stmt4->execute();
    $result4 = $stmt4->get_result();

    if ($result4->num_rows > 0) {
        $row4 = $result4->fetch_assoc();
        $u_uname = $row4['user_id'];
        $br_id = $row4['user_id'];
    } else {
        header("location:logout.php");
        exit;
    }
} else {
    header("location:logout.php");
    exit;
}

// Check if there is already an entry in networth for the given bank and month
$sql422 = "SELECT * FROM networth WHERE bank_name = ? AND month_year = ?";
$stmt422 = $link->prepare($sql422);
$stmt422->bind_param("ss", $id, $month);
$stmt422->execute();
$result422 = $stmt422->get_result();

$exists = $result422->num_rows > 0;

if ($id && $month) {
    // Get all sums
    $paid_up_share_capital = getSum($link, $id, $month, "category='SHARE CAPITAL' AND subcategory='Paid up Share Capital'");
    $working_capital      = getSum($link, $id, $month, "category='WORKING CAPITAL'");
    
    $res_fund      = getSum($link, $id, $month, "category='FUNDS AND RESERVES' AND subcategory='RESERVE FUND'");
    $res_building   = getSum($link, $id, $month, "category='FUNDS AND RESERVES' AND subcategory='BUILDING FUND'");
    $res_other     = getSum($link, $id, $month, "category='FUNDS AND RESERVES' AND subcategory='Other'");
    
    $casa_deposite     = getSum($link, $id, $month, "category='DEPOSITS' AND subcategory='CASA DEPOSIT'");
    $term_deposite     = getSum($link, $id, $month, "category='DEPOSITS' AND subcategory='Term Deposit'");

    $loans_advance = getSum($link, $id, $month, "category='LOANS & ADVANCES'");
    $personal_loan = getSum($link, $id, $month, "category='LOANS & ADVANCES' AND subcategory='Personal Loan'");
    $unsecure_loan = getSum($link, $id, $month, "category='LOANS & ADVANCES' AND subcategory='Director Loan Unsecure'");
    $unauthorised = getSum($link, $id, $month, "category='LOANS & ADVANCES' AND subcategory='Director Loans with Unauthorised'");
    $loans_advance_other = getSum($link, $id, $month, "category='LOANS & ADVANCES' AND subcategory='Other'");
    
    $investment = getSum($link, $id, $month, "category='INVESTMENTS'");
    $cashatbank = getSum($link, $id, $month, "category='CASH AT BANK'");
    $cashinhand = getSum($link, $id, $month, "category='CASH IN HAND'");
    
    $slr_agari_patsanstha =9999;
    $npa_gross=9999;
    $npa_net=9999;
     
    $last_loss = getSum($link, $id, $month, "category='LOSS' AND subcategory='LAST YEAR'");
    $current_loss = getSum($link, $id, $month, "category='LOSS' AND subcategory='CURRENT YEAR'");
    
    $last_profit = getSum($link, $id, $month, "category='PROFIT AND LOSS' AND subcategory='PREVIOUS YEAR PROFIT'");
    $current_profit = getSum($link, $id, $month, "category='PROFIT AND LOSS' AND subcategory='CURRENT YEAR PROFIT'");
    
    $total_deposite_total_loans_tot_employee=9999;
    $crar_report=9999;
    $cd_ration=9999;
    $mgmt_cost=9999;
    $average_abr=9999;
    $average_alr=9999;
    $tot_average=$average_alr-$average_abr;
    
    $total=$average_alr+$average_alr-$mgmt_cost;
    $pu_exp = $paid_up_share_capital; // Assuming this is what you want
    $res_build1 = $res_building;
    $res_free1 = $res_other;
    $res_inv = $investment;
    $bad_dbt = 0; // Calculate bad debt if available
    $profit_minus = $current_profit; // Or calculate as needed
    $tot_loss = $last_loss + $current_loss;
    $networth = $total; // Or calculate networth properly

    if (!$exists) {
        // Insert query
        $sql = "INSERT INTO `networth_camales`(`share_capital`, `reserve_fund`, `building_fund`, `dividand_fund`, `investment_fund`, `bad_dbt`, `current_profit`, 
           `total_loss`, `total_profit`, `networth`, `bank_name`, `month_year`, `by`) VALUES
           (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $link->prepare($sql);
        $stmt->bind_param("ddddddddddsss", $pu_exp, $res_fund, $res_build1, $res_free1, $res_inv, $bad_dbt, $profit_minus, $tot_loss, $total, $networth, $id, $month, $u_uname);

        if ($stmt->execute()) {
            $p = "Data Saved Successfully";
            header("Location:mis_report_form.php?bank_name=$id&month=$month");
            exit;
        } else {
            $p = "Data Not Saved";
            header("Location: mis_report_form.php?error=" . urlencode($p));
            exit;
        }
    } else {
        // Update query
        $sql = "UPDATE `networth_camales` 
          SET 
            share_capital   = ?,
            reserve_fund    = ?,
            building_fund   = ?,
            dividand_fund   = ?,
            investment_fund = ?,
            bad_dbt         = ?,
            current_profit  = ?,
            total_loss      = ?,
            total_profit    = ?,
            networth        = ?,
            bank_name       = ?,
            month_year      = ?,
            `by`            = ?
        WHERE bank_name = ? AND month_year = ?";

        $stmt = $link->prepare($sql);
        $stmt->bind_param("ddddddddddsssss", $pu_exp, $res_fund, $res_build1, $res_free1, $res_inv, $bad_dbt, $profit_minus, $tot_loss, $total, $networth, $id, $month, $u_uname, $id, $month);

        if ($stmt->execute()) {
            $p = "Data Saved Successfully";
            header("Location:mis_report_form.php?bank_name=$id&month=$month");
            exit;
        } else {
            $p = "Data Not Saved";
            header("Location: mis_report_form.php?error=" . urlencode($p));
            exit;
        }
    }
}
?>