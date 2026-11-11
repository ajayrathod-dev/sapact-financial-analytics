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
         <form action="sick_action.php" class="row g-3" method="get" enctype="multipart/form-data">
           <div class="col-md-6">
            <label for="inputFirstName" class="form-label">Bank Name</label>
              <select class="form-control" id="inputFirstName" name="bank_name" required>
                <option value="">--Select--</option>
                   <?php 
                       $sql1 = "SELECT * FROM society_info ORDER BY id DESC";
                       $result1 = mysqli_query($link, $sql1);
                       if ($result1->num_rows > 0) {
                           while ($usr1 = mysqli_fetch_array($result1)) {
                               echo "<option value='{$usr1['id']}'>{$usr1['society_name']}</option>";
                           }
                       }
                   ?>
             </select>     
          </div>
          <div class="col-md-6">
            <label for="inputFirstName" class="form-label">Year</label>
            <input type="month" name="month" class="form-control" required>
          </div>
         
          <div class="col-12" align="center">
            <button type="submit" class="btn btn-outline-primary px-5">calibration</button>
          </div>
        </form>
      </div>
    </div>    
    <div class="card">
       <div class="card-body">
          <div class="table-responsive">
             <table id="example2" class="table table-striped table-bordered">
                 <thead>
                    <th>Sr. No</th>
                    <th>Bank Name</th>
                    <th>Month & Year</th>
                    <th>Net Loss / Total Erosion</th>
                    <th>CRAR NETWORTH</th>
                    <th>Actual Gross NPA</th>
                    <th>Gross NPA Threshold</th>
                    <th>CRAR Remark</th>
                    <th>NPA Remark</th>
                    <th>Final Result</th>
                    <th>Date</th>
                 </thead>
                 <tbody>
                   <?php
                    $sql = "SELECT * FROM sick_society_report ORDER BY id DESC LIMIT 500";
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
                        // Handle null values with default values
                        $net_loss = htmlspecialchars($row['net_loss_total_erosion'] ?? '0.00');
                        $crar_networth = htmlspecialchars($row['crar_networth'] ?? '0.00');
                        $actual_gross_npa = htmlspecialchars($row['actual_gross_npa'] ?? '0.00');
                        $gross_npa_threshold = htmlspecialchars($row['gross_npa_threshold'] ?? '10.00');
                        $remark_crar = htmlspecialchars($row['remark_crar'] ?? 'N/A');
                        $remark_npa = htmlspecialchars($row['remark_npa'] ?? 'N/A');
                        $final_result = htmlspecialchars($row['final_result'] ?? 'N/A');
                        $created_at = htmlspecialchars($row['created_at'] ?? '');

                        echo "<td>" . htmlspecialchars($row['month_year'] ?? '') . "</td>";
                        echo "<td>" . $net_loss . "</td>";
                        echo "<td>" . $crar_networth . "</td>";
                        echo "<td>" . $actual_gross_npa . "%</td>";
                        echo "<td>" . $gross_npa_threshold . "%</td>";
                        echo "<td><span class='badge bg-" . ($remark_crar == 'SICK' ? 'danger' : 'success') . "'>" . $remark_crar . "</span></td>";
                        echo "<td><span class='badge bg-" . ($remark_npa == 'SICK' ? 'danger' : 'success') . "'>" . $remark_npa . "</span></td>";
                        echo "<td><span class='badge bg-" . ($final_result == 'SICK' ? 'danger' : 'success') . "'>" . $final_result . "</span></td>";
                        echo "<td>" . $created_at . "</td>";
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