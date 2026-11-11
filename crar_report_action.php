<?php
include 'connection.php';

// Get values from GET request safely
$bankname = $link->real_escape_string($_GET['bank_name'] ?? '');
$month    = $link->real_escape_string($_GET['month'] ?? '');

// Initialize all CRAR fields with default values
$cash_balance = 0;
$cashatbankcuttent = 0;
$cashatbankfd =0;
$bank_nonprod_current = 0;
$bank_nonprod_savings = 0;
$bank_nonprod_fd = 0;
$nonprod_credit_society_inv = 0;
$inv_dccb_productive = 0;
$inv_dccb_nonproductive = 0;
$inv_coop_productive = 0;
$inv_coop_nonproductive = 0;
$inv_bonds_liquid_fund = 0;
$inv_govt_securities = 0;
$inv_mutual_funds = 0;
$inv_other_societies = 0;
$inv_credit_society_balance = 0;
$loan_fd_nsc_lic_not_overdue = 0;
$loan_fd_nsc_lic_overdue = 0;
$loan_personal_guarantee = 0;
$loan_employee = 0;
$loan_gold_under_10l_not_overdue = 0;
$loan_gold_over_10l_not_overdue = 0;
$loan_gold_overdue = 0;
$loan_housing_under_30l = 0;
$loan_housing_over_30l = 0;
$loan_salary_guarantee = 0;
$loan_director_unsecured = 0;
$loan_director_regular = 0;
$loan_director_limit_breach = 0;
$loan_personal_group_limit_breach = 0;
$loan_other_secured = 0;
$fixed_assets_legal = 0;
$fixed_assets_illegal = 0;
$fixed_assets_furniture = 0;
$fixed_assets_nonbanking_legal = 0;
$fixed_assets_nonbanking_illegal = 0;
$fixed_assets_nonbanking_expired = 0;
$other_assets_govt_interest = 0;
$other_assets_bank_interest_prod = 0;
$other_assets_bank_interest_nonprod = 0;
$loan_interest_fd_not_overdue = 0;
$loan_interest_fd_overdue = 0;
$loan_interest_personal_guarantee = 0;
$loan_interest_employee = 0;
$loan_interest_other = 0;
$other_receivables_capital_less_6m = 0;
$other_receivables_capital_more_6m = 0;
$other_receivables_stationery = 0;
$other_receivables_tax_deposits = 0;
$other_receivables_branch_reconciliation = 0;
$zero_risk_items = 0;
$accumulated_loss = 0;

// 1. रोख शिल्लक (Cash Balance)
$sql = "SELECT SUM(amount) AS cash_balance 
        FROM balance_sheet 
        WHERE bank_name='$bankname' AND month='$month' 
        AND side='Assets' AND category='CASH IN HAND'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $cash_balance = $row["cash_balance"] ?? 0;
}

// 2.बँकांमधील उत्पादित गुंतवणूक - चालू खाते

$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='CASH AT BANK' AND `subcategory`='Current Account'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $cashatbankcuttent = $row["CASH AT BANK"] ?? 0;
}


// 3. बँकांमधील उत्पादित गुंतवणूक - बचत ठेव खाते

$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='CASH AT BANK' AND `subcategory`='Saving Account'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $cashatbanksavings = $row["CASH AT BANK"] ?? 0;
}

// 4. बँकांमधील उत्पादित गुंतवणूक - मुदत ठेवी

$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='CASH AT BANK' AND `subcategory`='FD'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $cashatbankfd = $row["CASH AT BANK"] ?? 0;
}
 // 5 बँकांमधील बंद असलेल्या / अनुत्पादित गुंतवणूक-चालू खाते
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='CASH AT BANK'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $bank_nonprod_current = $row["CASH AT BANK"] ?? 0;
}

// 6 बँकांमधील बंद असलेल्या / अनुत्पादित गुंतवणूक-बचत ठेव खाते
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='CASH AT BANK'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $bank_nonprod_savings = $row["CASH AT BANK"] ?? 0;
}
//7 बँकांमधील बंद असलेल्या / अनुत्पादित गुंतवणूक-मुदत ठेवी
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='CASH AT BANK'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $bank_nonprod_fd = $row["CASH AT BANK"] ?? 0;
}

// 8 अनुत्पादित / बंद असलेल्या कोणत्याही पतसंस्थेतील गुंतवणूक शिल्लक
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='CASH AT BANK'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $nonprod_credit_society_inv = $row["CASH AT BANK"] ?? 0;
}

// 9 गुंतवणूक -जि.म.स. बँक/राज्य सह. बँक भाग भांडवल उत्पादित गुंतवणूक
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='INVESTMENTS' AND `subcategory`='Shares Investment'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $inv_dccb_productive = $row["CASH AT BANK"] ?? 0;
}

