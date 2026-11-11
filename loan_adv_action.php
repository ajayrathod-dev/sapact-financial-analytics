<?php
include 'connection.php';

// Get values from GET request safely
$bankname = $link->real_escape_string($_GET['bank_name'] ?? '');
$month    = $link->real_escape_string($_GET['month'] ?? '');

 // casa start 
$sql = "SELECT SUM(amount) AS merchant_l
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='LOANS & ADVANCES' AND subcategory='Merchant loan'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $merchant_l = $row["merchant_l"];
}
 

 // gold loan start
 $sql = "SELECT SUM(amount) AS gold_l
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='LOANS & ADVANCES' AND sub_subcategory='Gold Loan (135)'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $gold_l = $row["gold_l"];
}

// special gl start 
$sql = "SELECT SUM(amount) AS special_gl
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='LOANS & ADVANCES' AND subcategory='Special Gold Loan'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $special_gl = $row["special_gl"];
}
 //end
 
 //inst loan start 
$sql = "SELECT SUM(amount) AS inst_l
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='LOANS & ADVANCES' AND subcategory='Instant Loan'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $inst_l = $row["inst_l"];
}



// fd od loan start
 
$sql = "SELECT SUM(amount) AS fd_od_l
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='LOANS & ADVANCES' AND subcategory='FD OD Loan'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $fd_od_l = $row["fd_od_l"];
}


// employee loan start

$sql = "SELECT SUM(amount) AS emp_l
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='LOANS & ADVANCES' AND subcategory='Employee Loan'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $emp_l = $row["emp_l"];
}



// mahila sam start
$sql = "SELECT SUM(amount) AS mahila_l
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='LOANS & ADVANCES' AND subcategory='Mahila Samruddhi Loan'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $mahila_l = $row["mahila_l"];
}

// morgage loan start
$sql = "SELECT SUM(amount) AS morg_l
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='LOANS & ADVANCES' AND subcategory='Morgage Loan'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $morg_l = $row["morg_l"];
}

// rd od loan start
$sql = "SELECT SUM(amount) AS rd_od
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='LOANS & ADVANCES' AND subcategory='RD OD Loan'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $rd_od = $row["rd_od"];
}

//  fd cc  loan start
$sql = "SELECT SUM(amount) AS fd_cc_l
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='LOANS & ADVANCES' AND subcategory='FD CC Loan'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $fd_cc_l = $row["fd_cc_l"];
}

// salary loan start
$sql = "SELECT SUM(amount) AS salary_l
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Assets' AND category='LOANS & ADVANCES' AND subcategory='Salary Loan'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $salary_l = $row["salary_l"];
}

// total start 
$total = $merchant_l + $gold_l + $special_gl + $inst_l + $fd_od_l + $emp_l + $mahila_l + $morg_l + $rd_od_l + $fd_cc_l + $salary_l;

// locker dep end
//total debts end
//  $id = 0;
//  $merchant_l = 0;
//$gold_l = 0;
// $special_gl = 0;
// $inst_l = 0;
// $fd_od_l = 0;
// $emp_l = 0;
// $mahila_l = 0;
// $morg_l = 0;
// $rd_od = 0;
// $fd_cc_l = 0;
// $salary_l = 0;
// $total = 0;
$total_incr = 0;
$in_per = 0;
$monthly_incr = 0;
$monthly_incr_perct = 0;
$vision_per_m = 0;
$vision_gap = 0;
$created_at = date("Y-m-d H:i:s");

// Insert into cd_ratio with correct column order
$sql = "INSERT INTO `loans_adv`(`id`, `bank_name`, `month_year`, `merchant_l`, `gold_l`, `special_gl`, `inst_l`, `fd_od_l`, `emp_l`, `mahila_l`, `morg_l`,
`rd_od`, `fd_cc_l`, `salary_l`, `total`,
`total_incr`, `in_per`, `monthly_incr`, `monthly_incr_perct`, `vision_per_m`, `vision_gap`, `created_at`) VALUES ('$id','$bankname','$month','$merchant_l','$gold_l',
'$special_gl','$inst_l','$fd_od_l','$emp_l','$mahila_l','$morg_l','$rd_od','$fd_cc_l','$salary_l',
'$total','$total_incr','$in_per','$monthly_incr','$monthly_incr_perct','$vision_per_m','$vision_gap','$created_at')";

if ($link->query($sql) === TRUE) {
    header("Location: loan_adv.php");
    exit();
} else {
    echo "Error: " . $link->error;
}
?>
