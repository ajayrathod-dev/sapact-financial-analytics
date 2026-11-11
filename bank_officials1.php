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
$bank_name = clean_input($_POST['bank_name'] ?? '');
$name_of_person = clean_input($_POST['name_of_person'] ?? '');
$contact1 = clean_input($_POST['contact1'] ?? '');
$contact2 = clean_input($_POST['contact2'] ?? '');
$post = clean_input($_POST['post'] ?? '');


// Insert into database using prepared statement
$stmt = $link->prepare("INSERT INTO bank_officials 
    (bank_name, name_of_person, contact1, alt_contact2, post, `by`) 
    VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $bank_name, $name_of_person, $contact1, $contact2, $post, $u_uname);

if ($stmt->execute()) {
  
     $p = "Bank Staff Added Successfully'";
        header("Location:bank_officials.php?success=$p");
} else {
     $p = "Bank Staff Not Added'";
        header("Location:bank_officials.php?error=$p");
}

$stmt->close();
$link->close();
?>

