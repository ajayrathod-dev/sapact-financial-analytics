<?php
include('header.php');
date_default_timezone_set('Asia/Kolkata');
$currentDateTime = date("Y-m-d H:i:s");

?>
<div class="page-wrapper">
  <div class="page-content">
    <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#exampleFullScreenModal">+ Profit & Loss A/C</button>

    <div class="modal fade" id="exampleFullScreenModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Profit & Loss A/C</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form method="GET" action="profit_loss_form1.php">
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
                      
                      <th>Side</th>
                      <th>Category</th>
                      <th>Subcategory</th>
                      <th>Sub-subcategory</th>
                      <th>Amount</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      
                      <td>
                        <select name="side[]" class="form-select">
                              <option value="">-- Select --</option>
                          <option value="Expenses">Expenses</option>
                           <option value="Income">Income</option>
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
                      <th>Amount</th>
                      <th>Date</th>
                   </thead>
                 <tbody>
                   <?php
            $sql = "SELECT * FROM profit_loss ORDER BY id DESC";
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
 const categoryMap = {
      Expenses: ["INTEREST PAID ON DEPOSIT","INTEREST PAID ON BORROWINGS","MANAGEMENT EXPENSES","ALL MANAGEMENT EXPENCES","PROVISIONS","PROFIT"],
      Income: ["INTEREST RECEIVED ON LOAN", "INTEREST RECEIVED ON INVESTMENTS", "INTEREST RECEIVED ON SAVING A/Cs", "OTHER INCOME", "LOSS"]
    };

const subcategoryMap = {
  "INTEREST PAID ON DEPOSIT": ["CASA DEPOSIT", "Term Deposit"],
  
  "MANAGEMENT EXPENSES": [
    "EMPLOYEE EXP / ASTHAPANA EXP",
    "SALARY AND ALLOWANCES - STAFF",
    "BONUS",
    "STAFF PF",
    "EMPLOYEE EXP"
  ],

  "PROVISIONS": [
    "Bonus/Gratuity",
    "Bad & Doubt",
    "Over Due Interest",
    "Standard Assets",
    "Other"
  ]
};

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


 document.addEventListener("change", function (e) {
      if (e.target && e.target.matches("select[name='side[]']")) {
        const row = e.target.closest('tr');
        const categorySelect = row.querySelector('.categorySelect');
        const subcategorySelect = row.querySelector('.subcategorySelect');
        const selectedSide = e.target.value;

        categorySelect.innerHTML = `<option value="">-- Select --</option>`;
        if (categoryMap[selectedSide]) {
          categoryMap[selectedSide].forEach(cat => {
            categorySelect.innerHTML += `<option value="${cat}">${cat}</option>`;
          });
        }
        categorySelect.innerHTML += `<option value="__other__">Other</option>`;

        subcategorySelect.innerHTML = `<option value="">-- Select --</option><option value="__other__">Other</option>`;
      }

      if (e.target && e.target.matches("select[name='category[]']")) {
        const row = e.target.closest('tr');
        const subcategorySelect = row.querySelector('.subcategorySelect');
        const selectedCategory = e.target.value;

        subcategorySelect.innerHTML = `<option value="">-- Select --</option>`;
        if (subcategoryMap[selectedCategory]) {
          subcategoryMap[selectedCategory].forEach(sub => {
            subcategorySelect.innerHTML += `<option value="${sub}">${sub}</option>`;
          });
        }
        subcategorySelect.innerHTML += `<option value="__other__">Other</option>`;
      }
    });


</script>

<?php include('footer.php'); ?>
