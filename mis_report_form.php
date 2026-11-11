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
        <form action="mis_report.php" class="row g-3" method="get" enctype="multipart/form-data">
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
                    echo "<option value='".htmlspecialchars($usr1['id'])."' $selected>".htmlspecialchars($usr1['society_name'])."</option>";
                  }
                }
              ?>
            </select>     
          </div>
          <div class="col-md-6">
            <label for="inputFirstName" class="form-label">Month & Year</label>
            <input type="month" name="month" class="form-control" value="<?php echo htmlspecialchars($month); ?>" required>
          </div>
          <div class="col-12" align="center">
            <button type="submit" class="btn btn-outline-primary px-5">Calculate & Save MIS Data</button>
          </div>
        </form>
      </div>
    </div>    

    <!-- MIS Report Data Table -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Share Capital</th>
                <th>Reserve Fund</th>
                <th>Building Fund</th>
                <th>Dividend Fund</th>
                <th>Investment Fund</th>
                <th>Bad Debt</th>
                <th>Current Profit</th>
                <th>Total Loss</th>
                <th>Total Profit</th>
                <th>Networth</th>
                <!--<th>Created By</th>-->
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM networth_camales ORDER BY id DESC LIMIT 500";
              $result = mysqli_query($link, $sql);
              $sr = 1;
              while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $sr++ . "</td>"; 
                ?>
                <td>
                  <?php 
                  $bname = $row['bank_name'];
                  $sql120 = "SELECT * FROM society_info WHERE id = ?";
                  $stmt120 = $link->prepare($sql120);
                  $stmt120->bind_param("s", $bname);
                  $stmt120->execute();
                  $result120 = $stmt120->get_result();
                  while ($row120 = mysqli_fetch_assoc($result120)) {
                    echo htmlspecialchars($row120['society_name']); 
                  }
                  ?>
                </td>
                <?php
                echo "<td>" . htmlspecialchars($row['month_year']) . "</td>";
                echo "<td>" . number_format($row['share_capital'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['reserve_fund'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['building_fund'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['dividand_fund'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['investment_fund'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['bad_dbt'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['current_profit'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['total_loss'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['total_profit'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['networth'] ?? 0, 2) . "</td>";
                
                // Display created by user
                // echo "<td>";
                // $created_by = $row['by'];
                // $sql_user = "SELECT * FROM users WHERE id = ?";
                // $stmt_user = $link->prepare($sql_user);
                // $stmt_user->bind_param("s", $created_by);
                // $stmt_user->execute();
                // $result_user = $stmt_user->get_result();
                // if ($result_user && $user_row = $result_user->fetch_assoc()) {
                //   echo htmlspecialchars($user_row['name'] ?? $user_row['username'] ?? 'Unknown');
                // } else {
                //   echo 'Unknown';
                // }
                // echo "</td>";
                
                echo "<td>" . htmlspecialchars($row['created_at'] ?? 'N/A') . "</td>";
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