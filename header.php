<?php
session_start();

include("connection.php");

if (isset($_COOKIE['uuid'])) {

	$u_uname = mysqli_real_escape_string($link, $_COOKIE['uuid']);

} else {

	$u_uname = '';

}
 $sql4 = "SELECT * FROM ods WHERE uuid='$u_uname' AND status='verified'";

$result4 = mysqli_query($link, $sql4);

if ($result4->num_rows > 0) {

	while ($row4 = $result4->fetch_assoc()) {

		$u_uname = mysqli_real_escape_string($link, $row4['user_id']);

		$br_id = mysqli_real_escape_string($link, $row4['user_id']);

	}

} else {

	header("location:logout.php");

	exit;

}


// $sql10 = "SELECT * FROM admin WHERE user_id='$u_uname'";

// $result10 = mysqli_query($link, $sql10);

// if ($result10->num_rows > 0) {

// 	while ($row10 = $result10->fetch_assoc()) {

// 		$designation = mysqli_real_escape_string($link, $row10['designation']);
// 		$designation_if = mysqli_real_escape_string($link, $row10['designation_if']);
// 		$mybranch = mysqli_real_escape_string($link, $row10['branch']);

// 			}

// }


?>

<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8"> 
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--favicon-->
	<link rel="icon" href="logo.png" type="image/png" />
	<!--plugins-->
	<link href="assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet"/>
	<link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
	<link href="assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
	<link href="assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="assets/plugins/notifications/css/lobibox.min.css" />
	<!-- loader-->
	<link href="assets/css/pace.min.css" rel="stylesheet" />
	<script src="assets/js/pace.min.js"></script>
	<!-- Bootstrap CSS -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/css/bootstrap-extended.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
	<link href="assets/css/app.css" rel="stylesheet">
	<link href="assets/css/icons.css" rel="stylesheet">
	<!-- Theme Style CSS -->
	<link rel="stylesheet" href="assets/css/dark-theme.css" />
	<link rel="stylesheet" href="assets/css/semi-dark.css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
	<link rel="stylesheet" href="assets/css/header-colors.css" />
	<title>Sapact</title>
</head>

