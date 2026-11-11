<?php
include('header.php');
date_default_timezone_set('Asia/Kolkata'); 
$currentDateTime = date("Y-m-d H:i:s");

$categories = ["व्यवसाय कर्ज","गृह तारण कर्ज","ठेव तारण कर्ज","दसरा दिवाळी ठेव कर्ज","वाहण तारण कर्ज","कर्मचारी कर्ज","सोने तारण कर्ज","महिला बचत गट कर्ज (SHG)","श्री निनाईदेवी तात्काळ कर्ज","घरतारण कर्ज (Staff)","चॉईस कर्मचारी गृह तारण कर्ज","श्री गणेश लखपती कर्ज","घरतारण कर्ज (इ एम आय )","Personal Loane [ Emi ]","Motrage Loan [ Emi ]","Houseing Loan(Emi)","PERSONAL LOANE [ JLG ]","MOTRAGE LOAN","LOAN AGAINST DAILY","LOAN AGAINST RECURRING","CHOISE GOLD LOAN (1141)","CHOISE MOTRAGE LOAN (EMI) (1157)","CHOISE Houseing Loan(Emi) (1176)"]; 

?>
<div class="page-wrapper">
  <div class="page-content">
    <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#exampleFullScreenModal">+ Loans/Advances</button>

    <div class="modal fade" id="exampleFullScreenModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Loans/Advances</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form method="POST" action="loan_advances1.php">
                  <div class="row g-3 mb-4" >
                                   <div class="col-md-6">
                                    <label for="inputFirstName" class="form-label">Bank Name</label>
                                     <select class="form-control" id="inputFirstName" name="bank" required>
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
                                    <label for="inputFirstName" class="form-label">Month & Year</label>
                                     <input type="month" name="month" class="form-control" required>
                                     
                                  </div>
                     </div>  
              <div class="table-responsive">
                <table id="balanceSheetTable" class="table table-bordered align-middle">
                  <thead class="table-light">
                    <tr>
                     
                      <th>Loans/Advances</th>
                      <th>Acc.No Standard</th>
                      <th>Amount Standard</th>
                      <th>Acc.No Sub-Standard</th>
                      <th>Amount Sub-Standard</th>
                      <th>Acc.No Doubtful-1</th>
                      <th>Amount Doubtful-1</th>
                      <th>Acc.No Doubtful-2</th>
                      <th>Amount Doubtful-2</th>
                      <th>Acc.No Doubtful-3</th>
                      <th>Amount Doubtful-3</th>
                      <th>Acc.No Total</th>
                      <th>Amount Total</th>
                      
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                       
                      
                      <td>
                        <select name="category[]" class="form-select categorySelect" onchange="toggleOtherInput(this, 'categoryOtherInput')">
                          <option value="">-- Select --</option>
                          <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat ?>"><?= $cat ?></option>
                          <?php endforeach; ?>
                          <option value="__other__">Other</option>
                        </select>
                        <input type="text" name="category_other[]" class="form-control mt-2 categoryOtherInput" style="display:none;" placeholder="Enter new category">
                      </td>
                      <td><input type="number" name="no_of_accounts_std[]" class="form-control"></td>
                      <td><input type="number" step="0.01" name="amount_std[]" class="form-control"></td>
                      
                      <td><input type="number" name="no_of_accounts_sub[]" class="form-control"></td>
                      <td><input type="number" step="0.01" name="amount_sub[]" class="form-control"></td>
                      <td><input type="number" name="no_of_accounts_dobt1[]" class="form-control"></td>
                      <td><input type="number" step="0.01" name="amount_dobt1[]" class="form-control"></td>
                      <td><input type="number" name="no_of_accounts_dobt2[]" class="form-control"></td>
                      <td><input type="number" step="0.01" name="amount_dobt2[]" class="form-control"></td>
                      <td><input type="number" name="no_of_accounts_dobt3[]" class="form-control"></td>
                      <td><input type="number" step="0.01" name="amount_dobt3[]" class="form-control"></td>
                      <td><input type="number" name="no_of_accounts_tot[]" class="form-control"></td>
                      <td><input type="number" step="0.01" name="amount_tot[]" class="form-control"></td>
                      <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="d-flex justify-content-between mt-3">
                <button type="button" class="btn btn-primary" onclick="addRow()">+ Add Row</button>
                <input type="submit" value="Submit" class="btn btn-success">
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
                      <th>Month & Year</th>
                      <th>Loans/Advances</th>
                      <th>Acc.No Standard</th>
                      <th>Amount Standard</th>
                      <th>Acc.No Sub-Standard</th>
                      <th>Amount Sub-Standard</th>
                      <th>Acc.No Doubtful-1</th>
                      <th>Amount Doubtful-1</th>
                      <th>Acc.No Doubtful-2</th>
                      <th>Amount Doubtful-2</th>
                      <th>Acc.No Doubtful-3</th>
                      <th>Amount Doubtful-3</th>
                      <th>Acc.No Total</th>
                      <th>Amount Total</th>
                      <th>Date</th>
                   </thead>
                 <tbody>
                   <?php
            $sql = "SELECT * FROM  loans_advances ORDER BY id DESC";
            $result = mysqli_query($link, $sql);
            $sr = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $sr++ . "</td>"; 
                
                // Bank name
                echo "<td>";
                $bname = $row['bank_name'];
                $sql120 = "SELECT * FROM society_info WHERE id='$bname' ORDER BY id DESC";
                $result120 = mysqli_query($link, $sql120);
                while ($row120 = mysqli_fetch_assoc($result120)) {
                    echo htmlspecialchars($row120['society_name'] ?? '');
                }
                echo "</td>";
                
                // Other fields with null checks
                echo "<td>" . htmlspecialchars($row['month'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['category'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['no_of_accounts_std'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['amount_std'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['no_of_accounts_sub'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['amount_sub'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['no_of_accounts_dobt1'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['amount_dobt1'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['no_of_accounts_dobt2'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['amount_dobt2'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['no_of_accounts_dobt3'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['amount_dobt3'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['no_of_accounts_tot'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['amount_tot'] ?? '') . "</td>";
                echo "<td>" . ($row['created_at'] ?? '') . "</td>";
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

<script>
function addRow() {
  const tableBody = document.querySelector("#balanceSheetTable tbody");
  const firstRow = tableBody.rows[0];
  const newRow = firstRow.cloneNode(true);

  // Clear inputs in cloned row
  newRow.querySelectorAll('input').forEach(input => input.value = '');
  newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
  newRow.querySelectorAll('.categoryOtherInput').forEach(el => el.style.display = 'none');

  tableBody.appendChild(newRow);
}

function removeRow(button) {
  const row = button.closest('tr');
  const tableBody = document.querySelector("#balanceSheetTable tbody");
  if (tableBody.rows.length > 1) row.remove();
}

function toggleOtherInput(selectEl, className) {
  const row = selectEl.closest('tr');
  const otherInput = row.querySelector('.' + className);
  if (selectEl.value === '__other__') {
    otherInput.style.display = 'block';
  } else {
    otherInput.style.display = 'none';
  }
}
</script>

<?php include('footer.php'); ?>