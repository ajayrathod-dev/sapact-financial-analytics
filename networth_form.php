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
    
    <!-- Success/Error Messages -->
    <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!</strong> Networth data calculated and saved successfully.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Form Section -->
    <div class="card">
      <div class="card-body">
        <form action="networth.php" class="row g-3" method="get" enctype="multipart/form-data">
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
            <button type="submit" class="btn btn-outline-primary px-5">Calculate & Save Networth Data</button>
          </div>
        </form>
      </div>
    </div>    

    <!-- Networth Data Table -->
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
                <th>Free Reserve</th>
                <th>Standard Assets</th>
                <th>Dividend Fund</th>
                <th>Gross Profit</th>
                <th>Gross Loss</th>
                <th>Total A</th>
                <th>Total B</th>
                <th>Networth</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM networth ORDER BY id DESC LIMIT 500";
              $result = mysqli_query($link, $sql);
              $sr = 1;
              if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                  echo "<tr>";
                  echo "<td>" . $sr++ . "</td>"; 
                  ?>
                  <td>
                    <?php 
                    $bname = $row['bank_name'];
                    $sql120 = "SELECT * FROM society_info WHERE id='$bname' ORDER BY id ASC";
                    $result120 = mysqli_query($link, $sql120);
                    if ($result120 && mysqli_num_rows($result120) > 0) {
                      while ($row120 = mysqli_fetch_assoc($result120)) {
                        echo $row120['society_name']; 
                      }
                    } else {
                      echo 'N/A';
                    }
                    ?>
                  </td>
                  <?php
                  echo "<td>" . htmlspecialchars($row['month_year']) . "</td>";
                  echo "<td>" . number_format($row['share_capital'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['reserve_fund'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['building_fund'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['free_reserve'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['standard_assets'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['dividand_fund'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['gross_profit'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['gross_loss'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['total_a'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['total_b'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row['networth'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row['created_at'] ?? 'N/A') . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='15' class='text-center'>No networth data available</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Calculation Details -->
<!--    <div class="card">-->
<!--      <div class="card-body">-->
<!--        <h5 class="card-title">Networth Calculation Formula</h5>-->
<!--        <div class="row">-->
<!--          <div class="col-md-6">-->
<!--            <h6>Total A (Assets) Components:</h6>-->
<!--            <ul class="list-group">-->
<!--              <li class="list-group-item">Share Capital</li>-->
<!--              <li class="list-group-item">Reserve Fund</li>-->
<!--              <li class="list-group-item">Building Fund</li>-->
<!--              <li class="list-group-item">Free Reserve</li>-->
<!--              <li class="list-group-item">Standard Assets Provision</li>-->
<!--              <li class="list-group-item">25% of Profit & Loss</li>-->
<!--            </ul>-->
<!--          </div>-->
<!--          <div class="col-md-6">-->
<!--            <h6>Calculation Formula:</h6>-->
<!--            <div class="alert alert-info">-->
<!--              <strong>Total A = Share Capital + Reserve Fund + Building Fund + Free Reserve + Standard Assets + (25% of Profit & Loss)</strong>-->
<!--            </div>-->
<!--            <div class="alert alert-info">-->
<!--              <strong>Total B = Total Loss</strong>-->
<!--            </div>-->
<!--            <div class="alert alert-success">-->
<!--              <strong>Networth = Total A - Total B</strong>-->
<!--            </div>-->
<!--            <div class="alert alert-warning">-->
<!--              <small>Note: Only 25% of the total Profit & Loss is added to Total A as per business rules.</small>-->
<!--            </div>-->
<!--          </div>-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->
    
<!--  </div>-->
<!--</div>-->

<?php include('footer.php'); ?>