<?php
session_start();
include("connection.php");
date_default_timezone_set('Asia/Kolkata');
$today = date("Y-m-d h:i:s");

$u_uname = '';
if (isset($_COOKIE['uuid'])) {
    $u_uname = mysqli_real_escape_string($link, $_COOKIE['uuid']);
}

$sql4 = "SELECT * FROM ods WHERE uuid='$u_uname' AND status='verified'";
$result4 = mysqli_query($link, $sql4);

if ($result4->num_rows > 0) {
    while ($row4 = $result4->fetch_assoc()) {
        $u_uname = mysqli_real_escape_string($link, $row4['user_id']);
        $br_id = mysqli_real_escape_string($link, $row4['user_id']);
    }
} else {
    header("location:logout.php");
    exit;
}


$submission_date = date('Y-m-d');
$bank = $_POST['bank'];
$month = $_POST['month'];
$category = $_POST['category'];
$category_other = $_POST['category_other'];


$no_of_accounts_std = $_POST['no_of_accounts_std'];
$amount_std = $_POST['amount_std'];
$no_of_accounts_sub = $_POST['no_of_accounts_sub'];
$amount_sub = $_POST['amount_sub'];

$no_of_accounts_dobt1 = $_POST['no_of_accounts_dobt1'];
$amount_dobt1 = $_POST['amount_dobt1'];

$no_of_accounts_dobt2 = $_POST['no_of_accounts_dobt2'];
$amount_dobt2 = $_POST['amount_dobt2'];

$no_of_accounts_dobt3 = $_POST['no_of_accounts_dobt3'];
$amount_dobt3 = $_POST['amount_dobt3'];

$no_of_accounts_tot = $_POST['no_of_accounts_tot'];
$amount_tot = $_POST['amount_tot'];

foreach ($category as $i => $cat_val) {
    $cat = $cat_val === '__other__' ? $category_other[$i] : $cat_val;
    $acc_std = !empty($no_of_accounts_std[$i]) ? $no_of_accounts_std[$i] : null;
    $amt_std = !empty($amount_std[$i]) ? $amount_std[$i] : null;
    $acc_sub = !empty($no_of_accounts_sub[$i]) ? $no_of_accounts_sub[$i] : null;
    $amt_sub = !empty($amount_sub[$i]) ? $amount_sub[$i] : null;
   
    $acc_dbt1 = !empty($no_of_accounts_dobt1[$i]) ? $no_of_accounts_dobt1[$i] : null;
    $amt_dbt1 = !empty($amount_dobt1[$i]) ? $amount_dobt1[$i] : null;
    $acc_dbt2 = !empty($no_of_accounts_dobt2[$i]) ? $no_of_accounts_dobt2[$i] : null;
    $amt_dbt2 = !empty($amount_dobt2[$i]) ? $amount_dobt2[$i] : null;
    $acc_dbt3 = !empty($no_of_accounts_dobt3[$i]) ? $no_of_accounts_dobt3[$i] : null;
    $amt_dbt3= !empty($amount_dobt3[$i]) ? $amount_dobt3[$i] : null;
    $acc_tot = !empty($no_of_accounts_tot[$i]) ? $no_of_accounts_tot[$i] : null;
    $amt_tot = !empty($amount_tot[$i]) ? $amount_tot[$i] : null;

    // Skip empty rows
    if ($cat || $acc_std || $amt_std || $acc_sub || $amt_sub || $acc_dbt1 || $amt_dbt1 || $acc_dbt2 || $amt_dbt2 || $acc_dbt3 || $amt_dbt3 || $acc_tot || $amt_tot) {
        $stmt = $link->prepare("INSERT INTO  loans_advances (`submission_date`, `bank_name`, `month`, `category`, `no_of_accounts_std`, `amount_std`, `no_of_accounts_sub`, `amount_sub`, `no_of_accounts_dobt1`,
        `amount_dobt1`, `no_of_accounts_dobt2`, `amount_dobt2`, `no_of_accounts_dobt3`, `amount_dobt3`, `no_of_accounts_tot`, `amount_tot`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$submission_date, $bank, $month, $cat, $acc_std, $amt_std, $acc_sub, $amt_sub,$acc_dbt1,$amt_dbt1,$acc_dbt2,$amt_dbt2,$acc_dbt3,$amt_dbt3,$acc_tot,$amt_tot]);
    }
}

$p="Data saved successfully!";

 header("Location:loan_advances.php?success=$p");
?>
