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
        <form action="abr_alr_action.php" class="row g-3" method="get" enctype="multipart/form-data">
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

    <!-- Main ABR & ALR Table -->
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
                <th>ABR</th>
                <th>ALR</th>
                <th>Spread</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM abr_alr ORDER BY id DESC LIMIT 500";
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
                echo "<td>" . number_format($row['spread'] ?? 0, 2) . "%</td>";
                echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                echo "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Deposit Types Table -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Deposit Types Breakdown</h5>
        <div class="table-responsive">
          <table id="example3" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Type</th>
                <th>April</th>
                <th>May</th>
                <th>June</th>
                <th>July</th>
                <th>August</th>
                <th>September</th>
                <th>October</th>
                <th>November</th>
                <th>December</th>
                <th>January</th>
                <th>February</th>
                <th>March</th>
                <th>Total</th>
                <th>Monthly Avg</th>
                <th>Interest</th>
                <th>Interest %</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_deposit = "SELECT * FROM deposit_types ORDER BY id DESC LIMIT 500";
              $result_deposit = mysqli_query($link, $sql_deposit);
              $sr_deposit = 1;
              
              if ($result_deposit && mysqli_num_rows($result_deposit) > 0) {
                while ($row_deposit = mysqli_fetch_assoc($result_deposit)) {
                  echo "<tr>";
                  echo "<td>" . $sr_deposit++ . "</td>"; 
                  ?>
                  <td>
                    <?php 
                    $bname_deposit = $row_deposit['bank_name'];
                    $sql120_deposit = "SELECT * FROM society_info WHERE id='$bname_deposit' ORDER BY id ASC";
                    $result120_deposit = mysqli_query($link, $sql120_deposit);
                    while ($row120_deposit = mysqli_fetch_assoc($result120_deposit)) {
                      echo $row120_deposit['society_name']; 
                    }
                    ?>
                  </td>
                  <?php
                  echo "<td>" . htmlspecialchars($row_deposit['month_year']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_deposit['type_name']) . "</td>";
                  echo "<td>" . number_format($row_deposit['april'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_deposit['may'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_deposit['june'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_deposit['july'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_deposit['august'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_deposit['september'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_deposit['october'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_deposit['november'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_deposit['december'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_deposit['january'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_deposit['february'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_deposit['march'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_deposit['total'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_deposit['monthly_avg'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_deposit['interest'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_deposit['interest_percentage'] ?? 0, 2) . "%</td>";
                  echo "<td>" . htmlspecialchars($row_deposit['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='21' class='text-center'>No deposit type data available</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Loan Types Table -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Loan Types Breakdown</h5>
        <div class="table-responsive">
          <table id="example4" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Type</th>
                <th>April</th>
                <th>May</th>
                <th>June</th>
                <th>July</th>
                <th>August</th>
                <th>September</th>
                <th>October</th>
                <th>November</th>
                <th>December</th>
                <th>January</th>
                <th>February</th>
                <th>March</th>
                <th>Total</th>
                <th>Monthly Avg</th>
                <th>Interest</th>
                <th>Interest %</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_loan = "SELECT * FROM loan_types ORDER BY id DESC LIMIT 500";
              $result_loan = mysqli_query($link, $sql_loan);
              $sr_loan = 1;
              
              if ($result_loan && mysqli_num_rows($result_loan) > 0) {
                while ($row_loan = mysqli_fetch_assoc($result_loan)) {
                  echo "<tr>";
                  echo "<td>" . $sr_loan++ . "</td>"; 
                  ?>
                  <td>
                    <?php 
                    $bname_loan = $row_loan['bank_name'];
                    $sql120_loan = "SELECT * FROM society_info WHERE id='$bname_loan' ORDER BY id ASC";
                    $result120_loan = mysqli_query($link, $sql120_loan);
                    while ($row120_loan = mysqli_fetch_assoc($result120_loan)) {
                      echo $row120_loan['society_name']; 
                    }
                    ?>
                  </td>
                  <?php
                  echo "<td>" . htmlspecialchars($row_loan['month_year']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_loan['type_name']) . "</td>";
                  echo "<td>" . number_format($row_loan['april'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_loan['may'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_loan['june'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_loan['july'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_loan['august'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_loan['september'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_loan['october'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_loan['november'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_loan['december'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_loan['january'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_loan['february'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_loan['march'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_loan['total'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_loan['monthly_avg'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_loan['interest'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_loan['interest_percentage'] ?? 0, 2) . "%</td>";
                  echo "<td>" . htmlspecialchars($row_loan['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='21' class='text-center'>No loan type data available</td></tr>";
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