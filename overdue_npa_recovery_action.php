<?php
include 'connection.php';

date_default_timezone_set('Asia/Kolkata');

// Get values from GET request
$bank_name = mysqli_real_escape_string($link, $_GET['bank_name'] ?? '');
$month = mysqli_real_escape_string($link, $_GET['month'] ?? '');

if (empty($bank_name) || empty($month)) {
    die("Bank name and month are required.");
}

// Initialize all variables
$total_overdue = $total_loan = $overdue_percentage = 0;
$march_npa_amount = $recovery_amount = $recovery_percentage = 0;

try {
    // 1. GET TOTAL OVERDUE FROM LOAN TYPES
    $sql_overdue = "SELECT SUM(amount) as total_overdue FROM balance_sheet 
                   WHERE bank_name='$bank_name' AND month='$month' 
                   AND side='Assets' AND (category LIKE '%OVERDUE%' OR subcategory LIKE '%OVERDUE%' 
                   OR category LIKE '%NPA%' OR subcategory LIKE '%NPA%')";
    $result_overdue = mysqli_query($link, $sql_overdue);
    $total_overdue = ($result_overdue && $row = mysqli_fetch_assoc($result_overdue)) ? ($row['total_overdue'] ?? 0) : 0;

    // 2. GET TOTAL LOAN AMOUNT
    $sql_loan = "SELECT SUM(amount) as total_loan FROM balance_sheet 
                WHERE bank_name='$bank_name' AND month='$month' 
                AND side='Assets' AND (category LIKE '%LOAN%' OR subcategory LIKE '%LOAN%' OR category LIKE '%ADVANCE%')";
    $result_loan = mysqli_query($link, $sql_loan);
    $total_loan = ($result_loan && $row = mysqli_fetch_assoc($result_loan)) ? ($row['total_loan'] ?? 0) : 0;

    // 3. CALCULATE OVERDUE PERCENTAGE
    if ($total_loan > 0) {
        $overdue_percentage = ($total_overdue / $total_loan) * 100;
    }

    // 4. GET MARCH NPA AMOUNT (from previous March data)
    $march_year = date('Y', strtotime($month . '-01')) . '-03';
    $sql_npa = "SELECT total_overdue as march_npa FROM overdue_npa_recovery 
               WHERE bank_name='$bank_name' AND month_year='$march_year' 
               ORDER BY id DESC LIMIT 1";
    $result_npa = mysqli_query($link, $sql_npa);
    $march_npa_amount = ($result_npa && $row = mysqli_fetch_assoc($result_npa)) ? ($row['march_npa'] ?? 0) : 0;

    // 5. CALCULATE RECOVERY AMOUNT (difference from March NPA)
    $recovery_amount = $march_npa_amount - $total_overdue;
    if ($recovery_amount < 0) $recovery_amount = 0;

    // 6. CALCULATE RECOVERY PERCENTAGE
    if ($march_npa_amount > 0) {
        $recovery_percentage = ($recovery_amount / $march_npa_amount) * 100;
    }

    // Format all values
    $total_overdue = number_format($total_overdue, 2, '.', '');
    $total_loan = number_format($total_loan, 2, '.', '');
    $overdue_percentage = number_format($overdue_percentage, 2, '.', '');
    $march_npa_amount = number_format($march_npa_amount, 2, '.', '');
    $recovery_amount = number_format($recovery_amount, 2, '.', '');
    $recovery_percentage = number_format($recovery_percentage, 2, '.', '');

    // Insert into main table
    $currentDateTime = date('Y-m-d H:i:s');
    
    $sql_insert = "INSERT INTO overdue_npa_recovery (
        bank_name, month_year, total_overdue, total_loan, overdue_percentage,
        march_npa_amount, recovery_amount, recovery_percentage, created_at
    ) VALUES (
        '$bank_name', '$month', '$total_overdue', '$total_loan', '$overdue_percentage',
        '$march_npa_amount', '$recovery_amount', '$recovery_percentage', '$currentDateTime'
    ) ON DUPLICATE KEY UPDATE
        total_overdue = VALUES(total_overdue),
        total_loan = VALUES(total_loan),
        overdue_percentage = VALUES(overdue_percentage),
        march_npa_amount = VALUES(march_npa_amount),
        recovery_amount = VALUES(recovery_amount),
        recovery_percentage = VALUES(recovery_percentage),
        created_at = VALUES(created_at)";

    if (mysqli_query($link, $sql_insert)) {
        
        // Insert overdue loan types data
        $loan_types = [
            'Merchant Loan' => 'BS- Loans & Advance - Merchant Loan (123)',
            'Gold Loan' => 'BS- Loans & Advance - Gold Loan (135)',
            'Special Gold Loan' => 'BS- Loans & Advance - Special Gold Loan (141)',
            'Instant Loan' => 'BS- Loans & Advance - Instant Loan',
            'FD OD Loan' => 'BS- Loans & Advance - FD OD Loan (147)',
            'Employee Loan' => 'BS- Loans & Advance - EMPLOYEE LOAN (196)',
            'Mahila Samruddhi Loan' => 'BS- Loans & Advance - Mahila Samruddhi Loan (247)',
            'Mortgage Loan' => 'BS- Loans & Advance - Mortgage Loan (188)',
            'RD OD Loan' => 'BS- Loans & Advance - RD OD Loan (280)',
            'FD C C Loan' => 'BS- Loans & Advance - FD C C Loan (323)',
            'Salary Loan' => 'BS- Loans & Advance - Salary Loan (330)'
        ];
        
        foreach ($loan_types as $type_name => $description) {
            // Get amount for each loan type from balance_sheet
            $sql_loan_type = "SELECT SUM(amount) as type_amount FROM balance_sheet 
                             WHERE bank_name='$bank_name' AND month='$month' 
                             AND side='Assets' AND (category LIKE '%" . mysqli_real_escape_string($link, $type_name) . "%' 
                             OR subcategory LIKE '%" . mysqli_real_escape_string($link, $type_name) . "%')";
            $result_loan_type = mysqli_query($link, $sql_loan_type);
            $type_amount = ($result_loan_type && $row = mysqli_fetch_assoc($result_loan_type)) ? ($row['type_amount'] ?? 0) : 0;
            
            $sql_loan_insert = "INSERT INTO overdue_loan_types (
                bank_name, month_year, loan_type, loan_description, amount, created_at
            ) VALUES (
                '$bank_name', '$month', '$type_name', '$description', '$type_amount', '$currentDateTime'
            ) ON DUPLICATE KEY UPDATE 
                loan_description = VALUES(loan_description),
                amount = VALUES(amount),
                created_at = VALUES(created_at)";
            mysqli_query($link, $sql_loan_insert);
        }
        
        // Insert NPA recovery details for different periods
        $recovery_periods = [
            'March Recovery' => $recovery_amount,
            'Quarter 1' => $recovery_amount * 0.25,
            'Quarter 2' => $recovery_amount * 0.25,
            'Quarter 3' => $recovery_amount * 0.25,
            'Quarter 4' => $recovery_amount * 0.25
        ];
        
        foreach ($recovery_periods as $period => $amount) {
            $sql_recovery_insert = "INSERT INTO npa_recovery_details (
                bank_name, month_year, period, recovery_amount, created_at
            ) VALUES (
                '$bank_name', '$month', '$period', '$amount', '$currentDateTime'
            ) ON DUPLICATE KEY UPDATE 
                recovery_amount = VALUES(recovery_amount),
                created_at = VALUES(created_at)";
            mysqli_query($link, $sql_recovery_insert);
        }
        
        // Success - redirect back
        mysqli_close($link);
        header("Location: overdue_npa_recovery.php?bank_name=$bank_name&month=$month&success=1");
        exit();
        
    } else {
        throw new Exception("Error inserting data: " . mysqli_error($link));
    }

} catch (Exception $e) {
    echo "<div class='container mt-5'>";
    echo "<div class='alert alert-danger'>";
    echo "<h4>Error Calculating Overdue & NPA Recovery Data</h4>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<a href='overdue_npa_recovery.php' class='btn btn-primary'>Go Back</a>";
    echo "</div>";
    echo "</div>";
}

// Close connection
mysqli_close($link);
?>