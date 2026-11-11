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
        <form action="action_investment.php" class="row g-3" method="get" enctype="multipart/form-data">
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
            <input type="month" name="month" class="form-control" value="<?php echo htmlspecialchars($month); ?>" required>
          </div>
          <div class="col-12" align="center">
            <button type="submit" class="btn btn-outline-primary px-5">Calculate Investment & Dead Stock</button>
          </div>
        </form>
      </div>
    </div>    

    <!-- Investment & Dead Stock Table -->
    <div class="card mt-3">
      <div class="card-body">
        <h5 class="card-title">Investment & Dead Stock Calculation Data</h5>
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Total Fixed & Dead Stock</th>
                <th>Total Investment</th>
                <th>Required CRR</th>
                <th>Actual CRR</th>
                <th>CRR (%)</th>
                <th>Required SLR</th>
                <th>Actual SLR</th>
                <th>SLR (%)</th>
                <th>NPA Investment CRR</th>
                <th>NPA Investment SLR</th>
                <th>Interest Receivable</th>
                <th>30% Investment Limit</th>
                <th>Created At</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $where_clause = "";
              if (!empty($bank_name)) {
                  $where_clause .= " WHERE ifds.bank_name = '$bank_name'";
              }
              if (!empty($month)) {
                  $where_clause .= ($where_clause ? " AND" : " WHERE") . " ifds.month = '$month'";
              }
              $sql = "SELECT ifds.*, si.society_name 
                      FROM investment_fix_dead_stock ifds 
                      LEFT JOIN society_info si ON ifds.bank_name = si.id 
                      $where_clause 
                      ORDER BY ifds.id DESC LIMIT 500";
              $result = mysqli_query($link, $sql);
              $sr = 1;
              
              if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr>";
                  echo "<td>" . $sr++ . "</td>";
                  echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                  echo "<td>" . htmlspecialchars($row['month']) . "</td>";
                  echo "<td>" . number_format($row['total_fixed_dead_stock'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['total_investment'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['required_crr'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['actual_crr'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['crr_percentage'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['required_slr'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['actual_slr'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['slr_percentage'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['npa_investment_crr'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['npa_investment_slr'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['interest_receivable'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['thirty_percent_investment_limit'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row['created_at'] ?? 'N/A') . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='16' class='text-center'>No investment & dead stock data available</td></tr>";
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