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
$bank = $_GET['bank'];
$month = $_GET['month'];
$side = $_GET['side'];
$category = $_GET['category'];
$category_other = $_GET['category_other'];
$subcategory = $_GET['subcategory'];
$subcategory_other = $_GET['subcategory_other'];
$sub_subcategory = $_GET['sub_subcategory'];
$amount = $_GET['amount'];

foreach ($side as $i => $val) {
    $cat = $category[$i] === '__other__' ? $category_other[$i] : $category[$i];
    $sub = $subcategory[$i] === '__other__' ? $subcategory_other[$i] : $subcategory[$i];
    $subsub = isset($sub_subcategory[$i]) ? $sub_subcategory[$i] : '';
    $amt = !empty($amount[$i]) ? $amount[$i] : 0;

    if ($cat || $sub || $subsub || $amt) {
        $stmt = $link->prepare("INSERT INTO profit_loss (submission_date, bank_name, month, side, category, subcategory, sub_subcategory, amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: (" . $link->errno . ") " . $link->error);
        }
        $stmt->bind_param("sssssssd", $submission_date, $bank, $month, $val, $cat, $sub, $subsub, $amt);
        if (!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }
        $stmt->close();
    }
}

$p = "Data saved successfully!";
header("Location:profit_loss_form.php?success=" . urlencode($p));
exit;
?>