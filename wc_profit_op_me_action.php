<?php
include 'connection.php';

date_default_timezone_set('Asia/Kolkata');

// Get values safely
$bank_name = mysqli_real_escape_string($link, $_GET['bank_name'] ?? '');
$month = mysqli_real_escape_string($link, $_GET['month'] ?? '');

if (empty($bank_name) || empty($month)) {
    die("Bank name and month are required.");
}

// Initialize all variables
$working_capital = $previous_wc = $avg_working_capital = $total_deposit = 0;
$profit = $operating_profit = $profit_percentage = $operating_profit_percentage = 0;
$management_expense = $management_expense_percentage = 0;

try {
    // 1. WORKING CAPITAL (Total Assets - Total Liabilities)
    $sql_assets = "SELECT SUM(amount) as total_assets FROM balance_sheet 
                   WHERE bank_name='$bank_name' AND month='$month' AND side='Assets'";
    $result_assets = mysqli_query($link, $sql_assets);
    $total_assets = ($result_assets && $row = mysqli_fetch_assoc($result_assets)) ? ($row['total_assets'] ?? 0) : 0;

    $sql_liabilities = "SELECT SUM(amount) as total_liabilities FROM balance_sheet 
                        WHERE bank_name='$bank_name' AND month='$month' AND side='Liabilities'";
    $result_liabilities = mysqli_query($link, $sql_liabilities);
    $total_liabilities = ($result_liabilities && $row = mysqli_fetch_assoc($result_liabilities)) ? ($row['total_liabilities'] ?? 0) : 0;

    $working_capital = $total_assets - $total_liabilities;

    // 2. PREVIOUS MONTH WORKING CAPITAL
    $prev_month = date('Y-m', strtotime($month . '-01 -1 month'));
    $sql_prev_wc = "SELECT working_capital FROM wc_profit_op_me 
                    WHERE bank_name='$bank_name' AND month_year='$prev_month' 
                    ORDER BY id DESC LIMIT 1";
    $result_prev_wc = mysqli_query($link, $sql_prev_wc);
    $previous_wc = ($result_prev_wc && $row = mysqli_fetch_assoc($result_prev_wc)) ? ($row['working_capital'] ?? 0) : 0;

    // 3. AVERAGE WORKING CAPITAL (Current + Previous) / 2
    $avg_working_capital = ($working_capital + $previous_wc) / 2;

    // 4. TOTAL DEPOSITS
    $sql_deposit = "SELECT SUM(amount) as total_deposit FROM balance_sheet 
                    WHERE bank_name='$bank_name' AND month='$month' 
                    AND side='Liabilities' AND category LIKE '%DEPOSIT%'";
    $result_deposit = mysqli_query($link, $sql_deposit);
    $total_deposit = ($result_deposit && $row = mysqli_fetch_assoc($result_deposit)) ? ($row['total_deposit'] ?? 0) : 0;

    // 5. PROFIT (From networth_camales or calculate from profit_loss)
    $sql_profit = "SELECT total_profit FROM networth_camales 
                   WHERE bank_name='$bank_name' AND month_year='$month'";
    $result_profit = mysqli_query($link, $sql_profit);
    if ($result_profit && $row = mysqli_fetch_assoc($result_profit)) {
        $profit = $row['total_profit'] ?? 0;
    }

    // Alternative profit calculation from profit_loss table
    if ($profit == 0) {
        $sql_profit_calc = "SELECT 
                            SUM(CASE WHEN side = 'Income' THEN amount ELSE 0 END) -
                            SUM(CASE WHEN side = 'Expenses' THEN amount ELSE 0 END) as calculated_profit
                            FROM profit_loss 
                            WHERE bank_name='$bank_name' AND month='$month'";
        $result_profit_calc = mysqli_query($link, $sql_profit_calc);
        if ($result_profit_calc && $row = mysqli_fetch_assoc($result_profit_calc)) {
            $profit = $row['calculated_profit'] ?? 0;
        }
    }

    // 6. OPERATING PROFIT (Profit + Provisions)
    $sql_provision = "SELECT SUM(amount) as total_provision FROM balance_sheet 
                      WHERE bank_name='$bank_name' AND month='$month' 
                      AND side='Liabilities' 
                      AND (category LIKE '%PROVISION%' OR subcategory LIKE '%PROVISION%')";
    $result_provision = mysqli_query($link, $sql_provision);
    $provision_total = ($result_provision && $row = mysqli_fetch_assoc($result_provision)) ? ($row['total_provision'] ?? 0) : 0;

    $operating_profit = $profit + $provision_total;

    // 7. PERCENTAGE CALCULATIONS
    if ($total_deposit > 0) {
        $profit_percentage = ($profit / $total_deposit) * 100;
        $operating_profit_percentage = ($operating_profit / $total_deposit) * 100;
    }

    // 8. MANAGEMENT EXPENSE
    $sql_management = "SELECT SUM(amount) as management_exp FROM profit_loss 
                       WHERE bank_name='$bank_name' AND month='$month' 
                       AND side='Expenses' 
                       AND (category LIKE '%MANAGEMENT%' OR category LIKE '%EMPLOYEE%' OR category LIKE '%ADMIN%')";
    $result_management = mysqli_query($link, $sql_management);
    $management_expense = ($result_management && $row = mysqli_fetch_assoc($result_management)) ? ($row['management_exp'] ?? 0) : 0;

    // Management Expense Percentage
    if ($total_deposit > 0) {
        $management_expense_percentage = ($management_expense / $total_deposit) * 100;
    }

    // Insert/Update main data
    $currentDateTime = date('Y-m-d H:i:s');
    
    $sql_insert = "INSERT INTO wc_profit_op_me (
        bank_name, month_year, working_capital, previous_wc, avg_working_capital,
        total_deposit, profit, operating_profit, profit_percentage, 
        operating_profit_percentage, management_expense, management_expense_percentage, created_at
    ) VALUES (
        '$bank_name', '$month', '$working_capital', '$previous_wc', '$avg_working_capital',
        '$total_deposit', '$profit', '$operating_profit', '$profit_percentage',
        '$operating_profit_percentage', '$management_expense', '$management_expense_percentage', '$currentDateTime'
    ) ON DUPLICATE KEY UPDATE
        working_capital = VALUES(working_capital),
        previous_wc = VALUES(previous_wc),
        avg_working_capital = VALUES(avg_working_capital),
        total_deposit = VALUES(total_deposit),
        profit = VALUES(profit),
        operating_profit = VALUES(operating_profit),
        profit_percentage = VALUES(profit_percentage),
        operating_profit_percentage = VALUES(operating_profit_percentage),
        management_expense = VALUES(management_expense),
        management_expense_percentage = VALUES(management_expense_percentage),
        created_at = VALUES(created_at)";

    if (mysqli_query($link, $sql_insert)) {
        // Calculate profit split based on Excel percentages
        $profit_split_items = [];
        
        if ($profit > 0) {
            // Based on Excel percentages (25%, 15%, 5%, 10%, 10%, 5%, 15%, 15%)
            $profit_split_items = [
                'RESERVE FUND' => ['amount' => $profit * 0.25, 'percentage' => 25.00],
                'BAD DEBT' => ['amount' => $profit * 0.15, 'percentage' => 15.00],
                'SAMAJIK FUND' => ['amount' => $profit * 0.05, 'percentage' => 5.00],
                'EDUCATION FUND' => ['amount' => $profit * 0.10, 'percentage' => 10.00],
                'DIVIDEND FLUCTUATION FUND' => ['amount' => $profit * 0.10, 'percentage' => 10.00],
                'TRAINING FUND' => ['amount' => $profit * 0.05, 'percentage' => 5.00],
                'BUILDING FUND' => ['amount' => $profit * 0.15, 'percentage' => 15.00],
                'NET PROFIT' => ['amount' => $profit * 0.15, 'percentage' => 15.00]
            ];
        } else {
            // Zero values if no profit
            $profit_split_items = [
                'RESERVE FUND' => ['amount' => 0, 'percentage' => 0],
                'BAD DEBT' => ['amount' => 0, 'percentage' => 0],
                'SAMAJIK FUND' => ['amount' => 0, 'percentage' => 0],
                'EDUCATION FUND' => ['amount' => 0, 'percentage' => 0],
                'DIVIDEND FLUCTUATION FUND' => ['amount' => 0, 'percentage' => 0],
                'TRAINING FUND' => ['amount' => 0, 'percentage' => 0],
                'BUILDING FUND' => ['amount' => 0, 'percentage' => 0],
                'NET PROFIT' => ['amount' => 0, 'percentage' => 0]
            ];
        }
        
        // Insert profit split data
        foreach ($profit_split_items as $particular => $data) {
            $amount = number_format($data['amount'], 2, '.', '');
            $percentage = $data['percentage'];
            
            $sql_profit_split = "INSERT INTO profit_split (bank_name, month_year, particular, amount, percentage, created_at)
                                VALUES ('$bank_name', '$month', '$particular', '$amount', '$percentage', '$currentDateTime')
                                ON DUPLICATE KEY UPDATE 
                                amount = VALUES(amount), 
                                percentage = VALUES(percentage), 
                                created_at = VALUES(created_at)";
            mysqli_query($link, $sql_profit_split);
        }
        
        // Success - redirect back
        mysqli_close($link);
        header("Location: wc_profit_op_me.php?bank_name=$bank_name&month=$month&success=1");
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