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
        <form action="wc_profit_op_me_action.php" class="row g-3" method="get" enctype="multipart/form-data">
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
            <button type="submit" class="btn btn-outline-primary px-5">Calculate & Save Data</button>
          </div>
        </form>
      </div>
    </div>    

    <!-- First Table: Working Capital & Profit Data -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Working Capital</th>
                <th>Previous WC</th>
                <th>Average Working Capital</th>
                <th>Total Deposit</th>
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
              $sql = "SELECT * FROM wc_profit_op_me ORDER BY id DESC LIMIT 500";
              $result = mysqli_query($link, $sql);
              $sr = 1;
              while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $sr++ . "</td>"; 
                ?>
                <td>
                  <?php 
                  $bname = $row['bank_name'];
                  $sql120 = "SELECT * FROM society_info WHERE id='$bname' ORDER BY id ASC";
                  $result120 = mysqli_query($link, $sql120);
                  while ($row120 = mysqli_fetch_assoc($result120)) {
                    echo $row120['society_name']; 
                  }
                  ?>
                </td>
                <?php
                echo "<td>" . htmlspecialchars($row['month_year']) . "</td>";
                echo "<td>" . number_format($row['working_capital'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['previous_wc'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['avg_working_capital'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['total_deposit'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['profit'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['operating_profit'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['profit_percentage'] ?? 0, 2) . "%</td>";
                echo "<td>" . number_format($row['operating_profit_percentage'] ?? 0, 2) . "%</td>";
                echo "<td>" . number_format($row['management_expense'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['management_expense_percentage'] ?? 0, 2) . "%</td>";
                echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                echo "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Second Table: Profit Split Data -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="example3" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Particular</th>
                <th>Amount</th>
                <th>Percentage</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_profit = "SELECT * FROM profit_split ORDER BY id DESC LIMIT 500";
              $result_profit = mysqli_query($link, $sql_profit);
              $sr_profit = 1;
              
              if ($result_profit && mysqli_num_rows($result_profit) > 0) {
                while ($row_profit = mysqli_fetch_assoc($result_profit)) {
                  echo "<tr>";
                  echo "<td>" . $sr_profit++ . "</td>"; 
                  ?>
                  <td>
                    <?php 
                    $bname_profit = $row_profit['bank_name'];
                    $sql120_profit = "SELECT * FROM society_info WHERE id='$bname_profit' ORDER BY id ASC";
                    $result120_profit = mysqli_query($link, $sql120_profit);
                    while ($row120_profit = mysqli_fetch_assoc($result120_profit)) {
                      echo $row120_profit['society_name']; 
                    }
                    ?>
                  </td>
                  <?php
                  echo "<td>" . htmlspecialchars($row_profit['month_year']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_profit['particular']) . "</td>";
                  echo "<td>" . number_format($row_profit['amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_profit['percentage'] ?? 0, 2) . "%</td>";
                  echo "<td>" . htmlspecialchars($row_profit['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='7' class='text-center'>No profit split data available</td></tr>";
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