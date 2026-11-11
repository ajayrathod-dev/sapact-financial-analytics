<?php
session_start();
include("connection.php");
date_default_timezone_set('Asia/Kolkata');
$today = date("Y-m-d h:i:s");

function clean_input($data) {
    return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
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



// Fetch & sanitize input
$employee = clean_input($_POST['employee'] ?? '');
$followup_remark = clean_input($_POST['followup_remark'] ?? '');

$sql43 = "SELECT * FROM bank_officials WHERE contact1='$employee'";
$result43 = mysqli_query($link, $sql43);

if ($result43->num_rows > 0) {
    while ($row43 = $result43->fetch_assoc()) {
        $name_of_person = mysqli_real_escape_string($link, $row43['name_of_person']);
        $bank_name = mysqli_real_escape_string($link, $row43['bank_name']);
        
    }
}

// Insert into database using prepared statement
$stmt = $link->prepare("INSERT INTO followup 
    (emp_name, bank_name, mobile_no, followup_remark, `by`) 
    VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name_of_person, $bank_name, $employee, $followup_remark, $u_uname);

if ($stmt->execute()) {
  
     $p = "Followup Submitted Successfully'";
        header("Location:followup.php?success=$p");
} else {
     $p = "Followup Not Submitted'";
        header("Location:followup.php?error=$p");
}

$stmt->close();
$link->close();
?>

