<?php
include 'connection.php';


// Get values from GET request safely
$bankname = $link->real_escape_string($_GET['bank_name'] ?? '');
$month    = $link->real_escape_string($_GET['month'] ?? '');

// Initialize all variables
$crr_percentage = 0; $crr_status = 0;
$slr_percentage = 0; $slr_status = 0;
$crar_percentage = 0; $crar_status = 0;
$cd_ratio_percentage = 0;
$gross_npa_percentage = 0; $gross_npa_status = 0;
$net_npa_percentage = 0; $net_npa_status = 0;
$overdue_percentage = 0;
$profit_loss = 0; $profit_loss_status = 0;
$total_score = 0;
$result = "WEAK";

// 1. CRR Calculation (Daily Cash Reserve Ratio) - Minimum 1%
$sql_deposits = "SELECT COALESCE(SUM(amount), 0) AS total_deposits 
                 FROM balance_sheet 
                 WHERE bank_name='$bankname' AND month='$month' 
                 AND side='Liabilities' AND category='DEPOSITS'";
$result_deposits = $link->query($sql_deposits);
if ($result_deposits && $row_dep = $result_deposits->fetch_assoc()) {
    $total_deposits = $row_dep["total_deposits"] ?? 0;
    
    $sql_cash = "SELECT COALESCE(SUM(amount), 0) AS cash_in_hand 
                 FROM balance_sheet 
                 WHERE bank_name='$bankname' AND month='$month' 
                 AND side='Assets' AND (category='CASH IN HAND' OR category='CASH')";
    $result_cash = $link->query($sql_cash);
    if ($result_cash && $row_cash = $result_cash->fetch_assoc()) {
        $cash_in_hand = $row_cash["cash_in_hand"] ?? 0;
        $crr_percentage = ($total_deposits > 0) ? ($cash_in_hand / $total_deposits) * 100 : 0;
        $crr_status = ($crr_percentage >= 1) ? 1 : 0;
    }
}

// 2. SLR Calculation (Statutory Liquidity Ratio) - Minimum 25%
$sql_slr_assets = "SELECT COALESCE(SUM(amount), 0) AS slr_assets 
                   FROM balance_sheet 
                   WHERE bank_name='$bankname' AND month='$month' 
                   AND side='Assets' 
                   AND (category='INVESTMENTS' OR category='CASH IN HAND' OR category='CASH' 
                        OR category='CASH AT BANK')";
$result_slr_assets = $link->query($sql_slr_assets);
if ($result_slr_assets && $row_slr = $result_slr_assets->fetch_assoc()) {
    $slr_assets = $row_slr["slr_assets"] ?? 0;
    $slr_percentage = ($total_deposits > 0) ? ($slr_assets / $total_deposits) * 100 : 0;
    $slr_status = ($slr_percentage >= 25) ? 1 : 0;
}

// 3. CRAR Calculation (Capital to Risk Weighted Assets Ratio) - Minimum 9%
$sql_crar = "SELECT crar_percentage FROM capital_adequacy 
             WHERE bank_name='$bankname' AND month='$month' 
             ORDER BY id DESC LIMIT 1";
$result_crar = $link->query($sql_crar);
if ($result_crar && $row_crar = $result_crar->fetch_assoc()) {
    $crar_percentage = $row_crar["crar_percentage"] ?? 0;
    $crar_status = ($crar_percentage >= 9) ? 1 : 0;
}

// 4. CD Ratio Calculation
$sql_cd = "SELECT cd_ratio FROM cd_ratio 
           WHERE bank_name='$bankname' AND month='$month' 
           ORDER BY id DESC LIMIT 1";
$result_cd = $link->query($sql_cd);
if ($result_cd && $row_cd = $result_cd->fetch_assoc()) {
    $cd_ratio_percentage = $row_cd["cd_ratio"] ?? 0;
}

// 5. Gross NPA Calculation - Should be less than 10%
// CORRECTED NPA CALCULATION
$sql_gross_npa = "SELECT 
    COALESCE(SUM(
        COALESCE(amount_sub, 0) + 
        COALESCE(amount_dobt1, 0) + 
        COALESCE(amount_dobt2, 0) + 
        COALESCE(amount_dobt3, 0)
    ), 0) AS gross_npa 
    FROM loans_advances 
    WHERE bank_name='$bankname' AND month='$month'";

$result_gross_npa = $link->query($sql_gross_npa);
if ($result_gross_npa && $row_gnpa = $result_gross_npa->fetch_assoc()) {
    $gross_npa = $row_gnpa["gross_npa"] ?? 0;
    
    // Get total advances (standard + all NPA categories)
    $sql_total_advances = "SELECT 
        COALESCE(SUM(
            COALESCE(amount_std, 0) + 
            COALESCE(amount_sub, 0) + 
            COALESCE(amount_dobt1, 0) + 
            COALESCE(amount_dobt2, 0) + 
            COALESCE(amount_dobt3, 0)
        ), 0) AS total_advances 
        FROM loans_advances 
        WHERE bank_name='$bankname' AND month='$month'";
    
    $result_advances = $link->query($sql_total_advances);
    if ($result_advances && $row_adv = $result_advances->fetch_assoc()) {
        $total_advances = $row_adv["total_advances"] ?? 1; // Use 1 to avoid division by zero
        
        // Calculate percentages
        $gross_npa_percentage = ($total_advances > 0) ? ($gross_npa / $total_advances) * 100 : 0;
        $gross_npa_status = ($gross_npa_percentage < 10) ? 1 : 0;
        
        // Debug output (you can remove this in production)
        echo "Gross NPA: " . $gross_npa . "<br>";
        echo "Total Advances: " . $total_advances . "<br>";
        echo "Gross NPA Percentage: " . $gross_npa_percentage . "%<br>";
    }
}

