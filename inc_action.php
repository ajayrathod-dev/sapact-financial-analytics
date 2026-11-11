<?php
include 'connection.php';
// error_reporting(0);
// ini_set('display_errors', 0);

// Get values from GET request safely
$bankname = $link->real_escape_string($_GET['bank_name'] ?? '');
$month    = $link->real_escape_string($_GET['month'] ?? '');

// Validate inputs
if (empty($bankname) || empty($month)) {
    http_response_code(400);
    die("Error: Bank name or month is missing.");
}

// Fetch profit from balance_sheet as an example of select query usage
$prof_monthly = 0.00;
$sql_profit = "SELECT amount FROM balance_sheet WHERE bank_name = '$bankname' AND month = '$month' AND side = 'Liabilities' AND category = 'PROFIT AND LOSS' AND subcategory = 'CURRENT YEAR PROFIT' LIMIT 1";
$result_profit = $link->query($sql_profit);
if ($result_profit->num_rows > 0) {
    $row_profit = $result_profit->fetch_assoc();
    $prof_monthly = (float)$row_profit['amount'];
}

// Fetch interest payable for deposit (interest expense example)
$int_monthly = 0.00;
$sql_int = "SELECT amount FROM balance_sheet WHERE bank_name = '$bankname' AND month = '$month' AND side = 'Liabilities' AND category = 'OTHER LIABILITIES' AND subcategory = 'INT.PAYABLE FOR DEPOSIT' LIMIT 1";
$result_int = $link->query($sql_int);
if ($result_int->num_rows > 0) {
    $row_int = $result_int->fetch_assoc();
    $int_monthly = (float)$row_int['amount'];
}

// Fetch interest receivable (interest income example)
$inco_monthly_income = 0.00;
$sql_inco = "SELECT amount FROM balance_sheet WHERE bank_name = '$bankname' AND month = '$month' AND side = 'Assets' AND category = 'INT.RECEIVABLE ON INVESTMENT & ADVANCES' LIMIT 1";
$result_inco = $link->query($sql_inco);
if ($result_inco->num_rows > 0) {
    $row_inco = $result_inco->fetch_assoc();
    $inco_monthly_income = (float)$row_inco['amount'];
}

// Fetch sum of loans & advances for adv_monthly_income (as per analysis, representing total advances base for interest)
$adv_monthly_income = 0.00;
$sql_adv = "SELECT SUM(amount) AS total_adv FROM balance_sheet WHERE bank_name = '$bankname' AND month = '$month' AND side = 'Assets' AND category = 'LOANS & ADVANCES' AND amount IS NOT NULL AND amount > 0";
$result_adv = $link->query($sql_adv);
if ($result_adv->num_rows > 0) {
    $row_adv = $result_adv->fetch_assoc();
    $adv_monthly_income = (float)$row_adv['total_adv'];
}

// Fetch sum of investments for inv_monthly_income
$inv_monthly_income = 0.00;
$sql_inv = "SELECT SUM(amount) AS total_inv FROM balance_sheet WHERE bank_name = '$bankname' AND month = '$month' AND side = 'Assets' AND category = 'INVESTMENTS' AND amount IS NOT NULL AND amount > 0";
$result_inv = $link->query($sql_inv);
if ($result_inv->num_rows > 0) {
    $row_inv = $result_inv->fetch_assoc();
    $inv_monthly_income = (float)$row_inv['total_inv'];
}

// Fetch sum of own assets for mgmt_total (as per analysis, representing management/operational expenses base)
$mgmt_total = 0.00;
$sql_mgmt = "SELECT SUM(amount) AS total_mgmt FROM balance_sheet WHERE bank_name = '$bankname' AND month = '$month' AND side = 'Assets' AND category = 'OWN ASSETS' AND amount IS NOT NULL AND amount > 0";
$result_mgmt = $link->query($sql_mgmt);
if ($result_mgmt->num_rows > 0) {
    $row_mgmt = $result_mgmt->fetch_assoc();
    $mgmt_total = (float)$row_mgmt['total_mgmt'];
}

// Accurate calculations based on sheet analysis and db data
// Sheet shows cumulative and totals; here we use db sums and calculate differences/percentages where possible
// Since db has single period, incre is 0, perc is 0 or calculated if needed
$adv_total = $adv_monthly_income;
$adv_total_incr = 0.00;
$adv_total_incr_perc = 0.00;

// Inv
$inv_total = $inv_monthly_income;
$inv_total_incr = 0.00;
$inv_total_incr_perc = 0.00;

// Oth (set to db other assets if applicable)
$oth_monthly_income = 0.00;
$sql_oth = "SELECT SUM(amount) AS total_oth FROM balance_sheet WHERE bank_name = '$bankname' AND month = '$month' AND side = 'Assets' AND category = 'OTHER ASSETS/ RECEIVABLE' AND amount IS NOT NULL AND amount > 0";
$result_oth = $link->query($sql_oth);
if ($result_oth->num_rows > 0) {
    $row_oth = $result_oth->fetch_assoc();
    $oth_monthly_income = (float)$row_oth['total_oth'];
}
$oth_total = $oth_monthly_income;
$oth_total_incr = 0.00;
$oth_total_incr_perc = 0.00;

