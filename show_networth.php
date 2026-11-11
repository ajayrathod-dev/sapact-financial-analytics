<?php
error_reporting(0);
ini_set('display_errors', 0);
include('header.php');
date_default_timezone_set('Asia/Kolkata');
$currentDateTime = date("Y-m-d H:i:s");

$bank_name = isset($_GET['bank_name']) ? $_GET['bank_name'] : '';
$month = isset($_GET['month']) ? $_GET['month'] : '';
?>
<div class="page-wrapper">
  <div class="page-content">
                              <div class="card">
                               <div class="card-body">
                                 <form class="row g-3"  method="get" enctype="multipart/form-data">
                                   <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Bank Name</label>
                                      <select class="form-control" id="inputFirstName" name="bank_name">
                                        <option value="">--Select--</option>
                                           <?php 
                                               $sql1 = "SELECT * FROM society_info  ORDER BY id DESC";
                                               $result1 = mysqli_query($link, $sql1);
                                               if ($result1 && $result1->num_rows > 0) {
                                                   while ($usr1 = mysqli_fetch_array($result1)) {
                                                       $selected = ($usr1['id'] == $bank_name) ? 'selected' : '';
                                                       echo "<option value='{$usr1['id']}' $selected>{$usr1['society_name']}</option>";
                                                   }
                                               }
                                           ?>
                                     </select>     
                                  </div>
                                  <div class="col-md-6">
                                    <label for="inputMonth" class="form-label">Month & Year</label>
                                    <input type="month" id="inputMonth" name="month" class="form-control" value="<?php echo htmlspecialchars($month); ?>">
                                     
                                  </div>
                                
                                 
                                  <div class="col-12" align="center">
                                    <button type="submit" class="btn btn-outline-primary px-5">Search</button>
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
                      <th>Bankのための Name</th>
                      <th>Month & Year</th>
                      <th>Share Capital</th>
                      <th>Reserve Fund</th>
                      <th>Building Fund</th>
                      <th>Free Reserve</th>
                      <th>Standard Assets Provision</th>
                      <th>Dividend Fluctuation</th>
                      <th>Profit And Loss</th>
                      <th>Loss</th>
                      <th>Total A</th>
                      <th>Total B</th>
                      <th>Total Networth</th>
                      <th>Date</th>
                   </thead>
                 <tbody>
                   <?php
            $search_bank = $bank_name ? mysqli_real_escape_string($link, $bank_name) : '';
            $search_month = $month ? mysqli_real_escape_string($link, $month) : '';
            $sql = "SELECT * FROM networth WHERE bank_name LIKE '%$search_bank%' AND month_year LIKE '%$search_month%' ORDER BY id DESC";
            $result = mysqli_query($link, $sql);
            $sr = 1;
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>";
                    ?>
                    <td><?php 
                    $bname = $row['bank_name'];
                    $sql120 = "SELECT society_name FROM society_info WHERE id = '" . mysqli_real_escape_string($link, $bname) . "' LIMIT 1";
                    $result120 = mysqli_query($link, $sql120);
                    $bankDisplay = 'Unknown';
                    if ($result120 && $row120 = mysqli_fetch_assoc($result120)) {
                        $bankDisplay = htmlspecialchars($row120['society_name']);
                    }
                    echo $bankDisplay;
                    ?></td>
                    <?php
                    echo "<td>" . htmlspecialchars($row['month_year']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['share_capital']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['reserve_fund']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['building_fund']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['free_reserve']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['standard_assets']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['dividand_fund']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['gross_profit']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['gross_loss']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['total_a']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['total_b']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['networth']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                    echo "</tr>";
                }
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