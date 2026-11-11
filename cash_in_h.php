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
        <form action="cash_action.php" class="row g-3" method="get" enctype="multipart/form-data">
          <div class="col-md-6">
            <label for="inputFirstName" class="form-label">Bank Name</label>
            <select class="form-control" id="inputFirstName" name="bank_name" required>
              <option value="">--Select--</option>
              <?php 
                $sql1 = "SELECT * FROM society_info ORDER BY id DESC";
                $result1 = mysqli_query($link, $sql1);
                if ($result1->num_rows > 0) {
                  while ($usr1 = mysqli_fetch_array($result1)) {
                    $selected = ($bank_name == $usr1['id']) ? 'selected' : '';
                    echo "<option value='{$usr1['id']}' $selected>{$usr1['society_name']}</option>";
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
            <button type="submit" class="btn btn-outline-primary px-5">Calculate</button>
          </div>
        </form>
      </div>
    </div>    

    <!-- Table 1: Cash In Hand -->
    <div class="card mt-4">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Cash In Hand</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="cash_in_hand_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>Monthly Cash</th>
                <th>TOTAL</th>
                <th>TOTAL INCREASE</th>
                <th>TOTAL INCREASE IN %</th>
                <th>Monthly Increase</th>
                <th>Monthly Increase In %</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT c.*, s.society_name 
                        FROM cash_in_h c 
                        LEFT JOIN society_info s ON c.bank_name = s.id 
                        ORDER BY c.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                $sr = 1;
                if ($result && $result->num_rows > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['month']) . "</td>";
                    echo "<td>" . number_format($row['monthly_cash'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_cash'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_cash_increase'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_cash_increase_percent'], 2) . "%</td>";
                    echo "<td>" . number_format($row['monthly_cash_increase'], 2) . "</td>";
                    echo "<td>" . number_format($row['monthly_cash_increase_percent'], 2) . "%</td>";
                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='10'>No data available in table</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Table 2: Borrowings / OD -->
    <div class="card mt-4">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0">Borrowings / OD</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="borrowings_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>Borrowings Total</th>
                <th>TOTAL INCREASE</th>
                <th>TOTAL INCREASE IN %</th>
                <th>Monthly Increase</th>
                <th>Monthly Increase In %</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT c.*, s.society_name 
                        FROM cash_in_h c 
                        LEFT JOIN society_info s ON c.bank_name = s.id 
                        ORDER BY c.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                $sr = 1;
                if ($result && $result->num_rows > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['month']) . "</td>";
                    echo "<td>" . number_format($row['borrowings_total'], 2) . "</td>";
                    echo "<td>" . number_format($row['borrowings_total_increase'], 2) . "</td>";
                    echo "<td>" . number_format($row['borrowings_total_increase_percent'], 2) . "%</td>";
                    echo "<td>" . number_format($row['borrowings_monthly_increase'], 2) . "</td>";
                    echo "<td>" . number_format($row['borrowings_monthly_increase_percent'], 2) . "%</td>";
                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='9'>No data available in table</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Table 3: Cash at Bank -->
    <div class="card mt-4">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0">Cash at Bank</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="cash_at_bank_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>ADCC Ahmednagar</th>
                <th>RBL Bank</th>
                <th>Shri Venkatesh Multistate</th>
                <th>ICICI Bank Nagar</th>
                <th>Samruddhi G.B.S.P.M Kukana</th>
                <th>Buldhana Urban Society</th>
                <th>Yes Bank (308)</th>
                <th>TOTAL INCREASE</th>
                <th>TOTAL INCREASE IN %</th>
                <th>Monthly Increase</th>
                <th>Monthly Increase In %</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT c.*, s.society_name 
                        FROM cash_in_h c 
                        LEFT JOIN society_info s ON c.bank_name = s.id 
                        ORDER BY c.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                $sr = 1;
                if ($result && $result->num_rows > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['month']) . "</td>";
                    echo "<td>" . number_format($row['adcc_ahmednagar'], 2) . "</td>";
                    echo "<td>" . number_format($row['rbl_bank'], 2) . "</td>";
                    echo "<td>" . number_format($row['shri_venkatesh_multistate'], 2) . "</td>";
                    echo "<td>" . number_format($row['icici_bank_nagar'], 2) . "</td>";
                    echo "<td>" . number_format($row['samruddhi_gbspm_kukana'], 2) . "</td>";
                    echo "<td>" . number_format($row['buldhana_urban_society'], 2) . "</td>";
                    echo "<td>" . number_format($row['yes_bank'], 2) . "</td>";
                    echo "<td>" . number_format($row['cash_bank_total_increase'], 2) . "</td>";
                    echo "<td>" . number_format($row['cash_bank_total_increase_percent'], 2) . "%</td>";
                    echo "<td>" . number_format($row['cash_bank_monthly_increase'], 2) . "</td>";
                    echo "<td>" . number_format($row['cash_bank_monthly_increase_percent'], 2) . "%</td>";
                    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='15'>No data available in table</td></tr>";
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