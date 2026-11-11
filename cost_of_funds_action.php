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
$working_capital = $total_liabilities = $total_assets = 0;
$total_interest_paid = $total_interest_received = 0;
$cost_of_funds_percentage = $yield_on_assets_percentage = 0;
$gross_margin_percentage = $net_margin_percentage = $cd_ratio = 0;

try {
    // 1. CALCULATE WORKING CAPITAL (Total Liabilities - Total Assets)
    $sql_liabilities_total = "SELECT SUM(amount) as total_liabilities FROM balance_sheet 
                             WHERE bank_name='$bank_name' AND month='$month' AND side='Liabilities'";
    $result_liabilities = mysqli_query($link, $sql_liabilities_total);
    $total_liabilities = ($result_liabilities && $row = mysqli_fetch_assoc($result_liabilities)) ? ($row['total_liabilities'] ?? 0) : 0;

    $sql_assets_total = "SELECT SUM(amount) as total_assets FROM balance_sheet 
                        WHERE bank_name='$bank_name' AND month='$month' AND side='Assets'";
    $result_assets = mysqli_query($link, $sql_assets_total);
    $total_assets = ($result_assets && $row = mysqli_fetch_assoc($result_assets)) ? ($row['total_assets'] ?? 0) : 0;

    $working_capital = $total_liabilities - $total_assets;
    if ($working_capital < 0) $working_capital = 0;

    // 2. GET TOTAL INTEREST PAID - FIXED: Using correct column names
    $sql_interest_paid = "SELECT SUM(amount) as total_interest FROM profit_loss 
                         WHERE bank_name='$bank_name' AND month='$month' 
                         AND side='Expenses' AND (category LIKE '%INTEREST%' OR subcategory LIKE '%INTEREST%')";
    $result_interest_paid = mysqli_query($link, $sql_interest_paid);
    $total_interest_paid = ($result_interest_paid && $row = mysqli_fetch_assoc($result_interest_paid)) ? ($row['total_interest'] ?? 0) : 0;

    // If no interest found in profit_loss, try balance_sheet
    if ($total_interest_paid == 0) {
        $sql_interest_paid_bs = "SELECT SUM(amount) as total_interest FROM balance_sheet 
                                WHERE bank_name='$bank_name' AND month='$month' 
                                AND side='Expenses' AND (category LIKE '%INTEREST%' OR subcategory LIKE '%INTEREST%')";
        $result_interest_paid_bs = mysqli_query($link, $sql_interest_paid_bs);
        $total_interest_paid = ($result_interest_paid_bs && $row = mysqli_fetch_assoc($result_interest_paid_bs)) ? ($row['total_interest'] ?? 0) : 0;
    }

    // 3. GET TOTAL INTEREST RECEIVED - FIXED: Using correct column names
    $sql_interest_received = "SELECT SUM(amount) as total_interest FROM profit_loss 
                             WHERE bank_name='$bank_name' AND month='$month' 
                             AND side='Income' AND (category LIKE '%INTEREST%' OR subcategory LIKE '%INTEREST%')";
    $result_interest_received = mysqli_query($link, $sql_interest_received);
    $total_interest_received = ($result_interest_received && $row = mysqli_fetch_assoc($result_interest_received)) ? ($row['total_interest'] ?? 0) : 0;

    // If no interest found in profit_loss, try balance_sheet
    if ($total_interest_received == 0) {
        $sql_interest_received_bs = "SELECT SUM(amount) as total_interest FROM balance_sheet 
                                    WHERE bank_name='$bank_name' AND month='$month' 
                                    AND side='Income' AND (category LIKE '%INTEREST%' OR subcategory LIKE '%INTEREST%')";
        $result_interest_received_bs = mysqli_query($link, $sql_interest_received_bs);
        $total_interest_received = ($result_interest_received_bs && $row = mysqli_fetch_assoc($result_interest_received_bs)) ? ($row['total_interest'] ?? 0) : 0;
    }

    // 4. CALCULATE COST OF FUNDS PERCENTAGE
    if ($working_capital > 0) {
        $cost_of_funds_percentage = ($total_interest_paid / $working_capital) * 100;
    }

    // 5. CALCULATE YIELD ON ASSETS PERCENTAGE
    if ($total_assets > 0) {
        $yield_on_assets_percentage = ($total_interest_received / $total_assets) * 100;
    }

    // 6. CALCULATE GROSS MARGIN
    $gross_margin_percentage = $yield_on_assets_percentage - $cost_of_funds_percentage;

    // 7. CALCULATE NET MARGIN (considering other expenses)
    $sql_other_expenses = "SELECT SUM(amount) as other_expenses FROM profit_loss 
                          WHERE bank_name='$bank_name' AND month='$month' 
                          AND side='Expenses' AND category NOT LIKE '%INTEREST%'";
    $result_other_expenses = mysqli_query($link, $sql_other_expenses);
    $other_expenses = ($result_other_expenses && $row = mysqli_fetch_assoc($result_other_expenses)) ? ($row['other_expenses'] ?? 0) : 0;

    // If no expenses found in profit_loss, try balance_sheet
    if ($other_expenses == 0) {
        $sql_other_expenses_bs = "SELECT SUM(amount) as other_expenses FROM balance_sheet 
                                 WHERE bank_name='$bank_name' AND month='$month' 
                                 AND side='Expenses' AND category NOT LIKE '%INTEREST%'";
        $result_other_expenses_bs = mysqli_query($link, $sql_other_expenses_bs);
        $other_expenses = ($result_other_expenses_bs && $row = mysqli_fetch_assoc($result_other_expenses_bs)) ? ($row['other_expenses'] ?? 0) : 0;
    }

    $burden_percentage = ($working_capital > 0) ? ($other_expenses / $working_capital) * 100 : 0;
    $net_margin_percentage = $gross_margin_percentage - $burden_percentage;

    // 8. CALCULATE C/D RATIO
    $sql_deposits = "SELECT SUM(amount) as total_deposits FROM balance_sheet 
                    WHERE bank_name='$bank_name' AND month='$month' 
                    AND side='Liabilities' AND (category LIKE '%DEPOSIT%' OR subcategory LIKE '%DEPOSIT%')";
    $result_deposits = mysqli_query($link, $sql_deposits);
    $total_deposits = ($result_deposits && $row = mysqli_fetch_assoc($result_deposits)) ? ($row['total_deposits'] ?? 0) : 0;

    $sql_loans = "SELECT SUM(amount) as total_loans FROM balance_sheet 
                 WHERE bank_name='$bank_name' AND month='$month' 
                 AND side='Assets' AND (category LIKE '%LOAN%' OR subcategory LIKE '%LOAN%' OR category LIKE '%ADVANCE%')";
    $result_loans = mysqli_query($link, $sql_loans);
    $total_loans = ($result_loans && $row = mysqli_fetch_assoc($result_loans)) ? ($row['total_loans'] ?? 0) : 0;

    if ($total_deposits > 0) {
        $cd_ratio = ($total_loans / $total_deposits) * 100;
    }

    // Format all values
    $working_capital = number_format($working_capital, 2, '.', '');
    $total_liabilities = number_format($total_liabilities, 2, '.', '');
    $total_assets = number_format($total_assets, 2, '.', '');
    $total_interest_paid = number_format($total_interest_paid, 2, '.', '');
    $total_interest_received = number_format($total_interest_received, 2, '.', '');
    $cost_of_funds_percentage = number_format($cost_of_funds_percentage, 2, '.', '');
    $yield_on_assets_percentage = number_format($yield_on_assets_percentage, 2, '.', '');
    $gross_margin_percentage = number_format($gross_margin_percentage, 2, '.', '');
    $net_margin_percentage = number_format($net_margin_percentage, 2, '.', '');
    $cd_ratio = number_format($cd_ratio, 2, '.', '');

    // Insert into main table
    $currentDateTime = date('Y-m-d H:i:s');
    
    $sql_insert = "INSERT INTO cost_of_funds (
        bank_name, month_year, working_capital, total_liabilities, total_assets,
        total_interest_paid, total_interest_received, cost_of_funds_percentage,
        yield_on_assets_percentage, gross_margin_percentage, net_margin_percentage,
        cd_ratio, created_at
    ) VALUES (
        '$bank_name', '$month', '$working_capital', '$total_liabilities', '$total_assets',
        '$total_interest_paid', '$total_interest_received', '$cost_of_funds_percentage',
        '$yield_on_assets_percentage', '$gross_margin_percentage', '$net_margin_percentage',
        '$cd_ratio', '$currentDateTime'
    ) ON DUPLICATE KEY UPDATE
        working_capital = VALUES(working_capital),
        total_liabilities = VALUES(total_liabilities),
        total_assets = VALUES(total_assets),
        total_interest_paid = VALUES(total_interest_paid),
        total_interest_received = VALUES(total_interest_received),
        cost_of_funds_percentage = VALUES(cost_of_funds_percentage),
        yield_on_assets_percentage = VALUES(yield_on_assets_percentage),
        gross_margin_percentage = VALUES(gross_margin_percentage),
        net_margin_percentage = VALUES(net_margin_percentage),
        cd_ratio = VALUES(cd_ratio),
        created_at = VALUES(created_at)";

    if (mysqli_query($link, $sql_insert)) {
        
        // Insert liabilities breakdown
        $liability_types = [
            'Share Capital' => [
                'desc' => 'BS - Liabilities - Share Capital - Paid up Share Capital',
                'interest_rate' => 8.00
            ],
            'All Funds' => [
                'desc' => 'BS - Liabilities - Funds & Reserves - All Fund Total',
                'interest_rate' => 0.00
            ],
            'Deposits' => [
                'desc' => 'BS - Liabilities - Total Deposits -(CASA + Term)',
                'interest_rate' => 6.50
            ],
            'Loan' => [
                'desc' => 'BS - Liabilities - Borrowings',
                'interest_rate' => 7.00
            ],
            'Other Liabilities' => [
                'desc' => 'BS - Liabilities - OTHER LIABILITIES Total',
                'interest_rate' => 0.00
            ],
            'Profit' => [
                'desc' => 'BS - Liabilities - Total Profit',
                'interest_rate' => 0.00
            ]
        ];
        
        foreach ($liability_types as $type_name => $data) {
            $sql_liability = "SELECT SUM(amount) as type_amount FROM balance_sheet 
                             WHERE bank_name='$bank_name' AND month='$month' 
                             AND side='Liabilities' AND (category LIKE '%" . mysqli_real_escape_string($link, $type_name) . "%' 
                             OR subcategory LIKE '%" . mysqli_real_escape_string($link, $type_name) . "%')";
            $result_liability = mysqli_query($link, $sql_liability);
            $type_amount = ($result_liability && $row = mysqli_fetch_assoc($result_liability)) ? ($row['type_amount'] ?? 0) : 0;
            
            $percentage_to_wc = ($working_capital > 0) ? ($type_amount / $working_capital) * 100 : 0;
            $interest_paid = $type_amount * ($data['interest_rate'] / 100);
            $interest_rate = $data['interest_rate'];
            $weighted_average = $percentage_to_wc * ($interest_rate / 100);
            $parameters = 0.05; // Default parameter
            
            $sql_liability_insert = "INSERT INTO liabilities_breakdown (
                bank_name, month_year, liability_type, description, amount, percentage_to_wc,
                interest_paid, interest_rate, weighted_average, parameters, created_at
            ) VALUES (
                '$bank_name', '$month', '$type_name', '{$data['desc']}', '$type_amount', '$percentage_to_wc',
                '$interest_paid', '$interest_rate', '$weighted_average', '$parameters', '$currentDateTime'
            ) ON DUPLICATE KEY UPDATE 
                description = VALUES(description),
                amount = VALUES(amount),
                percentage_to_wc = VALUES(percentage_to_wc),
                interest_paid = VALUES(interest_paid),
                interest_rate = VALUES(interest_rate),
                weighted_average = VALUES(weighted_average),
                parameters = VALUES(parameters),
                created_at = VALUES(created_at)";
            mysqli_query($link, $sql_liability_insert);
        }
        
        // Insert assets breakdown
        $asset_types = [
            'Cash On Hand' => [
                'desc' => 'BS - Assets - Cash In Hand Total',
                'interest_rate' => 0.00
            ],
            'Bank' => [
                'desc' => 'BS - Assets - Cash at Bank Total',
                'interest_rate' => 4.00
            ],
            'Investment' => [
                'desc' => 'BS - Assets - Total Investments',
                'interest_rate' => 7.50
            ],
            'Loans & Advances' => [
                'desc' => 'BS - Assets - Total Loans & Advances',
                'interest_rate' => 12.00
            ],
            'Other Assets' => [
                'desc' => 'BS - Assets - Other Assets Total',
                'interest_rate' => 0.00
            ]
        ];
        
        foreach ($asset_types as $type_name => $data) {
            $sql_asset = "SELECT SUM(amount) as type_amount FROM balance_sheet 
                         WHERE bank_name='$bank_name' AND month='$month' 
                         AND side='Assets' AND (category LIKE '%" . mysqli_real_escape_string($link, $type_name) . "%' 
                         OR subcategory LIKE '%" . mysqli_real_escape_string($link, $type_name) . "%')";
            $result_asset = mysqli_query($link, $sql_asset);
            $type_amount = ($result_asset && $row = mysqli_fetch_assoc($result_asset)) ? ($row['type_amount'] ?? 0) : 0;
            
            $percentage_to_wc = ($working_capital > 0) ? ($type_amount / $working_capital) * 100 : 0;
            $interest_received = $type_amount * ($data['interest_rate'] / 100);
            $interest_rate = $data['interest_rate'];
            $weighted_average = $percentage_to_wc * ($interest_rate / 100);
            $parameters = 0.05; // Default parameter
            
            $sql_asset_insert = "INSERT INTO assets_breakdown (
                bank_name, month_year, asset_type, description, amount, percentage_to_wc,
                interest_received, interest_rate, weighted_average, parameters, created_at
            ) VALUES (
                '$bank_name', '$month', '$type_name', '{$data['desc']}', '$type_amount', '$percentage_to_wc',
                '$interest_received', '$interest_rate', '$weighted_average', '$parameters', '$currentDateTime'
            ) ON DUPLICATE KEY UPDATE 
                description = VALUES(description),
                amount = VALUES(amount),
                percentage_to_wc = VALUES(percentage_to_wc),
                interest_received = VALUES(interest_received),
                interest_rate = VALUES(interest_rate),
                weighted_average = VALUES(weighted_average),
                parameters = VALUES(parameters),
                created_at = VALUES(created_at)";
            mysqli_query($link, $sql_asset_insert);
        }
        
        // Insert expenses breakdown - FIXED: Using correct column names
        $expense_types = [
            'Interest Paid On Deposit' => 'INT. PAID ON DEPOSIT Total',
            'Interest Paid On Loan' => 'INT.PAID ON BORROWINGS Total',
            'Management Exp' => 'MANAGEMENT EXPENSES Total',
            'Employee Exp' => 'EMPLOYEE EXP / ASTHAPANA EXP Total',
            'Provision' => 'Profit & Loss - Exp - Provisions'
        ];
        
        $total_expenses = $total_interest_paid + $other_expenses;
        
        foreach ($expense_types as $type_name => $description) {
            $sql_expense = "SELECT SUM(amount) as type_amount FROM profit_loss 
                           WHERE bank_name='$bank_name' AND month='$month' 
                           AND side='Expenses' AND (category LIKE '%" . mysqli_real_escape_string($link, $type_name) . "%' 
                           OR subcategory LIKE '%" . mysqli_real_escape_string($link, $type_name) . "%')";
            $result_expense = mysqli_query($link, $sql_expense);
            $type_amount = ($result_expense && $row = mysqli_fetch_assoc($result_expense)) ? ($row['type_amount'] ?? 0) : 0;
            
            // If not found in profit_loss, try balance_sheet
            if ($type_amount == 0) {
                $sql_expense_bs = "SELECT SUM(amount) as type_amount FROM balance_sheet 
                                  WHERE bank_name='$bank_name' AND month='$month' 
                                  AND side='Expenses' AND (category LIKE '%" . mysqli_real_escape_string($link, $type_name) . "%' 
                                  OR subcategory LIKE '%" . mysqli_real_escape_string($link, $type_name) . "%')";
                $result_expense_bs = mysqli_query($link, $sql_expense_bs);
                $type_amount = ($result_expense_bs && $row = mysqli_fetch_assoc($result_expense_bs)) ? ($row['type_amount'] ?? 0) : 0;
            }
            
            $percentage_to_total = ($total_expenses > 0) ? ($type_amount / $total_expenses) * 100 : 0;
            $parameters = 0.05; // Default parameter
            
            $sql_expense_insert = "INSERT INTO expenses_breakdown (
                bank_name, month_year, expense_type, description, amount, percentage_to_total, parameters, created_at
            ) VALUES (
                '$bank_name', '$month', '$type_name', '$description', '$type_amount', '$percentage_to_total', '$parameters', '$currentDateTime'
            ) ON DUPLICATE KEY UPDATE 
                description = VALUES(description),
                amount = VALUES(amount),
                percentage_to_total = VALUES(percentage_to_total),
                parameters = VALUES(parameters),
                created_at = VALUES(created_at)";
            mysqli_query($link, $sql_expense_insert);
        }
        
        // Insert income breakdown - FIXED: Using correct column names
        $income_types = [
            'Interest On Loan' => 'INTREST RECEIVED ON LOAN Total',
            'Interest On Investment' => 'INTEREST RECEIVED ON INVESTMENTS Total',
            'Other Income' => 'OTHER INCOME Total'
        ];
        
        $total_income = $total_interest_received;
        $sql_other_income = "SELECT SUM(amount) as other_income FROM profit_loss 
                            WHERE bank_name='$bank_name' AND month='$month' 
                            AND side='Income' AND category NOT LIKE '%INTEREST%'";
        $result_other_income = mysqli_query($link, $sql_other_income);
        $other_income = ($result_other_income && $row = mysqli_fetch_assoc($result_other_income)) ? ($row['other_income'] ?? 0) : 0;
        $total_income += $other_income;
        
        foreach ($income_types as $type_name => $description) {
            $sql_income = "SELECT SUM(amount) as type_amount FROM profit_loss 
                          WHERE bank_name='$bank_name' AND month='$month' 
                          AND side='Income' AND (category LIKE '%" . mysqli_real_escape_string($link, $type_name) . "%' 
                          OR subcategory LIKE '%" . mysqli_real_escape_string($link, $type_name) . "%')";
            $result_income = mysqli_query($link, $sql_income);
            $type_amount = ($result_income && $row = mysqli_fetch_assoc($result_income)) ? ($row['type_amount'] ?? 0) : 0;
            
            // If not found in profit_loss, try balance_sheet
            if ($type_amount == 0) {
                $sql_income_bs = "SELECT SUM(amount) as type_amount FROM balance_sheet 
                                 WHERE bank_name='$bank_name' AND month='$month' 
                                 AND side='Income' AND (category LIKE '%" . mysqli_real_escape_string($link, $type_name) . "%' 
                                 OR subcategory LIKE '%" . mysqli_real_escape_string($link, $type_name) . "%')";
                $result_income_bs = mysqli_query($link, $sql_income_bs);
                $type_amount = ($result_income_bs && $row = mysqli_fetch_assoc($result_income_bs)) ? ($row['type_amount'] ?? 0) : 0;
            }
            
            $percentage_to_total = ($total_income > 0) ? ($type_amount / $total_income) * 100 : 0;
            $parameters = 0.05; // Default parameter
            
            $sql_income_insert = "INSERT INTO income_breakdown (
                bank_name, month_year, income_type, description, amount, percentage_to_total, parameters, created_at
            ) VALUES (
                '$bank_name', '$month', '$type_name', '$description', '$type_amount', '$percentage_to_total', '$parameters', '$currentDateTime'
            ) ON DUPLICATE KEY UPDATE 
                description = VALUES(description),
                amount = VALUES(amount),
                percentage_to_total = VALUES(percentage_to_total),
                parameters = VALUES(parameters),
                created_at = VALUES(created_at)";
            mysqli_query($link, $sql_income_insert);
        }
        
        // Success - redirect back
        mysqli_close($link);
        header("Location: cost_of_funds.php?bank_name=$bank_name&month=$month&success=1");
        exit();
        
    } else {
        throw new Exception("Error inserting data: " . mysqli_error($link));
    }

} catch (Exception $e) {
    echo "<div class='container mt-5'>";
    echo "<div class='alert alert-danger'>";
    echo "<h4>Error Calculating Cost of Funds Data</h4>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<a href='cost_of_funds.php' class='btn btn-primary'>Go Back</a>";
    echo "</div>";
    echo "</div>";
}

// Close connection
mysqli_close($link);
?>