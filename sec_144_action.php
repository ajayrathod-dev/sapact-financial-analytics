<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include 'connection.php';
date_default_timezone_set('Asia/Kolkata');

// Debug: Check what parameters are received
echo "<!-- Debug: GET parameters -->\n";
echo "<!-- bank_name: " . ($_GET['bank_name'] ?? 'NOT SET') . " -->\n";
echo "<!-- month: " . ($_GET['month'] ?? 'NOT SET') . " -->\n";

// Get values safely
$bank_name = mysqli_real_escape_string($link, $_GET['bank_name'] ?? '');
$month = mysqli_real_escape_string($link, $_GET['month'] ?? '');

if (empty($bank_name) || empty($month)) {
    echo "<!-- Error: Bank name or month is empty -->\n";
    die("Bank name and month are required. Received - Bank: '$bank_name', Month: '$month'");
}

// Initialize all variables
$working_capital = $add_previous_wc = $avg_working_capital = 0;
$profit = $operating_profit = $management_expense = 0;
$profit_percentage = $operating_profit_percentage = $management_expense_percentage = 0;
$reserve_fund = $bad_debt = $samajik_fund = $education_fund = 0;
$dividend_fluctuation_fund = $training_fund = $building_fund = $net_profit = 0;
$total_profit_split = $total_actual_profit = $profit_split_percentage = 0;

