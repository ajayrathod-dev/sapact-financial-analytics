<?php
include('header.php');
date_default_timezone_set('Asia/Kolkata');
$currentDateTime = date("Y-m-d H:i:s");

$bank_name=$_GET['bank_name'];
$month=$_GET['month'];
?>
<div class="page-wrapper">
  <div class="page-content">
                              <div class="card">
                               <div class="card-body">
                                 <form class="row g-3"  method="get" enctype="multipart/form-data">
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
                                    <button type="submit" class="btn btn-outline-primary px-5">Serach</button>
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
                      <th>Share Capital</th>
                      <th>Reserve Fund</th>
                      <th>Building Fund</th>
                      <th>Dividend Fiuctuation Fund</th>
                      <th>Investment Fiuctuation Fund</th>
                      <th>Bad and Doubtful Debts Provision</th>
                      <th>Profit</th>
                      <th>Loss</th>
                      <th>Total</th>
                      <th>Total Networth</th>
                      <th>Date</th>
                   </thead>
                 <tbody>
                   <?php
            $sql = "SELECT * FROM networth_camales WHERE bank_name LIKE '%$bank_name%' AND month_year LIKE '%$month%'ORDER BY id DESC";
            $result = mysqli_query($link, $sql);
            $sr = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $sr++ . "</td>"; ?>
                <td><?php $bname= $row['bank_name'];
                
                
                            $sql120 = "SELECT * FROM society_info WHERE id='$bname' ORDER BY id DESC";
                            $result120 = mysqli_query($link, $sql120);
          
                            while ($row120 = mysqli_fetch_assoc($result120)) {
                       
                                echo $row120['society_name']; 
                        
                        
                          }
                         
                      
                
                ?></td>
                <?php
                echo "<td>" . htmlspecialchars($row['month_year']) . "</td>";
                echo "<td>" . htmlspecialchars($row['share_capital']) . "</td>";
                echo "<td>" . htmlspecialchars($row['reserve_fund']) . "</td>";
                echo "<td>" . htmlspecialchars($row['building_fund']) . "</td>";
                echo "<td>" . htmlspecialchars($row['dividand_fund']) . "</td>";
                echo "<td>" . htmlspecialchars($row['investment_fund']) . "</td>";
                echo "<td>" . htmlspecialchars($row['bad_dbt']) . "</td>";
                echo "<td>" . htmlspecialchars($row['current_profit']) . "</td>";
                echo "<td>" . htmlspecialchars($row['total_loss']) . "</td>";
                echo "<td>" . htmlspecialchars($row['total_profit']) . "</td>";
                echo "<td>" . htmlspecialchars($row['networth']) . "</td>";
              
                echo "<td>" . $row['date'] . "</td>";
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
