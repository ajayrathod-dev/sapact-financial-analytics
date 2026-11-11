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
        <form action="networth_action.php" class="row g-3" method="get" enctype="multipart/form-data">
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

    <!-- Table 1: Networth Components -->
    <div class="card mt-4">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Networth Components</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="networth_components_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>Share Capital</th>
                <th>Share Anamat</th>
                <th>Reserve Fund</th>
                <th>Building Fund</th>
                <th>Dividend Fluctuation Fund</th>
                <th>Investment Fluctuation Fund</th>
                <th>Profit Distribution</th>
                <th>Standard Assets Fund</th>
                <th>Previous Year Profit</th>
                <th>Current Year Profit</th>
                <th>Total Profit Before Adj</th>
                <th>Profit Adjustment</th>
                <th>Profit</th>
                <th>25% Reserve on Profit</th>
                <th>Loss</th>
                <th>Short NPA Provision</th>
                <th>Short NPA Int Provision</th>
                <th>Short Exp Provision</th>
                <th>New Baylows Profit Not Add</th>
                <th>Total Networth</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT n.*, s.society_name 
                        FROM networth96 n 
                        LEFT JOIN society_info s ON n.bank_name = s.id 
                        ORDER BY n.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                
                if ($result && $result->num_rows > 0) {
                  $sr = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['month'] ?? '') . "</td>";
                    echo "<td>" . number_format($row['share_capital'], 2) . "</td>";
                    echo "<td>" . number_format($row['share_anamat'], 2) . "</td>";
                    echo "<td>" . number_format($row['reserve_fund'], 2) . "</td>";
                    echo "<td>" . number_format($row['building_fund'], 2) . "</td>";
                    echo "<td>" . number_format($row['dividend_fluctuation_fund'], 2) . "</td>";
                    echo "<td>" . number_format($row['investment_fluctuation_fund'], 2) . "</td>";
                    echo "<td>" . number_format($row['profit_distribution'], 2) . "</td>";
                    echo "<td>" . number_format($row['standard_assets_fund'], 2) . "</td>";
                    echo "<td>" . number_format($row['previous_year_profit'], 2) . "</td>";
                    echo "<td>" . number_format($row['current_year_profit'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_profit_before_adjustment'], 2) . "</td>";
                    echo "<td>" . number_format($row['profit_adjustment'], 2) . "</td>";
                    echo "<td>" . number_format($row['profit'], 2) . "</td>";
                    echo "<td>" . number_format($row['reserve_on_profit_25'], 2) . "</td>";
                    echo "<td>" . number_format($row['loss'], 2) . "</td>";
                    echo "<td>" . number_format($row['short_npa_provision'], 2) . "</td>";
                    echo "<td>" . number_format($row['short_npa_int_provision'], 2) . "</td>";
                    echo "<td>" . number_format($row['short_exp_provision'], 2) . "</td>";
                    echo "<td>" . number_format($row['new_baylows_say_profit_not_add'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_networth'], 2) . "</td>";
                    echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='24' class='text-center'>No data available in table</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Table 2: Networth Increases & Ratios -->
    <div class="card mt-4">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0">Networth Increases & Ratios</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="networth_ratios_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>Increase in NW</th>
                <th>Profit to Networth %</th>
                <th>Working Capital</th>
                <th>Networth % of WC</th>
                <th>NW Increase %</th>
                <th>Total Deposits</th>
                <th>Core Funds</th>
                <th>Total Core Funds</th>
                <th>Law 35 Ratio</th>
                <th>Divided By 5</th>
                <th>New Law Self 15%</th>
                <th>New Law Group 20%</th>
                <th>New Law NW 15 Self</th>
                <th>New Law NW 20 Group</th>
                <th>Cost of Share Capital</th>
                <th>No of Shares</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT n.*, s.society_name 
                        FROM networth96 n 
                        LEFT JOIN society_info s ON n.bank_name = s.id 
                        ORDER BY n.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                
                if ($result && $result->num_rows > 0) {
                  $sr = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['month'] ?? '') . "</td>";
                    echo "<td>" . number_format($row['increase_in_nw'], 2) . "</td>";
                    echo "<td>" . number_format($row['profit_to_networth'], 2) . "%</td>";
                    echo "<td>" . number_format($row['working_capital'], 2) . "</td>";
                    echo "<td>" . number_format($row['networth_percentage'], 2) . "%</td>";
                    echo "<td>" . number_format($row['nw_increase_percentage'], 2) . "%</td>";
                    echo "<td>" . number_format($row['total_deposits'], 2) . "</td>";
                    echo "<td>" . number_format($row['core_funds'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_core_funds'], 2) . "</td>";
                    echo "<td>" . number_format($row['law_35'], 2) . "</td>";
                    echo "<td>" . number_format($row['divided_by_5'], 2) . "</td>";
                    echo "<td>" . number_format($row['new_law_self_15'], 2) . "</td>";
                    echo "<td>" . number_format($row['new_law_group_20'], 2) . "</td>";
                    echo "<td>" . number_format($row['new_law_networth_15_self'], 2) . "</td>";
                    echo "<td>" . number_format($row['new_law_networth_20_group'], 2) . "</td>";
                    echo "<td>" . number_format($row['cost_of_share_capital'], 2) . "</td>";
                    echo "<td>" . number_format($row['no_of_shares'], 0) . "</td>";
                    echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='20' class='text-center'>No data available in table</td></tr>";
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Table 3: CD Ratio, CRAR & Share Values -->
    <div class="card mt-4">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0">CD Ratio, CRAR & Share Values</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="networth_cd_crar_table" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month</th>
                <th>Assets Total</th>
                <th>Investments in Shares</th>
                <th>Funds Available for Loan</th>
                <th>Total Loans</th>
                <th>Gross NPA</th>
                <th>Deposits for CD</th>
                <th>Total Deposits Law 35</th>
                <th>CD Ratio %</th>
                <th>Earning CD Ratio %</th>
                <th>RWA Proxy</th>
                <th>CRAR %</th>
                <th>Target CRAR %</th>
                <th>CRAR Increase Needed</th>
                <th>CRAR Increase in Rupees</th>
                <th>Calculation After CRAR Inc</th>
                <th>Per Share Value</th>
                <th>Target Per Share</th>
                <th>Value 500</th>
                <th>Deficit in Rupees</th>
                <th>Calculation After Share Inc</th>
                <th>Market Capitalization</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $sql = "SELECT n.*, s.society_name 
                        FROM networth96 n 
                        LEFT JOIN society_info s ON n.bank_name = s.id 
                        ORDER BY n.id DESC LIMIT 500";
                $result = mysqli_query($link, $sql);
                
                if ($result && $result->num_rows > 0) {
                  $sr = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>"; 
                    echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['month'] ?? '') . "</td>";
                    echo "<td>" . number_format($row['assets_total'], 2) . "</td>";
                    echo "<td>" . number_format($row['investments_in_shares'], 2) . "</td>";
                    echo "<td>" . number_format($row['funds_available_for_loan'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_loans'], 2) . "</td>";
                    echo "<td>" . number_format($row['gross_npa'], 2) . "</td>";
                    echo "<td>" . number_format($row['deposits_for_cd'], 2) . "</td>";
                    echo "<td>" . number_format($row['total_deposits_law_35'], 2) . "</td>";
                    echo "<td>" . number_format($row['cd_ratio'], 2) . "%</td>";
                    echo "<td>" . number_format($row['earning_cd_ratio'], 2) . "%</td>";
                    echo "<td>" . number_format($row['rwa_proxy'], 2) . "</td>";
                    echo "<td>" . number_format($row['crar'], 2) . "%</td>";
                    echo "<td>" . number_format($row['target_crar'], 2) . "%</td>";
                    echo "<td>" . number_format($row['crar_increase_needed'], 2) . "</td>";
                    echo "<td>" . number_format($row['crar_increase_rupees'], 2) . "</td>";
                    echo "<td>" . number_format($row['calculation_after_crar_increase'], 2) . "%</td>";
                    echo "<td>" . number_format($row['per_share_value'], 2) . "</td>";
                    echo "<td>" . number_format($row['target_per_share'], 2) . "</td>";
                    echo "<td>" . number_format($row['value_500'], 2) . "</td>";
                    echo "<td>" . number_format($row['deficit_in_rupees'], 2) . "</td>";
                    echo "<td>" . number_format($row['calculation_after_share_increase'], 2) . "</td>";
                    echo "<td>" . number_format($row['market_capitalization'], 2) . "</td>";
                    echo "<td>" . htmlspecialchars($row['created_at'] ?? '') . "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='25' class='text-center'>No data available in table</td></tr>";
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