//10 गुंतवणूक-जि.म.स. बँक/राज्य सह. बँक भाग भांडवल अनुत्पादित अथवा बंद असलेल्या संस्थेतील गुंतवणूक
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='INVESTMENTS' AND `subcategory`='Private Bank Investment'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $inv_dccb_nonproductive = $row["CASH AT BANK"] ?? 0;
}

// 11 गुंतवणूक-उपविधी मान्यतेनुसार इतर सहकारी संस्थांमध्ये कोणत्याही स्वरुपाची केलेली उत्पादित गुंतवणूक
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='INVESTMENTS' AND `subcategory`='DCC Bank Investment'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $inv_coop_productive = $row["CASH AT BANK"] ?? 0;
}

//12 गुंतवणूक-उपविधी मान्यतेनुसार इतर सहकारी संस्थांमध्ये कोणत्याही स्वरुपाची केलेली अनुत्पादित गुंतवणूक
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='INVESTMENTS' AND `subcategory`='Private Bank Investment'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $inv_coop_nonproductive = $row["CASH AT BANK"] ?? 0;
}

//13 गुंतवणूक-मान्यताप्राप्त अन्य रोखे / बॉन्ड / लिक्विड फंड गुंतवणूक
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='INVESTMENTS'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $inv_bonds_liquid_fund = $row["CASH AT BANK"] ?? 0;
}

//14 गुंतवणूक-केंद्र / राज्य सरकारी रोखे पोस्टल एन.एस.सी. केव्हीपीमधील गुंतवणूक
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='INVESTMENTS'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $inv_govt_securities = $row["CASH AT BANK"] ?? 0;
}

// 15 गुंतवणूक-कोणतीही म्युच्युअल फंड गुंतवणूक
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='INVESTMENTS' AND `subcategory`='Mutual Fund Investment'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $inv_mutual_funds = $row["CASH AT BANK"] ?? 0;
}

// 16 गुंतवणूक-सह. साखर कारखाने सूत गिरण्या शिक्षण संस्था
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='INVESTMENTS' AND `subcategory`='Other Investment'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $inv_other_societies = $row["CASH AT BANK"] ?? 0;
}

// 17 गुंतवणूक-उत्पादित / अनुत्पादित / बंद असलेल्या कोणत्याही पतसंस्थेतील गुंतवणूक शिल्लक
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='INVESTMENTS' AND `subcategory`='Other Investment'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $inv_credit_society_balance = $row["CASH AT BANK"] ?? 0;
}

// 18 कर्जे व अग्रीमे-मुदत ठेव तारण / NSC तारण/ LIC तारण कर्ज- कर्ज थकीत नसल्यास

$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='LOANS & ADVANCES'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $loan_fd_nsc_lic_not_overdue = $row["CASH AT BANK"] ?? 0;
}

// 19 कर्जे व अग्रीमे-मुदत ठेव तारण / NSC तारण/ LIC तारण कर्ज- कर्ज थकीत असल्यास
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='LOANS & ADVANCES'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $loan_fd_nsc_lic_overdue = $row["CASH AT BANK"] ?? 0;
}

// 20 कर्जे व अग्रीमे-वैयक्तिक जामीनकी विनातारणी कर्ज

$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='LOANS & ADVANCES'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $loan_personal_guarantee = $row["CASH AT BANK"] ?? 0;
}

// 21 कर्जे व अग्रीमे-कर्मचारी कर्ज
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='LOANS & ADVANCES'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $loan_employee = $row["CASH AT BANK"] ?? 0;
}

// 22 10Lk below gold loan 
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='LOANS & ADVANCES'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $loan_gold_under_10l_not_overdue = $row["CASH AT BANK"] ?? 0;
}

// 23 10LK above gold loan 
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='LOANS & ADVANCES'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $loan_gold_over_10l_not_overdue = $row["CASH AT BANK"] ?? 0;
}

// 24 gold loan overdue more than 12 months 
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='LOANS & ADVANCES'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $loan_gold_overdue = $row["CASH AT BANK"] ?? 0;
}

// 25 - 30 LK belo home loan sanction 
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='LOANS & ADVANCES'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $loan_housing_under_30l = $row["CASH AT BANK"] ?? 0;
}

// 26 - 30 LK above home loan sanction 
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='LOANS & ADVANCES'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $loan_housing_over_30l = $row["CASH AT BANK"] ?? 0;
}

// 27 कर्जे व अग्रीमे-पगारदार हमीपत्र कर्ज
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='LOANS & ADVANCES'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $loan_salary_guarantee = $row["CASH AT BANK"] ?? 0;
}

