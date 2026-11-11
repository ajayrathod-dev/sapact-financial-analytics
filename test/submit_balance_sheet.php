<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('connection.php'); // This must return a PDO object in $link

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $submission_date = $_POST['submission_date'];

    // Liabilities
    if (!empty($_POST['liabilities'])) {
        foreach ($_POST['liabilities'] as $category => $data) {
            if (strpos($category, 'Total') !== false || $category == 'WORKING CAPITAL') {
                $stmt = $link->prepare("INSERT INTO balance_sheet (submission_date, side, category, amount) VALUES (:date, :side, :category, :amount)");
                $stmt->execute([
                    ':date' => $submission_date,
                    ':side' => 'Liabilities',
                    ':category' => $category,
                    ':amount' => !empty($data['amount']) ? $data['amount'] : null
                ]);

                if ($category == 'DEPOSITS Total' && !empty($data['no_of_accounts'])) {
                    $stmt = $link->prepare("UPDATE balance_sheet SET no_of_accounts = :no_of_accounts WHERE submission_date = :date AND side = :side AND category = :category");
                    $stmt->execute([
                        ':no_of_accounts' => $data['no_of_accounts'],
                        ':date' => $submission_date,
                        ':side' => 'Liabilities',
                        ':category' => $category
                    ]);
                }
            } elseif ($category == 'Authorised Share Capital' || $category == 'BRANCH ADJUSTMENT') {
                $stmt = $link->prepare("INSERT INTO balance_sheet (submission_date, side, category, amount) VALUES (:date, :side, :category, :amount)");
                $stmt->execute([
                    ':date' => $submission_date,
                    ':side' => 'Liabilities',
                    ':category' => $category,
                    ':amount' => !empty($data['amount']) ? $data['amount'] : null
                ]);
            } else {
                if (!empty($data['subcategories'])) {
                    foreach ($data['subcategories'] as $subcat) {
                        if (!empty($subcat['name'])) {
                            $stmt = $link->prepare("INSERT INTO balance_sheet (submission_date, side, category, subcategory, no_of_accounts, amount) VALUES (:date, :side, :category, :subcategory, :no_of_accounts, :amount)");
                            $stmt->execute([
                                ':date' => $submission_date,
                                ':side' => 'Liabilities',
                                ':category' => $category,
                                ':subcategory' => $subcat['name'],
                                ':no_of_accounts' => !empty($subcat['no_of_accounts']) ? $subcat['no_of_accounts'] : null,
                                ':amount' => !empty($subcat['amount']) ? $subcat['amount'] : null
                            ]);
                        }
                    }
                }
            }
        }
    }

    // Assets
    if (!empty($_POST['assets'])) {
        foreach ($_POST['assets'] as $category => $data) {
            if (strpos($category, 'Total') !== false || $category == 'WORKING CAPITAL') {
                $stmt = $link->prepare("INSERT INTO balance_sheet (submission_date, side, category, amount) VALUES (:date, :side, :category, :amount)");
                $stmt->execute([
                    ':date' => $submission_date,
                    ':side' => 'Assets',
                    ':category' => $category,
                    ':amount' => !empty($data['amount']) ? $data['amount'] : null
                ]);

                if ($category == 'LOANS & ADVANCES Total' && !empty($data['no_of_accounts'])) {
                    $stmt = $link->prepare("UPDATE balance_sheet SET no_of_accounts = :no_of_accounts WHERE submission_date = :date AND side = :side AND category = :category");
                    $stmt->execute([
                        ':no_of_accounts' => $data['no_of_accounts'],
                        ':date' => $submission_date,
                        ':side' => 'Assets',
                        ':category' => $category
                    ]);
                }
            } elseif ($category == 'BRANCH ADJUSTMENT') {
                $stmt = $link->prepare("INSERT INTO balance_sheet (submission_date, side, category, amount) VALUES (:date, :side, :category, :amount)");
                $stmt->execute([
                    ':date' => $submission_date,
                    ':side' => 'Assets',
                    ':category' => $category,
                    ':amount' => !empty($data['amount']) ? $data['amount'] : null
                ]);
            } else {
                if (!empty($data['subcategories'])) {
                    foreach ($data['subcategories'] as $subcat) {
                        if (!empty($subcat['name'])) {
                            $stmt = $link->prepare("INSERT INTO balance_sheet (submission_date, side, category, subcategory, no_of_accounts, amount) VALUES (:date, :side, :category, :subcategory, :no_of_accounts, :amount)");
                            $stmt->execute([
                                ':date' => $submission_date,
                                ':side' => 'Assets',
                                ':category' => $category,
                                ':subcategory' => $subcat['name'],
                                ':no_of_accounts' => !empty($subcat['no_of_accounts']) ? $subcat['no_of_accounts'] : null,
                                ':amount' => !empty($subcat['amount']) ? $subcat['amount'] : null
                            ]);
                        }
                    }
                }
            }
        }
    }

    echo "Data saved successfully!";
} else {
    echo "Invalid request.";
}

$link = null;
?>
