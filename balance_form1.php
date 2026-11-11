<?php
include('header.php');
date_default_timezone_set('Asia/Kolkata');
$currentDateTime = date("Y-m-d H:i:s");

$categories = ["CASH IN HAND","CASH AT BANK","INVESTMENTS","LOANS & ADVANCES","INT.RECEIVABLE ON INVESTMENT & ADVANCES","OWN ASSETS","OTHER ASSETS/ RECEIVABLE","LOSS"];

$subcategories = [
    "DCC Bank",
    "Co-operative Bank",
    "Nationalise Bank",
    "Private Bank",
    "Nagari & Multistate Society",
    "NPA Bank",
    "Shares Investment",
    "DCC Bank Investment",
    "Co-operative Bank Investment",
    "Nationalise Bank Investment",
    "Private Bank Investment",
    "Small Finance Bank Investment",
    "Nagari & Multistate Society Investment",
    "Mutual Fund Investment",
    "NPA Bank Investment",
    "Other Investment",
    "Secured Loan",
    "Unsecured Loan",
    "INTEREST RECEIVABLE ON ADVANCES",
    "INTEREST RECEIVABLE ON INVESTMENT",
    "LEGAL PROPERTY - LAND & BUILDING",
    "UNLEGAL PROPERTY - LAND & BUILDING",
    "FURNITURE AND FIXTURE & DEAD STOCK A/C",
    "COMPUTER HARDWARE & SOFTWARE & ELECTRONICS",
    "LEGAL - NON BANKING ASSETS - WITHIN 7 YEARS",
    "UNLEGAL NON BANKING ASSETS - ABOVE 7 YEARS",
    "भांडवली खर्च (जागा / इमारत खरेदी / बांधकाम) तसलमात / अडव्हान्स येणे, इतर येणे, रक्कम आगाऊ रक्कम ६ महिन्यापेक्षा कमी प्रलंबित असल्यास",
    "शिल्लक स्टेशनरी",
    "टीडीएस, सुरक्षा ठेव, जीएसटी, आयकर येणे रकमा",
    "शाखा जुळवणी निव्वळ येणे रक्कम",
    "LAST YEAR",
    "CURRENT YEAR"
];

?>

<div class="page-wrapper">
  <div class="page-content">
    <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#exampleFullScreenModal">+ Assets Balance Sheet</button>

    <div class="modal fade" id="exampleFullScreenModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Assets Balance Sheet</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form method="POST" action="balance_submit1.php">
              <div class="table-responsive">
                <table id="balanceSheetTable" class="table table-bordered align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Bank Name</th>
                      <th>Month & Year</th>
                      <th>Side</th>
                      <th>Category</th>
                      <th>Subcategory</th>
                      <th>Sub-subcategory</th>
                      <th>No of Accounts</th>
                      <th>Amount</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                        <td>
                        <select name="bank" class="form-select" required>
                            <option>--Select--</option>
                            <?php
                            
                            $sql12 = "SELECT * FROM society_info ORDER BY id DESC";
                            $result12 = mysqli_query($link, $sql12);
          
                            while ($row12 = mysqli_fetch_assoc($result12)) {
                       
                              ?>
               
                                 <option value="<?= $row12['id']; ?>"><?= $row12['society_name']; ?></option>
                         <?php
                        
                          }
                         
                        ?>
                        </select>
                       
                      </td>
                       <td><input type="month" name="month" class="form-control" placeholder="Optional"></td>
                      <td>
                        <select name="side[]" class="form-select">
                           <option value="Assets">Assets</option>
                        </select>
                      </td>
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
                      <td>
                        <select name="subcategory[]" class="form-select subcategorySelect" onchange="toggleOtherInput(this, 'subcategoryOtherInput')">
                          <option value="">-- Select --</option>
                          <?php foreach($subcategories as $sub): ?>
                            <option value="<?= $sub ?>"><?= $sub ?></option>
                          <?php endforeach; ?>
                          <option value="__other__">Other</option>
                        </select>
                        <input type="text" name="subcategory_other[]" class="form-control mt-2 subcategoryOtherInput" style="display:none;" placeholder="Enter new subcategory">
                      </td>
                      <td><input type="text" name="sub_subcategory[]" class="form-control" placeholder="Optional"></td>
                      <td><input type="number" name="no_of_accounts[]" class="form-control"></td>
                      <td><input type="number" step="0.01" name="amount[]" class="form-control"></td>
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
                      <th>Side</th>
                      <th>Category</th>
                      <th>Subcategory</th>
                      <th>Sub-subcategory</th>
                      <th>No of Accounts</th>
                      <th>Amount</th>
                      <th>Date</th>
                   </thead>
                 <tbody>
                   <?php
            $sql = "SELECT * FROM  balance_sheet WHERE side='Assets' ORDER BY id DESC";
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
                 echo "<td>" . htmlspecialchars($row['month']) . "</td>";
                echo "<td>" . htmlspecialchars($row['side']) . "</td>";
                echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                echo "<td>" . htmlspecialchars($row['subcategory']) . "</td>";
                echo "<td>" . htmlspecialchars($row['sub_subcategory']) . "</td>";
                echo "<td>" . htmlspecialchars($row['no_of_accounts']) . "</td>";
                echo "<td>" . htmlspecialchars($row['amount']) . "</td>";
                echo "<td>" . $row['created_at'] . "</td>";
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
  newRow.querySelectorAll('.categoryOtherInput, .subcategoryOtherInput').forEach(el => el.style.display = 'none');

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
