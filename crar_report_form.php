<?php
include 'header.php';
date_default_timezone_set('Asia/Kolkata');

$bank_name = $_GET['bank_name'] ?? '';
$month_year = $_GET['month_year'] ?? '';
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>

<div class="page-wrapper">
  <div class="page-content">
    <!-- Display Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($success); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Form Section -->
    <div class="card">
        <div class="card-body">
            <form action="crar_report_action.php" class="row g-3" method="get" enctype="multipart/form-data">
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

    <!-- CRAR Log Table -->
    <div class="card mt-4">
      <div class="card-body">
        <h5 class="card-title">CRAR Calculation Log</h5>
        <div class="table-responsive">
          <table id="crarLogTable" class="table table-striped table-bordered">
            <thead class="table-dark">
              <tr>
                <th>Sr.No</th>
                <th>Bank Name</th>
                <th>Month Year</th>
                <th>रोख रक्कम </th>
                <th>चालू खाते </th>
                <th>बचत खाते </th>
                <th>मुदत ठेवी  </th>
                <th>बँकांमधील बंद असलेल्या / अनुत्पादित गुंतवणूक-चालू खाते</th>
                <th>बँकांमधील उत्पादित गुंतवणूक - बचत ठेव खाते</th>
                <th>बँकांमधील उत्पादित गुंतवणूक - मुदत ठेवी</th>
                <th>बँकांमधील बंद असलेल्या / अनुत्पादित गुंतवणूक-मुदत ठेवीतवणूक शिल्लक</th>
                <th>गुंतवणूक -जि.म.स. बँक/राज्य सह. बँक भाग भांडवल उत्पादित गुंतवणूक</th>
                <th>गुंतवणूक-जि.म.स. बँक/राज्य सह. बँक भाग भांडवल अनुत्पादित अथवा बंद असलेल्या संस्थेतील गुंतवणूक</th>
                <th>गुंतवणूक-उपविधी मान्यतेनुसार इतर सहकारी संस्थांमध्ये कोणत्याही स्वरुपाची केलेली उत्पादित गुंतवणूक</th>
                <th>गुंतवणूक-उपविधी मान्यतेनुसार इतर सहकारी संस्थांमध्ये कोणत्याही स्वरुपाची केलेली अनुत्पादित गुंतवणूक</th>
                <th>गुंतवणूक-मान्यताप्राप्त अन्य रोखे / बॉन्ड / लिक्विड फंड गुंतवणूक</th>
                <th>गुंतवणूक-केंद्र / राज्य सरकारी रोखे पोस्टल एन.एस.सी. केव्हीपीमधील गुंतवणूक</th>
                <th>गुंतवणूक-कोणतीही म्युच्युअल फंड गुंतवणूक</th>
                <th>गुंतवणूक-सह. साखर कारखाने सूत गिरण्या शिक्षण संस्था प्रक्रिया संस्था धर्मादाय संस्था इतर सर्व संस्था इ. मधील कोणतीही गुंतवणूक / अग्रिम इत्यादी.</th>
                <th>गुंतवणूक-उत्पादित / अनुत्पादित / बंद असलेल्या कोणत्याही पतसंस्थेतील गुंतवणूक शिल्लक</th>
                <th>कर्जे व अग्रीमे-मुदत ठेव तारण / NSC तारण/ LIC तारण कर्ज- कर्ज थकीत नसल्यास</th>
                <th>कर्जे व अग्रीमे-मुदत ठेव तारण / NSC तारण/ LIC तारण कर्ज- कर्ज थकीत असल्यास</th>
                <th></th>
                <th></th>
                <th></th>
                <th> 10L</th>
                <th>Loan Gold Overdue</th>
                <th>Loan House Under 30L</th>
                <th>Loan House Over 30L</th>
                <th>Loan Salary</th>
                <th>Loan Dir Unsecured</th>
                <th>Loan Dir Regular</th>
                <th>Loan Dir Limit Breach</th>
                <th>Loan Limit Breach</th>
                <th>Loan Other</th>
                <th>Fixed Assets Legal</th>
                <th>Fixed Assets Illegal</th>
                <th>Fixed Assets Furniture</th>
                <th>Fixed NB Legal</th>
                <th>Fixed NB Illegal</th>
                <th>Fixed NB Expired</th>
                <th>Other Govt Interest</th>
                <th>Other Bank Int Prod</th>
                <th>Other Bank Int NonProd</th>
                <th>Int FD Not Overdue</th>
                <th>Int FD Overdue</th>
                <th>Int Personal</th>
                <th>Int Employee</th>
                <th>Int Other Loans</th>
                <th>Receivable Capital Short</th>
                <th>Receivable Capital Long</th>
                <th>Receivable Stationery</th>
                <th>Receivable Tax</th>
                <th>Receivable Branch</th>
                <th>Zero Risk</th>
                <th>Accumulated Loss</th>
                <th>Total Risk Weighted</th>
                <th>Networth</th>
                <th>CRAR %</th>
                <th>Created At</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Fetch CRAR data from crar table instead of crar_log
              $log_sql = "SELECT c.*, si.society_name 
                         FROM crar c 
                         LEFT JOIN society_info si ON c.bank_name = si.id 
                         ORDER BY c.created_at DESC LIMIT 100";
              $log_result = mysqli_query($link, $log_sql);
              
              if ($log_result && mysqli_num_rows($log_result) > 0) {
                $sr = 1;
                while ($log_row = mysqli_fetch_assoc($log_result)) {
                  echo "<tr>";
                  echo "<td>" . $sr++ . "</td>";
                  echo "<td>" . htmlspecialchars($log_row['society_name'] ?? 'N/A') . "</td>";
                  echo "<td>" . htmlspecialchars($log_row['month_year']) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['cash_balance'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['bank_prod_current'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['bank_prod_savings'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['bank_prod_fd'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['bank_nonprod_current'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['bank_nonprod_savings'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['bank_nonprod_fd'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['nonprod_credit_society_inv'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['inv_dccb_productive'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['inv_dccb_nonproductive'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['inv_coop_productive'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['inv_coop_nonproductive'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['inv_bonds_liquid_fund'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['inv_govt_securities'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['inv_mutual_funds'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['inv_other_societies'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['inv_credit_society_balance'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_fd_nsc_lic_not_overdue'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_fd_nsc_lic_overdue'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_personal_guarantee'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_employee'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_gold_under_10l_not_overdue'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_gold_over_10l_not_overdue'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_gold_overdue'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_housing_under_30l'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_housing_over_30l'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_salary_guarantee'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_director_unsecured'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_director_regular'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_director_limit_breach'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_personal_group_limit_breach'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_other_secured'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['fixed_assets_legal'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['fixed_assets_illegal'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['fixed_assets_furniture'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['fixed_assets_nonbanking_legal'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['fixed_assets_nonbanking_illegal'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['fixed_assets_nonbanking_expired'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['other_assets_govt_interest'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['other_assets_bank_interest_prod'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['other_assets_bank_interest_nonprod'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_interest_fd_not_overdue'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_interest_fd_overdue'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_interest_personal_guarantee'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_interest_employee'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['loan_interest_other'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['other_receivables_capital_less_6m'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['other_receivables_capital_more_6m'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['other_receivables_stationery'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['other_receivables_tax_deposits'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['other_receivables_branch_reconciliation'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['zero_risk_items'], 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($log_row['accumulated_loss'], 2) . "</td>";
                  
                  // Calculate and display CRAR related fields
                  $total_risk_weighted = $log_row['total_risk_weighted'] ?? 0;
                  $networth = $log_row['networth'] ?? 0;
                  $crar_percentage = ($total_risk_weighted > 0) ? ($networth / $total_risk_weighted * 100) : 0;
                  
                  echo "<td class='text-end fw-bold'>" . number_format($total_risk_weighted, 2) . "</td>";
                  echo "<td class='text-end'>" . number_format($networth, 2) . "</td>";
                  echo "<td class='text-end fw-bold text-primary'>" . number_format($crar_percentage, 2) . "%</td>";
                  echo "<td>" . htmlspecialchars($log_row['created_at']) . "</td>";
                  echo "</tr>";
                }
              } else {
                echo "<tr><td colspan='64' class='text-center'>No CRAR calculation logs available</td></tr>";
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
$(document).ready(function() {
    $('#crarLogTable').DataTable({
        "pageLength": 10,
        "order": [[0, "desc"]]
    });
});
</script>

<?php include('footer.php'); ?>