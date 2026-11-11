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
        <form action="inc_action.php" class="row g-3" method="get" enctype="multipart/form-data">
          <div class="col-md-6">
            <label for="inputFirstName" class="form-label">Bank Name</label>
            <select class="form-control" id="inputFirstName" name="bank_name" required>
              <option value="">--Select--</option>
              <?php 
                $sql1 = "SELECT * FROM society_info ORDER BY id DESC";
                $result1 = mysqli_query($link, $sql1);
                if ($result1 && $result1->num_rows > 0) {
                  while ($usr1 = mysqli_fetch_array($result1)) {
                    $selected = ($bank_name == $usr1['id']) ? 'selected' : '';
                    echo "<option value='{$usr1['id']}' $selected>{$usr1['society_name']}</option>";
                  }
                } else {
                  echo "<option value=''>No banks found</option>";
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

    <!-- Table 1: Advances Section -->
    <div class="card mt-4">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Advances Section</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="advances_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>Monthly Income</th>
                <th>Total</th>
                <th>Total Increase</th>
                <th>Total Increase %</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT i.*, s.society_name 
                        FROM income_exp i 
                        LEFT JOIN society_info s ON i.bank_name = s.id 
                        ORDER BY i.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                
                if ($result && $result->num_rows > 0) {
                  $sr = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['month_year'] ?? '') . "</td>";
                    echo "<td>" . number_format($row['adv_monthly_income'], 2) . "</td>";
                    echo "<td>" . number_format($row['adv_total'], 2) . "</td>";
                    echo "<td>" . number_format($row['adv_total_incr'], 2) . "</td>";
                    echo "<td>" . number_format($row['adv_total_incr_perc'], 2) . "%</td>";
                    echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='8' class='text-center'>No data available in table</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Table 2: Investment Section -->
    <div class="card mt-4">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0">Investment Section</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="investment_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>Monthly Income</th>
                <th>Total</th>
                <th>Total Increase</th>
                <th>Total Increase %</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT i.*, s.society_name 
                        FROM income_exp i 
                        LEFT JOIN society_info s ON i.bank_name = s.id 
                        ORDER BY i.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                
                if ($result && $result->num_rows > 0) {
                  $sr = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['month_year'] ?? '') . "</td>";
                    echo "<td>" . number_format($row['inv_monthly_income'], 2) . "</td>";
                    echo "<td>" . number_format($row['inv_total'], 2) . "</td>";
                    echo "<td>" . number_format($row['inv_total_incr'], 2) . "</td>";
                    echo "<td>" . number_format($row['inv_total_incr_perc'], 2) . "%</td>";
                    echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='8' class='text-center'>No data available in table</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Table 3: Other Income Section -->
    <div class="card mt-4">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0">Other Income Section</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="other_income_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>Monthly Income</th>
                <th>Total</th>
                <th>Total Increase</th>
                <th>Total Increase %</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT i.*, s.society_name 
                        FROM income_exp i 
                        LEFT JOIN society_info s ON i.bank_name = s.id 
                        ORDER BY i.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                
                if ($result && $result->num_rows > 0) {
                  $sr = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['month_year'] ?? '') . "</td>";
                    echo "<td>" . number_format($row['oth_monthly_income'], 2) . "</td>";
                    echo "<td>" . number_format($row['oth_total'], 2) . "</td>";
                    echo "<td>" . number_format($row['oth_total_incr'], 2) . "</td>";
                    echo "<td>" . number_format($row['oth_total_incr_perc'], 2) . "%</td>";
                    echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='8' class='text-center'>No data available in table</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Table 4: Total Income Section -->
    <div class="card mt-4">
      <div class="card-header bg-warning text-white">
        <h5 class="mb-0">Total Income Section</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="total_income_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>Monthly Income</th>
                <th>Total</th>
                <th>Vision</th>
                <th>Total Increase %</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT i.*, s.society_name 
                        FROM income_exp i 
                        LEFT JOIN society_info s ON i.bank_name = s.id 
                        ORDER BY i.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                
                if ($result && $result->num_rows > 0) {
                  $sr = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['month_year'] ?? '') . "</td>";
                    echo "<td>" . number_format($row['inco_monthly_income'], 2) . "</td>";
                    echo "<td>" . number_format($row['inco_total'], 2) . "</td>";
                    echo "<td>" . number_format($row['vision'], 2) . "</td>";
                    echo "<td>" . number_format($row['inco_total_incr_perc'], 2) . "%</td>";
                    echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='8' class='text-center'>No data available in table</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Table 5: Interest on Deposit Section -->
    <div class="card mt-4">
      <div class="card-header bg-danger text-white">
        <h5 class="mb-0">Interest on Deposit Section</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="interest_deposit_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>Monthly</th>
                <th>Total</th>
                <th>Total Increase</th>
                <th>In Percentage</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT i.*, s.society_name 
                        FROM income_exp i 
                        LEFT JOIN society_info s ON i.bank_name = s.id 
                        ORDER BY i.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                
                if ($result && $result->num_rows > 0) {
                  $sr = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['month_year'] ?? '') . "</td>";
                    echo "<td>" . number_format($row['int_monthly'], 2) . "</td>";
                    echo "<td>" . number_format($row['int_total'], 2) . "</td>";
                    echo "<td>" . number_format($row['int_total_incr'], 2) . "</td>";
                    echo "<td>" . number_format($row['int_in_perc'], 2) . "%</td>";
                    echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='8' class='text-center'>No data available in table</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Table 6: On O/D or Loans Section -->
    <div class="card mt-4">
      <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">On O/D or Loans Section</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="od_loans_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>Monthly</th>
                <th>Total</th>
                <th>Total Increase</th>
                <th>In Percentage</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT i.*, s.society_name 
                        FROM income_exp i 
                        LEFT JOIN society_info s ON i.bank_name = s.id 
                        ORDER BY i.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                
                if ($result && $result->num_rows > 0) {
                  $sr = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['month_year'] ?? '') . "</td>";
                    echo "<td>" . number_format($row['od_monthly'], 2) . "</td>";
                    echo "<td>" . number_format($row['od_total'], 2) . "</td>";
                    echo "<td>" . number_format($row['od_total_incr'], 2) . "</td>";
                    echo "<td>" . number_format($row['od_in_perc'], 2) . "%</td>";
                    echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='8' class='text-center'>No data available in table</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Table 7: Management Expenses Section -->
    <div class="card mt-4">
      <div class="card-header bg-dark text-white">
        <h5 class="mb-0">Management Expenses Section</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="management_expenses_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>Prashaskiy Exp</th>
                <th>Asthapna Exp</th>
                <th>Total</th>
                <th>Vision</th>
                <th>In Percentage</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT i.*, s.society_name 
                        FROM income_exp i 
                        LEFT JOIN society_info s ON i.bank_name = s.id 
                        ORDER BY i.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                
                if ($result && $result->num_rows > 0) {
                  $sr = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['month_year'] ?? '') . "</td>";
                    echo "<td>" . number_format($row['mgmt_prashaskiy'], 2) . "</td>";
                    echo "<td>" . number_format($row['mgmt_asthapna'], 2) . "</td>";
                    echo "<td>" . number_format($row['mgmt_total'], 2) . "</td>";
                    echo "<td>" . number_format($row['mgmt_vision'], 2) . "</td>";
                    echo "<td>" . number_format($row['mgmt_in_perc'], 2) . "%</td>";
                    echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='9' class='text-center'>No data available in table</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Table 8: Provision Section -->
    <div class="card mt-4">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Provision Section</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="provision_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>Monthly</th>
                <th>Total</th>
                <th>Total Increase</th>
                <th>In Percentage</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT i.*, s.society_name 
                        FROM income_exp i 
                        LEFT JOIN society_info s ON i.bank_name = s.id 
                        ORDER BY i.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                
                if ($result && $result->num_rows > 0) {
                  $sr = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['month_year'] ?? '') . "</td>";
                    echo "<td>" . number_format($row['prov_monthly'], 2) . "</td>";
                    echo "<td>" . number_format($row['prov_total'], 2) . "</td>";
                    echo "<td>" . number_format($row['prov_total_incr'], 2) . "</td>";
                    echo "<td>" . number_format($row['prov_in_perc'], 2) . "%</td>";
                    echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='8' class='text-center'>No data available in table</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Table 9: Profit Section -->
    <div class="card mt-4">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0">Profit Section</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="profit_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>Monthly</th>
                <th>Total</th>
                <th>Total Increase</th>
                <th>In Percentage</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT i.*, s.society_name 
                        FROM income_exp i 
                        LEFT JOIN society_info s ON i.bank_name = s.id 
                        ORDER BY i.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                
                if ($result && $result->num_rows > 0) {
                  $sr = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['month_year'] ?? '') . "</td>";
                    echo "<td>" . number_format($row['prof_monthly'], 2) . "</td>";
                    echo "<td>" . number_format($row['prof_total'], 2) . "</td>";
                    echo "<td>" . number_format($row['prof_total_incr'], 2) . "</td>";
                    echo "<td>" . number_format($row['prof_in_perc'], 2) . "%</td>";
                    echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='8' class='text-center'>No data available in table</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Table 10: Total MGE + Interest Section -->
    <div class="card mt-4">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0">Total MGE + Interest Section</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="total_mge_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>Total MGE</th>
                <th>Vision</th>
                <th>In Percentage</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT i.*, s.society_name 
                        FROM income_exp i 
                        LEFT JOIN society_info s ON i.bank_name = s.id 
                        ORDER BY i.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                
                if ($result && $result->num_rows > 0) {
                  $sr = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['month_year'] ?? '') . "</td>";
                    echo "<td>" . number_format($row['total_mge'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_vision'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_in_perc'], 2) . "%</td>";
                    echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='7' class='text-center'>No data available in table</td></tr>";
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