<?php
include 'connection.php';


// Get values from GET request safely
$bankname = $link->real_escape_string($_GET['bank_name'] ?? '');
$month    = $link->real_escape_string($_GET['month'] ?? '');

// Get self fund (CRAR NETWORTH) - From networth table networth
$sql = "SELECT networth FROM networth WHERE bank_name = ? AND month_year = ? LIMIT 1";
$stmt = $link->prepare($sql);
$stmt->bind_param("ss", $bankname, $month);
$stmt->execute();
$result = $stmt->get_result();
$crar_networth = 0;
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $crar_networth = floatval($row['networth'] ?? 0);
}
$stmt->close();

// Debugging: Output the fetched value (remove in production)
// echo "CRAR Networth: $crar_networth<br>";

// Get net loss/total erosion - Sum of LOSS category from balance_sheet
$sql = "SELECT SUM(amount) AS net_loss_total_erosion 
        FROM balance_sheet 
        WHERE bank_name = ? AND month = ? AND side = 'Assets' AND category = 'LOSS'";
$stmt = $link->prepare($sql);
$stmt->bind_param("ss", $bankname, $month);
$stmt->execute();
$result = $stmt->get_result();
$net_loss_total_erosion = 0;
if ($result && $row = $result->fetch_assoc()) {
    $net_loss_total_erosion = floatval($row["net_loss_total_erosion"] ?? 0);
}
$stmt->close();

// Debugging: Output the fetched value (remove in production)
// echo "Net Loss/Total Erosion: $net_loss_total_erosion<br>";

// Calculate REMARK for CRAR (Adjusted to use > for better logic alignment)
$remark_crar = ($crar_networth == 0) ? "INSUFFICIENT DATA" : (($net_loss_total_erosion > $crar_networth) ? "SICK" : "HEALTHY");

// Get Actual Gross NPA
$sql = "SELECT 
            SUM(COALESCE(amount_sub, 0) + COALESCE(amount_dobt1, 0) + COALESCE(amount_dobt2, 0) + COALESCE(amount_dobt3, 0)) as npa_amount,
            SUM(COALESCE(amount_tot, 0)) as total_loans
        FROM loans_advances 
        WHERE bank_name = ? AND month = ?";
$stmt = $link->prepare($sql);
$stmt->bind_param("ss", $bankname, $month);
$stmt->execute();
$result = $stmt->get_result();

// Debugging (remove in production)
if ($result) {
    echo "Num rows: " . $result->num_rows . "<br>";
    while ($row = $result->fetch_assoc()) {
        echo "npa_amount: " . $row['npa_amount'] . ", total_loans: " . $row['total_loans'] . "<br>";
    }
    $result->data_seek(0); // Reset pointer
}

$actual_gross_npa = 0;
if ($result && $row = $result->fetch_assoc()) {
    $total_loans = $row["total_loans"] ?? 1; // Avoid division by zero
    $npa_amount = $row["npa_amount"] ?? 0;
    if ($total_loans > 0) {
        $actual_gross_npa = ($npa_amount / $total_loans) * 100;
    }
}
$stmt->close();

// GROSS NPA Threshold is fixed at 10%
$gross_npa_threshold = 10;

// Calculate REMARK for NPA
$remark_npa = ($actual_gross_npa >= $gross_npa_threshold) ? "SICK" : "HEALTHY";

// Calculate FINAL RESULT
$final_result = ($remark_crar == "HEALTHY" && $remark_npa == "HEALTHY") ? "HEALTHY" : "SICK";

$created_at = date("Y-m-d H:i:s");

// Insert into sick_society_report
$sql = "INSERT INTO sick_society_report 
        (bank_name, month_year, net_loss_total_erosion, crar_networth, actual_gross_npa, 
         gross_npa_threshold, remark_crar, remark_npa, final_result, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $link->prepare($sql);
$stmt->bind_param("ssddssssss", $bankname, $month, $net_loss_total_erosion, $crar_networth, 
    $actual_gross_npa, $gross_npa_threshold, $remark_crar, $remark_npa, $final_result, $created_at);

if ($stmt->execute()) {
    header("Location: sick.php");
    exit();
} else {
    echo "Error: " . $link->error;
}

$stmt->close();
$link->close();
?>