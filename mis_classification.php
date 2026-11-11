<?php
include 'header.php';
date_default_timezone_set('Asia/Kolkata');

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

    <!-- Form Section -->
    <div class="card">
      <div class="card-body">
        <form action="mis_classification_action.php" class="row g-3" method="get">
          <div class="col-md-6">
            <label class="form-label">Bank Name</label>
            <select class="form-control" name="bank_name" required>
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
            <label class="form-label">Month & Year</label>
            <input type="month" name="month" class="form-control" value="<?php echo htmlspecialchars($month); ?>" required>
          </div>
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-outline-primary px-5">Calculate Classification</button>
          </div>
        </form>
      </div>
    </div>    

    <!-- Classification Table -->
    <div class="card mt-3">
      <div class="card-body">
        <h5 class="card-title">MIS Classification Data</h5>
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>CD Ratio (%)</th>
                <th>CD Marks</th>
                <th>Overdue/NPA (%)</th>
                <th>Overdue Marks</th>
                <th>CRR (%)</th>
                <th>CRR Marks</th>
                <th>SLR (%)</th>
                <th>SLR Marks</th>
                <th>Profit</th>
                <th>Profit Marks</th>
                <th>Loss</th>
                <th>Loss Marks</th>
                <th>Total Marks</th>
                <th>Classification</th>
                <th>Created At</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $where_clause = "";
              if (!empty($bank_name)) {
                  $where_clause .= " WHERE mc.bank_name = '$bank_name'";
              }
              if (!empty($month)) {
                  $where_clause .= ($where_clause ? " AND" : " WHERE") . " mc.month = '$month'";
              }
              $sql = "SELECT mc.*, si.society_name 
                      FROM mis_classification mc 
                      LEFT JOIN society_info si ON mc.bank_name = si.id 
                      $where_clause 
                      ORDER BY mc.id DESC LIMIT 500";
              $result = mysqli_query($link, $sql);
              $sr = 1;
              
              if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr>";
                  echo "<td>" . $sr++ . "</td>";
                  echo "<td>" . htmlspecialchars($row['society_name'] ?? 'N/A') . "</td>";
                  echo "<td>" . htmlspecialchars($row['month']) . "</td>";
                  echo "<td>" . number_format($row['cd_ratio_actual'], 2) . "</td>";
                  echo "<td>" . number_format($row['cd_ratio_marks'], 0) . "</td>";
                  echo "<td>" . number_format($row['overdue_actual'], 2) . "</td>";
                  echo "<td>" . number_format($row['overdue_marks'], 0) . "</td>";
                  echo "<td>" . number_format($row['crr_actual'], 2) . "</td>";
                  echo "<td>" . number_format($row['crr_marks'], 0) . "</td>";
                  echo "<td>" . number_format($row['slr_actual'], 2) . "</td>";
                  echo "<td>" . number_format($row['slr_marks'], 0) . "</td>";
                  echo "<td>" . number_format($row['profit_actual'], 2) . "</td>";
                  echo "<td>" . number_format($row['profit_marks'], 0) . "</td>";
                  echo "<td>" . number_format($row['loss_actual'], 2) . "</td>";
                  echo "<td>" . number_format($row['loss_marks'], 0) . "</td>";
                  echo "<td>" . number_format($row['total_marks'], 0) . "</td>";
                  echo "<td><strong>" . htmlspecialchars($row['classification']) . "</strong></td>";
                  echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='18' class='text-center'>No MIS classification data available</td></tr>";
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