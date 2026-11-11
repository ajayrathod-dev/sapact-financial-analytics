<?php

include('header.php');
date_default_timezone_set('Asia/Kolkata'); // Set to your timezone
$currentDateTime = date("Y-m-d H:i:s"); // Format: 2025-06-09 11:45:00

$id=$_GET['id'];
 $sql = "SELECT * FROM crar_report WHERE id='$id'";
            $result = mysqli_query($link, $sql);
            $sr = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                
                      $per=$row['percentage'];
                      $taleband=$row['taleband'];
                      $taleband1=$row['taleband1'];
            }
            
        

?>

<style>
  .switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
  }

  .switch input {
    display: none;
  }

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
  }

  input:checked+.slider {
    background-color: #2196F3;
  }

  input:focus+.slider {
    box-shadow: 0 0 1px #2196F3;
  }

  input:checked+.slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
  }

  /* Rounded sliders */
  .slider.round {
    border-radius: 34px;
  }

  .slider.round:before {
    border-radius: 50%;
  }
</style>

<div class="page-wrapper">
   <div class="page-content">


      <div class="card">
         <div class="card-body">
             <form class="row g-3" action="edit_crar_report1.php" method="post" enctype="multipart/form-data">
                                   <div class="col-md-12">
                                    <label for="inputFirstName" class="form-label">टक्के (Percentage)</label>
                                     <input type="text" class="form-control" id="inputFirstName" name="per" required value="<?php echo $per; ?>">
                                      <input type="text" class="form-control" id="inputFirstName" name="tal" required value="<?php echo $taleband; ?>">
                                       <input type="text" class="form-control" id="inputFirstName" name="tal1" required value="<?php echo $taleband1; ?>">
                                     <input type="hidden" class="form-control" id="inputFirstName" name="id" required value="<?php echo $id; ?>">
                                  </div>
                                
                                 
                                  <div class="col-12" align="center">
                                    <button type="submit" class="btn btn-outline-primary px-5">Submit</button>
                                  </div>
                                </form>
         </div>
      </div>
   </div>
  </div>
<?php
   include('footer.php');
?>