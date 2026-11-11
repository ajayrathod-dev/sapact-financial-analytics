<?php
include 'header.php';
date_default_timezone_set('Asia/Kolkata');
$currentDateTime = date('Y-m-d H:i:s');

$bank_name = $_GET['bank_name'] ?? '';
$month     = $_GET['month'] ?? '';
?>

<div class="page-wrapper">
  <div class="page-content">
    <div class="card">
      <div class="card-body">
        <form action="weak_society_action.php" class="row g-3" method="get" enctype="multipart/form-data">
          <div class="col-md-6">
            <label for="inputBankName" class="form-label">Bank Name</label>
            <select class="form-control" id="inputBankName" name="bank_name" required>
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
            <label for="inputMonth" class="form-label">Year & Month</label>
            <input type="month" name="month" class="form-control" value="<?php echo $month; ?>" required>
          </div>
          <div class="col-12" align="center">
            <button type="submit" class="btn btn-outline-primary px-5">Calculate Weak Society Status</button>
          </div>
        </form>
      </div>
    </div>    

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>CRR %</th>
                <th>CRR Status</th>
                <th>SLR %</th>
                <th>SLR Status</th>
                <th>CRAR %</th>
                <th>CRAR Status</th>
                <th>CD Ratio %</th>
                <th>Gross NPA %</th>
                <th>Gross NPA Status</th>
                <th>Net NPA %</th>
                <th>Net NPA Status</th>
                <th>Overdue %</th>
                <th>Profit/Loss</th>
                <th>P/L Status</th>
                <th>Total Score</th>
                <th>Result</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM weak_society ORDER BY id DESC LIMIT 500";
              $result = mysqli_query($link, $sql);
              $sr = 1;
              while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $sr++ . "</td>"; 
                
                // Bank Name
                echo "<td>";
                $bname = $row['bank_name'];
                $sql120 = "SELECT * FROM society_info WHERE id='$bname' ORDER BY id ASC";
                $result120 = mysqli_query($link, $sql120);
                while ($row120 = mysqli_fetch_assoc($result120)) {
                  echo $row120['society_name']; 
                }
                echo "</td>";
                
                // Other columns
                echo "<td>" . htmlspecialchars($row['month_year']) . "</td>";
                echo "<td>" . htmlspecialchars($row['crr_percentage']) . "</td>";
                echo "<td>" . ($row['crr_status'] == 1 ? 'Green' : 'Red') . "</td>";
                echo "<td>" . htmlspecialchars($row['slr_percentage']) . "</td>";
                echo "<td>" . ($row['slr_status'] == 1 ? 'Green' : 'Red') . "</td>";
                echo "<td>" . htmlspecialchars($row['crar_percentage']) . "</td>";
                echo "<td>" . ($row['crar_status'] == 1 ? 'Green' : 'Red') . "</td>";
                echo "<td>" . htmlspecialchars($row['cd_ratio_percentage']) . "</td>";
                echo "<td>" . htmlspecialchars($row['gross_npa_percentage']) . "</td>";
                echo "<td>" . ($row['gross_npa_status'] == 1 ? 'Green' : 'Red') . "</td>";
                echo "<td>" . htmlspecialchars($row['net_npa_percentage']) . "</td>";
                echo "<td>" . ($row['net_npa_status'] == 1 ? 'Green' : 'Red') . "</td>";
                echo "<td>" . htmlspecialchars($row['overdue_percentage']) . "</td>";
                echo "<td>" . htmlspecialchars($row['profit_loss']) . "</td>";
                echo "<td>" . ($row['profit_loss_status'] == 1 ? 'Green' : 'Red') . "</td>";
                echo "<td>" . htmlspecialchars($row['total_score']) . "</td>";
                echo "<td>" . htmlspecialchars($row['result']) . "</td>";
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