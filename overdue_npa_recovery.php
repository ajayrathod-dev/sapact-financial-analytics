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
        <form action="overdue_npa_recovery_action.php" class="row g-3" method="get" enctype="multipart/form-data">
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

    <!-- Main Overdue & NPA Recovery Table -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Total Overdue</th>
                <th>Total Loan</th>
                <th>Overdue %</th>
                <th>March NPA Amount</th>
                <th>Recovery Amount</th>
                <th>Recovery %</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM overdue_npa_recovery ORDER BY id DESC LIMIT 500";
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
                echo "<td>" . number_format($row['total_overdue'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['total_loan'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['overdue_percentage'] ?? 0, 2) . "%</td>";
                echo "<td>" . number_format($row['march_npa_amount'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['recovery_amount'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['recovery_percentage'] ?? 0, 2) . "%</td>";
                echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                echo "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Overdue Loan Types Breakdown -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Overdue Loan Types Breakdown</h5>
        <div class="table-responsive">
          <table id="example3" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Loan Type</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_loans = "SELECT * FROM overdue_loan_types ORDER BY id DESC LIMIT 500";
              $result_loans = mysqli_query($link, $sql_loans);
              $sr_loans = 1;
              
              if ($result_loans && mysqli_num_rows($result_loans) > 0) {
                while ($row_loans = mysqli_fetch_assoc($result_loans)) {
                  echo "<tr>";
                  echo "<td>" . $sr_loans++ . "</td>"; 
                  ?>
                  <td>
                    <?php 
                    $bname_loans = $row_loans['bank_name'];
                    $sql120_loans = "SELECT * FROM society_info WHERE id='$bname_loans' ORDER BY id ASC";
                    $result120_loans = mysqli_query($link, $sql120_loans);
                    while ($row120_loans = mysqli_fetch_assoc($result120_loans)) {
                      echo $row120_loans['society_name']; 
                    }
                    ?>
                  </td>
                  <?php
                  echo "<td>" . htmlspecialchars($row_loans['month_year']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_loans['loan_type']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_loans['loan_description']) . "</td>";
                  echo "<td>" . number_format($row_loans['amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_loans['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='7' class='text-center'>No overdue loan data available</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- NPA Recovery Details -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">NPA Recovery Details</h5>
        <div class="table-responsive">
          <table id="example4" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Period</th>
                <th>Recovery Amount</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_recovery = "SELECT * FROM npa_recovery_details ORDER BY id DESC LIMIT 500";
              $result_recovery = mysqli_query($link, $sql_recovery);
              $sr_recovery = 1;
              
              if ($result_recovery && mysqli_num_rows($result_recovery) > 0) {
                while ($row_recovery = mysqli_fetch_assoc($result_recovery)) {
                  echo "<tr>";
                  echo "<td>" . $sr_recovery++ . "</td>"; 
                  ?>
                  <td>
                    <?php 
                    $bname_recovery = $row_recovery['bank_name'];
                    $sql120_recovery = "SELECT * FROM society_info WHERE id='$bname_recovery' ORDER BY id ASC";
                    $result120_recovery = mysqli_query($link, $sql120_recovery);
                    while ($row120_recovery = mysqli_fetch_assoc($result120_recovery)) {
                      echo $row120_recovery['society_name']; 
                    }
                    ?>
                  </td>
                  <?php
                  echo "<td>" . htmlspecialchars($row_recovery['month_year']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_recovery['period']) . "</td>";
                  echo "<td>" . number_format($row_recovery['recovery_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_recovery['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='6' class='text-center'>No NPA recovery data available</td></tr>";
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