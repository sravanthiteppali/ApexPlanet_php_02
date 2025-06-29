<?php
session_start();
include 'includes/header.php';
if (!isset($_SESSION['user_id'])) {
    die("Access denied. <a href='login.php'>Login</a>");
}
?>

<h2>Welcome to Dashboard</h2>
<a href="create_post.php">Create New Post</a> | 
<a href="logout.php">Logout</a><br><br>

<?php
$conn = new mysqli("localhost", "root", "", "blog");
$result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");

while ($row = $result->fetch_assoc()) {
    echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
    echo "<p>" . nl2br(htmlspecialchars($row['content'])) . "</p>";
    echo "<small>Posted on " . $row['created_at'] . "</small><br>";
    echo "<a href='edit_post.php?id=" . $row['id'] . "'>Edit</a> | ";
    echo "<a href='delete_post.php?id=" . $row['id'] . "'>Delete</a><hr>";
}
?>
<?php include 'includes/footer.php'; ?>
