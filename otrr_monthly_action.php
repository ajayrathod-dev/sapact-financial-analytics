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
    // Since we don't have the actual data source for OTRR, we'll create sample data structure
    // In real implementation, this would fetch from the actual data source referenced in Excel
    
    $currentDateTime = date('Y-m-d H:i:s');
    
    // Sample weekly data structure - in real scenario, this would come from external source
    $weeklyData = [
        'Week 1' => [
            ['days' => 'Day 1', 'date' => '2025-03-01', 'demand_accounts' => 100, 'demand_amount' => 50000, 'recovery_accounts' => 50, 'received_amount' => 25000],
            ['days' => 'Day 2', 'date' => '2025-03-02', 'demand_accounts' => 120, 'demand_amount' => 60000, 'recovery_accounts' => 60, 'received_amount' => 30000],
            // ... more days
        ],
        // ... more weeks
    ];
    
    // For demo purposes, creating sample calculation logic
    // In production, replace with actual data fetching logic
    
    // Clear previous data for this bank/month
    $sql_delete = "DELETE FROM otrr_monthly_report WHERE bank_name = '$bank_name' AND month_year = '$month'";
    mysqli_query($link, $sql_delete);
    
    // Sample data insertion - replace with actual data processing
    $weeks = ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5'];
    
    foreach ($weeks as $week) {
        $weekly_total_demand_accounts = 0;
        $weekly_total_demand_amount = 0;
        $weekly_total_recovery_accounts = 0;
        $weekly_total_received_amount = 0;
        
        // Process daily data for each week (sample data)
        for ($day = 1; $day <= 7; $day++) {
            $days = "Day $day";
            $report_date = "$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
            
            // Sample calculations - replace with actual data
            $total_demand_accounts = rand(80, 150);
            $total_demand_amount = rand(40000, 80000);
            $total_recovery_accounts = rand(40, 120);
            $actually_received_amount = rand(20000, 60000);
            
            $diff_accounts = $total_demand_accounts - $total_recovery_accounts;
            $actually_diff_amount = $total_demand_amount - $actually_received_amount;
            
            // Calculate OTRR percentage
            $otrr_percentage = 0;
            if ($total_demand_amount > 0) {
                $otrr_percentage = ($actually_received_amount / $total_demand_amount) * 100;
            }
            
            // Calculate Missing Account OTRR
            $missing_account_otrr = 0;
            if ($total_demand_accounts > 0) {
                $missing_account_otrr = ($diff_accounts / $total_demand_accounts) * 100;
            }
            
            // Insert daily data
            $sql_insert = "INSERT INTO otrr_monthly_report (
                bank_name, month_year, week_number, days, report_date,
                total_demand_accounts, total_demand_amount, 
                total_recovery_accounts, actually_received_amount,
                diff_accounts, actually_diff_amount, otrr_percentage, missing_account_otrr,
                created_at
            ) VALUES (
                '$bank_name', '$month', '$week', '$days', '$report_date',
                '$total_demand_accounts', '$total_demand_amount',
                '$total_recovery_accounts', '$actually_received_amount',
                '$diff_accounts', '$actually_diff_amount', '$otrr_percentage', '$missing_account_otrr',
                '$currentDateTime'
            )";
            
            mysqli_query($link, $sql_insert);
            
            // Accumulate weekly totals
            $weekly_total_demand_accounts += $total_demand_accounts;
            $weekly_total_demand_amount += $total_demand_amount;
            $weekly_total_recovery_accounts += $total_recovery_accounts;
            $weekly_total_received_amount += $actually_received_amount;
        }
        
        // Calculate weekly totals
        $weekly_diff_accounts = $weekly_total_demand_accounts - $weekly_total_recovery_accounts;
        $weekly_actually_diff_amount = $weekly_total_demand_amount - $weekly_total_received_amount;
        
        $weekly_otrr_percentage = 0;
        if ($weekly_total_demand_amount > 0) {
            $weekly_otrr_percentage = ($weekly_total_received_amount / $weekly_total_demand_amount) * 100;
        }
        
        $weekly_missing_account_otrr = 0;
        if ($weekly_total_demand_accounts > 0) {
            $weekly_missing_account_otrr = ($weekly_diff_accounts / $weekly_total_demand_accounts) * 100;
        }
        
        // Insert weekly total
        $sql_weekly_total = "INSERT INTO otrr_monthly_report (
            bank_name, month_year, week_number, days, report_date,
            total_demand_accounts, total_demand_amount, 
            total_recovery_accounts, actually_received_amount,
            diff_accounts, actually_diff_amount, otrr_percentage, missing_account_otrr,
            is_weekly_total, created_at
        ) VALUES (
            '$bank_name', '$month', '$week', 'TOTAL', '',
            '$weekly_total_demand_accounts', '$weekly_total_demand_amount',
            '$weekly_total_recovery_accounts', '$weekly_total_received_amount',
            '$weekly_diff_accounts', '$weekly_actually_diff_amount', '$weekly_otrr_percentage', '$weekly_missing_account_otrr',
            1, '$currentDateTime'
        )";
        
        mysqli_query($link, $sql_weekly_total);
    }
    
    // Calculate grand totals (simplified)
    $sql_grand_totals = "SELECT 
                        SUM(total_demand_accounts) as grand_demand_accounts,
                        SUM(total_demand_amount) as grand_demand_amount,
                        SUM(total_recovery_accounts) as grand_recovery_accounts,
                        SUM(actually_received_amount) as grand_received_amount
                        FROM otrr_monthly_report 
                        WHERE bank_name = '$bank_name' 
                        AND month_year = '$month' 
                        AND is_weekly_total = 1";
    
    $result_grand = mysqli_query($link, $sql_grand_totals);
    $grand = mysqli_fetch_assoc($result_grand);
    
    $grand_demand_accounts = $grand['grand_demand_accounts'] ?? 0;
    $grand_demand_amount = $grand['grand_demand_amount'] ?? 0;
    $grand_recovery_accounts = $grand['grand_recovery_accounts'] ?? 0;
    $grand_received_amount = $grand['grand_received_amount'] ?? 0;
    
    $grand_diff_accounts = $grand_demand_accounts - $grand_recovery_accounts;
    $grand_actually_diff_amount = $grand_demand_amount - $grand_received_amount;
    
    $grand_otrr_percentage = 0;
    if ($grand_demand_amount > 0) {
        $grand_otrr_percentage = ($grand_received_amount / $grand_demand_amount) * 100;
    }
    
    $grand_missing_account_otrr = 0;
    if ($grand_demand_accounts > 0) {
        $grand_missing_account_otrr = ($grand_diff_accounts / $grand_demand_accounts) * 100;
    }
    
    // Insert grand total
    $sql_grand_total = "INSERT INTO otrr_monthly_report (
        bank_name, month_year, week_number, days, report_date,
        total_demand_accounts, total_demand_amount, 
        total_recovery_accounts, actually_received_amount,
        diff_accounts, actually_diff_amount, otrr_percentage, missing_account_otrr,
        is_grand_total, created_at
    ) VALUES (
        '$bank_name', '$month', 'Grand Total', '', '',
        '$grand_demand_accounts', '$grand_demand_amount',
        '$grand_recovery_accounts', '$grand_received_amount',
        '$grand_diff_accounts', '$grand_actually_diff_amount', '$grand_otrr_percentage', '$grand_missing_account_otrr',
        1, '$currentDateTime'
    )";
    
    mysqli_query($link, $sql_grand_total);
    
    // Success - redirect back
    mysqli_close($link);
    header("Location: otrr_monthly.php?bank_name=$bank_name&month=$month&success=1");
    exit();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
mysqli_close($link);
?>