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
$per=$_POST['per'];
$tal=$_POST['tal'];
$tal1=$_POST['tal1'];
$id=$_POST['id'];
$stmt = $link->prepare("UPDATE crar_report SET percentage = ?, taleband=?, taleband1=? WHERE id = ?");
$stmt->bind_param("sssi", $per,$tal,$tal1,$id);

// Execute and check
if ($stmt->execute()) {
    $p = "Record Updated Successfully";
    header("Location: crar_report.php?success=" . urlencode($p));
} else {
    $p = "Record Not Updated";
    header("Location: crar_report.php?error=" . urlencode($p));
}

$stmt->close();
$link->close();
?>

