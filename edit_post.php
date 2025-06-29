<?php
session_start();
include 'includes/header.php';
$conn = new mysqli("localhost", "root", "", "blog");

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM posts WHERE id=$id");
$post = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $stmt = $conn->prepare("UPDATE posts SET title=?, content=? WHERE id=?");
    $stmt->bind_param("ssi", $title, $content, $id);
    $stmt->execute();
    header("Location: dashboard.php");
}
?>

<form method="post">
    Title: <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required><br>
    Content:<br><textarea name="content" rows="5" cols="30" required><?= htmlspecialchars($post['content']) ?></textarea><br>
    <input type="submit" value="Update Post">
</form>
<?php include 'includes/footer.php'; ?>