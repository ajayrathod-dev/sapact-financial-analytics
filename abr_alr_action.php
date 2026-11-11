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
$deposit = $loan = $average_deposit = $average_loan = 0;
$monthly_avg_deposit = $monthly_avg_loan = $interest_paid = $interest_received = 0;
$abr = $alr = $spread = 0;

try {
    // 1. GET DEPOSIT AMOUNT
    $sql_deposit = "SELECT SUM(amount) as total_deposit FROM balance_sheet 
                   WHERE bank_name='$bank_name' AND month='$month' 
                   AND side='Liabilities' AND (category LIKE '%DEPOSIT%' OR subcategory LIKE '%DEPOSIT%')";
    $result_deposit = mysqli_query($link, $sql_deposit);
    $deposit = ($result_deposit && $row = mysqli_fetch_assoc($result_deposit)) ? ($row['total_deposit'] ?? 0) : 0;

    // 2. GET LOAN AMOUNT
    $sql_loan = "SELECT SUM(amount) as total_loan FROM balance_sheet 
                WHERE bank_name='$bank_name' AND month='$month' 
                AND side='Assets' AND (category LIKE '%LOAN%' OR subcategory LIKE '%LOAN%' OR category LIKE '%ADVANCE%')";
    $result_loan = mysqli_query($link, $sql_loan);
    $loan = ($result_loan && $row = mysqli_fetch_assoc($result_loan)) ? ($row['total_loan'] ?? 0) : 0;

    // 3. GET AVERAGE DEPOSIT (Cumulative from previous months)
    $prev_month = date('Y-m', strtotime($month . '-01 -1 month'));
    $sql_prev_avg = "SELECT average_deposit, average_loan FROM abr_alr 
                     WHERE bank_name='$bank_name' AND month_year='$prev_month' 
                     ORDER BY id DESC LIMIT 1";
    $result_prev_avg = mysqli_query($link, $sql_prev_avg);
    
    if ($result_prev_avg && $row = mysqli_fetch_assoc($result_prev_avg)) {
        $prev_avg_deposit = $row['average_deposit'] ?? 0;
        $prev_avg_loan = $row['average_loan'] ?? 0;
    } else {
        // First month - use current values
        $prev_avg_deposit = $deposit;
        $prev_avg_loan = $loan;
    }
    
    $average_deposit = $prev_avg_deposit + $deposit;
    $average_loan = $prev_avg_loan + $loan;

    // 4. CALCULATE MONTHLY AVERAGES
    $monthly_avg_deposit = $average_deposit / 12;
    $monthly_avg_loan = $average_loan / 12;

    // 5. GET INTEREST PAID ON DEPOSITS - FIXED: Using correct column names
    $sql_interest_paid = "SELECT SUM(amount) as total_interest FROM profit_loss 
                         WHERE bank_name='$bank_name' AND month='$month' 
                         AND side='Expenses' AND category LIKE '%INTEREST%'";
    $result_interest_paid = mysqli_query($link, $sql_interest_paid);
    $interest_paid = ($result_interest_paid && $row = mysqli_fetch_assoc($result_interest_paid)) ? ($row['total_interest'] ?? 0) : 0;

    // If no interest found in profit_loss, try balance_sheet
    if ($interest_paid == 0) {
        $sql_interest_paid_bs = "SELECT SUM(amount) as total_interest FROM balance_sheet 
                                WHERE bank_name='$bank_name' AND month='$month' 
                                AND side='Expenses' AND (category LIKE '%INTEREST%' OR subcategory LIKE '%INTEREST%')";
        $result_interest_paid_bs = mysqli_query($link, $sql_interest_paid_bs);
        $interest_paid = ($result_interest_paid_bs && $row = mysqli_fetch_assoc($result_interest_paid_bs)) ? ($row['total_interest'] ?? 0) : 0;
    }

    // 6. GET INTEREST RECEIVED ON LOANS - FIXED: Using correct column names
    $sql_interest_received = "SELECT SUM(amount) as total_interest FROM profit_loss 
                             WHERE bank_name='$bank_name' AND month='$month' 
                             AND side='Income' AND category LIKE '%INTEREST%'";
    $result_interest_received = mysqli_query($link, $sql_interest_received);
    $interest_received = ($result_interest_received && $row = mysqli_fetch_assoc($result_interest_received)) ? ($row['total_interest'] ?? 0) : 0;

    // If no interest found in profit_loss, try balance_sheet
    if ($interest_received == 0) {
        $sql_interest_received_bs = "SELECT SUM(amount) as total_interest FROM balance_sheet 
                                    WHERE bank_name='$bank_name' AND month='$month' 
                                    AND side='Income' AND (category LIKE '%INTEREST%' OR subcategory LIKE '%INTEREST%')";
        $result_interest_received_bs = mysqli_query($link, $sql_interest_received_bs);
        $interest_received = ($result_interest_received_bs && $row = mysqli_fetch_assoc($result_interest_received_bs)) ? ($row['total_interest'] ?? 0) : 0;
    }

    // 7. CALCULATE ABR & ALR
    if ($monthly_avg_deposit > 0) {
        $abr = ($interest_paid / $monthly_avg_deposit) * 100;
    }
    
    if ($monthly_avg_loan > 0) {
        $alr = ($interest_received / $monthly_avg_loan) * 100;
    }

    // 8. CALCULATE SPREAD
    $spread = $alr - $abr;

    // Format all values
    $deposit = number_format($deposit, 2, '.', '');
    $loan = number_format($loan, 2, '.', '');
    $average_deposit = number_format($average_deposit, 2, '.', '');
    $average_loan = number_format($average_loan, 2, '.', '');
    $monthly_avg_deposit = number_format($monthly_avg_deposit, 2, '.', '');
    $monthly_avg_loan = number_format($monthly_avg_loan, 2, '.', '');
    $interest_paid = number_format($interest_paid, 2, '.', '');
    $interest_received = number_format($interest_received, 2, '.', '');
    $abr = number_format($abr, 2, '.', '');
    $alr = number_format($alr, 2, '.', '');
    $spread = number_format($spread, 2, '.', '');

    // Insert into main table
    $currentDateTime = date('Y-m-d H:i:s');
    
    $sql_insert = "INSERT INTO abr_alr (
        bank_name, month_year, deposit, loan, average_deposit, average_loan,
        monthly_avg_deposit, monthly_avg_loan, interest_paid, interest_received,
        abr, alr, spread, created_at
    ) VALUES (
        '$bank_name', '$month', '$deposit', '$loan', '$average_deposit', '$average_loan',
        '$monthly_avg_deposit', '$monthly_avg_loan', '$interest_paid', '$interest_received',
        '$abr', '$alr', '$spread', '$currentDateTime'
    ) ON DUPLICATE KEY UPDATE
        deposit = VALUES(deposit),
        loan = VALUES(loan),
        average_deposit = VALUES(average_deposit),
        average_loan = VALUES(average_loan),
        monthly_avg_deposit = VALUES(monthly_avg_deposit),
        monthly_avg_loan = VALUES(monthly_avg_loan),
        interest_paid = VALUES(interest_paid),
        interest_received = VALUES(interest_received),
        abr = VALUES(abr),
        alr = VALUES(alr),
        spread = VALUES(spread),
        created_at = VALUES(created_at)";

    if (mysqli_query($link, $sql_insert)) {
        
        // Insert sample deposit types data
        $deposit_types = [
            'SAVINGS' => ['interest' => 124341],
            'DAILY' => ['interest' => 77781],
            'RECURRING' => ['interest' => 137803],
            'MONTHLY' => ['interest' => 254690],
            'EDUCATION' => ['interest' => 7632],
            'SHORT TERM' => ['interest' => 50906],
            'AKSHAY' => ['interest' => 2731421]
        ];
        
        foreach ($deposit_types as $type_name => $data) {
            $interest = $data['interest'];
            $total = $interest * 12; // Sample calculation
            $monthly_avg = $total / 12;
            $interest_percentage = ($monthly_avg > 0) ? ($interest / $monthly_avg * 100) : 0;
            
            $sql_deposit_type = "INSERT INTO deposit_types (
                bank_name, month_year, type_name, interest, total, monthly_avg, interest_percentage, created_at
            ) VALUES (
                '$bank_name', '$month', '$type_name', '$interest', '$total', '$monthly_avg', '$interest_percentage', '$currentDateTime'
            ) ON DUPLICATE KEY UPDATE 
                interest = VALUES(interest),
                total = VALUES(total),
                monthly_avg = VALUES(monthly_avg),
                interest_percentage = VALUES(interest_percentage),
                created_at = VALUES(created_at)";
            mysqli_query($link, $sql_deposit_type);
        }
        
        // Insert sample loan types data
        $loan_types = [
            'PERSONAL Loan' => ['interest' => 2201368],
            'TARAN Loan' => ['interest' => 1575859],
            'SARASWATI Loan' => ['interest' => 12828],
            'GOLD PLEDGE' => ['interest' => 370672],
            'Loan AGAINST F.D.' => ['interest' => 111140]
        ];
        
        foreach ($loan_types as $type_name => $data) {
            $interest = $data['interest'];
            $total = $interest * 12; // Sample calculation
            $monthly_avg = $total / 12;
            $interest_percentage = ($monthly_avg > 0) ? ($interest / $monthly_avg * 100) : 0;
            
            $sql_loan_type = "INSERT INTO loan_types (
                bank_name, month_year, type_name, interest, total, monthly_avg, interest_percentage, created_at
            ) VALUES (
                '$bank_name', '$month', '$type_name', '$interest', '$total', '$monthly_avg', '$interest_percentage', '$currentDateTime'
            ) ON DUPLICATE KEY UPDATE 
                interest = VALUES(interest),
                total = VALUES(total),
                monthly_avg = VALUES(monthly_avg),
                interest_percentage = VALUES(interest_percentage),
                created_at = VALUES(created_at)";
            mysqli_query($link, $sql_loan_type);
        }
        
        // Success - redirect back
        mysqli_close($link);
        header("Location: abr_alr.php?bank_name=$bank_name&month=$month&success=1");
        exit();
        
    } else {
        throw new Exception("Error inserting data: " . mysqli_error($link));
    }

} catch (Exception $e) {
    echo "<div class='container mt-5'>";
    echo "<div class='alert alert-danger'>";
    echo "<h4>Error Calculating ABR & ALR Data</h4>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<a href='abr_alr.php' class='btn btn-primary'>Go Back</a>";
    echo "</div>";
    echo "</div>";
}

// Close connection
mysqli_close($link);
?>