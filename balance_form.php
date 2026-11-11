<?php
include('header.php');
date_default_timezone_set('Asia/Kolkata');
$currentDateTime = date("Y-m-d H:i:s");
include 'connection.php';
$categories = [
    "Authorised Share Capital","SHARE CAPITAL","ASSOCIATE SHARE CAPITAL","FUNDS AND RESERVES","DEPOSITS","BORROWINGS","OTHER LIABILITIES","BRANCH ADJUSTMENT",
    "PROFIT AND LOSS","WORKING CAPITAL","CASH IN HAND","CASH AT BANK","INVESTMENTS","LOANS & ADVANCES","INT.RECEIVABLE ON INVESTMENT & ADVANCES","OWN ASSETS","OTHER ASSETS/ RECEIVABLE","LOSS"];

$subcategories = [
    "Paid up Share Capital","Shares Anamat","RESERVE FUND","BUILDING FUND","DIVIDEND FLUCTUATION FUND","INVESTMENT FLUCTUATION FUND","STANDARD ASSETS FUND","BAD & DOUBT FUNDS (NPA FUNDS)",
    "OVERDUE INTEREST RESERVE",
    "FREE RESERVE",
    "OTHER RESERVE",
    "CASA DEPOSIT",
    "Term Deposit",
    "INT.PAYABLE FOR DEPOSIT",
    "OTHER PAYABLE",
    "LAST YEAR PROFIT",
    "CURRENT YEAR PROFIT",
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

// Initialize success message variable
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bank_name'])) {
    $bank_name = mysqli_real_escape_string($link, $_POST['bank_name']);
    $month = mysqli_real_escape_string($link, $_POST['month']);
    $submission_date = date("Y-m-d");

    $sides = $_POST['side'];
    $categories_post = $_POST['category'];
    $categories_other = $_POST['category_other'] ?? [];
    $subcategories_post = $_POST['subcategory'];
    $subcategories_other = $_POST['subcategory_other'] ?? [];
    $sub_subcategories = $_POST['sub_subcategory'] ?? [];
    $no_of_accounts_arr = $_POST['no_of_accounts'] ?? [];
    $amounts = $_POST['amount'] ?? [];
    
    $insert_count = 0;

    for ($i = 0; $i < count($sides); $i++) {
        $side = mysqli_real_escape_string($link, $sides[$i]);

        $category = ($categories_post[$i] === '__other__' && !empty($categories_other[$i])) 
            ? mysqli_real_escape_string($link, $categories_other[$i]) 
            : mysqli_real_escape_string($link, $categories_post[$i]);

        $subcategory = ($subcategories_post[$i] === '__other__' && !empty($subcategories_other[$i])) 
            ? mysqli_real_escape_string($link, $subcategories_other[$i]) 
            : mysqli_real_escape_string($link, $subcategories_post[$i]);

        $sub_subcategory = mysqli_real_escape_string($link, $sub_subcategories[$i] ?? '');
        $no_of_accounts = (int)($no_of_accounts_arr[$i] ?? 0);
        $amount = (float)($amounts[$i] ?? 0.00);

        if (!empty($side) && !empty($category)) {  // Skip empty rows
            $sql = "INSERT INTO balance_sheet (submission_date, bank_name, month, side, category, subcategory, sub_subcategory, no_of_accounts, amount) 
                    VALUES ('$submission_date', '$bank_name', '$month', '$side', '$category', '$subcategory', '$sub_subcategory', $no_of_accounts, $amount)";
            
            if (mysqli_query($link, $sql)) {
                $insert_count++;
            } else {
                // Log error instead of displaying it to users
                error_log("Balance sheet insertion error: " . mysqli_error($link));
            }
        }
    }
    
    if ($insert_count > 0) {
        $success_message = "$insert_count records inserted successfully!";
    } else {
        $success_message = "No records were inserted. Please check your input.";
    }
}

?>

