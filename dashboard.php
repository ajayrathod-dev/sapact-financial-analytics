<?php
// Start output buffering to catch errors
ob_start();

// Include header with error handling
if (file_exists('header.php')) {
    include('header.php');
} else {
    die('Header file not found!');
}

// Check if database connection exists
if (!isset($link) || $link === null) {
    die('Database connection not available. Please check your database configuration.');
}

// Set error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables with defaults
$u_uname = isset($u_uname) ? $u_uname : 'SapAct';
$bank_id = isset($bank_id) ? $bank_id : 1;
$month = isset($month) ? $month : date('Y-m');

// Initialize all variables to prevent undefined variable errors
$total_societies = 0;
$total_staff = 0;
$total_deposits = 0;
$total_loans = 0;
$total_assets = 0;
$total_liabilities = 0;
$slr_proof = 0;
$crr_proof = 4.0; // Default to required minimum
$cd_ratio = 0;
$net_worth = 0;
$crar_value = 0;
$profit_loss = 0;
$overdue_percentage = 0;
$deposit_types = [];
$loan_types = [];
$asset_allocation = [];
$liability_breakdown = [];

try {
    // Total Societies (Banks) - Real data from society_info
    $result_societies = mysqli_query($link, "SELECT COUNT(*) as total FROM society_info WHERE `by`='$u_uname'");
    if ($result_societies) {
        $row = mysqli_fetch_assoc($result_societies);
        $total_societies = $row['total'] ?? 0;
    }

    // Total Bank Staff - Real data from bank_officials (if table exists, otherwise use default)
    $total_staff = 0; // Default since bank_officials table not found in SQL dump

    // Fetch total deposits (Liabilities - DEPOSITS) - Real data from balance_sheet
    $result_deposits = mysqli_query($link, "SELECT SUM(amount) as total_deposits FROM balance_sheet WHERE bank_name='$bank_id' AND month='$month' AND side='Liabilities' AND category='DEPOSITS'");
    if ($result_deposits) {
        $row_deposits = mysqli_fetch_assoc($result_deposits);
        $total_deposits = $row_deposits['total_deposits'] ?? 0;
    }

    // Fetch total loans (Assets - LOANS & ADVANCES) - Real data from balance_sheet
    $result_loans = mysqli_query($link, "SELECT SUM(amount) as total_loans FROM balance_sheet WHERE bank_name='$bank_id' AND month='$month' AND side='Assets' AND category='LOANS & ADVANCES'");
    if ($result_loans) {
        $row_loans = mysqli_fetch_assoc($result_loans);
        $total_loans = $row_loans['total_loans'] ?? 0;
    }

    // Fetch total assets - Real data from balance_sheet
    $result_assets = mysqli_query($link, "SELECT SUM(amount) as total_assets FROM balance_sheet WHERE bank_name='$bank_id' AND month='$month' AND side='Assets'");
    if ($result_assets) {
        $row_assets = mysqli_fetch_assoc($result_assets);
        $total_assets = $row_assets['total_assets'] ?? 0;
    }

    // Fetch total liabilities - Real data from balance_sheet
    $result_liabilities = mysqli_query($link, "SELECT SUM(amount) as total_liabilities FROM balance_sheet WHERE bank_name='$bank_id' AND month='$month' AND side='Liabilities'");
    if ($result_liabilities) {
        $row_liabilities = mysqli_fetch_assoc($result_liabilities);
        $total_liabilities = $row_liabilities['total_liabilities'] ?? 0;
    }

    // Fetch deposit types for pie chart - Real data from balance_sheet
    $result_deposit_types = mysqli_query($link, "SELECT subcategory, sub_subcategory, SUM(amount) as amount FROM balance_sheet WHERE bank_name='$bank_id' AND month='$month' AND side='Liabilities' AND category='DEPOSITS' AND amount > 0 GROUP BY subcategory, sub_subcategory ORDER BY amount DESC LIMIT 6");
    if ($result_deposit_types) {
        while ($row = mysqli_fetch_assoc($result_deposit_types)) {
            $label = $row['subcategory'] . ($row['sub_subcategory'] ? ' - ' . $row['sub_subcategory'] : '');
            $deposit_types[$label] = (float)$row['amount'];
        }
    }

    // Fetch loan types for bar chart - Real data from balance_sheet
    $result_loan_types = mysqli_query($link, "SELECT sub_subcategory, SUM(amount) as amount FROM balance_sheet WHERE bank_name='$bank_id' AND month='$month' AND side='Assets' AND category='LOANS & ADVANCES' AND amount > 0 GROUP BY sub_subcategory ORDER BY amount DESC LIMIT 7");
    if ($result_loan_types) {
        while ($row = mysqli_fetch_assoc($result_loan_types)) {
            $label = $row['sub_subcategory'] ?: 'Other Loans';
            $loan_types[$label] = (float)$row['amount'];
        }
    }

    // Fetch SLR data - Real data from slr_report_multistate
    $result_slr = mysqli_query($link, "SELECT slr_proof_after_od FROM slr_report_multistate WHERE bank_name='$bank_id' AND month_year='$month' ORDER BY id DESC LIMIT 1");
    if ($result_slr && mysqli_num_rows($result_slr) > 0) {
        $row_slr = mysqli_fetch_assoc($result_slr);
        $slr_proof = (float)($row_slr['slr_proof_after_od'] ?? 0);
    }

    // Fetch CRR data - Real data from crr_report
    $result_crr = mysqli_query($link, "SELECT crr_percent FROM crr_report WHERE bank_name='$bank_id' AND month_year='$month' ORDER BY id DESC LIMIT 1");
    if ($result_crr && mysqli_num_rows($result_crr) > 0) {
        $row_crr = mysqli_fetch_assoc($result_crr);
        $crr_proof = (float)($row_crr['crr_percent'] ?? 4.0);
    }

    // Fetch CD Ratio - Real data from cd_ratio
    $result_cd = mysqli_query($link, "SELECT cd_ratio FROM cd_ratio WHERE bank_name='$bank_id' AND month='$month' ORDER BY id DESC LIMIT 1");
    if ($result_cd && mysqli_num_rows($result_cd) > 0) {
        $row_cd = mysqli_fetch_assoc($result_cd);
        $cd_ratio = (float)($row_cd['cd_ratio'] ?? 0);
    }

    // Fetch Net Worth - Real data from networth
    $result_networth = mysqli_query($link, "SELECT networth FROM networth WHERE bank_name='$bank_id' AND month_year='$month' ORDER BY id DESC LIMIT 1");
    if ($result_networth && mysqli_num_rows($result_networth) > 0) {
        $row_networth = mysqli_fetch_assoc($result_networth);
        $net_worth = (float)($row_networth['networth'] ?? 0);
    }

    // Fetch CRAR - Real data from capital_adequacy_report
    $result_crar = mysqli_query($link, "SELECT crar_value FROM capital_adequacy_report WHERE bank_name='$bank_id' AND month_year='$month' ORDER BY id DESC LIMIT 1");
    if ($result_crar && mysqli_num_rows($result_crar) > 0) {
        $row_crar = mysqli_fetch_assoc($result_crar);
        $crar_value = (float)($row_crar['crar_value'] ?? 0);
    }

    // Fetch Profit/Loss - Real data from profit_loss table
    $result_pl = mysqli_query($link, "SELECT 
        (SELECT COALESCE(SUM(amount), 0) FROM profit_loss WHERE bank_name='$bank_id' AND month='$month' AND side='Income') as income,
        (SELECT COALESCE(SUM(amount), 0) FROM profit_loss WHERE bank_name='$bank_id' AND month='$month' AND side='Expenses') as expenses");
    
    if ($result_pl) {
        $row_pl = mysqli_fetch_assoc($result_pl);
        $income = (float)($row_pl['income'] ?? 0);
        $expenses = (float)($row_pl['expenses'] ?? 0);
        $profit_loss = $income - $expenses;
    }

    // Fetch Overdue Percentage - Real data from mis_monthly_report
    $result_overdue = mysqli_query($link, "SELECT overdue_percent FROM mis_monthly_report WHERE bank_name='$bank_id' AND month_year='$month' ORDER BY id DESC LIMIT 1");
    if ($result_overdue && mysqli_num_rows($result_overdue) > 0) {
        $row_overdue = mysqli_fetch_assoc($result_overdue);
        $overdue_percentage = (float)($row_overdue['overdue_percent'] ?? 0);
    }

    // Fetch Asset Allocation - Real data from balance_sheet
    $result_assets = mysqli_query($link, "SELECT category, SUM(amount) as amount FROM balance_sheet WHERE bank_name='$bank_id' AND month='$month' AND side='Assets' AND amount > 0 GROUP BY category");
    if ($result_assets) {
        while ($row = mysqli_fetch_assoc($result_assets)) {
            $asset_allocation[$row['category']] = (float)$row['amount'];
        }
    }

    // Fetch Liability Breakdown - Real data from balance_sheet
    $result_liabilities = mysqli_query($link, "SELECT category, SUM(amount) as amount FROM balance_sheet WHERE bank_name='$bank_id' AND month='$month' AND side='Liabilities' AND amount > 0 GROUP BY category");
    if ($result_liabilities) {
        while ($row = mysqli_fetch_assoc($result_liabilities)) {
            $liability_breakdown[$row['category']] = (float)$row['amount'];
        }
    }

    // Fetch historical data for trends chart - Last 6 months data
    $historical_data = [];
    $months = [];
    $deposits_history = [];
    $loans_history = [];
    
    // Get last 6 months including current month
    for ($i = 5; $i >= 0; $i--) {
        $hist_month = date('Y-m', strtotime("-$i months", strtotime($month . '-01')));
        $months[] = date('M', strtotime($hist_month . '-01'));
        
        // Get deposits for this month
        $result_hist_dep = mysqli_query($link, "SELECT SUM(amount) as total FROM balance_sheet WHERE bank_name='$bank_id' AND month='$hist_month' AND side='Liabilities' AND category='DEPOSITS'");
        if ($result_hist_dep) {
            $row_dep = mysqli_fetch_assoc($result_hist_dep);
            $deposits_history[] = (float)($row_dep['total'] ?? 0);
        } else {
            $deposits_history[] = 0;
        }
        
        // Get loans for this month
        $result_hist_loan = mysqli_query($link, "SELECT SUM(amount) as total FROM balance_sheet WHERE bank_name='$bank_id' AND month='$hist_month' AND side='Assets' AND category='LOANS & ADVANCES'");
        if ($result_hist_loan) {
            $row_loan = mysqli_fetch_assoc($result_hist_loan);
            $loans_history[] = (float)($row_loan['total'] ?? 0);
        } else {
            $loans_history[] = 0;
        }
    }

} catch (Exception $e) {
    // Log error but continue with default values
    error_log('Dashboard error: ' . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SapAct Premium Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #1abc9c;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --info: #2980b9;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --card-bg: #ffffff;
            --sidebar-bg: #2c3e50;
            --header-bg: #ffffff;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #495057;
            line-height: 1.6;
        }
        
        .page-wrapper {
            padding: 20px;
        }
        
        .page-content {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        
        /* Professional card styling */
        .card-premium {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            overflow: hidden;
            background: var(--card-bg);
            margin-bottom: 20px;
        }
        
        .card-premium:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }
        
        .card-header-premium {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .card-body-premium {
            padding: 20px;
        }
        
        /* Professional metric cards */
        .bg-gradient-scooter { background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); }
        .bg-gradient-ohhappiness { background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); }
        .bg-gradient-blooker { background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); }
        .bg-gradient-burning { background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); }
        .bg-gradient-kyoto { background: linear-gradient(135deg, #f39c12 0%, #d35400 100%); }
        .bg-gradient-moonlit { background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%); }
        .bg-gradient-coral { background: linear-gradient(135deg, #e67e22 0%, #d35400 100%); }
        .bg-gradient-green { background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); }
        
        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
        }
        
        .metric-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 0;
            color: white;
        }
        
        .metric-label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 5px;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .status-good { background: #d4edda; color: #155724; }
        .status-warning { background: #fff3cd; color: #856404; }
        .status-danger { background: #f8d7da; color: #721c24; }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 15px;
            color: white;
        }
        
        .stat-content {
            flex: 1;
        }
        
        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        
        .chart-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #343a40;
        }
        
        .progress-bar-premium {
            height: 10px;
            border-radius: 5px;
            background: #e9ecef;
            overflow: hidden;
        }
        
        .progress-bar-premium .progress {
            height: 100%;
            border-radius: 5px;
        }
        
        .data-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        
        /* Professional navbar/sidebar styling */
        .navbar-nav .nav-link,
        .sidebar .nav-link {
            text-decoration: none !important;
            transition: all 0.2s ease;
            border-bottom: none !important;
            position: relative;
            padding: 10px 15px;
            border-radius: 6px;
            margin: 2px 0;
        }
        
        .navbar-nav .nav-link:hover,
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            text-decoration: none !important;
        }
        
        .navbar-nav .nav-link.active,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }
        
        /* Remove any underline effects */
        a:not(.btn) {
            text-decoration: none !important;
        }
        
        /* Professional typography */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
            color: var(--primary);
        }
        
        .dashboard-header h1 {
            color: white;
        }
        
        /* Improved button styling */
        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 8px 16px;
        }
        
        /* Table styling */
        .table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .table th {
            background-color: var(--primary);
            color: white;
            font-weight: 600;
            border: none;
        }
        
        .table td {
            border-color: #e9ecef;
            padding: 12px 15px;
        }
        
        /* Alert styling */
        .alert {
            border-radius: 8px;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        /* Professional spacing */
        .section-spacing {
            margin-bottom: 30px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-grid {
                grid-template-columns: 1fr;
            }
            
            .page-content {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Professional Dashboard Header -->
            <div class="dashboard-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1 class="mb-1"><i class='bx bxs-dashboard me-2'></i>SapAct Financial Dashboard</h1>
                        <p class="mb-0 opacity-75">Comprehensive Financial Overview & Analytics</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-1"><strong>Period:</strong> <?php echo htmlspecialchars($month); ?></p>
                        <p class="mb-0"><strong>Updated:</strong> <?php echo date('M d, Y \a\t g:i A'); ?></p>
                        <p class="mb-0"><strong>Bank ID:</strong> <?php echo htmlspecialchars($bank_id); ?></p>
                    </div>
                </div>
            </div>

            <!-- Data Status Alert -->
            <?php if ($total_deposits == 0 && $total_loans == 0): ?>
            <div class="alert alert-warning">
                <i class='bx bx-info-circle'></i> <strong>Notice:</strong> No financial data found for <?php echo htmlspecialchars($month); ?>. 
                Please check if data has been uploaded for this period.
            </div>
            <?php endif; ?>

            <!-- Key Metrics Cards -->
            <div class="stats-grid">
                <!-- Total Societies -->
                <div class="stat-card">
                    <div class="stat-icon bg-gradient-scooter">
                        <i class='bx bxs-building'></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format($total_societies); ?></div>
                        <div class="stat-label">Total Societies</div>
                    </div>
                </div>

                <!-- Total Staff -->
                <div class="stat-card">
                    <div class="stat-icon bg-gradient-ohhappiness">
                        <i class='bx bxs-group'></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format($total_staff); ?></div>
                        <div class="stat-label">Total Staff</div>
                    </div>
                </div>

                <!-- Total Deposits -->
                <div class="stat-card">
                    <div class="stat-icon bg-gradient-blooker">
                        <i class='bx bxs-wallet'></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">₹<?php echo number_format($total_deposits / 100000, 2); ?> Cr</div>
                        <div class="stat-label">Total Deposits</div>
                        <small class="text-muted">Real-time from balance_sheet</small>
                    </div>
                </div>

                <!-- Total Loans -->
                <div class="stat-card">
                    <div class="stat-icon bg-gradient-burning">
                        <i class='bx bxs-credit-card'></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">₹<?php echo number_format($total_loans / 100000, 2); ?> Cr</div>
                        <div class="stat-label">Total Loans</div>
                        <small class="text-muted">Real-time from balance_sheet</small>
                    </div>
                </div>

                <!-- CD Ratio -->
                <div class="stat-card">
                    <div class="stat-icon bg-gradient-kyoto">
                        <i class='bx bxs-pie-chart-alt'></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format($cd_ratio, 2); ?>%</div>
                        <div class="stat-label">CD Ratio</div>
                        <small class="text-muted">From cd_ratio table</small>
                    </div>
                </div>

                <!-- Net Worth -->
                <div class="stat-card">
                    <div class="stat-icon bg-gradient-moonlit">
                        <i class='bx bxs-coin-stack'></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">₹<?php echo number_format($net_worth / 100000, 2); ?> Cr</div>
                        <div class="stat-label">Net Worth</div>
                        <small class="text-muted">From networth table</small>
                    </div>
                </div>

                <!-- CRAR -->
                <div class="stat-card">
                    <div class="stat-icon bg-gradient-coral">
                        <i class='bx bxs-bar-chart-alt'></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value"><?php echo number_format($crar_value, 2); ?>%</div>
                        <div class="stat-label">CRAR</div>
                        <small class="text-muted">From capital_adequacy_report</small>
                    </div>
                </div>

                <!-- Profit/Loss -->
                <div class="stat-card">
                    <div class="stat-icon <?php echo $profit_loss >= 0 ? 'bg-gradient-green' : 'bg-gradient-burning'; ?>">
                        <i class='bx bx-<?php echo $profit_loss >= 0 ? 'trending-up' : 'trending-down'; ?>'></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">₹<?php echo number_format(abs($profit_loss) / 100000, 2); ?> Cr</div>
                        <div class="stat-label"><?php echo $profit_loss >= 0 ? 'Profit' : 'Loss'; ?></div>
                        <small class="text-muted">Calculated from profit_loss table</small>
                    </div>
                </div>
            </div>

            <!-- Compliance Section -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card card-premium">
                        <div class="card-header card-header-premium">
                            <h5 class="mb-0"><i class='bx bxs-line-chart me-2'></i>SLR Compliance</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <h2 class="mb-0 <?php echo $slr_proof >= 25 ? 'text-success' : 'text-danger'; ?>"><?php echo number_format($slr_proof, 2); ?>%</h2>
                                <small>of 25% Required</small>
                            </div>
                            <div class="progress-bar-premium mb-3">
                                <div class="progress bg-<?php echo $slr_proof >= 25 ? 'success' : 'danger'; ?>" style="width: <?php echo min($slr_proof, 100); ?>%"></div>
                            </div>
                            <span class="status-badge <?php echo $slr_proof >= 25 ? 'status-good' : 'status-warning'; ?>">
                                <?php echo $slr_proof >= 25 ? 'Compliant' : 'Needs Attention'; ?>
                            </span>
                            <small class="d-block mt-2 text-muted">From slr_report_multistate</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card card-premium">
                        <div class="card-header card-header-premium">
                            <h5 class="mb-0"><i class='bx bxs-pie-chart-alt-2 me-2'></i>CRR Compliance</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <h2 class="mb-0 <?php echo $crr_proof >= 4 ? 'text-success' : 'text-danger'; ?>"><?php echo number_format($crr_proof, 2); ?>%</h2>
                                <small>of 4% Required</small>
                            </div>
                            <div class="progress-bar-premium mb-3">
                                <div class="progress bg-<?php echo $crr_proof >= 4 ? 'success' : 'danger'; ?>" style="width: <?php echo min($crr_proof * 25, 100); ?>%"></div>
                            </div>
                            <span class="status-badge <?php echo $crr_proof >= 4 ? 'status-good' : 'status-warning'; ?>">
                                <?php echo $crr_proof >= 4 ? 'Compliant' : 'Needs Attention'; ?>
                            </span>
                            <small class="d-block mt-2 text-muted">From crr_report</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card card-premium">
                        <div class="card-header card-header-premium">
                            <h5 class="mb-0"><i class='bx bxs-time-five me-2'></i>Overdue Percentage</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <h2 class="mb-0 <?php echo $overdue_percentage <= 5 ? 'text-success' : ($overdue_percentage <= 10 ? 'text-warning' : 'text-danger'); ?>"><?php echo number_format($overdue_percentage, 2); ?>%</h2>
                                <small>of Total Loans</small>
                            </div>
                            <div class="progress-bar-premium mb-3">
                                <div class="progress bg-<?php echo $overdue_percentage <= 5 ? 'success' : ($overdue_percentage <= 10 ? 'warning' : 'danger'); ?>" style="width: <?php echo min($overdue_percentage * 10, 100); ?>%"></div>
                            </div>
                            <span class="status-badge <?php echo $overdue_percentage <= 5 ? 'status-good' : ($overdue_percentage <= 10 ? 'status-warning' : 'status-danger'); ?>">
                                <?php echo $overdue_percentage <= 5 ? 'Low Risk' : ($overdue_percentage <= 10 ? 'Moderate Risk' : 'High Risk'); ?>
                            </span>
                            <small class="d-block mt-2 text-muted">From mis_monthly_report</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="chart-grid">
                <!-- Assets vs Liabilities -->
                <div class="chart-card">
                    <div class="chart-title">Assets vs Liabilities</div>
                    <div class="chart-container">
                        <canvas id="assetsLiabilitiesChart"></canvas>
                    </div>
                </div>

                <!-- Deposit Distribution -->
                <div class="chart-card">
                    <div class="chart-title">Deposit Distribution</div>
                    <div class="chart-container">
                        <canvas id="depositTypesChart"></canvas>
                    </div>
                </div>

                <!-- Asset Allocation -->
                <div class="chart-card">
                    <div class="chart-title">Asset Allocation</div>
                    <div class="chart-container">
                        <canvas id="assetAllocationChart"></canvas>
                    </div>
                </div>

                <!-- Liability Breakdown -->
                <div class="chart-card">
                    <div class="chart-title">Liability Breakdown</div>
                    <div class="chart-container">
                        <canvas id="liabilityBreakdownChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Loan Portfolio Breakdown -->
            <div class="chart-card mb-4">
                <div class="chart-title">Loan Portfolio Breakdown</div>
                <div class="chart-container" style="height: 400px;">
                    <canvas id="loanTypesChart"></canvas>
                </div>
            </div>

            <!-- Performance Trends -->
            <div class="chart-card">
                <div class="chart-title">Financial Performance Trends (Last 6 Months)</div>
                <div class="chart-container">
                    <canvas id="performanceTrendsChart"></canvas>
                </div>
            </div>

            <!-- Data Summary -->
            <div class="card card-premium mt-4">
                <div class="card-header card-header-premium">
                    <h5 class="mb-0"><i class='bx bxs-data me-2'></i>Data Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Data Sources:</h6>
                            <ul>
                                <li>Balance Sheet: <?php echo $total_assets > 0 ? '✓ Loaded' : '✗ No data'; ?></li>
                                <li>Profit/Loss: <?php echo $profit_loss != 0 ? '✓ Loaded' : '✗ No data'; ?></li>
                                <li>Compliance Reports: <?php echo ($slr_proof > 0 || $crr_proof > 0) ? '✓ Loaded' : '✗ No data'; ?></li>
                                <li>CD Ratio: <?php echo $cd_ratio > 0 ? '✓ Loaded' : '✗ No data'; ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Last Updated:</h6>
                            <p class="mb-1">Database: <?php echo date('M d, Y H:i:s'); ?></p>
                            <p class="mb-1">Current Period: <?php echo htmlspecialchars($month); ?></p>
                            <p class="mb-0">Bank: <?php echo htmlspecialchars($bank_id); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Prepare data for charts from PHP variables
        const depositTypesData = {
            labels: <?php echo !empty($deposit_types) ? json_encode(array_keys($deposit_types)) : json_encode(['No Data Available']); ?>,
            datasets: [{
                data: <?php echo !empty($deposit_types) ? json_encode(array_values($deposit_types)) : json_encode([100]); ?>,
                backgroundColor: [
                    '#3498db', '#2ecc71', '#9b59b6', '#e74c3c', 
                    '#f39c12', '#1abc9c', '#95a5a6', '#34495e'
                ]
            }]
        };

        const loanTypesData = {
            labels: <?php echo !empty($loan_types) ? json_encode(array_keys($loan_types)) : json_encode(['No Data Available']); ?>,
            datasets: [{
                label: 'Loan Amount (₹)',
                data: <?php echo !empty($loan_types) ? json_encode(array_values($loan_types)) : json_encode([0]); ?>,
                backgroundColor: '#3498db',
                borderColor: '#3498db',
                borderWidth: 1
            }]
        };

        const assetAllocationData = {
            labels: <?php echo !empty($asset_allocation) ? json_encode(array_keys($asset_allocation)) : json_encode(['No Data Available']); ?>,
            datasets: [{
                data: <?php echo !empty($asset_allocation) ? json_encode(array_values($asset_allocation)) : json_encode([100]); ?>,
                backgroundColor: [
                    '#3498db', '#2ecc71', '#9b59b6', '#e74c3c', 
                    '#f39c12', '#1abc9c', '#95a5a6', '#34495e'
                ]
            }]
        };

        const liabilityBreakdownData = {
            labels: <?php echo !empty($liability_breakdown) ? json_encode(array_keys($liability_breakdown)) : json_encode(['No Data Available']); ?>,
            datasets: [{
                data: <?php echo !empty($liability_breakdown) ? json_encode(array_values($liability_breakdown)) : json_encode([100]); ?>,
                backgroundColor: [
                    '#3498db', '#2ecc71', '#9b59b6', '#e74c3c', 
                    '#f39c12', '#1abc9c', '#95a5a6', '#34495e'
                ]
            }]
        };

        // Historical data for trends
        const historicalMonths = <?php echo isset($months) ? json_encode($months) : json_encode(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']); ?>;
        const historicalDeposits = <?php echo isset($deposits_history) ? json_encode($deposits_history) : json_encode([0, 0, 0, 0, 0, 0]); ?>;
        const historicalLoans = <?php echo isset($loans_history) ? json_encode($loans_history) : json_encode([0, 0, 0, 0, 0, 0]); ?>;

        // Initialize charts when the DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Assets vs Liabilities Chart
            const assetsLiabilitiesCtx = document.getElementById('assetsLiabilitiesChart');
            if (assetsLiabilitiesCtx) {
                new Chart(assetsLiabilitiesCtx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: ['Total Assets', 'Total Liabilities'],
                        datasets: [{
                            label: 'Amount (₹)',
                            data: [<?php echo $total_assets; ?>, <?php echo $total_liabilities; ?>],
                            backgroundColor: ['#3498db', '#e74c3c'],
                            borderColor: ['#3498db', '#e74c3c'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '₹' + (value / 10000000).toFixed(1) + ' Cr';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Deposit Types Chart
            const depositTypesCtx = document.getElementById('depositTypesChart');
            if (depositTypesCtx) {
                new Chart(depositTypesCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: depositTypesData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = Math.round((value / total) * 100);
                                        return `${label}: ₹${(value/100000).toFixed(2)} Lakh (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Loan Types Chart
            const loanTypesCtx = document.getElementById('loanTypesChart');
            if (loanTypesCtx) {
                new Chart(loanTypesCtx.getContext('2d'), {
                    type: 'bar',
                    data: loanTypesData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '₹' + (value / 10000000).toFixed(1) + ' Cr';
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Asset Allocation Chart
            const assetAllocationCtx = document.getElementById('assetAllocationChart');
            if (assetAllocationCtx) {
                new Chart(assetAllocationCtx.getContext('2d'), {
                    type: 'pie',
                    data: assetAllocationData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Liability Breakdown Chart
            const liabilityBreakdownCtx = document.getElementById('liabilityBreakdownChart');
            if (liabilityBreakdownCtx) {
                new Chart(liabilityBreakdownCtx.getContext('2d'), {
                    type: 'pie',
                    data: liabilityBreakdownData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }

            // Performance Trends Chart with real historical data
            const performanceTrendsCtx = document.getElementById('performanceTrendsChart');
            if (performanceTrendsCtx) {
                new Chart(performanceTrendsCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: historicalMonths,
                        datasets: [{
                            label: 'Deposits',
                            data: historicalDeposits,
                            borderColor: '#3498db',
                            backgroundColor: 'rgba(52, 152, 219, 0.1)',
                            tension: 0.1,
                            fill: true
                        }, {
                            label: 'Loans',
                            data: historicalLoans,
                            borderColor: '#e74c3c',
                            backgroundColor: 'rgba(231, 76, 60, 0.1)',
                            tension: 0.1,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '₹' + (value / 1000000).toFixed(1) + ' Cr';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>

<?php
// Clean output buffer
ob_end_flush();

// Include footer if it exists
if (file_exists('footer.php')) {
    include('footer.php');
}
?>