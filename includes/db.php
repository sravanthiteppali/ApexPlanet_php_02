<?php
$host = "localhost";
$user = "root";
$password = ""; // leave blank if you didn't set a password for root
$dbname = "blog"; // your database name

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
