<?php
include 'header.php';
date_default_timezone_set('Asia/Kolkata');
$currentDateTime = date('Y-m-d H:i:s');

$bank_name = $_GET['bank_name'] ?? '';
$month     = $_GET['month'] ?? '';
$success   = $_GET['success'] ?? '';
$error     = $_GET['error'] ?? '';
?>

<div class="page-wrapper">
  <div class="page-content">
    <!-- Display Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Form Section -->
    <div class="card">
      <div class="card-body">
        <form action="crr_incl_nagri_action.php" class="row g-3" method="get" enctype="multipart/form-data">
          <div class="col-md-6">
            <label for="inputBank" class="form-label">Bank Name</label>
            <select class="form-control" id="inputBank" name="bank_name" required>
              <option value="">--Select--</option>
              <?php 
                $sql1 = "SELECT * FROM society_info ORDER BY id DESC";
                $result1 = mysqli_query($link, $sql1);
                if ($result1 && $result1->num_rows > 0) {
                  while ($usr1 = mysqli_fetch_array($result1)) {
                    $selected = ($usr1['id'] == $bank_name) ? 'selected' : '';
                    echo "<option value='{$usr1['id']}' $selected>{$usr1['society_name']}</option>";
                  }
                }
              ?>
            </select>     
          </div>
          <div class="col-md-6">
            <label for="inputMonth" class="form-label">Year-Month</label>
            <input type="month" name="month" class="form-control" value="<?php echo htmlspecialchars($month); ?>" required>
          </div>
          <div class="col-12" align="center">
            <button type="submit" class="btn btn-outline-primary px-5">Calculate CRR Including Nagri</button>
          </div>
        </form>
      </div>
    </div>    

    <!-- Including CRR Report Table (Log of all records; filtered by selected bank if provided) -->
    <div class="card mt-3">
      <div class="card-body">
        <h5 class="card-title">CRR Report (Including Nagri) Data</h5>
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Cash In Hand</th>
                <th>Cash at Bank+OD</th>
                <th>Short Term Deposits</th>
                <th>Excess Liquid Fund</th>
                <th>Private Bank Saving/Current</th>
                <th>Total CRR Assets</th>
                <th>Last Qtr Deposit</th>
                <th>CRR (%)</th>
                <th>Min Req (%)</th>
                <th>Deficit/Surplus (%)</th>
                <th>Deficit/Surplus (Amt)</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $where_clause = '';
              if (!empty($bank_name)) {
                  $where_clause = "WHERE bank_name = '$bank_name'";
                  if (!empty($month)) {
                      $where_clause .= " AND month_year = '$month'";
                  }
              }
              $sql = "SELECT cri.*, si.society_name 
                      FROM crr_report_incl_nagri cri 
                      LEFT JOIN society_info si ON cri.bank_name = si.id 
                      $where_clause
                      ORDER BY cri.id DESC LIMIT 500";
              $result = mysqli_query($link, $sql);
              $sr_no = 1;
              
              if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr>";
                  echo "<td>" . $sr_no++ . "</td>"; 
                  echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                  echo "<td>" . htmlspecialchars($row['month_year']) . "</td>";
                  echo "<td>" . number_format($row['cash_in_hand'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['cash_at_bank_od'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['short_term_deposits'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['excess_liquid_fund_invest'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['private_bank_saving_current'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['total_crr_assets'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['last_qtr_deposit_total'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['crr_percent'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row['min_req_percent'] ?? 1, 2) . "%</td>";
                  echo "<td>" . number_format($row['deficit_surplus_percent'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row['deficit_surplus_amt'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row['date'] ?? 'N/A') . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='15' class='text-center'>No CRR Including Nagri report data available</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include('footer.php'); ?>