// 6. Net NPA Calculation - Should be less than 5%
$sql_provisions = "SELECT 
    COALESCE(SUM(amount), 0) AS provisions 
    FROM balance_sheet 
    WHERE bank_name='$bankname' AND month='$month' 
    AND side='Liabilities' 
    AND (category LIKE '%PROVISION%' OR subcategory LIKE '%provision%' 
         OR category LIKE '%RESERVE%' AND subcategory LIKE '%DOUBTFUL%')";

$result_provisions = $link->query($sql_provisions);
if ($result_provisions && $row_prov = $result_provisions->fetch_assoc()) {
    $provisions = $row_prov["provisions"] ?? 0;
    $net_npa = max(0, $gross_npa - $provisions);
    $net_npa_percentage = ($total_advances > 0) ? ($net_npa / $total_advances) * 100 : 0;
    $net_npa_status = ($net_npa_percentage < 5) ? 1 : 0;
    
    // Debug output
    echo "Provisions: " . $provisions . "<br>";
    echo "Net NPA: " . $net_npa . "<br>";
    echo "Net NPA Percentage: " . $net_npa_percentage . "%<br>";
}

// 7. Overdue Percentage Calculation - Should be less than 5%
// Overdue typically includes Substandard and Doubtful assets
$sql_overdue = "SELECT 
    COALESCE(SUM(
        COALESCE(amount_sub, 0) + 
        COALESCE(amount_dobt1, 0) + 
        COALESCE(amount_dobt2, 0) + 
        COALESCE(amount_dobt3, 0)
    ), 0) AS overdue_amount 
    FROM loans_advances 
    WHERE bank_name='$bankname' AND month='$month'";

$result_overdue = $link->query($sql_overdue);
if ($result_overdue && $row_od = $result_overdue->fetch_assoc()) {
    $overdue_amount = $row_od["overdue_amount"] ?? 0;
    $overdue_percentage = ($total_advances > 0) ? ($overdue_amount / $total_advances) * 100 : 0;
}

// 8. Profit/Loss Calculation - Should be positive
$sql_profit = "SELECT total_profit FROM networth_camales 
               WHERE bank_name='$bankname' AND month_year='$month' 
               ORDER BY id DESC LIMIT 1";
$result_profit = $link->query($sql_profit);
if ($result_profit && $row_profit = $result_profit->fetch_assoc()) {
    $profit_loss = $row_profit["total_profit"] ?? 0;
    $profit_loss_status = ($profit_loss > 0) ? 1 : 0;
}

// Alternative profit calculation if networth_camales doesn't have data
if ($profit_loss == 0) {
    $sql_profit_alt = "SELECT 
        (SELECT COALESCE(SUM(amount), 0) FROM profit_loss WHERE bank_name='$bankname' AND month='$month' AND side='Income') -
        (SELECT COALESCE(SUM(amount), 0) FROM profit_loss WHERE bank_name='$bankname' AND month='$month' AND side='Expenses') AS net_profit";
    $result_profit_alt = $link->query($sql_profit_alt);
    if ($result_profit_alt && $row_profit_alt = $result_profit_alt->fetch_assoc()) {
        $profit_loss = $row_profit_alt["net_profit"] ?? 0;
        $profit_loss_status = ($profit_loss > 0) ? 1 : 0;
    }
}

// Calculate Total Score
$total_score = $crr_status + $slr_status + $crar_status + $gross_npa_status + $net_npa_status + $profit_loss_status;

// Determine Result (STRONG if score >= 6, WEAK otherwise)
$result = ($total_score >= 6) ? "STRONG" : "WEAK";

// // Display calculated values for debugging
// echo "<h3>Calculation Results:</h3>";
// echo "CRR: " . round($crr_percentage, 2) . "% - Status: " . ($crr_status ? "PASS" : "FAIL") . "<br>";
// echo "SLR: " . round($slr_percentage, 2) . "% - Status: " . ($slr_status ? "PASS" : "FAIL") . "<br>";
// echo "CRAR: " . round($crar_percentage, 2) . "% - Status: " . ($crar_status ? "PASS" : "FAIL") . "<br>";
// echo "Gross NPA: " . round($gross_npa_percentage, 2) . "% - Status: " . ($gross_npa_status ? "PASS" : "FAIL") . "<br>";
// echo "Net NPA: " . round($net_npa_percentage, 2) . "% - Status: " . ($net_npa_status ? "PASS" : "FAIL") . "<br>";
// echo "Profit/Loss: " . $profit_loss . " - Status: " . ($profit_loss_status ? "PASS" : "FAIL") . "<br>";
// echo "Total Score: " . $total_score . "/6 - Result: " . $result . "<br>";

// Insert into weak_society table
$sql_insert = "INSERT INTO weak_society (
    bank_name, month_year, crr_percentage, crr_status, 
    slr_percentage, slr_status, crar_percentage, crar_status, 
    cd_ratio_percentage, gross_npa_percentage, gross_npa_status, 
    net_npa_percentage, net_npa_status, overdue_percentage, 
    profit_loss, profit_loss_status, total_score, result
) VALUES (
    '$bankname', '$month', '$crr_percentage', '$crr_status',
    '$slr_percentage', '$slr_status', '$crar_percentage', '$crar_status',
    '$cd_ratio_percentage', '$gross_npa_percentage', '$gross_npa_status',
    '$net_npa_percentage', '$net_npa_status', '$overdue_percentage',
    '$profit_loss', '$profit_loss_status', '$total_score', '$result'
)";

if ($link->query($sql_insert) === TRUE) {
     header("Location: weak_society.php");
    exit();
   } 
   else {
    echo "<p style='color: red;'>Error inserting data: " . $link->error . "</p>";
}

$link->close();
?>