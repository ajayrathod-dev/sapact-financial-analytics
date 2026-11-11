<?php
session_start();
include("connection.php");
date_default_timezone_set('Asia/Kolkata');
$today = date("Y-m-d h:i:s");

// 🔐 Sanitize input
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize
    $society_name = sanitize_input($_POST['society_name']);
    $address = sanitize_input($_POST['address']);
    $doa = sanitize_input($_POST['doa']);
    $society_age = sanitize_input($_POST['society_age']);
    $chairman_name = sanitize_input($_POST['chairman_name']);
    $mobile_no = sanitize_input($_POST['mobile_no']);
    $v_chairman_name = sanitize_input($_POST['v_chairman_name']);
    $v_chairman_mobile = sanitize_input($_POST['v_mob'] ?? '');
    $ceo = sanitize_input($_POST['ceo']);
    $co_ordinator_name = sanitize_input($_POST['co_ordinator_name']);
    $co_ordinator_mob = sanitize_input($_POST['co_ordinator_mob']);

    $employee = preg_replace('/[^0-9.]/', '', $_POST['employee']);
    $branches = preg_replace('/[^0-9.]/', '', $_POST['branches']);
    $directors = preg_replace('/[^0-9.]/', '', $_POST['directors']);
    $pigmy = preg_replace('/[^0-9.]/', '', $_POST['pigmy']);
    $deposite = preg_replace('/[^0-9.]/', '', $_POST['deposite']);
    $loan = preg_replace('/[^0-9.]/', '', $_POST['loan']);
    $holder_member = preg_replace('/[^0-9.]/', '', $_POST['holder_member']);

    $stmt = $link->prepare("INSERT INTO society_info (
        society_name, address, doa, society_age, chairman_name, mobile_no, 
        v_chairman_name, v_chairman_mobile, ceo, co_ordinator_name, 
        co_ordinator_mob, branches, directors, employee, pigmy, 
        deposite, loan, holder_member,`by`
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?)");

    $stmt->bind_param(
        "sssssssssssssssssss",
        $society_name, $address, $doa, $society_age, $chairman_name, $mobile_no,
        $v_chairman_name, $v_chairman_mobile, $ceo, $co_ordinator_name,
        $co_ordinator_mob, $branches, $directors, $employee, $pigmy,
        $deposite, $loan, $holder_member,$u_uname
    );

    if ($stmt->execute()) {
        $p = "Bank Added Successfully";
        header("Location:add_bank.php?success=$p");
    } else {
       $p = "Bank Not Added";
        header("Location:add_bank.php?error=$p");
        exit;
    }

    $stmt->close();
}
?>
