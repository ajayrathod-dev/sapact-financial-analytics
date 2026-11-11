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
        <form action="89_abr_alr_action.php" class="row g-3" method="get" enctype="multipart/form-data">
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

    <!-- ABR & ALR Data Table -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Deposit</th>
                <th>Loan</th>
                <th>Average Deposit</th>
                <th>Average Loan</th>
                <th>Monthly Avg Deposit</th>
                <th>Monthly Avg Loan</th>
                <th>Interest Paid</th>
                <th>Interest Received</th>
                <th>ABR (%)</th>
                <th>ALR (%)</th>
                <th>Spread</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM abr_alr_89 ORDER BY id DESC LIMIT 500";
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
                echo "<td>" . number_format($row['deposit'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['loan'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['average_deposit'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['average_loan'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['monthly_avg_deposit'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['monthly_avg_loan'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['interest_paid'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['interest_received'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['abr'] ?? 0, 2) . "%</td>";
                echo "<td>" . number_format($row['alr'] ?? 0, 2) . "%</td>";
                echo "<td>" . number_format($row['spread'] ?? 0, 2) . "</td>";
                echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                echo "</tr>";
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