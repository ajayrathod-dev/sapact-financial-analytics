<?php 

include('connection.php');  
$id    = $_GET['bank_name'] ?? '';
$month = $_GET['month'] ?? '';

function getSum($link, $bankId, $month, $where) {
    $sql = "SELECT SUM(amount) AS total FROM balance_sheet 
            WHERE bank_name='$bankId' AND month='$month' AND $where";
    $result = $link->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        return $row['total'] ?? 0;
    }
    return 0;
}

$u_uname = '';
if (isset($_COOKIE['uuid'])) {
    $u_uname = $link->real_escape_string($_COOKIE['uuid']);
}

$sql4 = "SELECT * FROM ods WHERE uuid='$u_uname' AND status='verified'";
$result4 = $link->query($sql4);

if ($result4->num_rows > 0) {
    $row4 = $result4->fetch_assoc();
    $u_uname = $link->real_escape_string($row4['user_id']);
    $br_id = $link->real_escape_string($row4['user_id']);
} else {
    header("location:logout.php");
    exit;
}
//fetching data from balancesheet

$sql = "SELECT * FROM balance_sheet WHERE bank_name = '$id' AND month='$month'";
$result = $link->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {

    $bank_name=$row ['bank_name'];
    $month_year=$row ['month'];





$banksss = array(" Bank Shares");

foreach ($banksss as $array1) {
$xxx = getSum($link, $id, $month, "category='INVESTMENTS' AND subcategory='$array1'");
// $fed_shar  = getSum($link, $id, $month, "category='INVESTMENTS' AND subcategory='Fedration Shares'");
// $oth_shares  = getSum($link, $id, $month, "category='INVESTMENTS' AND subcategory='Other Shares'");
// $dcc_inv     = getSum($link, $id, $month, "category='INVESTMENTS' AND subcategory='DCC Bank Investment'");
// $coop_inv    = getSum($link, $id, $month, "category='INVESTMENTS' AND subcategory='Co-operative Bank Investment'");
// $nat_inv     = getSum($link, $id, $month, "category='INVESTMENTS' AND subcategory='Nationalise Bank Investment'");
// $priv_inv    = getSum($link, $id, $month, "category='INVESTMENTS' AND subcategory='Private Bank Investment'");
// $npa_inv     = getSum($link, $id, $month, "category='INVESTMENTS' AND subcategory='NPA Bank Investment'");
// $mutual_inv  = getSum($link, $id, $month, "category='INVESTMENTS' AND subcategory='Mutual Fund Investment'");
// $smal_inv    = getSum($link, $id, $month, "category='INVESTMENTS' AND subcategory='Small Finance Bank Investment'");
// $nagri_inv   = getSum($link, $id, $month, "category='INVESTMENTS' AND subcategory='Nagari & Multistate Society Investment'");
// $deposit     = getSum($link, $id, $month, "category='DEPOSITS'");

//investment calculations
$percent=$xxx/$deposit*100;
// $percent1=$fed_shares/$deposit*100;
// $percent2=$oth_shares/$deposit*100;
// $percent3=$dcc_inv/$deposit*100;
// $percent4=$coop_inv/$deposit*100;
// $percent5=$nat_inv/$deposit*100;
// $percent6=$priv_inv/$deposit*100;
// $percent7=$npa_inv/$deposit*100;
// $percent8=$mutual_inv/$deposit*100;
// $percent9=$smal_inv/$deposit*100;
// $percent10=$nagri_inv/$deposit*100;

  if($bank_name!=$id && $month_year!=$month){
    
    $sql = "INSERT INTO `investment_exposure`(`bank_id`, `month`, `bank_name`, `deposit_percentage`, `amount`, `by`) VALUES
       ('$id', '$month_year', '$array1', '$percent', '$xxx','$u_uname')";



    if ($link->query($sql) === TRUE) {
        $p = "Data Saved Successfully";
         header("Location:crar_report_log.php?bank_name=$id&month=$month");
        exit;
    } else {
        $p = "Data Not Saved";
        header("Location: balance_form.php?error=" . urlencode($p));
        exit;
    }
    }

    else
    {
      
         $sql = "UPDATE `networth_camales` 
          SET 
            share_capital   = '$pu_exp',
            reserve_fund    = '$res_fund',
            building_fund   = '$res_build1',
            dividand_fund   = '$res_free1',
            investment_fund = '$res_inv',
            bad_dbt         = '$bad_dbt',
            current_profit  = '$profit_minus',
            total_loss      = '$tot_loss',
            total_profit    = '$total',
            networth        = '$networth',
            bank_name       = '$id',
            month_year      = '$month',
            `by`            = '$u_uname'
        WHERE bank_name = '$id' AND month_year = '$month'";


    if ($link->query($sql) === TRUE) {
        $p = "Data Saved Successfully";
        
        header("Location:mis_report.php?bank_name=" . urlencode($p));
        exit;
    } else {
        $p = "Data Not Saved";
        header("Location: balance_form.php?error=" . urlencode($p));
        exit;
    }
    }
}

  }


} else {
  echo "0 results";
}


    
  
 