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
                                 <form action= "cd_month_action.php" class="row g-3"  method="get" enctype="multipart/form-data">
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
                                    <label for="inputFirstName" class="form-label">Month-Year</label>
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
                      <th>TOTAL NET WORTH</th>
                      <th>A</th>
                      <th>ASSETS</th>   
                     <th>INVESTMENT IN SHARE</th>
                      <th>RESERVE FUND</th>
                      <th>B  </th>
                      <th>A-B  </th>
                      <th> TOTAL LOAN </th>
                      <th> TOTAL DEPOSIT </th>
                      <th> C.D. RATIO </th>
                      <th> If C.D.Ratio High Calculate For 1 % </th>
                      <th>Between 65 to 70 %  </th>
                      <th>How Many Deposit COLLECTED</th>
                      <th>If C.D.Ratio Low Calculate For 1 %  </th>
                      <th>Between 65 to 70 %  </th>
                      <th>How Many Loan Distribution </th>
                      <th>Earning C.D.Ratio</th>
                      <th>Average CD Ratio</th>                   
                      <th>CREATED AT</th>
                   </thead>
                 <tbody>

                   <?php
            $sql = "SELECT * FROM cd_ratio_end ORDER BY id DESC limit 500";
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
echo "<td>" . htmlspecialchars($row['networth_b']) . "</td>";
echo "<td>" . htmlspecialchars($row['a']) . "</td>";
echo "<td>" . htmlspecialchars($row['assets']) . "</td>";
echo "<td>" . htmlspecialchars($row['invest_in_shares']) . "</td>";
echo "<td>" . htmlspecialchars($row['reserve_fund']) . "</td>"; // moved here
echo "<td>" . htmlspecialchars($row['b']) . "</td>";
echo "<td>" . htmlspecialchars($row['a_b']) . "</td>";
echo "<td>" . htmlspecialchars($row['total_loan']) . "</td>";
echo "<td>" . htmlspecialchars($row['total_deposit']) . "</td>";
echo "<td>" . htmlspecialchars($row['cd_ratio']) . "</td>";
echo "<td>" . htmlspecialchars($row['cd_high_1_perc']) . "</td>";
echo "<td>" . htmlspecialchars($row['cd_ratio_minus_65']) . "</td>";
echo "<td>" . htmlspecialchars($row['collected_deposit']) . "</td>";
echo "<td>" . htmlspecialchars($row['cd_low_1perc']) . "</td>";
echo "<td>" . htmlspecialchars($row['betwn_65_70']) . "</td>";
echo "<td>" . htmlspecialchars($row['loan_destribution']) . "</td>";
echo "<td>" . htmlspecialchars($row['earning_cd']) . "</td>";
echo "<td>" . htmlspecialchars($row['average_cd']) . "</td>";
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


<?php include('footer.php');
?>
