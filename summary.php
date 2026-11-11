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
        <form action="summary_action.php" class="row g-3" method="get" enctype="multipart/form-data">
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

    <!-- Summary Report Table -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Shares</th>
                <th>Funds</th>
                <th>Deposits</th>
                <th>Loans/Advances</th>
                <th>Cash In Hand</th>
                <th>Cash At Bank</th>
                <th>O.D.</th>
                <th>Investments</th>
                <th>Fixed & Dead Stock</th>
                <th>Interest On Loans</th>
                <th>Interest On Investment</th>
                <th>Other Income</th>
                <th>Total Income</th>
                <th>Interest On Deposit</th>
                <th>Interest On OD</th>
                <th>Prashaskiy</th>
                <th>Aasthapna</th>
                <th>OD Interest</th>
                <th>Total M E</th>
                <th>Provision</th>
                <th>Profit</th>
                <th>Total Business</th>
                <th>Employee</th>
                <th>Per Employee Business</th>
                <th>Per Employee Income</th>
                <th>Per Employee Profit</th>
                <th>Working Capital</th>
                <th>New Section 144 WC</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM summary_report ORDER BY id DESC LIMIT 500";
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
                echo "<td>" . number_format($row['shares'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['funds'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['deposits'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['loans_advances'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['cash_in_hand'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['cash_at_bank'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['od'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['investments'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['fixed_dead_stock'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['interest_on_loans'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['interest_on_investment'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['other_income'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['total_income'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['interest_on_deposit'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['interest_on_od'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['prashaskiy'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['aasthapna'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['od_interest'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['total_me'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['provision'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['profit'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['total_business'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['employee'] ?? 0, 0) . "</td>";
                echo "<td>" . number_format($row['per_employee_business'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['per_employee_income'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['per_employee_profit'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['working_capital'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['new_section_144_wc'] ?? 0, 2) . "</td>";
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