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
        <form action="sec_144_action.php" class="row g-3" method="GET">
          <div class="col-md-6">
            <label for="bank_name" class="form-label">Bank Name</label>
            <select class="form-control" id="bank_name" name="bank_name" required>
              <option value="">--Select Bank--</option>
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
            <label for="month" class="form-label">Month & Year</label>
            <input type="month" id="month" name="month" class="form-control" value="<?php echo $month; ?>" required>
          </div>
          <div class="col-12" align="center">
            <button type="submit" class="btn btn-outline-primary px-5">Calculate & Save Data</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Success Message -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!</strong> Data calculated and saved successfully.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Main Data Table -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Section 144 - Working Capital, Profit & Management Expense</h5>
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Working Capital</th>
                <th>Add Previous WC</th>
                <th>Average WC</th>
                <th>Profit</th>
                <th>Operating Profit</th>
                <th>Profit %</th>
                <th>Operating Profit %</th>
                <th>Management Expense</th>
                <th>Management Expense %</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT s.*, si.society_name 
                      FROM section_144_complete s 
                      LEFT JOIN society_info si ON s.bank_name = si.id 
                      ORDER BY s.id DESC LIMIT 500";
              $result = mysqli_query($link, $sql);
              $sr = 1;
              if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr>";
                  echo "<td>" . $sr++ . "</td>"; 
                  echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                  echo "<td>" . htmlspecialchars($row['month_year'] ?? '') . "</td>";
                  echo "<td>" . number_format($row['working_capital'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['add_previous_wc'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['avg_working_capital'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['profit'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['operating_profit'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['profit_percentage'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row['operating_profit_percentage'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row['management_expense'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['management_expense_percentage'] ?? 0, 2) . "%</td>";
                  echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='13' class='text-center'>No data found</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Profit Split Table -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Profit Split Details</h5>
        <div class="table-responsive">
          <table id="example3" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Reserve Fund</th>
                <th>Bad Debt</th>
                <th>Samajik Fund</th>
                <th>Education Fund</th>
                <th>Dividend Fluctuation</th>
                <th>Training Fund</th>
                <th>Building Fund</th>
                <th>Net Profit</th>
                <th>Total Profit Split</th>
                <th>Total Actual Profit</th>
                <th>Profit Split %</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_profit = "SELECT s.*, si.society_name 
                            FROM section_144_complete s 
                            LEFT JOIN society_info si ON s.bank_name = si.id 
                            ORDER BY s.id DESC LIMIT 500";
              $result_profit = mysqli_query($link, $sql_profit);
              $sr_profit = 1;
              if ($result_profit && mysqli_num_rows($result_profit) > 0) {
                while ($row_profit = mysqli_fetch_assoc($result_profit)) {
                  echo "<tr>";
                  echo "<td>" . $sr_profit++ . "</td>"; 
                  echo "<td>" . htmlspecialchars($row_profit['society_name'] ?? 'N/A') . "</td>";
                  echo "<td>" . htmlspecialchars($row_profit['month_year'] ?? '') . "</td>";
                  echo "<td>" . number_format($row_profit['reserve_fund'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_profit['bad_debt'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_profit['samajik_fund'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_profit['education_fund'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_profit['dividend_fluctuation_fund'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_profit['training_fund'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_profit['building_fund'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_profit['net_profit'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_profit['total_profit_split'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_profit['total_actual_profit'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_profit['profit_split_percentage'] ?? 0, 2) . "%</td>";
                  echo "<td>" . htmlspecialchars($row_profit['created_at'] ?? '') . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='15' class='text-center'>No profit split data found</td></tr>";
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