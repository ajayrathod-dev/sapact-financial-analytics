<?php

include('header.php');
date_default_timezone_set('Asia/Kolkata'); // Set to your timezone
$currentDateTime = date("Y-m-d H:i:s"); // Format: 2025-06-09 11:45:00
	

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
<script>
  function myfunction(str, str1) {
    

    alert("DO YOU WANT TO CHANGE STAUTS");

    if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp = new XMLHttpRequest();
    } else { // code for IE6, IE5
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        document.getElementById("prog_show").innerHTML = xmlhttp.responseText;
        location.reload();

      }
    }
    xmlhttp.open("GET", "show_program.php?uid=" + str + "&status=" + str1, true);
    xmlhttp.send();
  }
</script>
<div class="page-wrapper">
   <div class="page-content">

                     <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#exampleLargeModal">+ Add Bank Staff</button>
                     <div class="modal fade" id="exampleLargeModal" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Add Bank Staff</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                              <form class="row g-3" action="bank_officials1.php" method="post" enctype="multipart/form-data">
                                   <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Bank Name</label>
                                     <select class="form-control" id="inputFirstName" name="bank_name" required>
                                        <option value="">--Select--</option>
                                           <?php 
                                               $sql1 = "SELECT * FROM society_info  ORDER BY id DESC";
                                               $result1 = mysqli_query($link, $sql1);
                                               if ($result1->num_rows > 0) {
                                                   while ($usr1 = mysqli_fetch_array($result1)) {
                                                       echo "<option>{$usr1['society_name']}</option>";
                                                   }
                                               }
                                           ?>
                                     </select>     
                                   </div>
                                  <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Name Of Person</label>
                                     <input type="text" class="form-control" id="inputFirstName" name="name_of_person" required>
                                     
                                  </div>
                                 
                                  <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Contact No 1</label>
                                     <input type="number" class="form-control" id="inputFirstName" name="contact1" required>
                                  </div>
                                    <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Contact No 2</label>
                                     <input type="number" class="form-control" id="inputFirstName" name="contact2" required>
                                  </div>
                                   
                                   <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Post</label>
                                     <input type="text" class="form-control" id="inputFirstName" name="post" required>
                                  </div>
                                   
                                 
                                  <div class="col-12" align="center">
                                    <button type="submit" class="btn btn-outline-primary px-5">Submit</button>
                                  </div>
                                </form>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                          
                          </div>
                        </div>
                      </div>
                    </div>
                   

      <div class="card">
         <div class="card-body">
            <div class="table-responsive">
               <table id="example2" class="table table-striped table-bordered">
                   <thead>
                      <th>Sr. No</th>
                     <th>Bank Name</th>
                     <th>Name Of Person</th>
                     <th>Contact No 1</th>
                     <th>Contact No 2</th>
                     <th>Post</th>
                     <th>By</th>
                     <th>Date</th>
                   </thead>
                 <tbody>
                   <?php
            $sql = "SELECT * FROM  bank_officials ORDER BY id DESC";
            $result = mysqli_query($link, $sql);
            $sr = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $sr++ . "</td>";
                echo "<td>" . htmlspecialchars($row['bank_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name_of_person']) . "</td>";
                echo "<td>" . htmlspecialchars($row['contact1']) . "</td>";
                echo "<td>" . htmlspecialchars($row['alt_contact2']) . "</td>";
                echo "<td>" . htmlspecialchars($row['post']) . "</td>";
                echo "<td>" . htmlspecialchars($row['by']) . "</td>";
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
<?php
   include('footer.php');
?>