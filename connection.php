<?php
// Enable error reporting (all errors, warnings, notices)
error_reporting(E_ALL);
ini_set('display_errors', 1);

date_default_timezone_set("Asia/Kolkata"); 

$DB_SERVER = 'localhost';
$DB_NAME   = 'sapact'; // Use 'arjunlogix' if that's the intended database
$username  = 'root'; // Use 'arjunlogix' if that's the intended user
$password  = ''; // Update with correct password if needed

try {
    // Make mysqli throw exceptions instead of warnings
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // Connect
    $link = mysqli_connect($DB_SERVER, $username, $password, $DB_NAME);

    // Set charset
    mysqli_set_charset($link, "utf8mb4");

    // Optional: Uncomment to verify connection
    // echo "Connected successfully";
} catch (mysqli_sql_exception $e) {
    // Catch connection errors
    die("Database connection failed: " . $e->getMessage());
}

// For admin login (optional, adjust as needed)
$admin_username = "admin";
$admin_password = "admin123";

$company = "Arjunlogix";
?>