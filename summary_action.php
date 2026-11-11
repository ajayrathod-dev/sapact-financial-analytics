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
$shares = $funds = $deposits = $loans_advances = $cash_in_hand = $cash_at_bank = $od = 0;
$investments = $fixed_dead_stock = $interest_on_loans = $interest_on_investment = $other_income = 0;
$total_income = $interest_on_deposit = $interest_on_od = $prashaskiy = $aasthapna = 0;
$od_interest = $total_me = $provision = $profit = $total_business = $employee = 0;
$per_employee_business = $per_employee_income = $per_employee_profit = $working_capital = $new_section_144_wc = 0;

try {
    // 1. SHARES (Paid Up Share Capital)
    $sql_shares = "SELECT SUM(amount) as total_shares FROM balance_sheet 
                   WHERE bank_name='$bank_name' AND month='$month' 
                   AND side='Liabilities' AND category LIKE '%SHARE CAPITAL%' 
                   AND subcategory LIKE '%Paid up Share Capital%'";
    $result_shares = mysqli_query($link, $sql_shares);
    $shares = ($result_shares && $row = mysqli_fetch_assoc($result_shares)) ? ($row['total_shares'] ?? 0) : 0;

    // 2. FUNDS (Total Funds)
    $sql_funds = "SELECT SUM(amount) as total_funds FROM balance_sheet 
                  WHERE bank_name='$bank_name' AND month='$month' 
                  AND side='Liabilities' AND category LIKE '%FUNDS AND RESERVES%'";
    $result_funds = mysqli_query($link, $sql_funds);
    $funds = ($result_funds && $row = mysqli_fetch_assoc($result_funds)) ? ($row['total_funds'] ?? 0) : 0;

    // 3. DEPOSITS (Total Deposits)
    $sql_deposits = "SELECT SUM(amount) as total_deposits FROM balance_sheet 
                     WHERE bank_name='$bank_name' AND month='$month' 
                     AND side='Liabilities' AND category LIKE '%DEPOSITS%'";
    $result_deposits = mysqli_query($link, $sql_deposits);
    $deposits = ($result_deposits && $row = mysqli_fetch_assoc($result_deposits)) ? ($row['total_deposits'] ?? 0) : 0;

    // 4. LOANS/ADVANCES (Total Loans & Advances)
    $sql_loans = "SELECT SUM(amount) as total_loans FROM balance_sheet 
                  WHERE bank_name='$bank_name' AND month='$month' 
                  AND side='Assets' AND category LIKE '%LOANS & ADVANCES%'";
    $result_loans = mysqli_query($link, $sql_loans);
    $loans_advances = ($result_loans && $row = mysqli_fetch_assoc($result_loans)) ? ($row['total_loans'] ?? 0) : 0;

    // 5. CASH IN HAND
    $sql_cash_hand = "SELECT SUM(amount) as cash_hand FROM balance_sheet 
                      WHERE bank_name='$bank_name' AND month='$month' 
                      AND side='Assets' AND category LIKE '%CASH IN HAND%'";
    $result_cash_hand = mysqli_query($link, $sql_cash_hand);
    $cash_in_hand = ($result_cash_hand && $row = mysqli_fetch_assoc($result_cash_hand)) ? ($row['cash_hand'] ?? 0) : 0;

    // 6. CASH AT BANK
    $sql_cash_bank = "SELECT SUM(amount) as cash_bank FROM balance_sheet 
                      WHERE bank_name='$bank_name' AND month='$month' 
                      AND side='Assets' AND category LIKE '%CASH AT BANK%'";
    $result_cash_bank = mysqli_query($link, $sql_cash_bank);
    $cash_at_bank = ($result_cash_bank && $row = mysqli_fetch_assoc($result_cash_bank)) ? ($row['cash_bank'] ?? 0) : 0;

    // 7. O.D. (Borrowings)
    $sql_od = "SELECT SUM(amount) as total_od FROM balance_sheet 
               WHERE bank_name='$bank_name' AND month='$month' 
               AND side='Liabilities' AND category LIKE '%BORROWINGS%'";
    $result_od = mysqli_query($link, $sql_od);
    $od = ($result_od && $row = mysqli_fetch_assoc($result_od)) ? ($row['total_od'] ?? 0) : 0;

    // 8. INVESTMENTS
    $sql_investments = "SELECT SUM(amount) as total_investments FROM balance_sheet 
                        WHERE bank_name='$bank_name' AND month='$month' 
                        AND side='Assets' AND category LIKE '%INVESTMENTS%'";
    $result_investments = mysqli_query($link, $sql_investments);
    $investments = ($result_investments && $row = mysqli_fetch_assoc($result_investments)) ? ($row['total_investments'] ?? 0) : 0;

    // 9. FIXED & DEAD STOCK (Own Assets)
    $sql_fixed = "SELECT SUM(amount) as total_fixed FROM balance_sheet 
                  WHERE bank_name='$bank_name' AND month='$month' 
                  AND side='Assets' AND category LIKE '%OWN ASSETS%'";
    $result_fixed = mysqli_query($link, $sql_fixed);
    $fixed_dead_stock = ($result_fixed && $row = mysqli_fetch_assoc($result_fixed)) ? ($row['total_fixed'] ?? 0) : 0;

    // 10. INTEREST ON LOANS
    $sql_int_loans = "SELECT SUM(amount) as int_loans FROM profit_loss 
                      WHERE bank_name='$bank_name' AND month='$month' 
                      AND side='Income' AND category LIKE '%INTEREST RECEIVED ON LOAN%'";
    $result_int_loans = mysqli_query($link, $sql_int_loans);
    $interest_on_loans = ($result_int_loans && $row = mysqli_fetch_assoc($result_int_loans)) ? ($row['int_loans'] ?? 0) : 0;

    // 11. INTEREST ON INVESTMENT
    $sql_int_invest = "SELECT SUM(amount) as int_invest FROM profit_loss 
                       WHERE bank_name='$bank_name' AND month='$month' 
                       AND side='Income' AND category LIKE '%INTEREST RECEIVED ON INVESTMENT%'";
    $result_int_invest = mysqli_query($link, $sql_int_invest);
    $interest_on_investment = ($result_int_invest && $row = mysqli_fetch_assoc($result_int_invest)) ? ($row['int_invest'] ?? 0) : 0;

    // 12. OTHER INCOME
    $sql_other_income = "SELECT SUM(amount) as other_inc FROM profit_loss 
                         WHERE bank_name='$bank_name' AND month='$month' 
                         AND side='Income' AND category LIKE '%OTHER INCOME%'";
    $result_other_income = mysqli_query($link, $sql_other_income);
    $other_income = ($result_other_income && $row = mysqli_fetch_assoc($result_other_income)) ? ($row['other_inc'] ?? 0) : 0;

    // 13. TOTAL INCOME (Sum of interest on loans, investment and other income)
    $total_income = $interest_on_loans + $interest_on_investment + $other_income;

    // 14. INTEREST ON DEPOSIT
    $sql_int_deposit = "SELECT SUM(amount) as int_deposit FROM profit_loss 
                        WHERE bank_name='$bank_name' AND month='$month' 
                        AND side='Expenses' AND category LIKE '%INTEREST PAID ON DEPOSIT%'";
    $result_int_deposit = mysqli_query($link, $sql_int_deposit);
    $interest_on_deposit = ($result_int_deposit && $row = mysqli_fetch_assoc($result_int_deposit)) ? ($row['int_deposit'] ?? 0) : 0;

    // 15. INTEREST ON OD
    $sql_int_od = "SELECT SUM(amount) as int_od FROM profit_loss 
                   WHERE bank_name='$bank_name' AND month='$month' 
                   AND side='Expenses' AND category LIKE '%INTEREST PAID ON BORROWINGS%'";
    $result_int_od = mysqli_query($link, $sql_int_od);
    $interest_on_od = ($result_int_od && $row = mysqli_fetch_assoc($result_int_od)) ? ($row['int_od'] ?? 0) : 0;

    // 16. PRASHASKIY (Management Expenses)
    $sql_prashaskiy = "SELECT SUM(amount) as prashaskiy FROM profit_loss 
                       WHERE bank_name='$bank_name' AND month='$month' 
                       AND side='Expenses' AND category LIKE '%MANAGEMENT EXPENCES%'";
    $result_prashaskiy = mysqli_query($link, $sql_prashaskiy);
    $prashaskiy = ($result_prashaskiy && $row = mysqli_fetch_assoc($result_prashaskiy)) ? ($row['prashaskiy'] ?? 0) : 0;

    // 17. AASTHAPNA (Employee Expenses)
    $sql_aasthapna = "SELECT SUM(amount) as aasthapna FROM profit_loss 
                      WHERE bank_name='$bank_name' AND month='$month' 
                      AND side='Expenses' AND (category LIKE '%EMPLOYEE EXP%' OR category LIKE '%ASTHAPANA EXP%')";
    $result_aasthapna = mysqli_query($link, $sql_aasthapna);
    $aasthapna = ($result_aasthapna && $row = mysqli_fetch_assoc($result_aasthapna)) ? ($row['aasthapna'] ?? 0) : 0;

    // 18. OD INTEREST (Same as Interest on OD)
    $od_interest = $interest_on_od;

    // 19. TOTAL M E (Total Management Expense)
    $total_me = $prashaskiy + $aasthapna + $od_interest;

    // 20. PROVISION
    $sql_provision = "SELECT SUM(amount) as total_provision FROM profit_loss 
                      WHERE bank_name='$bank_name' AND month='$month' 
                      AND side='Expenses' AND category LIKE '%PROVISIONS%'";
    $result_provision = mysqli_query($link, $sql_provision);
    $provision = ($result_provision && $row = mysqli_fetch_assoc($result_provision)) ? ($row['total_provision'] ?? 0) : 0;

    // 21. PROFIT
    $sql_profit = "SELECT SUM(CASE WHEN side = 'Income' THEN amount ELSE -amount END) as net_profit 
                   FROM profit_loss 
                   WHERE bank_name='$bank_name' AND month='$month'";
    $result_profit = mysqli_query($link, $sql_profit);
    if ($result_profit && $row = mysqli_fetch_assoc($result_profit)) {
        $profit = $row['net_profit'] ?? 0;
    }

    // Alternative profit from networth_camales
    if ($profit == 0) {
        $sql_profit_alt = "SELECT total_profit FROM networth_camales 
                           WHERE bank_name='$bank_name' AND month_year='$month'";
        $result_profit_alt = mysqli_query($link, $sql_profit_alt);
        if ($result_profit_alt && $row = mysqli_fetch_assoc($result_profit_alt)) {
            $profit = $row['total_profit'] ?? 0;
        }
    }

    // 22. TOTAL BUSINESS (Deposits + Loans)
    $total_business = $deposits + $loans_advances;

    // 23. EMPLOYEE (Get from society_info or use default)
    $sql_employee = "SELECT employee FROM society_info WHERE id='$bank_name'";
    $result_employee = mysqli_query($link, $sql_employee);
    if ($result_employee && $row = mysqli_fetch_assoc($result_employee)) {
        $employee = $row['employee'] ?? 146; // Default to 146 as per Excel
    } else {
        $employee = 146;
    }

    // 24. PER EMPLOYEE BUSINESS
    $per_employee_business = ($employee > 0) ? $total_business / $employee : 0;

    // 25. PER EMPLOYEE INCOME
    $per_employee_income = ($employee > 0) ? $total_income / $employee : 0;

    // 26. PER EMPLOYEE PROFIT
    $per_employee_profit = ($employee > 0) ? $profit / $employee : 0;

    // 27. WORKING CAPITAL (Assets - Liabilities)
    $sql_assets = "SELECT SUM(amount) as total_assets FROM balance_sheet 
                   WHERE bank_name='$bank_name' AND month='$month' AND side='Assets'";
    $result_assets = mysqli_query($link, $sql_assets);
    $total_assets = ($result_assets && $row = mysqli_fetch_assoc($result_assets)) ? ($row['total_assets'] ?? 0) : 0;

    $sql_liabilities = "SELECT SUM(amount) as total_liabilities FROM balance_sheet 
                        WHERE bank_name='$bank_name' AND month='$month' AND side='Liabilities'";
    $result_liabilities = mysqli_query($link, $sql_liabilities);
    $total_liabilities = ($result_liabilities && $row = mysqli_fetch_assoc($result_liabilities)) ? ($row['total_liabilities'] ?? 0) : 0;

    $working_capital = $total_assets - $total_liabilities;

    // 28. NEW SECTION 144 WC (Share Capital + Funds + Deposits + Borrowings)
    $new_section_144_wc = $shares + $funds + $deposits + $od;

    // Insert/Update data
    $currentDateTime = date('Y-m-d H:i:s');
    
    $sql_insert = "INSERT INTO summary_report (
        bank_name, month_year, shares, funds, deposits, loans_advances, cash_in_hand, 
        cash_at_bank, od, investments, fixed_dead_stock, interest_on_loans, 
        interest_on_investment, other_income, total_income, interest_on_deposit, 
        interest_on_od, prashaskiy, aasthapna, od_interest, total_me, provision, 
        profit, total_business, employee, per_employee_business, per_employee_income, 
        per_employee_profit, working_capital, new_section_144_wc, created_at
    ) VALUES (
        '$bank_name', '$month', '$shares', '$funds', '$deposits', '$loans_advances', '$cash_in_hand',
        '$cash_at_bank', '$od', '$investments', '$fixed_dead_stock', '$interest_on_loans',
        '$interest_on_investment', '$other_income', '$total_income', '$interest_on_deposit',
        '$interest_on_od', '$prashaskiy', '$aasthapna', '$od_interest', '$total_me', '$provision',
        '$profit', '$total_business', '$employee', '$per_employee_business', '$per_employee_income',
        '$per_employee_profit', '$working_capital', '$new_section_144_wc', '$currentDateTime'
    ) ON DUPLICATE KEY UPDATE
        shares = VALUES(shares),
        funds = VALUES(funds),
        deposits = VALUES(deposits),
        loans_advances = VALUES(loans_advances),
        cash_in_hand = VALUES(cash_in_hand),
        cash_at_bank = VALUES(cash_at_bank),
        od = VALUES(od),
        investments = VALUES(investments),
        fixed_dead_stock = VALUES(fixed_dead_stock),
        interest_on_loans = VALUES(interest_on_loans),
        interest_on_investment = VALUES(interest_on_investment),
        other_income = VALUES(other_income),
        total_income = VALUES(total_income),
        interest_on_deposit = VALUES(interest_on_deposit),
        interest_on_od = VALUES(interest_on_od),
        prashaskiy = VALUES(prashaskiy),
        aasthapna = VALUES(aasthapna),
        od_interest = VALUES(od_interest),
        total_me = VALUES(total_me),
        provision = VALUES(provision),
        profit = VALUES(profit),
        total_business = VALUES(total_business),
        employee = VALUES(employee),
        per_employee_business = VALUES(per_employee_business),
        per_employee_income = VALUES(per_employee_income),
        per_employee_profit = VALUES(per_employee_profit),
        working_capital = VALUES(working_capital),
        new_section_144_wc = VALUES(new_section_144_wc),
        created_at = VALUES(created_at)";

    if (mysqli_query($link, $sql_insert)) {
        // Success - redirect back
        mysqli_close($link);
        header("Location: summary.php?bank_name=$bank_name&month=$month&success=1");
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