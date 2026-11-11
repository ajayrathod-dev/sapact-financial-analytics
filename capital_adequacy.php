<?php
include 'header.php';
date_default_timezone_set('Asia/Kolkata');
$currentDateTime = date('Y-m-d H:i:s');

$bank_name = $_GET['bank_name'] ?? '';
$month     = $_GET['month'] ?? '';
$success   = $_GET['success'] ?? '';
$error     = $_GET['error'] ?? '';
?>

<div class="page-wrapper">
  <div class="page-content">
    <!-- Display Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body">
        <form action="capital_adequacy_action.php" class="row g-3" method="get">
          <div class="col-md-6">
            <label class="form-label">Bank Name</label>
            <select class="form-control" name="bank_name" required>
              <option value="">--Select--</option>
              <?php 
              $sql1 = "SELECT * FROM society_info ORDER BY id DESC";
              $result1 = mysqli_query($link, $sql1);
              if ($result1->num_rows > 0) {
                  while ($usr1 = mysqli_fetch_assoc($result1)) {
                      $selected = ($bank_name == $usr1['id']) ? 'selected' : '';
                      echo "<option value='{$usr1['id']}' $selected>{$usr1['society_name']}</option>";
                  }
              }
              ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label">Month & Year</label>
            <input type="month" name="month" class="form-control" value="<?php echo htmlspecialchars($month); ?>" required>
          </div>

          <div class="col-12 text-center">
            <button type="submit" class="btn btn-outline-primary px-5">Calculate Capital Adequacy</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-body">
        <h5 class="card-title">Capital Adequacy Calculation Data</h5>
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Rule 35 Value</th>
                <th>Rule 35 Required</th>
                <th>CRAR (%)</th>
                <th>CRAR Required (%)</th>
                <th>Per Share Value</th>
                <th>Per Share Required</th>
                <th>Self Exposure Limit</th>
                <th>Group Exposure Limit</th>
                <th>COCA Capital</th>
                <th>Market Capitalization</th>
                <th>Cost of Funds (%)</th>
                <th>Yield of Funds (%)</th>
                <th>Management Cost (%)</th>
                <th>Margin (%)</th>
                <th>Created At</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $where_clause = "";
              if (!empty($bank_name)) {
                  $where_clause .= " WHERE ca.bank_name = '$bank_name'";
              }
              if (!empty($month)) {
                  $where_clause .= ($where_clause ? " AND" : " WHERE") . " ca.month = '$month'";
              }
              $sql = "SELECT ca.*, si.society_name 
                      FROM capital_adequacy ca 
                      LEFT JOIN society_info si ON ca.bank_name = si.id 
                      $where_clause 
                      ORDER BY ca.id DESC 
                      LIMIT 500";
              $result = mysqli_query($link, $sql);
              $sr = 1;

              if ($result && mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                      echo "<tr>";
                      echo "<td>" . $sr++ . "</td>";
                      echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                      echo "<td>" . htmlspecialchars($row['month']) . "</td>";
                      echo "<td>" . number_format($row['rule_35_value'], 2) . "</td>";
                      echo "<td>" . number_format($row['rule_35_required'], 2) . "</td>";
                      echo "<td>" . number_format($row['crar_percentage'], 2) . "</td>";
                      echo "<td>" . number_format($row['crar_required'], 2) . "</td>";
                      echo "<td>" . number_format($row['per_share_value'], 2) . "</td>";
                      echo "<td>" . number_format($row['per_share_required'], 2) . "</td>";
                      echo "<td>" . number_format($row['self_exposure_limit'], 2) . "</td>";
                      echo "<td>" . number_format($row['group_exposure_limit'], 2) . "</td>";
                      echo "<td>" . number_format($row['coca_capital'], 2) . "</td>";
                      echo "<td>" . number_format($row['market_capitalization'], 2) . "</td>";
                      echo "<td>" . number_format($row['cost_of_funds'], 2) . "</td>";
                      echo "<td>" . number_format($row['yield_of_funds'], 2) . "</td>";
                      echo "<td>" . number_format($row['management_cost'], 2) . "</td>";
                      echo "<td>" . number_format($row['margin'], 2) . "</td>";
                      echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                      echo "</tr>";
                  }
              } else {
                  echo "<tr><td colspan='18' class='text-center'>No capital adequacy data available</td></tr>";
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