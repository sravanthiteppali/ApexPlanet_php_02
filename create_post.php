<?php
session_start();
include 'includes/header.php';
$conn = new mysqli("localhost", "root", "", "blog");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);
    $stmt->execute();
    header("Location: dashboard.php");
}
?>

<form method="post">
    Title: <input type="text" name="title" required><br>
    Content:<br><textarea name="content" rows="5" cols="30" required></textarea><br>
    <input type="submit" value="Add Post">
</form>
<?php include 'includes/footer.php'; ?>