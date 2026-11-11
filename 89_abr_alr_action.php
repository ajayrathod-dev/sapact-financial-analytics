<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include 'connection.php';

date_default_timezone_set('Asia/Kolkata');

// Get values safely
$bank_name = mysqli_real_escape_string($link, $_GET['bank_name'] ?? '');
$month = mysqli_real_escape_string($link, $_GET['month'] ?? '');

if (empty($bank_name) || empty($month)) {
    die("Bank name and month are required.");
}

// Initialize all variables
$deposit = $loan = $average_deposit = $average_loan = 0;
$monthly_avg_deposit = $monthly_avg_loan = $interest_paid = $interest_received = 0;
$abr = $alr = $spread = 0;

try {
    // 1. GET DEPOSIT (Total Deposit - CASA + Term)
    $sql_deposit = "SELECT SUM(amount) as total_deposit FROM balance_sheet 
                   WHERE bank_name='$bank_name' AND month='$month' 
                   AND side='Liabilities' AND category LIKE '%DEPOSIT%'";
    $result_deposit = mysqli_query($link, $sql_deposit);
    $deposit = ($result_deposit && $row = mysqli_fetch_assoc($result_deposit)) ? ($row['total_deposit'] ?? 0) : 0;

    // 2. GET LOAN (Total Loans)
    $sql_loan = "SELECT SUM(amount) as total_loan FROM balance_sheet 
                WHERE bank_name='$bank_name' AND month='$month' 
                AND side='Assets' AND (category LIKE '%LOAN%' OR category LIKE '%ADVANCES%')";
    $result_loan = mysqli_query($link, $sql_loan);
    $loan = ($result_loan && $row = mysqli_fetch_assoc($result_loan)) ? ($row['total_loan'] ?? 0) : 0;

    // 3. CALCULATE AVERAGE DEPOSIT (Based on Excel logic)
    // For first month, take from last year data, for subsequent months: Current Month + Last Month Average
    $prev_month = date('Y-m', strtotime($month . '-01 -1 month'));
    
    $sql_prev_avg_deposit = "SELECT average_deposit FROM abr_alr 
                            WHERE bank_name='$bank_name' AND month_year='$prev_month' 
                            ORDER BY id DESC LIMIT 1";
    $result_prev_avg_deposit = mysqli_query($link, $sql_prev_avg_deposit);
    
    if ($result_prev_avg_deposit && $row = mysqli_fetch_assoc($result_prev_avg_deposit)) {
        $average_deposit = $row['average_deposit'] + $deposit;
    } else {
        // First month or no previous data - use current deposit as average
        $average_deposit = $deposit;
    }

    // 4. CALCULATE AVERAGE LOAN (Based on Excel logic)
    $sql_prev_avg_loan = "SELECT average_loan FROM abr_alr 
                         WHERE bank_name='$bank_name' AND month_year='$prev_month' 
                         ORDER BY id DESC LIMIT 1";
    $result_prev_avg_loan = mysqli_query($link, $sql_prev_avg_loan);
    
    if ($result_prev_avg_loan && $row = mysqli_fetch_assoc($result_prev_avg_loan)) {
        $average_loan = $row['average_loan'] + $loan;
    } else {
        // First month or no previous data - use current loan as average
        $average_loan = $loan;
    }

    // 5. CALCULATE MONTHLY AVERAGES (Divide by 12 as per Excel)
    $monthly_avg_deposit = $average_deposit / 12;
    $monthly_avg_loan = $average_loan / 12;

    // 6. GET INTEREST PAID (From profit_loss table)
    $sql_interest_paid = "SELECT SUM(amount) as total_interest_paid FROM profit_loss 
                         WHERE bank_name='$bank_name' AND month='$month' 
                         AND side='Expenses' 
                         AND (category LIKE '%INTEREST PAID%' OR category LIKE '%INT. PAID%')";
    $result_interest_paid = mysqli_query($link, $sql_interest_paid);
    $interest_paid = ($result_interest_paid && $row = mysqli_fetch_assoc($result_interest_paid)) ? ($row['total_interest_paid'] ?? 0) : 0;

    // 7. GET INTEREST RECEIVED (From profit_loss table)
    $sql_interest_received = "SELECT SUM(amount) as total_interest_received FROM profit_loss 
                             WHERE bank_name='$bank_name' AND month='$month' 
                             AND side='Income' 
                             AND (category LIKE '%INTEREST RECEIVED%' OR category LIKE '%INT.RCVD%')";
    $result_interest_received = mysqli_query($link, $sql_interest_received);
    $interest_received = ($result_interest_received && $row = mysqli_fetch_assoc($result_interest_received)) ? ($row['total_interest_received'] ?? 0) : 0;

    // 8. CALCULATE ABR & ALR (As per Excel formulas)
    if ($monthly_avg_deposit > 0) {
        $abr = ($interest_paid / $monthly_avg_deposit) * 100;
    }
    
    if ($monthly_avg_loan > 0) {
        $alr = ($interest_received / $monthly_avg_loan) * 100;
    }

    // 9. CALCULATE SPREAD
    $spread = $alr - $abr;

    // Insert/Update main data
    $currentDateTime = date('Y-m-d H:i:s');
    
    $sql_insert = "INSERT INTO abr_alr_89 (
        bank_name, month_year, deposit, loan, average_deposit, average_loan,
        monthly_avg_deposit, monthly_avg_loan, interest_paid, interest_received,
        abr, alr, spread, created_at
    ) VALUES (
        '$bank_name', '$month', '$deposit', '$loan', '$average_deposit', '$average_loan',
        '$monthly_avg_deposit', '$monthly_avg_loan', '$interest_paid', '$interest_received',
        '$abr', '$alr', '$spread', '$currentDateTime'
    )";
    
    if (mysqli_query($link, $sql_insert)) {
        // Success - redirect back
        mysqli_close($link);
        header("Location: 89_abr_alr_report.php?bank_name=$bank_name&month=$month&success=1");
        exit();
    } else {
        throw new Exception("Error inserting data: " . mysqli_error($link));
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
mysqli_close($link);
?>