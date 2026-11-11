<?php
include('connection.php');  

$id    = $_GET['bank_name'] ?? '';
$month = $_GET['month'] ?? '';

if (isset($_COOKIE['uuid'])) {
    $u_uname = $link->real_escape_string($_COOKIE['uuid']);
} else {
    $u_uname = '';
}

// Validate user
$sql4 = "SELECT * FROM ods WHERE uuid='$u_uname' AND status='verified'";
$result4 = $link->query($sql4);
if ($result4->num_rows > 0) {
    $row4 = $result4->fetch_assoc();
    $u_uname = $link->real_escape_string($row4['user_id']);
    $br_id = $u_uname;
} else {
    header("location:logout.php");
    exit;
}


// Get all crar_report rows
$sql12 = "SELECT * FROM crar_report";
$result12 = $link->query($sql12);

if ($result12->num_rows > 0) {
    while ($row12 = $result12->fetch_assoc()) {
        
        // ✅ check correct column name in crar_report
        $taleband1  = $link->real_escape_string($row12['taleband1']); 
        $percentage = $link->real_escape_string($row12['percentage']);
        
        if ($id && $month && $taleband1) {
            
            // ✅ Define function OUTSIDE loop

   if($taleband1=='CASH IN HAND')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='$taleband1'";
   }else if($taleband1=='CASH AT BANK')
   {
       
        $sql = "SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$id' AND `month`='$month' AND `side`='Assets' AND `category`='CASH AT BANK'";
   }
   else if($taleband1=='NPA Bank')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='CASH AT BANK' AND subcategory='NPA Bank'";
   }
    else if($taleband1=='DCC Bank Investment')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='INVESTMENTS' AND subcategory='DCC Bank Investment'";
   }
    else if($taleband1=='Private Bank Investment')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='INVESTMENTS' AND subcategory='Private Bank Investment'";
   }
    else if($taleband1=='Shares Investment')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='INVESTMENTS' AND subcategory='Shares Investment'";
   }
    else if($taleband1=='Co-operative Bank Investment')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='INVESTMENTS' AND subcategory='Co-operative Bank Investment'";
   }
    else if($taleband1=='Nationalise Bank Investment')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='INVESTMENTS' AND subcategory='Nationalise Bank Investment'";
   }
    else if($taleband1=='NPA Bank Investment')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='INVESTMENTS' AND subcategory='NPA Bank Investment'";
   }
    else if($taleband1=='INVESTMENTS')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='INVESTMENTS'";
   }
    else if($taleband1=='Mutual Fund Investment')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='INVESTMENTS' AND subcategory='Mutual Fund Investment'";
   }
     else if($taleband1=='Small Finance Bank Investment')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='INVESTMENTS' AND subcategory='Small Finance Bank Investment'";
   }
     else if($taleband1=='Nagari & Multistate Society Investment')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='INVESTMENTS' AND subcategory='Nagari & Multistate Society Investment'";
   }
     else if($taleband1=='Other Investment')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='INVESTMENTS' AND subcategory='Other Investment'";
   }
     else if($taleband1=='Deposit Loans Below 12 Month')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES' AND subcategory='Deposit Loans Below 12 Month'";
   }
     else if($taleband1=='Deposit Loans Above 12 Month')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES' AND subcategory='Deposit Loans Above 12 Month'";
   }
     else if($taleband1=='Personal Loan')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES' AND subcategory='Personal Loan'";
   }
     else if($taleband1=='Employee Loan')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES' AND subcategory='Employee Loan'";
   }
     else if($taleband1=='Gold Loan Below 12 Month')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES' AND subcategory='Gold Loan Below 12 Month'";
   }
     else if($taleband1=='Gold Loan Above 12 Month')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES' AND subcategory='Gold Loan Above 12 Month'";
   }
     else if($taleband1=='Gold Loan Above 12 Month (Amount)')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES' AND subcategory='Gold Loan Above 12 Month (Amount)'";
   }
     else if($taleband1=='Home Loan Below 30 L')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES' AND subcategory='Home Loan Below 30 L'";
   }
    else if($taleband1=='Home Loan Above 30 L')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES' AND subcategory='Home Loan Above 30 L'";
   }
    else if($taleband1=='Salary Loan')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES' AND subcategory='Salary Loan'";
   }
    else if($taleband1=='Director Loan Secure')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES' AND subcategory='Director Loan Secure'";
   }
    else if($taleband1=='Director Loan Unsecure')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES' AND subcategory='Director Loan Unsecure'";
   }
    else if($taleband1=='Director Loans with Authorised')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES' AND subcategory='Director Loans with Authorised'";
   }
    else if($taleband1=='Director Loans with Unauthorised')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES' AND subcategory='Director Loans with Unauthorised'";
   }
     else if($taleband1=='Other Loans')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOANS & ADVANCES' AND subcategory='Other Loans'";
   }
     else if($taleband1=='LAND & BUILDING')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LAND & BUILDING'";
   }
     else if($taleband1=='COMPUTER HARDWARE & SOFTWARE & ELECTRONICS')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='COMPUTER HARDWARE & SOFTWARE & ELECTRONICS'";
   }
     else if($taleband1=='NON BANKING ASSETS WITHIN 7 YEARS')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='NON BANKING ASSETS WITHIN 7 YEARS'";
   }
     else if($taleband1=='INTREST RECEIVABLE ON INVESTMENT & ADVANCES')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='INTREST RECEIVABLE ON INVESTMENT & ADVANCES'";
   }
     else if($taleband1=='OTHER ASSETS/ RECEIVABLE')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='OTHER ASSETS/ RECEIVABLE'";
   }
      else if($taleband1=='BRANCH ADJUSTMENT')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='BRANCH ADJUSTMENT'";
   }
      else if($taleband1=='LOSS')
   {
        $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$id' AND month='$month' AND category='LOSS'";
   }
   else
   {
       
   }
   
    $result = $link->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        $pu_exp= $row['total'] ?? 0;
    }
    else
    {
        $pu_exp= 0;
    }

      
            $tot_per = ($percentage/100)*$pu_exp;

            // Check if record exists
            $check_sql = "SELECT * FROM crar_report_log 
                          WHERE bank_name = '$id' AND month = '$month' AND taleband = '$taleband1'";
            $check_res = $link->query($check_sql);

            if ($check_res->num_rows == 0) {
                // INSERT
              echo  $insert_sql = "INSERT INTO crar_report_log 
                                (bank_name, month, taleband, percent, yene_baki, jokhim, `by`)
                               VALUES 
                                ('$id', '$month', '$taleband1', '$percentage', '$pu_exp', '$tot_per', '$u_uname')";
                $link->query($insert_sql);

            } else {
                // UPDATE
              echo  $update_sql = "UPDATE crar_report_log 
                               SET yene_baki = '$pu_exp', jokhim = '$tot_per' 
                               WHERE bank_name = '$id' AND month = '$month' AND taleband = '$taleband1'";
                $link->query($update_sql);
            }
        }
    }

    $p = "Data Saved Successfully";
     header("Location:crar_reports1.php?bank_name=$id&month=$month");
    exit;

} else {
    $p = "Data Not Saved";
        header("Location: crar_reports1.php?error=" . urlencode($p));
        exit;
}




?>
