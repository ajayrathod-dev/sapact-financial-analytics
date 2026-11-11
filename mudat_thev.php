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
        <form action="mudat_thev_action.php" class="row g-3" method="get" enctype="multipart/form-data">
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

    <!-- Main Table: Mudat Thev Deposit Data -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Particular</th>
                <th>Account No</th>
                <th>Amount</th>
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
                <th>Total Deposit</th>
                <th>Average Deposit</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM mudat_thev ORDER BY id DESC LIMIT 500";
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
                echo "<td>" . htmlspecialchars($row['particular']) . "</td>";
                echo "<td>" . htmlspecialchars($row['account_no'] ?? '') . "</td>";
                echo "<td>" . number_format($row['amount'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['april'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['may'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['june'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['july'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['august'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['september'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['october'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['november'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['december'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['january'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['february'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['march'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['total_deposit'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['avg_deposit'] ?? 0, 2) . "</td>";
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