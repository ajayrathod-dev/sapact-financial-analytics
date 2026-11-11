<?php
include('header.php');
date_default_timezone_set('Asia/Kolkata');
$currentDateTime = date("Y-m-d H:i:s");

?>
<div class="page-wrapper">
  <div class="page-content">
     <div class="card">
        <div class="card-body">
            <form action="crar_report_log.php" class="row g-3" method="get" enctype="multipart/form-data">
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
                      <th>Month</th>
                      <th>ताळेबंदातील मालमत्ता/जिंदगी घटक</th>
                      <th>जोखीम भार(रिस्क वेटेज)टक्के </th>
                      <th>येणेबाकी शिल्लक रक्कम रुपये</th>
                      <th>जोखीम भारीत मालमत्ता/ जिंदगी मूल्य</th>
                      
                      <th>Date</th>
                      
                   </thead>
                 <tbody>
                  <?php
$sql = "SELECT * FROM crar_report_log ORDER BY id ASC";
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
    echo "<td>" . htmlspecialchars($row['month']) . "</td>";
    echo "<td>" . htmlspecialchars($row['taleband']) . "</td>";
    echo "<td>" . htmlspecialchars($row['percent']) . "</td>";
    echo "<td>" . htmlspecialchars($row['yene_baki']) . "</td>";
    echo "<td>" . htmlspecialchars($row['jokhim']) . "</td>";
    ?>
    <td><?= $row['date']; ?></td>
    </tr>
    <?php
} // 👈 close while loop properly here
?>

<!-- Now print total row after the loop -->
<tr>
   <td></td>
   <td><b>एकूण जोखीम भारीत मालमत्ता /जिंदगी</b></td>
   <td></td>
   <td></td>
   <td></td>
   <td><b>
   <?php
   $sql1w = "SELECT SUM(yene_baki) AS total_baki FROM crar_report_log";
   $result1w = mysqli_query($link, $sql1w);
   if ($result1w) {
       $row1w = mysqli_fetch_assoc($result1w);
       $total_baki1w = $row1w['total_baki'] ?? 0;
       echo $total_baki1w;
   }
   ?>
   </b></td>
   <td><b>
   <?php
   $sql1w1 = "SELECT SUM(jokhim) AS total_baki11 FROM crar_report_log";
   $result1w1 = mysqli_query($link, $sql1w1);
   if ($result1w1) {
       $row1w1 = mysqli_fetch_assoc($result1w1);
       $total_baki1w1 = $row1w1['total_baki11'] ?? 0;
       echo $total_baki1w1;
   }
   ?>
   </b></td>
   <td></td>
</tr>

                 </tbody>

               </table>
            </div>
         </div>
      </div>
    
    
    
  </div>
</div>


<?php include('footer.php'); ?>
