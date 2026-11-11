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

                     <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#exampleLargeModal">+ Add New Bank</button>
                     <div class="modal fade" id="exampleLargeModal" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Add Bank</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                              <form class="row g-3" action="add_bank1.php" method="post" enctype="multipart/form-data">
                                   <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Society Name</label>
                                     <input type="text" class="form-control" id="inputFirstName" name="society_name" required>
                                  </div>
                                  <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Address</label>
                                     <input type="text" class="form-control" id="inputFirstName" name="address" required>
                                     
                                  </div>
                                 
                                  <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Date Of Society Anniversary</label>
                                     <input type="date" class="form-control" id="inputFirstName" name="doa" required>
                                  </div>
                                    <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Society Age</label>
                                     <input type="text" class="form-control" id="inputFirstName" name="society_age" required>
                                  </div>
                                    <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Chairman Name</label>
                                     <input type="text" class="form-control" id="inputFirstName" name="chairman_name" required>
                                     
                                  </div>
                                   <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Chairman Mobile No</label>
                                     <input type="number" class="form-control" id="inputFirstName" name="mobile_no" required>
                                  </div>
                                    <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Vice Chairman Name</label>
                                     <input type="text" class="form-control" id="inputFirstName" name="v_chairman_name" required>
                                     
                                  </div>
                                   <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Vice Chairman Mobile No</label>
                                     <input type="number" class="form-control" id="inputFirstName" name="v_mob" required>
                                  </div>
                                    <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">CEO/ MD Name</label>
                                     <input type="text" class="form-control" id="inputFirstName" name="ceo" required>
                                     
                                  </div>
                                   <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Society Co-ordinator Name</label>
                                     <input type="text" class="form-control" id="inputFirstName" name="co_ordinator_name" required>
                                  </div>
                                    <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Co-ordinator Mobile No</label>
                                     <input type="number" class="form-control" id="inputFirstName" name="co_ordinator_mob" required>
                                     
                                  </div>
                                  <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Total No Of Branches</label>
                                     <input type="number" class="form-control" id="inputFirstName" name="branches" required>
                                  </div>
                                    <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Total No Of Directors</label>
                                     <input type="number" class="form-control" id="inputFirstName" name="directors" required>
                                     
                                  </div>
                                  <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Total No Of Employee</label>
                                     <input type="number" class="form-control" id="inputFirstName" name="employee" required>
                                     
                                  </div>
                                  <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Total No Of Pigmy Agent</label>
                                     <input type="number" class="form-control" id="inputFirstName" name="pigmy" required>
                                     
                                  </div>
                                  <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Total No Of Deposite A/C Holder</label>
                                     <input type="number" class="form-control" id="inputFirstName" name="deposite" required>
                                     
                                  </div>
                                  <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Total No Of Loan A/C Holder</label>
                                     <input type="number" class="form-control" id="inputFirstName" name="loan" required>
                                     
                                  </div>
                                  <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Total No Of Share Holder Members</label>
                                     <input type="number" class="form-control" id="inputFirstName" name="holder_member" required>
                                     
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
                     <th>Society Name</th>
                     <th>Address</th>
                     <th>Date Of Society Anniversary</th>
                     <th>Chairman Name</th>
                <th>Chairman Mobile</th>
                <th>Vice Chairman Name</th>
                <th>Vice Chairman Mobile No</th>
                <th>CEO /MD Name</th>
                <th>Society Co-ordinator Name</th>
                <th>Coordinator Mob</th>
                <th>Total No Of Branches</th>
                <th>Total No Of Directors</th>
                <th>Total No Of Employees</th>
                <th>Total No Of Pigmy</th>
                <th>Total No Of Deposit</th>
                <th>Total No Of Loan</th>
                <th>Total No Of Share Holders</th>
                <th>Date</th>
                   </thead>
                 <tbody>
                   <?php
            $sql = "SELECT * FROM society_info ORDER BY id DESC";
            $result = mysqli_query($link, $sql);
            $sr = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $sr++ . "</td>";
                echo "<td>" . htmlspecialchars($row['society_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                echo "<td>" . htmlspecialchars($row['doa']) . "</td>";
                echo "<td>" . htmlspecialchars($row['chairman_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['mobile_no']) . "</td>";
                echo "<td>" . htmlspecialchars($row['v_chairman_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['v_chairman_mobile']) . "</td>";
                echo "<td>" . htmlspecialchars($row['ceo']) . "</td>";
                echo "<td>" . htmlspecialchars($row['co_ordinator_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['co_ordinator_mob']) . "</td>";
                echo "<td>" . $row['branches'] . "</td>";
                echo "<td>" . $row['directors'] . "</td>";
                echo "<td>" . $row['employee'] . "</td>";
                echo "<td>" . $row['pigmy'] . "</td>";
                echo "<td>" . $row['deposite'] . "</td>";
                echo "<td>" . $row['loan'] . "</td>";
                echo "<td>" . $row['holder_member'] . "</td>";
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