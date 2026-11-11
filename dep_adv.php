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
                                 <form action= "dep_adv_action.php" class="row g-3"  method="get" enctype="multipart/form-data">
                                   <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Bank Name</label>
                                      <select class="form-control" id="inputFirstName" name="bank_name" required>
                                        <option value="">--Select--</option>
                                           <?php 
                                               $sql1 = "SELECT * FROM society_info  ORDER BY id DESC";
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
                      <th>SR. No</th>
                      <th>BANK NAME</th>
                      <th>Month & Year</th>
                      <th>CASA</th>
                      <th>FIXED DEPOSIT</th>
                      <th>MONTHLY INCOME SCHEME</th>   
                     <th>NEW FIXED DEPOSIT</th>
                      <th>DAM DIDPAT SCHEME</th>
                      <th> DAM DUPAT SCHEME</th>
                      <th>RD</th>
                      <th>PIGMY COLLECTION</th>                   
                      <th>LADKI LEK THEV YOJANA</th>
                      <th>SECURITY DEPOSIT</th>
                      <th>LOCKER DEPOSIT</th>
                      <th>TOTAL</th>
                      <th>TOTAL INCREASE AS COMPARE TO MARCH</th>
                      <th>TOTAL INCREASE AS COMPARE TO MARCH (%)</th>
                      <th>MONTHLY INCREASE AS COMPARE TO LAST MONTH</th>
                      <th>MONTHLY INCREASE AS COMPARE TO LAST MONTH (%)</th>
                      <th>VISION PER MONTH 2026</th>
                      <th>VISION GAP</th>
                      <th>CASA (%)</th>
                      <th>CREATED AT</th>
                   </thead>
                 <tbody>

                   <?php
            $sql = "SELECT * FROM dep_adv ORDER BY id DESC limit 500";
            $result = mysqli_query($link, $sql);
            $sr = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $sr++ . "</td>"; 
                ?>
    <td>
        <?php 
        $bname= $row['bank_name'];
        $sql120 = "SELECT * FROM society_info WHERE id='$bname' ORDER BY id ASC";
        $result120 = mysqli_query($link, $sql120);
        while ($row120 = mysqli_fetch_assoc($result120)) {
            echo $row120['society_name']; 
        }
        ?>
    </td>
    <?php
   
           echo "<td>" . htmlspecialchars($row['month_year']) . "</td>";
echo "<td>" . htmlspecialchars($row['casa']) . "</td>";
echo "<td>" . htmlspecialchars($row['fixed_dep']) . "</td>";
echo "<td>" . htmlspecialchars($row['monthly_inc_sch']) . "</td>";
echo "<td>" . htmlspecialchars($row['new_fd']) . "</td>";
echo "<td>" . htmlspecialchars($row['dam_did']) . "</td>"; 
echo "<td>" . htmlspecialchars($row['dam_dup']) . "</td>";
echo "<td>" . htmlspecialchars($row['rd']) . "</td>";
echo "<td>" . htmlspecialchars($row['pigmy']) . "</td>";
echo "<td>" . htmlspecialchars($row['ladki_l']) . "</td>";
echo "<td>" . htmlspecialchars($row['security_dep']) . "</td>";
echo "<td>" . htmlspecialchars($row['locker_dep']) . "</td>";
echo "<td>" . htmlspecialchars($row['total']) . "</td>";
echo "<td>" . htmlspecialchars($row['total_incr_m']) . "</td>";
echo "<td>" . htmlspecialchars($row['total_incr_perct']) . "</td>";
echo "<td>" . htmlspecialchars($row['monthly_incr']) . "</td>";
echo "<td>" . htmlspecialchars($row['monthly_incr_perct']) . "</td>";
echo "<td>" . htmlspecialchars($row['vision_per_m']) . "</td>";
echo "<td>" . htmlspecialchars($row['vision_gap']) . "</td>";
echo "<td>" . htmlspecialchars($row['casa_per']) . "</td>";
echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";

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