// 28 Relative insecure loan 
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='LOANS & ADVANCES'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $loan_director_unsecured = $row["CASH AT BANK"] ?? 0;
}

//29 Relative secure loan 
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='LOANS & ADVANCES'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $loan_director_regular = $row["CASH AT BANK"] ?? 0;
}

// 30 relative loan if max duration period crossed 
$sql="SELECT SUM(`amount`) AS `CASH AT BANK` FROM `balance_sheet` WHERE `bank_name`='$bankname' AND `month`='$month' AND `side`='Assets' AND `category`='LOANS & ADVANCES'";
$result = $link->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $loan_director_limit_breach = $row["CASH AT BANK"] ?? 0;
}

//31 
$sql = "INSERT INTO crar (
    bank_name, month_year, cash_balance, bank_prod_current, bank_prod_savings, 
    bank_prod_fd, bank_nonprod_current, bank_nonprod_savings, bank_nonprod_fd, 
    nonprod_credit_society_inv, inv_dccb_productive, inv_dccb_nonproductive, 
    inv_coop_productive, inv_coop_nonproductive, inv_bonds_liquid_fund, 
    inv_govt_securities, inv_mutual_funds, inv_other_societies, inv_credit_society_balance, 
    loan_fd_nsc_lic_not_overdue, loan_fd_nsc_lic_overdue, loan_personal_guarantee, 
    loan_employee, loan_gold_under_10l_not_overdue, loan_gold_over_10l_not_overdue, 
    loan_gold_overdue, loan_housing_under_30l, loan_housing_over_30l, loan_salary_guarantee, 
    loan_director_unsecured, loan_director_regular, loan_director_limit_breach, 
    loan_personal_group_limit_breach, loan_other_secured, fixed_assets_legal, 
    fixed_assets_illegal, fixed_assets_furniture, fixed_assets_nonbanking_legal, 
    fixed_assets_nonbanking_illegal, fixed_assets_nonbanking_expired, other_assets_govt_interest, 
    other_assets_bank_interest_prod, other_assets_bank_interest_nonprod, loan_interest_fd_not_overdue, 
    loan_interest_fd_overdue, loan_interest_personal_guarantee, loan_interest_employee, 
    loan_interest_other, other_receivables_capital_less_6m, other_receivables_capital_more_6m, 
    other_receivables_stationery, other_receivables_tax_deposits, other_receivables_branch_reconciliation, 
    zero_risk_items, accumulated_loss, created_by, created_at
) VALUES (
    '$bankname', '$month', '$cash_balance', '$cashatbankcuttent', '$cashatbanksavings', 
    '$cashatbankfd', '$bank_nonprod_current', '$bank_nonprod_savings', '$bank_nonprod_fd', 
    '$nonprod_credit_society_inv', '$inv_dccb_productive', '$inv_dccb_nonproductive', 
    '$inv_coop_productive', '$inv_coop_nonproductive', '$inv_bonds_liquid_fund', 
    '$inv_govt_securities', '$inv_mutual_funds', '$inv_other_societies', '$inv_credit_society_balance', 
    '$loan_fd_nsc_lic_not_overdue', '$loan_fd_nsc_lic_overdue', '$loan_personal_guarantee', 
    '$loan_employee', '$loan_gold_under_10l_not_overdue', '$loan_gold_over_10l_not_overdue', 
    '$loan_gold_overdue', '$loan_housing_under_30l', '$loan_housing_over_30l', '$loan_salary_guarantee', 
    '$loan_director_unsecured', '$loan_director_regular', '$loan_director_limit_breach', 
    '$loan_personal_group_limit_breach', '$loan_other_secured', '$fixed_assets_legal', 
    '$fixed_assets_illegal', '$fixed_assets_furniture', '$fixed_assets_nonbanking_legal', 
    '$fixed_assets_nonbanking_illegal', '$fixed_assets_nonbanking_expired', '$other_assets_govt_interest', 
    '$other_assets_bank_interest_prod', '$other_assets_bank_interest_nonprod', '$loan_interest_fd_not_overdue', 
    '$loan_interest_fd_overdue', '$loan_interest_personal_guarantee', '$loan_interest_employee', 
    '$loan_interest_other', '$other_receivables_capital_less_6m', '$other_receivables_capital_more_6m', 
    '$other_receivables_stationery', '$other_receivables_tax_deposits', '$other_receivables_branch_reconciliation', 
    '$zero_risk_items', '$accumulated_loss', 'system', NOW()
)";

   
if ($link->query($sql) === TRUE) {
    header("Location: crar_report_form.php?success=CRAR calculation completed successfully");
    exit();
} else {
    header("Location: crar_report_form.php?error=Error: " . urlencode($link->error));
    exit();
}
?>