<body>
	<!--wrapper-->
	<div class="wrapper">
		<!--sidebar wrapper -->
		<div class="sidebar-wrapper" data-simplebar="true">
			<div class="sidebar-header">
				<div>
					<img src="logo.png" class="logo-icon" alt="logo icon" style="width:100px;">
				</div>
				<!--<div>-->
				<!--	<h4 class="logo-text">Multi.</h4>-->
				<!--</div>-->
				<div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i> 
				</div>
			</div>
			<!--navigation-->
			<ul class="metismenu" id="menu">
				<li>
					<a href="dashboard.php">
						<div class="parent-icon"><i class='bx bx-home-circle'></i>
						</div>
						<div class="menu-title">Dashboard</div>
					</a>
					
				</li>
					<li>
					<a class="has-arrow" href="javascript:;">
						<div class="parent-icon"><i class="bx bx-line-chart"></i>
						</div>
						<div class="menu-title">Bank Details</div> 
					</a>
					<ul>
						<li> <a href="add_bank.php"><i class="bx bx-right-arrow-alt"></i>Add Bank</a>
						</li>
					
					</ul>
				</li>
					<li>
					<a class="has-arrow" href="javascript:;">
						<div class="parent-icon"><i class="bx bx-line-chart"></i>
						</div>
						<div class="menu-title">Staff Details</div> 
					</a>
					<ul>
						<li> <a href="bank_officials.php"><i class="bx bx-right-arrow-alt"></i>Add Staff</a>
						</li>
							<li> <a href="followup.php"><i class="bx bx-right-arrow-alt"></i>Follow Up</a>
						</li>
					</ul>
				</li>
			
			 
			   	<li>
					<a class="has-arrow" href="javascript:;">
						<div class="parent-icon"><i class="bx bx-line-chart"></i>
						</div>
						<div class="menu-title">Upload</div> 
					</a>
					<ul>
						<li> <a href="balance_form.php"><i class="bx bx-right-arrow-alt"></i> Balance Sheet</a>
						</li>
							<li> <a href="profit_loss_form.php"><i class="bx bx-right-arrow-alt"></i>Profit & Loss A/c</a>
						</li>
							<li> <a href="loan_advances.php"><i class="bx bx-right-arrow-alt"></i>NPA</a>
						</li>
					
					</ul>
				</li>
			 	<li>
					<a class="has-arrow" href="javascript:;">
						<div class="parent-icon"><i class="bx bx-line-chart"></i>
						</div>
						<div class="menu-title"> NAGRI REPORT</div> 
					</a>
					<ul>
						<li> <a href="networth_form.php"><i class="bx bx-right-arrow-alt"></i>1) NETWORTH For CRAR</a>
						</li>
						<li> <a href="crar_reports1.php"><i class="bx bx-right-arrow-alt"></i>2) CRAR Report</a>
						</li>
						<li> <a href="camales_networth_form.php"><i class="bx bx-right-arrow-alt"></i>3) CAMELS Networth Report</a>
						</li>
						
						<li> <a href="CD_Ratio.php"><i class="bx bx-right-arrow-alt"></i>4) CD Ratio Report</a>
						</li>

						
						<li> <a href="slr_report.php"><i class="bx bx-right-arrow-alt"></i>5) SLR Report</a>
						</li>

						<li> <a href="slr_nagri.php"><i class="bx bx-right-arrow-alt"></i>6) SLR Report Incl Nagari Patsanstha INV</a>
						</li>
						
						<li> <a href="slr_multistate_report.php"><i class="bx bx-right-arrow-alt"></i>7) Multistate After OD - SLR  Report</a>
						</li>

						<li> <a href="crr_report.php"><i class="bx bx-right-arrow-alt"></i>8) CRR  Report</a>
						</li>

						<li> <a href="crr_report_incl_nagri.php"><i class="bx bx-right-arrow-alt"></i>9) CRR  Report Including Nagri Savings,Current Deposits </a>
						</li>

						<li> <a href="mis_monthly.php"><i class="bx bx-right-arrow-alt"></i>10) Monthly Mis Report </a>
						</li>
						<li> <a href="mis_classification.php"><i class="bx bx-right-arrow-alt"></i>11) MIS CLASSIFICATION REPORT </a>
						</li>
						<li> <a href="capital_adequacy.php"><i class="bx bx-right-arrow-alt"></i>12) Capital Adiquancy Report </a>
						</li>
							<li> <a href="dep_adv.php"><i class="bx bx-right-arrow-alt"></i>13) DEPOSITS & ADVANCES REPORT </a>
						</li>
							<li> <a href="loan_adv.php"><i class="bx bx-right-arrow-alt"></i>14) LOANS & ADVANCES REPORT </a>
						</li>
							<li> <a href="sick.php"><i class="bx bx-right-arrow-alt"></i>15) SICK SOCIETY REPORT </a>
						</li>
						<li> <a href="weak_society.php"><i class="bx bx-right-arrow-alt"></i>16) WEAK SOCIETY REPORT </a>
						</li>
							<li> <a href="Investment_dead.php"><i class="bx bx-right-arrow-alt"></i>17) INVESTMENT & FIX + DEAD STOCK REPORT </a>
						</li>
						<li> <a href="cash_in_h.php"><i class="bx bx-right-arrow-alt"></i>18) CASH IN HAND + BANK + OD REPORT </a>
						</li>
						<li> <a href="networth_rep.php"><i class="bx bx-right-arrow-alt"></i>19) NETWORTH REPORT + CD RATIO </a>
						</li>
						<li> <a href="income_exp.php"><i class="bx bx-right-arrow-alt"></i>20) INCOME & EXPENCES + PROFIT REPORT </a>
						</li>
							<li> <a href="cd_month.php"><i class="bx bx-right-arrow-alt"></i>21) C.D RATIO AT THE YEAR END REPORT </a>
						</li>
							<li> <a href="wc_profit_op_me.php"><i class="bx bx-right-arrow-alt"></i>22) WORKING CAPITAL + PROFIT + OP + ME REPORT </a>
						</li>
						<li> <a href="abr_alr.php"><i class="bx bx-right-arrow-alt"></i>23) ABR ALR REPORT </a>
						</li>
							<li> <a href="overdue_npa_recovery.php"><i class="bx bx-right-arrow-alt"></i>24) OVERDUE & OLD NPA RECOVERY REPORT </a>
						</li>
							<li> <a href="cost_of_funds.php"><i class="bx bx-right-arrow-alt"></i>25) COST ANALYSIS REPORT </a>
						</li>
							<li> <a href="89_abr_alr_report.php"><i class="bx bx-right-arrow-alt"></i>26) 89 A ABR ALR REPORT </a>
						</li>
							<li> <a href="sec_144.php"><i class="bx bx-right-arrow-alt"></i>27) SEC 144 REPORT </a>
						</li>
							<li> <a href="mudat_thev.php"><i class="bx bx-right-arrow-alt"></i>28) MUDAT THEV REPORT </a>
						</li>
							<li> <a href="share_fund.php"><i class="bx bx-right-arrow-alt"></i>29) SHARES + FUNDS REPORT </a>
						</li>
							<li> <a href="otrr_monthly.php"><i class="bx bx-right-arrow-alt"></i>30) OTRR MONTHLY REPORT </a>
						</li>
							<li> <a href="loan_exp.php"><i class="bx bx-right-arrow-alt"></i>31) LOAN EXPOSURE LIMIT REPORT </a>
						</li>
    							<li> <a href="move_in.php"><i class="bx bx-right-arrow-alt"></i>32) MOVE IN NPA REPORT </a>
						</li>
							<li> <a href="summary.php"><i class="bx bx-right-arrow-alt"></i>33) SUMMARY REPORT </a>
						</li>
						<!--	<li> <a href="networth_form.php"><i class="bx bx-right-arrow-alt"></i>34) NETWORTH REPORT </a>-->
						<!--</li>-->
							<li> <a href="mis_report_form.php"><i class="bx bx-right-arrow-alt"></i>34) MIS REPORT </a>
						</li>
						<!--	<li> <a href="camales_networth_form.php"><i class="bx bx-right-arrow-alt"></i>36) CAMALES NETWORTH </a>-->
						<!--</li>-->
						<!--	<li> <a href="crar_report_form.php"><i class="bx bx-right-arrow-alt"></i>37) CRAR REPORT </a>-->
						<!--</li>-->
						<!--	<li> <a href="#"><i class="bx bx-right-arrow-alt"></i>38) Under Development </a>-->
						<!--</li>-->
						<!--	<li> <a href="#"><i class="bx bx-right-arrow-alt"></i>39) Under Development </a>-->
						<!--</li>-->
						<!--	<li> <a href="#"><i class="bx bx-right-arrow-alt"></i>40) Under Development </a>-->
						<!--</li>-->
						<!--	<li> <a href="#"><i class="bx bx-right-arrow-alt"></i>41) Under Development </a>-->
						<!--</li>-->
						<!--	<li> <a href="#"><i class="bx bx-right-arrow-alt"></i>42) Under Development </a>-->
						<!--</li>-->
						<!--	<li> <a href="#"><i class="bx bx-right-arrow-alt"></i>43) Under Development </a>-->
						<!--</li>-->
						<!--    <li> <a href="#"><i class="bx bx-right-arrow-alt"></i>44) Under Development </a>-->
						<!--</li>-->
						<!--	<li> <a href="#"><i class="bx bx-right-arrow-alt"></i>45) Under Development </a>-->
						<!--</li>-->
						<!--	<li> <a href="#"><i class="bx bx-right-arrow-alt"></i>46) Under Development </a>-->
						<!--</li>-->
					
					</ul>
				</li>
				   	<li>
					<a class="has-arrow" href="javascript:;">
						<div class="parent-icon"><i class="bx bx-line-chart"></i>
						</div>
						<div class="menu-title">Rule Book</div> 
					</a>
					<ul>
						<li> <a href="crar_report.php"><i class="bx bx-right-arrow-alt"></i> CRAR Report</a>
						</li>
						
					
					</ul>
				</li>
			
			 	<li>
					<a href="logout.php">
						<div class="parent-icon"><i class='bx bx-log-out-circle'></i>
						</div>
						<div class="menu-title">Logout</div>
					</a>
					
				</li>
				
		
			</ul>
			<!--end navigation-->
		</div>
		<!--end sidebar wrapper -->
		<!--start header -->
		<header>
			<div class="topbar d-flex align-items-center">
				<nav class="navbar navbar-expand">
					<div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
					</div>
				
					<div class="top-menu ms-auto">
						<ul class="navbar-nav align-items-center">
							<li class="nav-item mobile-search-icon">
								<!-- <a class="nav-link" href="#">	<i class='bx bx-search'></i>
								</a> -->
							</li>
							<li class="nav-item dropdown dropdown-large">
							<!-- 	<a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">	<i class='bx bx-category'></i>
								</a> -->
								<!--<div class="dropdown-menu dropdown-menu-end">-->
								<!--	<div class="row row-cols-3 g-3 p-3">-->
								<!--		<div class="col text-center">-->
								<!--			<div class="app-box mx-auto bg-gradient-cosmic text-white"><i class='bx bx-group'></i>-->
								<!--			</div>-->
								<!--			<div class="app-title">Teams</div>-->
								<!--		</div>-->
								<!--		<div class="col text-center">-->
								<!--			<div class="app-box mx-auto bg-gradient-burning text-white"><i class='bx bx-atom'></i>-->
								<!--			</div>-->
								<!--			<div class="app-title">Projects</div>-->
								<!--		</div>-->
								<!--		<div class="col text-center">-->
								<!--			<div class="app-box mx-auto bg-gradient-lush text-white"><i class='bx bx-shield'></i>-->
								<!--			</div>-->
								<!--			<div class="app-title">Tasks</div>-->
								<!--		</div>-->
								<!--		<div class="col text-center">-->
								<!--			<div class="app-box mx-auto bg-gradient-kyoto text-dark"><i class='bx bx-notification'></i>-->
								<!--			</div>-->
								<!--			<div class="app-title">Feeds</div>-->
								<!--		</div>-->
								<!--		<div class="col text-center">-->
								<!--			<div class="app-box mx-auto bg-gradient-blues text-dark"><i class='bx bx-file'></i>-->
								<!--			</div>-->
								<!--			<div class="app-title">Files</div>-->
								<!--		</div>-->
								<!--		<div class="col text-center">-->
								<!--			<div class="app-box mx-auto bg-gradient-moonlit text-white"><i class='bx bx-filter-alt'></i>-->
								<!--			</div>-->
								<!--			<div class="app-title">Alerts</div>-->
								<!--		</div>-->
								<!--	</div>-->
								<!--</div>-->
							</li>
							<li class="nav-item dropdown dropdown-large">
								<!-- <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span class="alert-count">7</span>
									<i class='bx bx-bell'></i>
								</a> -->
								<div class="dropdown-menu dropdown-menu-end">
									<a href="javascript:;">
										<div class="msg-header">
											<p class="msg-header-title">Notifications</p>
											<p class="msg-header-clear ms-auto">Marks all as read</p>
										</div>
									</a>
									<div class="header-notifications-list">
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="notify bg-light-primary text-primary"><i class="bx bx-group"></i>
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">New Customers<span class="msg-time float-end">14 Sec
												ago</span></h6>
													<p class="msg-info">5 new user registered</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="notify bg-light-danger text-danger"><i class="bx bx-cart-alt"></i>
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">New Orders <span class="msg-time float-end">2 min
												ago</span></h6>
													<p class="msg-info">You have recived new orders</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="notify bg-light-success text-success"><i class="bx bx-file"></i>
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">24 PDF File<span class="msg-time float-end">19 min
												ago</span></h6>
													<p class="msg-info">The pdf files generated</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="notify bg-light-warning text-warning"><i class="bx bx-send"></i>
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">Time Response <span class="msg-time float-end">28 min
												ago</span></h6>
													<p class="msg-info">5.1 min avarage time response</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="notify bg-light-info text-info"><i class="bx bx-home-circle"></i>
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">New Product Approved <span
												class="msg-time float-end">2 hrs ago</span></h6>
													<p class="msg-info">Your new product has approved</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="notify bg-light-danger text-danger"><i class="bx bx-message-detail"></i>
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">New Comments <span class="msg-time float-end">4 hrs
												ago</span></h6>
													<p class="msg-info">New customer comments recived</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="notify bg-light-success text-success"><i class='bx bx-check-square'></i>
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">Your item is shipped <span class="msg-time float-end">5 hrs
												ago</span></h6>
													<p class="msg-info">Successfully shipped your item</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="notify bg-light-primary text-primary"><i class='bx bx-user-pin'></i>
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">New 24 authors<span class="msg-time float-end">1 day
												ago</span></h6>
													<p class="msg-info">24 new authors joined last week</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="notify bg-light-warning text-warning"><i class='bx bx-door-open'></i>
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">Defense Alerts <span class="msg-time float-end">2 weeks
												ago</span></h6>
													<p class="msg-info">45% less alerts last 4 weeks</p>
												</div>
											</div>
										</a>
									</div>
									<a href="javascript:;">
										<div class="text-center msg-footer">View All Notifications</div>
									</a>
								</div>
							</li>
						<li class="nav-item dropdown dropdown-large">
								<!-- <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> <span class="alert-count">8</span>
									<i class='bx bx-comment'></i>
								</a> -->
								<div class="dropdown-menu dropdown-menu-end">
									<a href="javascript:;">
										<div class="msg-header">
											<p class="msg-header-title">Messages</p>
											<p class="msg-header-clear ms-auto">Marks all as read</p>
										</div>
									</a>
									<div class="header-message-list">
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="user-online">
													<img src="siddhi.png" class="msg-avatar" alt="user avatar">
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">Daisy Anderson <span class="msg-time float-end">5 sec
												ago</span></h6>
													<p class="msg-info">The standard chunk of lorem</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="user-online">
													<img src="assets/images/avatars/avatar-2.png" class="msg-avatar" alt="user avatar">
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">Althea Cabardo <span class="msg-time float-end">14
												sec ago</span></h6>
													<p class="msg-info">Many desktop publishing packages</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="user-online">
													<img src="assets/images/avatars/avatar-3.png" class="msg-avatar" alt="user avatar">
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">Oscar Garner <span class="msg-time float-end">8 min
												ago</span></h6>
													<p class="msg-info">Various versions have evolved over</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="user-online">
													<img src="assets/images/avatars/avatar-4.png" class="msg-avatar" alt="user avatar">
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">Katherine Pechon <span class="msg-time float-end">15
												min ago</span></h6>
													<p class="msg-info">Making this the first true generator</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="user-online">
													<img src="assets/images/avatars/avatar-5.png" class="msg-avatar" alt="user avatar">
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">Amelia Doe <span class="msg-time float-end">22 min
												ago</span></h6>
													<p class="msg-info">Duis aute irure dolor in reprehenderit</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="user-online">
													<img src="assets/images/avatars/avatar-6.png" class="msg-avatar" alt="user avatar">
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">Cristina Jhons <span class="msg-time float-end">2 hrs
												ago</span></h6>
													<p class="msg-info">The passage is attributed to an unknown</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="user-online">
													<img src="assets/images/avatars/avatar-7.png" class="msg-avatar" alt="user avatar">
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">James Caviness <span class="msg-time float-end">4 hrs
												ago</span></h6>
													<p class="msg-info">The point of using Lorem</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="user-online">
													<img src="assets/images/avatars/avatar-8.png" class="msg-avatar" alt="user avatar">
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">Peter Costanzo <span class="msg-time float-end">6 hrs
												ago</span></h6>
													<p class="msg-info">It was popularised in the 1960s</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="user-online">
													<img src="assets/images/avatars/avatar-9.png" class="msg-avatar" alt="user avatar">
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">David Buckley <span class="msg-time float-end">2 hrs
												ago</span></h6>
													<p class="msg-info">Various versions have evolved over</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="user-online">
													<img src="assets/images/avatars/avatar-10.png" class="msg-avatar" alt="user avatar">
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">Thomas Wheeler <span class="msg-time float-end">2 days
												ago</span></h6>
													<p class="msg-info">If you are going to use a passage</p>
												</div>
											</div>
										</a>
										<a class="dropdown-item" href="javascript:;">
											<div class="d-flex align-items-center">
												<div class="user-online">
													<img src="assets/images/avatars/avatar-11.png" class="msg-avatar" alt="user avatar">
												</div>
												<div class="flex-grow-1">
													<h6 class="msg-name">Johnny Seitz <span class="msg-time float-end">5 days
												ago</span></h6>
													<p class="msg-info">All the Lorem Ipsum generators</p>
												</div>
											</div>
										</a>
									</div>
									<a href="javascript:;">
										<div class="text-center msg-footer">View All Messages</div>
									</a>
								</div>
							</li>
						</ul>
					</div>
					<div class="user-box dropdown">
						<a class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							<!--<img src="logo.png" class="user-img" alt="user avatar" style="width:100px;">-->
							<div class="user-info ps-3">
								<p class="user-name mb-0"></p>
													</div>
						</a>
						<!--<ul class="dropdown-menu dropdown-menu-end">-->
						<!--	<li><a class="dropdown-item" href="javascript:;"><i class="bx bx-user"></i><span>Profile</span></a>-->
						<!--	</li>-->
						<!--	<li><a class="dropdown-item" href="javascript:;"><i class="bx bx-cog"></i><span>Settings</span></a>-->
						<!--	</li>-->
						<!--	<li><a class="dropdown-item" href="javascript:;"><i class='bx bx-home-circle'></i><span>Dashboard</span></a>-->
						<!--	</li>-->
						<!--	<li><a class="dropdown-item" href="javascript:;"><i class='bx bx-dollar-circle'></i><span>Earnings</span></a>-->
						<!--	</li>-->
						<!--	<li><a class="dropdown-item" href="javascript:;"><i class='bx bx-download'></i><span>Downloads</span></a>-->
						<!--	</li>-->
						<!--	<li>-->
						<!--		<div class="dropdown-divider mb-0"></div>-->
						<!--	</li>-->
						<!--	<li><a class="dropdown-item" href="javascript:;"><i class='bx bx-log-out-circle'></i><span>Logout</span></a>-->
						<!--	</li>-->
						<!--</ul>-->
					</div>
				</nav>
			</div>
		</header>