// Total income (sum of adv, inv, oth, inco from receivable)
$inco_total = $inco_monthly_income;
$vision = 0.00; // No projection in db, set 0
$inco_total_incr_perc = 0.00;

// Int
$int_total = $int_monthly;
$int_total_incr = 0.00;
$int_in_perc = 0.00;

// Od
// $od_monthly = 0.00;
$sql = "SELECT SUM(amount) AS od_monthly
        FROM profit_loss 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Expenses' AND category='INTEREST PAID ON BORROWINGS'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $od_monthly = $row["od_monthly"];
}

$od_total = $od_monthly ;
$od_total_incr = 0.00;
$od_in_perc = 0.00;

// Mgmt (split total into prashaskiy and asthapna based on sheet ratio ~48% prash, 52% asth)
$mgmt_prashaskiy = $mgmt_total * 0.48;
$mgmt_asthapna = $mgmt_total * 0.52;
$mgmt_vision = 0.00;
$mgmt_in_perc = 0.00;

// Prov
// $od_monthly = 0.00;
$sql = "SELECT SUM(amount) AS prov_monthly
        FROM profit_loss 
        WHERE bank_name='$bankname' AND month='$month' 
          AND side='Expenses' AND category='PROVISIONS'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $prov_monthly = $row["prov_monthly"];
}

// $prov_monthly = 0.00; // No direct, set 0
$prov_total =$prov_monthly;
$prov_total_incr = 0.00;
$prov_in_perc = 0.00;

// Prof
$prof_total = $prof_monthly;
$prof_total_incr = 0.00;
$prof_in_perc = 0.00;

// Total
$total_mge = $int_monthly + $od_monthly + $mgmt_total;
$total_vision = 0.00;
$total_in_perc = 0.00;

// ============================================================================
// INSERT DATA WITH ALL COLUMNS
// ============================================================================

$sql = "INSERT INTO income_exp (
    bank_name, 
    month_year,
    
    -- Advances Section
    adv_monthly_income,
    adv_total,
    adv_total_incr,
    adv_total_incr_perc,
    
    -- Investment Section
    inv_monthly_income,
    inv_total,
    inv_total_incr,
    inv_total_incr_perc,
    
    -- Other Income Section
    oth_monthly_income,
    oth_total,
    oth_total_incr,
    oth_total_incr_perc,
    
    -- Total Income Section
    inco_monthly_income,
    inco_total,
    vision,
    inco_total_incr_perc,
    
    -- Interest on Deposit Section
    int_monthly,
    int_total,
    int_total_incr,
    int_in_perc,
    
    -- On O/D or Loans Section
    od_monthly,
    od_total,
    od_total_incr,
    od_in_perc,
    
    -- Management Expenses Section
    mgmt_prashaskiy,
    mgmt_asthapna,
    mgmt_total,
    mgmt_vision,
    mgmt_in_perc,
    
    -- Provision Section
    prov_monthly,
    prov_total,
    prov_total_incr,
    prov_in_perc,
    
    -- Profit Section
    prof_monthly,
    prof_total,
    prof_total_incr,
    prof_in_perc,
    
    -- Total MGE + Interest Section
    total_mge,
    total_vision,
    total_in_perc,
    
    created_at
) VALUES (
    '$bankname', 
    '$month',
    
    -- Advances Section
    $adv_monthly_income,
    $adv_total,
    $adv_total_incr,
    $adv_total_incr_perc,
    
    -- Investment Section
    $inv_monthly_income,
    $inv_total,
    $inv_total_incr,
    $inv_total_incr_perc,
    
    -- Other Income Section
    $oth_monthly_income,
    $oth_total,
    $oth_total_incr,
    $oth_total_incr_perc,
    
    -- Total Income Section
    $inco_monthly_income,
    $inco_total,
    $vision,
    $inco_total_incr_perc,
    
    -- Interest on Deposit Section
    $int_monthly,
    $int_total,
    $int_total_incr,
    $int_in_perc,
    
    -- On O/D or Loans Section
    $od_monthly,
    $od_total,
    $od_total_incr,
    $od_in_perc,
    
    -- Management Expenses Section
    $mgmt_prashaskiy,
    $mgmt_asthapna,
    $mgmt_total,
    $mgmt_vision,
    $mgmt_in_perc,
    
    -- Provision Section
    $prov_monthly,
    $prov_total,
    $prov_total_incr,
    $prov_in_perc,
    
    -- Profit Section
    $prof_monthly,
    $prof_total,
    $prof_total_incr,
    $prof_in_perc,
    
    -- Total MGE + Interest Section
    $total_mge,
    $total_vision,
    $total_in_perc,
    
    NOW()
)";

if ($link->query($sql) === TRUE) {
    $link->close();
    header("Location: income_exp.php?bank_name=");
    exit();
} else {
    $error = "Error inserting data: " . $link->error;
    error_log($error);
    error_log("SQL: " . $sql);
    http_response_code(500);
    echo $error;
    $link->close();
}
?>