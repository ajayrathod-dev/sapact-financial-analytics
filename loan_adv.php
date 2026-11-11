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
                                 <form action= "loan_adv_action.php" class="row g-3"  method="get" enctype="multipart/form-data">
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
                      <th>MERCHANT LOAN</th>
                      <th>GOLD LOAN</th>
                      <th>SPECIAL GOLD LOAN</th>   
                     <th>INSTANT LOAN</th>
                      <th>EMPLOYEE LOAN</th>
                      <th>MAHILA SAMRUDDHI LOAN </th>
                      <th>MORGAGE LOAN</th>
                      <th>RD OD LOAN</th>                   
                      <th>FD CC</th>
                      <th>SALARY</th>
                      <th>TOTAL</th>
                      <th>TOTAL INCRESE</th>
                      <th>IN %</th>
                      <th>Monthly Increase</th>
                      <th>Monthly Increase In % </th>
                      <th>Vision Per Month </th>
                      <th>Vision GAP</th>

                      <th>CREATED AT</th>
                   </thead>
                 <tbody>

                 <?php
$sql = "SELECT * FROM loans_adv ORDER BY id DESC LIMIT 500";
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
            echo htmlspecialchars($row120['society_name']); 
        }
        ?>
    </td>
    <?php
    echo "<td>" . htmlspecialchars($row['month_year']) . "</td>";
    echo "<td>" . htmlspecialchars($row['merchant_l']) . "</td>";
    echo "<td>" . htmlspecialchars($row['gold_l']) . "</td>";
    echo "<td>" . htmlspecialchars($row['special_gl']) . "</td>";
    echo "<td>" . htmlspecialchars($row['inst_l']) . "</td>";
    echo "<td>" . htmlspecialchars($row['emp_l']) . "</td>";
    echo "<td>" . htmlspecialchars($row['mahila_l']) . "</td>";
    echo "<td>" . htmlspecialchars($row['morg_l']) . "</td>";
    echo "<td>" . htmlspecialchars($row['rd_od']) . "</td>";
    echo "<td>" . htmlspecialchars($row['fd_cc_l']) . "</td>";
    echo "<td>" . htmlspecialchars($row['salary_l']) . "</td>";
    echo "<td>" . htmlspecialchars($row['total']) . "</td>";
    echo "<td>" . htmlspecialchars($row['total_incr']) . "</td>";
    echo "<td>" . htmlspecialchars($row['in_per']) . "</td>";
    echo "<td>" . htmlspecialchars($row['monthly_incr']) . "</td>";
    echo "<td>" . htmlspecialchars($row['monthly_incr_perct']) . "</td>";
    echo "<td>" . htmlspecialchars($row['vision_per_m']) . "</td>";
    echo "<td>" . htmlspecialchars($row['vision_gap']) . "</td>";

    echo "<td>" . htmlspecialchars($row['created_at']) . "</td>"; // added
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
