<?php
// error_reporting('0');
date_default_timezone_set("Asia/Kolkata"); 

$host = 'localhost';
$db = 'sapact';
$user = 'root';
$pass = '';

try {
    $link = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}






?>