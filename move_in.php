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
        <form action="move_in_action.php" class="row g-3" method="get" enctype="multipart/form-data">
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

    <!-- First Table: Movement in NPA -->
    <div class="card">
      <div class="card-body">
        <h4>MOVEMENT IN N.P.A.</h4>
        <div class="table-responsive">
          <table id="example2" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>0-6 Months A/C No</th>
                <th>0-6 Months Amount</th>
                <th>7-18 Months A/C No</th>
                <th>7-18 Months Amount</th>
                <th>19-42 Months A/C No</th>
                <th>19-42 Months Amount</th>
                <th>43-54 Months A/C No</th>
                <th>43-54 Months Amount</th>
                <th>55-Above Months A/C No</th>
                <th>55-Above Months Amount</th>
                <th>Total A/C No</th>
                <th>Total Amount</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT * FROM movement_in_npa ORDER BY id DESC LIMIT 500";
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
                echo "<td>" . htmlspecialchars($row['ac_no_0_6'] ?? '') . "</td>";
                echo "<td>" . number_format($row['amount_0_6'] ?? 0, 2) . "</td>";
                echo "<td>" . htmlspecialchars($row['ac_no_7_18'] ?? '') . "</td>";
                echo "<td>" . number_format($row['amount_7_18'] ?? 0, 2) . "</td>";
                echo "<td>" . htmlspecialchars($row['ac_no_19_42'] ?? '') . "</td>";
                echo "<td>" . number_format($row['amount_19_42'] ?? 0, 2) . "</td>";
                echo "<td>" . htmlspecialchars($row['ac_no_43_54'] ?? '') . "</td>";
                echo "<td>" . number_format($row['amount_43_54'] ?? 0, 2) . "</td>";
                echo "<td>" . htmlspecialchars($row['ac_no_55_above'] ?? '') . "</td>";
                echo "<td>" . number_format($row['amount_55_above'] ?? 0, 2) . "</td>";
                echo "<td>" . htmlspecialchars($row['total_ac_no'] ?? '') . "</td>";
                echo "<td>" . number_format($row['total_amount'] ?? 0, 2) . "</td>";
                echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                echo "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Second Table: Gross Loan & NPA -->
    <div class="card">
      <div class="card-body">
        <h4>GROSS LOAN & NPA ANALYSIS</h4>
        <div class="table-responsive">
          <table id="example3" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>Gross Loan A/C No</th>
                <th>Gross Loan Amount</th>
                <th>Net Loan A/C No</th>
                <th>Net Loan Amount</th>
                <th>Gross NPA A/C No</th>
                <th>Gross NPA Amount</th>
                <th>Net NPA A/C No</th>
                <th>Net NPA Amount</th>
                <th>Provision Amount</th>
                <th>Net NPA %</th>
                <th>Gross NPA %</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_gross = "SELECT * FROM movement_in_npa ORDER BY id DESC LIMIT 500";
              $result_gross = mysqli_query($link, $sql_gross);
              $sr_gross = 1;
              
              if ($result_gross && mysqli_num_rows($result_gross) > 0) {
                while ($row_gross = mysqli_fetch_assoc($result_gross)) {
                  echo "<tr>";
                  echo "<td>" . $sr_gross++ . "</td>"; 
                  ?>
                  <td>
                    <?php 
                    $bname_gross = $row_gross['bank_name'];
                    $sql120_gross = "SELECT * FROM society_info WHERE id='$bname_gross' ORDER BY id ASC";
                    $result120_gross = mysqli_query($link, $sql120_gross);
                    while ($row120_gross = mysqli_fetch_assoc($result120_gross)) {
                      echo $row120_gross['society_name']; 
                    }
                    ?>
                  </td>
                  <?php
                  echo "<td>" . htmlspecialchars($row_gross['month_year']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_gross['gross_loan_ac_no'] ?? '') . "</td>";
                  echo "<td>" . number_format($row_gross['gross_loan_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_gross['net_loan_ac_no'] ?? '') . "</td>";
                  echo "<td>" . number_format($row_gross['net_loan_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_gross['gross_npa_ac_no'] ?? '') . "</td>";
                  echo "<td>" . number_format($row_gross['gross_npa_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_gross['net_npa_ac_no'] ?? '') . "</td>";
                  echo "<td>" . number_format($row_gross['net_npa_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_gross['provision_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . number_format($row_gross['net_npa_percentage'] ?? 0, 2) . "%</td>";
                  echo "<td>" . number_format($row_gross['gross_npa_percentage'] ?? 0, 2) . "%</td>";
                  echo "<td>" . htmlspecialchars($row_gross['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='15' class='text-center'>No NPA data available</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Third Table: Movement in Overdue -->
    <div class="card">
      <div class="card-body">
        <h4>MOVEMENT IN OVERDUE</h4>
        <div class="table-responsive">
          <table id="example4" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Bank Name</th>
                <th>Month & Year</th>
                <th>0-3 Months A/C No</th>
                <th>0-3 Months Amount</th>
                <th>4-15 Months A/C No</th>
                <th>4-15 Months Amount</th>
                <th>16-39 Months A/C No</th>
                <th>16-39 Months Amount</th>
                <th>40-51 Months A/C No</th>
                <th>40-51 Months Amount</th>
                <th>52-Above Months A/C No</th>
                <th>52-Above Months Amount</th>
                <th>Total A/C No</th>
                <th>Total Amount</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql_overdue = "SELECT * FROM movement_in_npa ORDER BY id DESC LIMIT 500";
              $result_overdue = mysqli_query($link, $sql_overdue);
              $sr_overdue = 1;
              
              if ($result_overdue && mysqli_num_rows($result_overdue) > 0) {
                while ($row_overdue = mysqli_fetch_assoc($result_overdue)) {
                  echo "<tr>";
                  echo "<td>" . $sr_overdue++ . "</td>"; 
                  ?>
                  <td>
                    <?php 
                    $bname_overdue = $row_overdue['bank_name'];
                    $sql120_overdue = "SELECT * FROM society_info WHERE id='$bname_overdue' ORDER BY id ASC";
                    $result120_overdue = mysqli_query($link, $sql120_overdue);
                    while ($row120_overdue = mysqli_fetch_assoc($result120_overdue)) {
                      echo $row120_overdue['society_name']; 
                    }
                    ?>
                  </td>
                  <?php
                  echo "<td>" . htmlspecialchars($row_overdue['month_year']) . "</td>";
                  echo "<td>" . htmlspecialchars($row_overdue['overdue_0_3_ac_no'] ?? '') . "</td>";
                  echo "<td>" . number_format($row_overdue['overdue_0_3_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_overdue['overdue_4_15_ac_no'] ?? '') . "</td>";
                  echo "<td>" . number_format($row_overdue['overdue_4_15_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_overdue['overdue_16_39_ac_no'] ?? '') . "</td>";
                  echo "<td>" . number_format($row_overdue['overdue_16_39_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_overdue['overdue_40_51_ac_no'] ?? '') . "</td>";
                  echo "<td>" . number_format($row_overdue['overdue_40_51_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_overdue['overdue_52_above_ac_no'] ?? '') . "</td>";
                  echo "<td>" . number_format($row_overdue['overdue_52_above_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_overdue['overdue_total_ac_no'] ?? '') . "</td>";
                  echo "<td>" . number_format($row_overdue['overdue_total_amount'] ?? 0, 2) . "</td>";
                  echo "<td>" . htmlspecialchars($row_overdue['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='16' class='text-center'>No overdue data available</td></tr>";
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