<?php
include 'header.php';
date_default_timezone_set('Asia/Kolkata');
$currentDateTime = date('Y-m-d H:i:s');

$bank_name = $_GET['bank_name'] ?? '';
$month     = $_GET['month'] ?? '';
?>

<div class="page-wrapper">
  <div class="page-content">
    <!-- Form Section -->
    <div class="card">
      <div class="card-body">
        <form action="otrr_monthly_action.php" class="row g-3" method="get" enctype="multipart/form-data">
          <div class="col-md-6">
            <label for="inputFirstName" class="form-label">Bank Name</label>
            <select class="form-control" id="inputFirstName" name="bank_name" required>
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
            <label for="inputFirstName" class="form-label">Month & Year</label>
            <input type="month" name="month" class="form-control" value="<?php echo $month; ?>" required>
          </div>
          <div class="col-12" align="center">
            <button type="submit" class="btn btn-outline-primary px-5">Calculate & Save OTRR Data</button>
          </div>
        </form>
      </div>
    </div>    

    <!-- OTRR Report Table -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Week</th>
                <th>Days</th>
                <th>Date</th>
                <th>Total Demand A/c</th>
                <th>Total Demand Amount</th>
                <th>Total Recovery A/c</th>
                <th>Actually Received Amount</th>
                <th>Diff A/c</th>
                <th>Actually Diff Amount</th>
                <th>OTRR %</th>
                <th>Missing Account OTRR %</th>
                <th>Created Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT otrr.*, si.society_name 
                      FROM otrr_monthly_report otrr 
                      LEFT JOIN society_info si ON otrr.bank_name = si.id 
                      WHERE otrr.bank_name = '$bank_name' AND otrr.month_year = '$month'
                      ORDER BY 
                        CASE 
                          WHEN otrr.is_grand_total = 1 THEN 3
                          WHEN otrr.is_weekly_total = 1 THEN 2
                          ELSE 1
                        END,
                        otrr.week_number,
                        otrr.id";
              $result = mysqli_query($link, $sql);
              $sr = 1;
              
              if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                  $rowClass = '';
                  if ($row['is_grand_total'] == 1) {
                    $rowClass = 'table-success fw-bold';
                  } elseif ($row['is_weekly_total'] == 1) {
                    $rowClass = 'table-info fw-bold';
                  }
                  
                  echo "<tr class='$rowClass'>";
                  echo "<td>" . $sr++ . "</td>"; 
                  echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                  echo "<td>" . htmlspecialchars($row['month_year']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['week_number']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['days']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['report_date']) . "</td>";
                  echo "<td>" . number_format($row['total_demand_accounts'] ?? 0, 0) . "</td>";
                  echo "<td>" . number_format($row['total_demand_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['total_recovery_accounts'] ?? 0, 0) . "</td>";
                  echo "<td>" . number_format($row['actually_received_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['diff_accounts'] ?? 0, 0) . "</td>";
                  echo "<td>" . number_format($row['actually_diff_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['otrr_percentage'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row['missing_account_otrr'] ?? 0, 2) . "%</td>";
                  echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='15' class='text-center'>No OTRR data available. Please calculate data first.</td></tr>";
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