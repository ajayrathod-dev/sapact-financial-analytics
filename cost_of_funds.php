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
        <form action="cost_of_funds_action.php" class="row g-3" method="get" enctype="multipart/form-data">
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

    <!-- Main Cost of Funds Summary Table -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Working Capital</th>
                <th>Total Liabilities</th>
                <th>Total Assets</th>
                <th>Total Interest Paid</th>
                <th>Total Interest Received</th>
                <th>Cost of Funds %</th>
                <th>Yield on Assets %</th>
                <th>Gross Margin %</th>
                <th>Net Margin %</th>
                <th>C/D Ratio</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM cost_of_funds ORDER BY id DESC LIMIT 500";
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
                echo "<td>" . number_format($row['working_capital'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['total_liabilities'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['total_assets'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['total_interest_paid'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['total_interest_received'] ?? 0, 2) . "</td>";
                echo "<td>" . number_format($row['cost_of_funds_percentage'] ?? 0, 2) . "%</td>";
                echo "<td>" . number_format($row['yield_on_assets_percentage'] ?? 0, 2) . "%</td>";
                echo "<td>" . number_format($row['gross_margin_percentage'] ?? 0, 2) . "%</td>";
                echo "<td>" . number_format($row['net_margin_percentage'] ?? 0, 2) . "%</td>";
                echo "<td>" . number_format($row['cd_ratio'] ?? 0, 2) . "%</td>";
                echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                echo "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Liabilities Breakdown -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Liabilities Breakdown</h5>
        <div class="table-responsive">
          <table id="example3" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Liability Type</th>
                <th>Description</th>
                <th>Amount</th>
                <th>% to WC</th>
                <th>Interest Paid</th>
                <th>Interest Rate %</th>
                <th>Weighted Avg</th>
                <th>Parameters</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_liabilities = "SELECT * FROM liabilities_breakdown ORDER BY id DESC LIMIT 500";
              $result_liabilities = mysqli_query($link, $sql_liabilities);
              $sr_liabilities = 1;
              
              if ($result_liabilities && mysqli_num_rows($result_liabilities) > 0) {
                while ($row_liabilities = mysqli_fetch_assoc($result_liabilities)) {
                  echo "<tr>";
                  echo "<td>" . $sr_liabilities++ . "</td>"; 
                  ?>
                  <td>
                    <?php 
                    $bname_liabilities = $row_liabilities['bank_name'];
                    $sql120_liabilities = "SELECT * FROM society_info WHERE id='$bname_liabilities' ORDER BY id ASC";
                    $result120_liabilities = mysqli_query($link, $sql120_liabilities);
                    while ($row120_liabilities = mysqli_fetch_assoc($result120_liabilities)) {
                      echo $row120_liabilities['society_name']; 
                    }
                    ?>
                  </td>
                  <?php
                  echo "<td>" . htmlspecialchars($row_liabilities['month_year']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_liabilities['liability_type']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_liabilities['description']) . "</td>";
                  echo "<td>" . number_format($row_liabilities['amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_liabilities['percentage_to_wc'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row_liabilities['interest_paid'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_liabilities['interest_rate'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row_liabilities['weighted_average'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row_liabilities['parameters'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_liabilities['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='12' class='text-center'>No liabilities data available</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Assets Breakdown -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Assets Breakdown</h5>
        <div class="table-responsive">
          <table id="example4" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Asset Type</th>
                <th>Description</th>
                <th>Amount</th>
                <th>% to WC</th>
                <th>Interest Received</th>
                <th>Interest Rate %</th>
                <th>Weighted Avg</th>
                <th>Parameters</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_assets = "SELECT * FROM assets_breakdown ORDER BY id DESC LIMIT 500";
              $result_assets = mysqli_query($link, $sql_assets);
              $sr_assets = 1;
              
              if ($result_assets && mysqli_num_rows($result_assets) > 0) {
                while ($row_assets = mysqli_fetch_assoc($result_assets)) {
                  echo "<tr>";
                  echo "<td>" . $sr_assets++ . "</td>"; 
                  ?>
                  <td>
                    <?php 
                    $bname_assets = $row_assets['bank_name'];
                    $sql120_assets = "SELECT * FROM society_info WHERE id='$bname_assets' ORDER BY id ASC";
                    $result120_assets = mysqli_query($link, $sql120_assets);
                    while ($row120_assets = mysqli_fetch_assoc($result120_assets)) {
                      echo $row120_assets['society_name']; 
                    }
                    ?>
                  </td>
                  <?php
                  echo "<td>" . htmlspecialchars($row_assets['month_year']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_assets['asset_type']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_assets['description']) . "</td>";
                  echo "<td>" . number_format($row_assets['amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_assets['percentage_to_wc'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row_assets['interest_received'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_assets['interest_rate'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row_assets['weighted_average'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row_assets['parameters'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_assets['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='12' class='text-center'>No assets data available</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Expenses Breakdown -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Expenses Breakdown</h5>
        <div class="table-responsive">
          <table id="example5" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Expense Type</th>
                <th>Description</th>
                <th>Amount</th>
                <th>% to Total</th>
                <th>Parameters</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_expenses = "SELECT * FROM expenses_breakdown ORDER BY id DESC LIMIT 500";
              $result_expenses = mysqli_query($link, $sql_expenses);
              $sr_expenses = 1;
              
              if ($result_expenses && mysqli_num_rows($result_expenses) > 0) {
                while ($row_expenses = mysqli_fetch_assoc($result_expenses)) {
                  echo "<tr>";
                  echo "<td>" . $sr_expenses++ . "</td>"; 
                  ?>
                  <td>
                    <?php 
                    $bname_expenses = $row_expenses['bank_name'];
                    $sql120_expenses = "SELECT * FROM society_info WHERE id='$bname_expenses' ORDER BY id ASC";
                    $result120_expenses = mysqli_query($link, $sql120_expenses);
                    while ($row120_expenses = mysqli_fetch_assoc($result120_expenses)) {
                      echo $row120_expenses['society_name']; 
                    }
                    ?>
                  </td>
                  <?php
                  echo "<td>" . htmlspecialchars($row_expenses['month_year']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_expenses['expense_type']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_expenses['description']) . "</td>";
                  echo "<td>" . number_format($row_expenses['amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_expenses['percentage_to_total'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row_expenses['parameters'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_expenses['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='9' class='text-center'>No expenses data available</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Income Breakdown -->
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Income Breakdown</h5>
        <div class="table-responsive">
          <table id="example6" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Income Type</th>
                <th>Description</th>
                <th>Amount</th>
                <th>% to Total</th>
                <th>Parameters</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_income = "SELECT * FROM income_breakdown ORDER BY id DESC LIMIT 500";
              $result_income = mysqli_query($link, $sql_income);
              $sr_income = 1;
              
              if ($result_income && mysqli_num_rows($result_income) > 0) {
                while ($row_income = mysqli_fetch_assoc($result_income)) {
                  echo "<tr>";
                  echo "<td>" . $sr_income++ . "</td>"; 
                  ?>
                  <td>
                    <?php 
                    $bname_income = $row_income['bank_name'];
                    $sql120_income = "SELECT * FROM society_info WHERE id='$bname_income' ORDER BY id ASC";
                    $result120_income = mysqli_query($link, $sql120_income);
                    while ($row120_income = mysqli_fetch_assoc($result120_income)) {
                      echo $row120_income['society_name']; 
                    }
                    ?>
                  </td>
                  <?php
                  echo "<td>" . htmlspecialchars($row_income['month_year']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_income['income_type']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_income['description']) . "</td>";
                  echo "<td>" . number_format($row_income['amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_income['percentage_to_total'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row_income['parameters'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_income['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='9' class='text-center'>No income data available</td></tr>";
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