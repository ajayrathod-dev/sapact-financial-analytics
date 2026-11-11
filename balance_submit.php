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
$bank = $_POST['bank_name'];
$month = $_POST['month'];
$side = $_POST['side'];
$category = $_POST['category'];
$category_other = $_POST['category_other'];
$subcategory = $_POST['subcategory'];
$subcategory_other = $_POST['subcategory_other'];
$sub_subcategory = $_POST['sub_subcategory'];
$no_of_accounts = $_POST['no_of_accounts'];
$amount = $_POST['amount'];

foreach ($side as $i => $val) {
    $cat = $category[$i] === '__other__' ? $category_other[$i] : $category[$i];
    $sub = $subcategory[$i] === '__other__' ? $subcategory_other[$i] : $subcategory[$i];
    $subsub = $sub_subcategory[$i] ?? null;
    $acc = !empty($no_of_accounts[$i]) ? $no_of_accounts[$i] : null;
    $amt = !empty($amount[$i]) ? $amount[$i] : null;

    if ($cat || $sub || $subsub || $acc || $amt) {
        $stmt = $link->prepare("INSERT INTO balance_sheet (submission_date, bank_name, month ,side, category, subcategory, sub_subcategory, no_of_accounts, amount) VALUES (?,?,?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$submission_date,$bank, $month,$val, $cat, $sub, $subsub, $acc, $amt]);
        
    //      echo "INSERT INTO balance_sheet (submission_date, bank_name, month, side, category, subcategory, sub_subcategory, no_of_accounts, amount) VALUES (
    //     '$submission_date', '$bank', '$month', '$val', '$cat', '$sub', '$subsub', '$acc', '$amt'
    // );";
 
        
    }
}
 
$p="Data saved successfully!";

 header("Location:networth.php?bank_name=$bank&month=$month");
?>
