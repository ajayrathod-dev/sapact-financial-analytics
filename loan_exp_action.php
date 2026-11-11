<?php
include 'connection.php';

date_default_timezone_set('Asia/Kolkata');

// Get values safely
$bank_name = mysqli_real_escape_string($link, $_GET['bank_name'] ?? '');
$month = mysqli_real_escape_string($link, $_GET['month'] ?? '');

if (empty($bank_name) || empty($month)) {
    die("Bank name and month are required.");
}

try {
    // Initialize arrays for secured and unsecured loans
    $loan_data = [];
    
    // 1. SECURED LOANS - Fetch from balance_sheet and loans_advances tables
    
    // Merchant Loan (Secured)
    $sql_merchant_secured = "SELECT SUM(amount) as amount FROM balance_sheet 
                           WHERE bank_name='$bank_name' AND month='$month' 
                           AND side='Assets' AND category='LOANS & ADVANCES' 
                           AND subcategory LIKE '%Marchant Loan%'";
    $result_merchant_secured = mysqli_query($link, $sql_merchant_secured);
    $merchant_secured = ($result_merchant_secured && $row = mysqli_fetch_assoc($result_merchant_secured)) ? ($row['amount'] ?? 0) : 0;
    
    // Gold Loan (Secured)
    $sql_gold_secured = "SELECT SUM(amount) as amount FROM balance_sheet 
                        WHERE bank_name='$bank_name' AND month='$month' 
                        AND side='Assets' AND category='LOANS & ADVANCES' 
                        AND (subcategory LIKE '%Gold Loan%' OR subcategory LIKE '%Special Gold Loan%')";
    $result_gold_secured = mysqli_query($link, $sql_gold_secured);
    $gold_secured = ($result_gold_secured && $row = mysqli_fetch_assoc($result_gold_secured)) ? ($row['amount'] ?? 0) : 0;
    
    // FD OD Loan (Secured)
    $sql_fd_od_secured = "SELECT SUM(amount) as amount FROM balance_sheet 
                         WHERE bank_name='$bank_name' AND month='$month' 
                         AND side='Assets' AND category='LOANS & ADVANCES' 
                         AND subcategory LIKE '%FD OD Loan%'";
    $result_fd_od_secured = mysqli_query($link, $sql_fd_od_secured);
    $fd_od_secured = ($result_fd_od_secured && $row = mysqli_fetch_assoc($result_fd_od_secured)) ? ($row['amount'] ?? 0) : 0;
    
    // Employee Loan (Secured)
    $sql_emp_secured = "SELECT SUM(amount) as amount FROM balance_sheet 
                       WHERE bank_name='$bank_name' AND month='$month' 
                       AND side='Assets' AND category='LOANS & ADVANCES' 
                       AND subcategory LIKE '%EMPLOYEE LOAN%'";
    $result_emp_secured = mysqli_query($link, $sql_emp_secured);
    $emp_secured = ($result_emp_secured && $row = mysqli_fetch_assoc($result_emp_secured)) ? ($row['amount'] ?? 0) : 0;
    
    // Mahila Samruddhi Loan (Secured)
    $sql_mahila_secured = "SELECT SUM(amount) as amount FROM balance_sheet 
                          WHERE bank_name='$bank_name' AND month='$month' 
                          AND side='Assets' AND category='LOANS & ADVANCES' 
                          AND subcategory LIKE '%Mahila Samruddhi Loan%'";
    $result_mahila_secured = mysqli_query($link, $sql_mahila_secured);
    $mahila_secured = ($result_mahila_secured && $row = mysqli_fetch_assoc($result_mahila_secured)) ? ($row['amount'] ?? 0) : 0;
    
    // Mortgage Loan (Secured)
    $sql_mortgage_secured = "SELECT SUM(amount) as amount FROM balance_sheet 
                           WHERE bank_name='$bank_name' AND month='$month' 
                           AND side='Assets' AND category='LOANS & ADVANCES' 
                           AND subcategory LIKE '%Morgage Loan%'";
    $result_mortgage_secured = mysqli_query($link, $sql_mortgage_secured);
    $mortgage_secured = ($result_mortgage_secured && $row = mysqli_fetch_assoc($result_mortgage_secured)) ? ($row['amount'] ?? 0) : 0;
    
    // RD OD Loan (Secured)
    $sql_rd_od_secured = "SELECT SUM(amount) as amount FROM balance_sheet 
                         WHERE bank_name='$bank_name' AND month='$month' 
                         AND side='Assets' AND category='LOANS & ADVANCES' 
                         AND subcategory LIKE '%RD OD Loan%'";
    $result_rd_od_secured = mysqli_query($link, $sql_rd_od_secured);
    $rd_od_secured = ($result_rd_od_secured && $row = mysqli_fetch_assoc($result_rd_od_secured)) ? ($row['amount'] ?? 0) : 0;

    // 2. UNSECURED LOANS - These would typically be specific portions or types
    // For demonstration, we'll assume certain percentages are unsecured
    
    $merchant_unsecured = $merchant_secured * 0.10; // Assume 10% of merchant loan is unsecured
    $gold_unsecured = $gold_secured * 0.05; // Assume 5% of gold loan is unsecured
    $fd_od_unsecured = $fd_od_secured * 0.08; // Assume 8% of FD OD loan is unsecured
    
    // Adjust secured amounts by subtracting unsecured portions
    $merchant_secured_adj = $merchant_secured - $merchant_unsecured;
    $gold_secured_adj = $gold_secured - $gold_unsecured;
    $fd_od_secured_adj = $fd_od_secured - $fd_od_unsecured;

    // 3. POPULATE LOAN DATA ARRAY
    $loan_data = [
        'Merchant Loan' => ['secured' => $merchant_secured_adj, 'unsecured' => $merchant_unsecured],
        'Gold Loan' => ['secured' => $gold_secured_adj, 'unsecured' => $gold_unsecured],
        'Special Gold Loan' => ['secured' => $gold_secured * 0.5, 'unsecured' => 0], // All secured
        'FD OD Loan' => ['secured' => $fd_od_secured_adj, 'unsecured' => $fd_od_unsecured],
        'Employee Loan' => ['secured' => $emp_secured, 'unsecured' => 0],
        'Mahila Samruddhi Loan' => ['secured' => $mahila_secured, 'unsecured' => 0],
        'Mortgage Loan' => ['secured' => $mortgage_secured, 'unsecured' => 0],
        'RD OD Loan' => ['secured' => $rd_od_secured, 'unsecured' => 0],
    ];

    // 4. INSERT/UPDATE DATA
    $currentDateTime = date('Y-m-d H:i:s');
    
    // Clear existing data for this bank and month
    $sql_delete = "DELETE FROM loan_exposure_limit WHERE bank_name='$bank_name' AND month_year='$month'";
    mysqli_query($link, $sql_delete);
    
    // Insert new data
    foreach ($loan_data as $particular => $amounts) {
        $secured = number_format($amounts['secured'], 2, '.', '');
        $unsecured = number_format($amounts['unsecured'], 2, '.', '');
        
        $sql_insert = "INSERT INTO loan_exposure_limit (
            bank_name, month_year, particular, secured_amount, unsecured_amount, created_at
        ) VALUES (
            '$bank_name', '$month', '$particular', '$secured', '$unsecured', '$currentDateTime'
        )";
        mysqli_query($link, $sql_insert);
    }
    
    // 5. CALCULATE TOTALS AND PERCENTAGES
    $total_secured = array_sum(array_column($loan_data, 'secured'));
    $total_unsecured = array_sum(array_column($loan_data, 'unsecured'));
    $total_loans = $total_secured + $total_unsecured;
    $unsecured_percentage = $total_loans > 0 ? ($total_unsecured / $total_loans) * 100 : 0;
    
    // Insert summary record
    $sql_summary = "INSERT INTO loan_exposure_limit (
        bank_name, month_year, particular, secured_amount, unsecured_amount, created_at
    ) VALUES (
        '$bank_name', '$month', 'TOTAL LOANS', '$total_secured', '$total_unsecured', '$currentDateTime'
    )";
    mysqli_query($link, $sql_summary);
    
    // Success - redirect back
    mysqli_close($link);
    header("Location: loan_exp.php?bank_name=$bank_name&month=$month&success=1");
    exit();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
mysqli_close($link);
?>