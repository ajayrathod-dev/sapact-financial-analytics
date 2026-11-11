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
        <form action="share_fund_action.php" class="row g-3" method="get" enctype="multipart/form-data">
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
            <button type="submit" class="btn btn-outline-primary px-5">Calculate & Save Share & Funds Data</button>
          </div>
        </form>
      </div>
    </div>    

    <!-- Share & Funds Data Table -->
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
                <th>April Amount</th>
                <th>May Amount</th>
                <th>June Amount</th>
                <th>June Inc/Dec</th>
                <th>July Amount</th>
                <th>August Amount</th>
                <th>September Amount</th>
                <th>Sep Inc/Dec</th>
                <th>October Amount</th>
                <th>November Amount</th>
                <th>December Amount</th>
                <th>Dec Inc/Dec</th>
                <th>January Amount</th>
                <th>February Amount</th>
                <th>March Amount</th>
                <th>Mar Inc/Dec</th>
                <th>Total Shares</th>
                <th>Average Shares</th>
                <th>Interest Paid</th>
                <th>Interest Rate</th>
                <th>Percentage</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT sf.*, si.society_name 
                      FROM share_funds sf 
                      LEFT JOIN society_info si ON sf.bank_name = si.id 
                      ORDER BY sf.id DESC 
                      LIMIT 500";
              $result = mysqli_query($link, $sql);
              $sr = 1;
              while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $sr++ . "</td>"; 
                echo "<td>" . htmlspecialchars($row['society_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['month_year']) . "</td>";
                echo "<td>" . htmlspecialchars($row['particular']) . "</td>";
                echo "<td>" . number_format($row['amount_april'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['amount_may'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['amount_june'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['increase_decrease_june'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['amount_july'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['amount_august'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['amount_september'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['increase_decrease_september'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['amount_october'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['amount_november'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['amount_december'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['increase_decrease_december'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['amount_january'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['amount_february'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['amount_march'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['increase_decrease_march'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['total_shares'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['average_shares'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['interest_paid'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['interest_rate'] ?? 0, 2) . "%</td>";
                echo "<td>" . number_format($row['percentage_of_total'] ?? 0, 2) . "%</td>";
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