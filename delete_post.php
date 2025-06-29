<?php
session_start();
include 'includes/header.php';
$conn = new mysqli("localhost", "root", "", "blog");

$id = $_GET['id'];
$conn->query("DELETE FROM posts WHERE id=$id");
header("Location: dashboard.php");
?>
<?php include 'includes/footer.php'; ?>