try {
    echo "<!-- Starting calculations for Bank: $bank_name, Month: $month -->\n";
    
    // 1. GET WORKING CAPITAL (From Balance Sheet)
    $sql_wc = "SELECT amount FROM balance_sheet 
               WHERE bank_name='$bank_name' AND month='$month' 
               AND side='Liabilities' AND category='WORKING CAPITAL'";
    echo "<!-- WC Query: $sql_wc -->\n";
    $result_wc = mysqli_query($link, $sql_wc);
    $working_capital = ($result_wc && $row = mysqli_fetch_assoc($result_wc)) ? ($row['amount'] ?? 0) : 0;
    echo "<!-- Working Capital: $working_capital -->\n";

    // 2. GET PREVIOUS ADD PREVIOUS WC (Cumulative logic as per Excel - Column F)
    $sql_prev_wc = "SELECT add_previous_wc FROM section_144_complete 
                    WHERE bank_name='$bank_name' 
                    ORDER BY id DESC LIMIT 1";
    $result_prev_wc = mysqli_query($link, $sql_prev_wc);
    $previous_add_wc = ($result_prev_wc && $row = mysqli_fetch_assoc($result_prev_wc)) ? ($row['add_previous_wc'] ?? 0) : 0;
    echo "<!-- Previous Add WC: $previous_add_wc -->\n";

    // 3. CALCULATE ADD PREVIOUS WC (Current Working Capital + Previous Add Previous WC)
    $add_previous_wc = $previous_add_wc + $working_capital;
    echo "<!-- Add Previous WC: $add_previous_wc -->\n";

    // 4. CALCULATE AVERAGE WORKING CAPITAL (Divide by 12 as per Excel - Column H)
    $avg_working_capital = $add_previous_wc / 12;
    echo "<!-- Average WC: $avg_working_capital -->\n";

    // 5. GET PROFIT (From Balance Sheet - Current Year Profit)
    $sql_profit = "SELECT amount FROM balance_sheet 
                   WHERE bank_name='$bank_name' AND month='$month' 
                   AND side='Liabilities' AND category='PROFIT AND LOSS' 
                   AND subcategory='CURRENT YEAR PROFIT'";
    echo "<!-- Profit Query 1: $sql_profit -->\n";
    $result_profit = mysqli_query($link, $sql_profit);
    $profit = ($result_profit && $row = mysqli_fetch_assoc($result_profit)) ? ($row['amount'] ?? 0) : 0;
    echo "<!-- Profit from Balance Sheet: $profit -->\n";

    // If not found in balance_sheet, try profit_loss table
    if ($profit == 0) {
        $sql_profit_alt = "SELECT amount FROM profit_loss 
                          WHERE bank_name='$bank_name' AND month='$month' 
                          AND side='Income' AND category='PROFIT'";
        echo "<!-- Profit Query 2: $sql_profit_alt -->\n";
        $result_profit_alt = mysqli_query($link, $sql_profit_alt);
        $profit = ($result_profit_alt && $row = mysqli_fetch_assoc($result_profit_alt)) ? ($row['amount'] ?? 0) : 0;
        echo "<!-- Profit from Profit Loss: $profit -->\n";
    }

    // 6. CALCULATE OPERATING PROFIT (Profit + Provisions as per Excel)
    $sql_provisions = "SELECT SUM(amount) as total_provisions FROM profit_loss 
                      WHERE bank_name='$bank_name' AND month='$month' 
                      AND side='Expenses' AND category LIKE '%PROVISION%'";
    echo "<!-- Provisions Query: $sql_provisions -->\n";
    $result_provisions = mysqli_query($link, $sql_provisions);
    $provisions = ($result_provisions && $row = mysqli_fetch_assoc($result_provisions)) ? ($row['total_provisions'] ?? 0) : 0;
    echo "<!-- Provisions: $provisions -->\n";
    
    $operating_profit = $profit + $provisions;
    echo "<!-- Operating Profit: $operating_profit -->\n";

    // 7. GET MANAGEMENT EXPENSE (From profit_loss table)
    $sql_management = "SELECT SUM(amount) as total_management FROM profit_loss 
                      WHERE bank_name='$bank_name' AND month='$month' 
                      AND side='Expenses' 
                      AND (category LIKE '%MANAGEMENT%' OR category LIKE '%SALARY%' OR category LIKE '%EMPLOYEE%')";
    echo "<!-- Management Query: $sql_management -->\n";
    $result_management = mysqli_query($link, $sql_management);
    $management_expense = ($result_management && $row = mysqli_fetch_assoc($result_management)) ? ($row['total_management'] ?? 0) : 0;
    echo "<!-- Management Expense: $management_expense -->\n";

    // 8. CALCULATE PERCENTAGES (Based on Average Working Capital)
    if ($avg_working_capital > 0) {
        $profit_percentage = ($profit / $avg_working_capital) * 100;
        $operating_profit_percentage = ($operating_profit / $avg_working_capital) * 100;
        $management_expense_percentage = ($management_expense / $avg_working_capital) * 100;
    }
    echo "<!-- Profit %: $profit_percentage -->\n";
    echo "<!-- Operating Profit %: $operating_profit_percentage -->\n";
    echo "<!-- Management Expense %: $management_expense_percentage -->\n";

    // 9. PROFIT SPLIT CALCULATIONS
    $total_actual_profit = $profit;
    echo "<!-- Total Actual Profit: $total_actual_profit -->\n";
    
    // Calculate profit split based on fixed percentages
    $reserve_fund = $total_actual_profit * 0.25;      // 25%
    $bad_debt = $total_actual_profit * 0.15;          // 15% 
    $samajik_fund = $total_actual_profit * 0.05;      // 5%
    $education_fund = $total_actual_profit * 0.10;    // 10%
    $dividend_fluctuation_fund = $total_actual_profit * 0.10; // 10%
    $training_fund = $total_actual_profit * 0.05;     // 5%
    $building_fund = $total_actual_profit * 0.15;     // 15%
    $net_profit = $total_actual_profit * 0.15;        // 15%

    // Calculate totals
    $total_profit_split = $reserve_fund + $bad_debt + $samajik_fund + $education_fund + 
                         $dividend_fluctuation_fund + $training_fund + $building_fund + $net_profit;
    $profit_split_percentage = ($total_actual_profit > 0) ? ($total_profit_split / $total_actual_profit) * 100 : 0;

    echo "<!-- Total Profit Split: $total_profit_split -->\n";
    echo "<!-- Profit Split %: $profit_split_percentage -->\n";

    // 10. Insert data into section_144_complete table
    $currentDateTime = date('Y-m-d H:i:s');
    
    $sql_insert = "INSERT INTO section_144_complete (
        bank_name, month_year, working_capital, add_previous_wc, avg_working_capital,
        profit, operating_profit, profit_percentage, operating_profit_percentage,
        management_expense, management_expense_percentage,
        reserve_fund, bad_debt, samajik_fund, education_fund, 
        dividend_fluctuation_fund, training_fund, building_fund, net_profit,
        total_profit_split, total_actual_profit, profit_split_percentage, created_at
    ) VALUES (
        '$bank_name', '$month', '$working_capital', '$add_previous_wc', '$avg_working_capital',
        '$profit', '$operating_profit', '$profit_percentage', '$operating_profit_percentage',
        '$management_expense', '$management_expense_percentage',
        '$reserve_fund', '$bad_debt', '$samajik_fund', '$education_fund',
        '$dividend_fluctuation_fund', '$training_fund', '$building_fund', '$net_profit',
        '$total_profit_split', '$total_actual_profit', '$profit_split_percentage', '$currentDateTime'
    )";
    
    echo "<!-- Insert Query: $sql_insert -->\n";
    
    if (mysqli_query($link, $sql_insert)) {
        echo "<!-- Data inserted successfully -->\n";
        // Success - redirect back
        mysqli_close($link);
        header("Location: sec_144.php?bank_name=$bank_name&month=$month&success=1");
        exit();
    } else {
        throw new Exception("Error inserting data: " . mysqli_error($link));
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

mysqli_close($link);
?>