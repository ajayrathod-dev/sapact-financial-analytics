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
        <form action="loan_exp_action.php" class="row g-3" method="get" enctype="multipart/form-data">
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

    <!-- First Table: Secured Loans -->
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Secured Loans</h4>
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Particular</th>
                <th>Secured Amount</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_secured = "SELECT * FROM loan_exposure_limit WHERE secured_amount > 0 ORDER BY id DESC LIMIT 500";
              $result_secured = mysqli_query($link, $sql_secured);
              $sr_secured = 1;
              while ($row_secured = mysqli_fetch_assoc($result_secured)) {
                echo "<tr>";
                echo "<td>" . $sr_secured++ . "</td>"; 
                ?>
                <td>
                  <?php 
                  $bname_secured = $row_secured['bank_name'];
                  $sql120_secured = "SELECT * FROM society_info WHERE id='$bname_secured' ORDER BY id ASC";
                  $result120_secured = mysqli_query($link, $sql120_secured);
                  while ($row120_secured = mysqli_fetch_assoc($result120_secured)) {
                    echo $row120_secured['society_name']; 
                  }
                  ?>
                </td>
                <?php
                echo "<td>" . htmlspecialchars($row_secured['month_year']) . "</td>";
                echo "<td>" . htmlspecialchars($row_secured['particular']) . "</td>";
                echo "<td>" . number_format($row_secured['secured_amount'] ?? 0, 2) . "</td>";
                echo "<td>" . htmlspecialchars($row_secured['created_at']) . "</td>";
                echo "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Second Table: Unsecured Loans -->
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Unsecured Loans</h4>
        <div class="table-responsive">
          <table id="example3" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Particular</th>
                <th>Unsecured Amount</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_unsecured = "SELECT * FROM loan_exposure_limit WHERE unsecured_amount > 0 ORDER BY id DESC LIMIT 500";
              $result_unsecured = mysqli_query($link, $sql_unsecured);
              $sr_unsecured = 1;
              
              if ($result_unsecured && mysqli_num_rows($result_unsecured) > 0) {
                while ($row_unsecured = mysqli_fetch_assoc($result_unsecured)) {
                  echo "<tr>";
                  echo "<td>" . $sr_unsecured++ . "</td>"; 
                  ?>
                  <td>
                    <?php 
                    $bname_unsecured = $row_unsecured['bank_name'];
                    $sql120_unsecured = "SELECT * FROM society_info WHERE id='$bname_unsecured' ORDER BY id ASC";
                    $result120_unsecured = mysqli_query($link, $sql120_unsecured);
                    while ($row120_unsecured = mysqli_fetch_assoc($result120_unsecured)) {
                      echo $row120_unsecured['society_name']; 
                    }
                    ?>
                  </td>
                  <?php
                  echo "<td>" . htmlspecialchars($row_unsecured['month_year']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_unsecured['particular']) . "</td>";
                  echo "<td>" . number_format($row_unsecured['unsecured_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_unsecured['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='6' class='text-center'>No unsecured loan data available</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Third Table: Summary Calculations -->
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Loan Exposure Summary</h4>
        <div class="table-responsive">
          <table id="example4" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Total Secured Loan</th>
                <th>Total Unsecured Loan</th>
                <th>Total Loans</th>
                <th>% Unsecured</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_summary = "SELECT 
                              bank_name,
                              month_year,
                              SUM(secured_amount) as total_secured,
                              SUM(unsecured_amount) as total_unsecured,
                              created_at
                            FROM loan_exposure_limit 
                            GROUP BY bank_name, month_year 
                            ORDER BY created_at DESC 
                            LIMIT 500";
              $result_summary = mysqli_query($link, $sql_summary);
              $sr_summary = 1;
              
              if ($result_summary && mysqli_num_rows($result_summary) > 0) {
                while ($row_summary = mysqli_fetch_assoc($result_summary)) {
                  $total_secured = $row_summary['total_secured'] ?? 0;
                  $total_unsecured = $row_summary['total_unsecured'] ?? 0;
                  $total_loans = $total_secured + $total_unsecured;
                  $unsecured_percentage = $total_loans > 0 ? ($total_unsecured / $total_loans) * 100 : 0;
                  $status = $unsecured_percentage <= 15 ? 'Within Limit' : 'Exceeded Limit';
                  $status_class = $unsecured_percentage <= 15 ? 'text-success' : 'text-danger';
                  
                  echo "<tr>";
                  echo "<td>" . $sr_summary++ . "</td>"; 
                  ?>
                  <td>
                    <?php 
                    $bname_summary = $row_summary['bank_name'];
                    $sql120_summary = "SELECT * FROM society_info WHERE id='$bname_summary' ORDER BY id ASC";
                    $result120_summary = mysqli_query($link, $sql120_summary);
                    while ($row120_summary = mysqli_fetch_assoc($result120_summary)) {
                      echo $row120_summary['society_name']; 
                    }
                    ?>
                  </td>
                  <?php
                  echo "<td>" . htmlspecialchars($row_summary['month_year']) . "</td>";
                  echo "<td>" . number_format($total_secured, 2) . "</td>";
                  echo "<td>" . number_format($total_unsecured, 2) . "</td>";
                  echo "<td>" . number_format($total_loans, 2) . "</td>";
                  echo "<td>" . number_format($unsecured_percentage, 2) . "%</td>";
                  echo "<td class='$status_class'>" . $status . "</td>";
                  echo "<td>" . htmlspecialchars($row_summary['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='9' class='text-center'>No loan exposure data available</td></tr>";
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