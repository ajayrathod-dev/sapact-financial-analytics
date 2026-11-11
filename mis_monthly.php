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
        <form action="mis_monthly_action.php" class="row g-3" method="get" enctype="multipart/form-data">
          <div class="col-md-6">
            <label for="inputBank" class="form-label">Bank Name</label>
            <select class="form-control" id="inputBank" name="bank_name" required>
              <option value="">--Select--</option>
              <?php 
                $sql1 = "SELECT * FROM society_info ORDER BY id DESC";
                $result1 = mysqli_query($link, $sql1);
                if ($result1->num_rows > 0) {
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
            <button type="submit" class="btn btn-outline-primary px-5">Generate Report</button>
          </div>
        </form>
      </div>
    </div>    

    <!-- Report Table -->
    <div class="card mt-3">
      <div class="card-body">
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>CD Ratio (%)</th>
                <th>Overdue (%)</th>
                <th>Deposits Last Month</th>
                <th>Deposits Last Quarter</th>
                <th>Cash Reserve Required</th>
                <th>Cash Reserve Maintained</th>
                <th>Cash Reserve (%)</th>
                <th>Statutory Liquid Required</th>
                <th>Statutory Liquid Maintained</th>
                <th>Statutory Liquid (%)</th>
                <th>Profit</th>
                <th>Loss</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $where_clause = "";
              if (!empty($bank_name)) {
                  $where_clause .= " WHERE bank_name = '$bank_name'";
              }
              if (!empty($month)) {
                  $where_clause .= ($where_clause ? " AND" : " WHERE") . " month_year = '$month'";
              }
              $sql = "SELECT * FROM mis_monthly_report $where_clause ORDER BY id DESC LIMIT 500";
              $result = mysqli_query($link, $sql);
              $sr = 1;
              
              if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr>";
                  echo "<td>" . $sr++ . "</td>";
                  // Get bank name from society_info
                  $bname = $row['bank_name'];
                  $sql120 = "SELECT society_name FROM society_info WHERE id='$bname' LIMIT 1";
                  $result120 = mysqli_query($link, $sql120);
                  $bank_display = ($result120 && $row120 = mysqli_fetch_assoc($result120)) 
                                  ? $row120['society_name'] 
                                  : $bname;
                  echo "<td>" . htmlspecialchars($bank_display) . "</td>";
                  echo "<td>" . htmlspecialchars($row['month_year']) . "</td>";
                  echo "<td>" . number_format($row['cd_ratio'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row['overdue_percent'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row['total_deposits_last_month'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['total_deposits_last_quarter'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['cash_reserve_required'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['cash_reserve_maintained'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['cash_reserve_percent'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row['statutory_liquid_required'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['statutory_liquid_maintained'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['statutory_liquid_percent'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row['profit'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['loss'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row['date'] ?? '') . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='16' class='text-center'>No MIS monthly data available</td></tr>";
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