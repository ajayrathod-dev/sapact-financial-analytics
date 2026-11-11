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
    // Define deposit particulars based on Excel sheet structure
    $deposit_particulars = [
        // CASA DEPOSIT
        [
            'particular' => 'Savings Deposit',
            'account_no' => '',
            'description' => 'BS - Saving Deposit'
        ],
        [
            'particular' => 'Current Deposit', 
            'account_no' => '',
            'description' => 'BS - Saving Deposit'
        ],
        // MUDAT/OTHER DEPOSIT - These will be dynamically populated from balance_sheet
    ];

    // Get all term deposit types from balance_sheet for Mudat/Other deposits
    $sql_term_deposits = "SELECT DISTINCT sub_subcategory 
                          FROM balance_sheet 
                          WHERE bank_name='$bank_name' 
                          AND month='$month' 
                          AND side='Liabilities' 
                          AND category='DEPOSITS' 
                          AND subcategory='Term Deposit' 
                          AND sub_subcategory != ''";
    
    $result_term = mysqli_query($link, $sql_term_deposits);
    while ($row_term = mysqli_fetch_assoc($result_term)) {
        $deposit_particulars[] = [
            'particular' => $row_term['sub_subcategory'],
            'account_no' => '',
            'description' => 'BS - Liabilities - Deposits - Term Deposit'
        ];
    }

    $currentDateTime = date('Y-m-d H:i:s');
    
    // Process each deposit particular
    foreach ($deposit_particulars as $deposit) {
        $particular = mysqli_real_escape_string($link, $deposit['particular']);
        $account_no = mysqli_real_escape_string($link, $deposit['account_no'] ?? '');
        
        // Get base amount from balance_sheet for current month
        $base_amount = 0;
        $sql_base = "SELECT amount FROM balance_sheet 
                     WHERE bank_name='$bank_name' 
                     AND month='$month' 
                     AND side='Liabilities' 
                     AND category='DEPOSITS' 
                     AND sub_subcategory LIKE '%$particular%'";
        
        $result_base = mysqli_query($link, $sql_base);
        if ($result_base && $row_base = mysqli_fetch_assoc($result_base)) {
            $base_amount = $row_base['amount'] ?? 0;
        }

        // For demonstration - in real scenario, you would fetch monthly data from respective tables
        // Using placeholder values - replace with actual data fetching logic
        $april = $base_amount * 0.95;  // Example calculation
        $may = $base_amount * 0.96;    // Example calculation  
        $june = $base_amount * 0.97;   // Example calculation
        $july = $base_amount * 0.98;   // Example calculation
        $august = $base_amount * 0.99; // Example calculation
        $september = $base_amount;     // Current month value
        $october = $base_amount * 1.01; // Example calculation
        $november = $base_amount * 1.02; // Example calculation
        $december = $base_amount * 1.03; // Example calculation
        $january = $base_amount * 1.04;  // Example calculation
        $february = $base_amount * 1.05; // Example calculation
        $march = $base_amount * 1.06;   // Example calculation

        // Calculate totals and averages
        $total_deposit = $april + $may + $june + $july + $august + $september + 
                        $october + $november + $december + $january + $february + $march;
        
        $avg_deposit = $total_deposit / 12;

        // Insert/Update data
        $sql_insert = "INSERT INTO mudat_thev (
            bank_name, month_year, particular, account_no, amount,
            april, may, june, july, august, september, october, november, december, january, february, march,
            total_deposit, avg_deposit, created_at
        ) VALUES (
            '$bank_name', '$month', '$particular', '$account_no', '$base_amount',
            '$april', '$may', '$june', '$july', '$august', '$september', '$october', '$november', '$december', '$january', '$february', '$march',
            '$total_deposit', '$avg_deposit', '$currentDateTime'
        ) ON DUPLICATE KEY UPDATE
            amount = VALUES(amount),
            april = VALUES(april),
            may = VALUES(may),
            june = VALUES(june),
            july = VALUES(july),
            august = VALUES(august),
            september = VALUES(september),
            october = VALUES(october),
            november = VALUES(november),
            december = VALUES(december),
            january = VALUES(january),
            february = VALUES(february),
            march = VALUES(march),
            total_deposit = VALUES(total_deposit),
            avg_deposit = VALUES(avg_deposit),
            created_at = VALUES(created_at)";

        if (!mysqli_query($link, $sql_insert)) {
            throw new Exception("Error inserting data for $particular: " . mysqli_error($link));
        }
    }
    
    // Success - redirect back
    mysqli_close($link);
    header("Location: mudat_thev.php?bank_name=$bank_name&month=$month&success=1");
    exit();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Close connection
mysqli_close($link);
?>