<div class="page-wrapper">
  <div class="page-content">
    <?php if (!empty($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $success_message; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    
    <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#exampleFullScreenModal">+ Upload Balance Sheet</button>

    <div class="modal fade" id="exampleFullScreenModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Upload Balance Sheet</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <form method="POST" action="">
                   <div class="row g-3 mb-4" >
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
                      <th>No of Accounts</th>
                      <th>Amount</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                    
                      <td>
                        <select name="side[]" class="form-select" required>
                          <option value="">--Select--</option>
                          <option value="Liabilities">Liabilities</option>
                           <option value="Assets">Assets</option>
                          
                        </select>
                      </td>
                      <td>
                        <select name="category[]" class="form-select categorySelect" onchange="toggleOtherInput(this, 'categoryOtherInput')" required>
                          <option value="">-- Select --</option>
                          <option value="__other__">Other</option>
                        </select>
                        <input type="text" name="category_other[]" class="form-control mt-2 categoryOtherInput" style="display:none;" placeholder="Enter new category">
                      </td>
                      <td>
                        <select name="subcategory[]" class="form-select subcategorySelect" onchange="toggleOtherInput(this, 'subcategoryOtherInput')">
                          <option value="">-- Select --</option>
                          <option value="__other__">Other</option>
                        </select>
                        <input type="text" name="subcategory_other[]" class="form-control mt-2 subcategoryOtherInput" style="display:none;" placeholder="Enter new subcategory">
                      </td>
                      <td><input type="text" name="sub_subcategory[]" class="form-control" placeholder="Optional"></td>
                      <td><input type="number" name="no_of_accounts[]" class="form-control" min="0"></td>
                      <td><input type="number" step="0.01" name="amount[]" class="form-control" min="0"></td>
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
            $sql = "SELECT * FROM  balance_sheet ORDER BY id DESC";
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
                echo "<td>" . htmlspecialchars($row['month'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['side'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['category'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['subcategory'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['sub_subcategory'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['no_of_accounts'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['amount'] ?? '') . "</td>";
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
      Liabilities: [
        "Authorised Share Capital", "SHARE CAPITAL", "ASSOCIATE SHARE CAPITAL", "FUNDS AND RESERVES", "DEPOSITS",
        "BORROWINGS", "OTHER LIABILITIES", "BRANCH ADJUSTMENT", "PROFIT AND LOSS", "WORKING CAPITAL","FREE RESERVES"
      ],
      Assets: [
        "CASH IN HAND", "CASH AT BANK", "INVESTMENTS", "LOANS & ADVANCES", "LAND & BUILDING ", "COMPUTER HARDWARE & SOFTWARE & ELECTRONICS", 
        "NON BANKING ASSETS WITHIN 7 YEARS","INTREST RECEIVABLE ON INVESTMENT & ADVANCES",
        "OWN ASSETS", "OTHER ASSETS/ RECEIVABLE", "BRANCH ADJUSTMENT","LOSS"
      ]
    };

    const subcategoryMap = {
      "SHARE CAPITAL": ["Paid up Share Capital", "Shares Anamat"],
      "FUNDS AND RESERVES": ["RESERVE FUND", "BUILDING FUND", "DIVIDEND FLUCTUATION FUND", "INVESTMENT FLUCTUATION FUND","Standard Assets Provision","Bad and Doubtful Debts Provision","Overdue Interest Reserve","STANDARED ASSETS FUND"," PROFIT DISTRUBUTION",""],
      "DEPOSITS": ["CASA DEPOSIT", "Term Deposit"],
      "PROFIT AND LOSS": ["PREVIOUS YEAR PROFIT", "CURRENT YEAR PROFIT"],
      "INVESTMENTS": [
        "Shares Investment", "DCC Bank Investment", "Co-operative Bank Investment"," State Co-operative Bank Investment",
        "Nationalise Bank Investment", "Private Bank Investment", "NPA Bank Investment","Mutual Fund Investment","Federation Shares","Bank Shares",
         "Small Finance Bank Investment","Nagari & Multistate Society Investment","Other Investment","Other Shares","Other Bank"
      ],
      "LOANS & ADVANCES":["Deposit Loans Below 12 Month", "Deposit Loans Above 12 Month", "Personal Loan", "Employee Loan","Gold Loan Below 12 Month","Gold Loan Above 12 Month","Special Gold Loan",
      "Gold Loan Above 12 Month (Amount)","Home Loan Below 30 L","Home Loan Above 30 L","Salary Loan","Director Loan Secure","Director Loan Unsecure","Director Loans with Authorised",
      "Director Loans with Unauthorised","Merchant loan","Instant Loan","FD OD Loan","Mahila Samruddhi Loan","Morgage Loan","RD OD Loan","FD CC Loan","Other Loans"
      ],
            "OTHER LIABILITIES": ["INT.PAYABLE FOR DEPOSIT", "OTHER PAYABLE"],

      "CASH AT BANK": ["DCC Bank", "Co-operative Bank", "Private Bank", "NPA Bank","Current Account", "Saving Account", "FD"],  
      "OTHER ASSETS/ RECEIVABLE": ["भांडवली खर्च (जागा / इमारत खरेदी / बांधकाम) तसलमात / अडव्हान्स येणे, इतर येणे, रक्कम आगाऊ रक्कम ६ महिन्यापेक्षा कमी प्रलंबित असल्यास,","टीडीएस, सुरक्षा ठेव, जीएसटी, आयकर येणे रकमा", "शाखा जुळवणी निव्वळ येणे रक्कम"],
      "LOSS": ["LAST YEAR", "CURRENT YEAR"],
       "OWN ASSETS": ["LEGAL PROPERTY - LAND & BUILDING ", "UNLEGAL PROPERTY - LAND & BUILDING", "FURNITURE AND FIXTURE & DEAD STOCK A/C", " COMPUTER HARDWARE & SOFTWARE &  ELECTRONICS","LEGAL - NON BANKING ASSETS  - WITHIN 7 YEARS",
       "UNLEGAL NON BANKING ASSETS  -ABOVE 7 YEARS"],
     
    };

// "DCC Bank", "Co-operative Bank", "Private Bank", "NPA Bank",

function addRow() {
  const tableBody = document.querySelector("#balanceSheetTable tbody");
  const firstRow = tableBody.rows[0];
  const newRow = firstRow.cloneNode(true);

  // Clear inputs in cloned row
  newRow.querySelectorAll('input').forEach(input => input.value = '');
  newRow.querySelectorAll('select').forEach(select => {
      select.selectedIndex = 0;
      // Reattach event handlers for category and subcategory selects
      if (select.classList.contains('categorySelect')) {
          select.onchange = function() { toggleOtherInput(this, 'categoryOtherInput'); };
      } else if (select.classList.contains('subcategorySelect')) {
          select.onchange = function() { toggleOtherInput(this, 'subcategoryOtherInput'); };
      }
  });
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
    otherInput.setAttribute('required', 'required');
  } else {
    otherInput.style.display = 'none';
    otherInput.removeAttribute('required');
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