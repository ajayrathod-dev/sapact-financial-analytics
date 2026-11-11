<?php
include('header.php');
date_default_timezone_set('Asia/Kolkata');
$currentDateTime = date("Y-m-d H:i:s");

?>
<div class="page-wrapper">
  <div class="page-content">
                        
      <div class="card">
         <div class="card-body">
            <div class="table-responsive">
               <table id="example2" class="table table-striped table-bordered">
                   <thead>
                      <th>Sr. No</th>
                      <th>ताळेबंदातील मालमत्ता/जिंदगी घटक</th>
                      <th>Balancesheet</th>

                      <th>जोखीम भार (रिस्क वेटेज)टक्के</th>
                      
                      <th>Date</th>
                      <th>Action</th>
                   </thead>
                 <tbody>
                   <?php
            $sql = "SELECT * FROM crar_report ORDER BY id ASC";
            $result = mysqli_query($link, $sql);
            $sr = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $sr++ . "</td>"; 
               
                echo "<td>" . htmlspecialchars($row['taleband']) . "</td>";
                   echo "<td>" . htmlspecialchars($row['taleband1']) . "</td>";
                echo "<td>" . htmlspecialchars($row['percentage']) . "</td>";
                echo "<td>" . $row['date'] . "</td>";
                ?>
              
                <td><a href="edit_crar_report.php?id=<?php echo $row['id']; ?>">Edit</a></td>
               </tr>
               <?php
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
