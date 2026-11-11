<?php
include 'connection.php';

// Get values from GET request safely
$bankname = $link->real_escape_string($_GET['bank_name'] ?? '');
$month    = $link->real_escape_string($_GET['month'] ?? '');

 // casa start 
$sql = "SELECT SUM(amount) AS casa 
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='DEPOSITS' AND subcategory='CASA DEPOSIT'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $casa = $row["casa"];
}
 //casa end

 // fixed deposit start
 $sql = "SELECT SUM(amount) AS fixed_dep
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='DEPOSITS' AND subcategory='Term Deposit' AND sub_subcategory='Fixed Deposit'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $fixed_dep = $row["fixed_dep"];
}

// monthly income scheme start 
$sql = "SELECT SUM(amount) AS monthly_inc_sch
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='DEPOSITS' AND subcategory='Term Deposit' AND sub_subcategory='Monthly Income Scheme'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $monthly_inc_sch = $row["monthly_inc_sch"];
}
 //end
 
 //new fd start 
$sql = "SELECT SUM(amount) AS new_fd
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='DEPOSITS' AND subcategory='Term Deposit' AND sub_subcategory='New Fixed Deposit'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $new_fd = $row["new_fd"];
}

// new fd end

// dam did scheme start
$sql = "SELECT SUM(amount) AS dam_did
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='DEPOSITS' AND subcategory='Term Deposit' AND sub_subcategory='Dam Didpat Scheme'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $dam_did = $row["dam_did"];
}

// dam did scheme end

// dam dup sch start

$sql = "SELECT SUM(amount) AS dam_dup
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='DEPOSITS' AND subcategory='Term Deposit' AND sub_subcategory='Dam Dupat Scheme'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $dam_dup = $row["dam_dup"];
}

// dam dup sch end

// rd start
$sql = "SELECT SUM(amount) AS rd
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='DEPOSITS' AND subcategory='Term Deposit' AND sub_subcategory='RD'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $rd = $row["rd"];
}
//rd end 

// pigmy start
$sql = "SELECT SUM(amount) AS pigmy
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='DEPOSITS' AND subcategory='Term Deposit' AND sub_subcategory='Pigmy Collection'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $pigmy = $row["pigmy"];
}
// pigmy end

//ladki lek start 
$sql = "SELECT SUM(amount) AS ladki_l
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='DEPOSITS' AND subcategory='Term Deposit' AND sub_subcategory='Ladki Lek Thev Yojana'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $ladki_l = $row["ladki_l"];
}
// ladki lek end

// security dep start 
$sql = "SELECT SUM(amount) AS security_dep
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='DEPOSITS' AND subcategory='Term Deposit' AND sub_subcategory='Security Deposit'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $security_dep = $row["security_dep"];
}
// security dep end 

// locker dep start
$sql = "SELECT SUM(amount) AS locker_dep
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Liabilities' AND category='DEPOSITS' AND subcategory='Term Deposit' AND sub_subcategory='Locker Deposit'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $locker_dep = $row["locker_dep"];
}

// locker dep end


//total start 
$total =  $casa + $fixed_dep + $monthly_inc_sch + $new_fd + $dam_did + $dam_dup + $rd + $pigmy + $ladki_l + $security_dep + $locker_dep ;


//casa per start 
$casa_per = ($casa*100)/$total;

// $id = 0;
// // $casa = 0;
// // $fixed_dep = 0;
// // $monthly_inc_sch = 0;
// $new_fd = 0;
// $dam_did = 0;
// $dam_dup = 0;
// $rd = 0;
// $pigmy = 0;
// $ladki_l = 0;
// $security_dep = 0;
// $locker_dep = 0;
// $casa_per = 0;
// $total = 0;
$total_incr_m = 0;
$total_incr_perct =0;
$monthly_incr = 0;
$monthly_incr_perct = 0;
$vision_per_m = 0;
$vision_gap = 0;
$created_at = date("Y-m-d H:i:s");

// Insert into cd_ratio with correct column order
$sql = "INSERT INTO `dep_adv`
(`id`, `bank_name`, `month_year`, `casa`, `fixed_dep`, `monthly_inc_sch`, `new_fd`,
`dam_did`, `dam_dup`, `rd`, `pigmy`, `ladki_l`, `security_dep`, `locker_dep`,`total`,`total_incr_m`,`total_incr_perct`,`monthly_incr`,`monthly_incr_perct`,`vision_per_m`,`vision_gap`,`casa_per`, `created_at`)
VALUES 
('$id', '$bankname', '$month', '$casa', '$fixed_dep', '$monthly_inc_sch', '$new_fd',
'$dam_did', '$dam_dup', '$rd', '$pigmy', '$ladki_l', '$security_dep', '$locker_dep','$total','$total_incr_m','$total_incr_perct','$monthly_incr','$monthly_incr_perct','$vision_per_m','$vision_gap','$casa_per', '$created_at')";

if ($link->query($sql) === TRUE) {
    header("Location: dep_adv.php");
    exit();
} else {
    echo "Error: " . $link->error;
}
?>
