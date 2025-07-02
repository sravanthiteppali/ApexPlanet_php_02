<?php
session_start();
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'], $_POST['post_id'])) {
    $comment = $_POST['comment'];
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $post_id, $user_id, $comment);
    $stmt->execute();
}

// Pagination
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Search
$search = "";
$search_condition = "";
if (!empty($_GET['search'])) {
    $search = htmlspecialchars($_GET['search']);
    $search_condition = "WHERE title LIKE '%$search%' OR content LIKE '%$search%'";
}

// Count total
$count_sql = "SELECT COUNT(*) as total FROM posts $search_condition";
$total_result = $conn->query($count_sql);
$total_posts = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $limit);

// Fetch posts
$sql = "SELECT * FROM posts $search_condition ORDER BY created_at DESC LIMIT $start, $limit";
$result = $conn->query($sql);
?>

<h3>Blog Posts</h3>

<form method="get" action="dashboard.php" class="mb-3">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Search posts..." value="<?= $search ?>">
        <button type="submit" class="btn btn-outline-secondary">Search</button>
    </div>
</form>

<?php
if ($result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
?>
        <div class="card mb-4">
            <div class="card-body">
                <h4><?= htmlspecialchars($row['title']) ?></h4>
                <?php if (!empty($row['image'])): ?>
                    <img src="<?= htmlspecialchars($row['image']) ?>" class="img-fluid mb-2" style="max-height: 300px;" alt="Post Image">
                <?php endif; ?>
                <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                <small class="text-muted">Posted on <?= $row['created_at'] ?></small><br>
                <a href='edit_post.php?id=<?= $row['id'] ?>' class="btn btn-sm btn-warning">Edit</a>
                <a href='delete_post.php?id=<?= $row['id'] ?>' class="btn btn-sm btn-danger">Delete</a>
            </div>

            <div class="card-footer">
                <form method="post" class="mb-2">
                    <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                    <textarea name="comment" class="form-control" rows="2" placeholder="Write a comment..." required></textarea>
                    <button type="submit" class="btn btn-sm btn-primary mt-2">Post Comment</button>
                </form>

                <strong>Comments:</strong>
                <?php
                $stmt = $conn->prepare("SELECT c.comment, c.created_at, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at DESC");
                $stmt->bind_param("i", $row['id']);
                $stmt->execute();
                $comments = $stmt->get_result();

                while ($c = $comments->fetch_assoc()):
                ?>
                    <div class="border p-2 mb-2">
                        <strong><?= htmlspecialchars($c['username']) ?>:</strong>
                        <p class="mb-1"><?= htmlspecialchars($c['comment']) ?></p>
                        <small class="text-muted"><?= $c['created_at'] ?></small>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
<?php
    endwhile;
else:
    echo "<p>No posts found.</p>";
endif;

// Pagination
if ($total_pages > 1):
    echo "<nav><ul class='pagination justify-content-center'>";
    for ($i = 1; $i <= $total_pages; $i++):
        $link = "?page=$i";
        if (!empty($search)) $link .= "&search=" . urlencode($search);
        $active = ($i == $page) ? "active" : "";
        echo "<li class='page-item $active'><a class='page-link' href='$link'>$i</a></li>";
    endfor;
    echo "</ul></nav>";
endif;
?>

<?php include 'includes/footer.php'; ?>
