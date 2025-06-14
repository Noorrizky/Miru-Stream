<?php
// category.php
include 'includes/header.php'; // Includes session_start() and config.php
?>

<?php
// Get category_id from $_GET
$category_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$category_name = "Category"; // Default name if not found or invalid ID

if ($category_id > 0) {
    $stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $category_data = $result->fetch_assoc();
        $category_name = htmlspecialchars($category_data['name']);
    }
    $stmt->close();
}
?>

<h1 class="text-center mb-5 display-5">Videos in <?php echo $category_name; ?></h1>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php
    if ($category_id > 0) {
        $stmt = $conn->prepare("SELECT id, title, description, thumbnail_url FROM videos WHERE category_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($video = $result->fetch_assoc()) {
                $thumbnail = !empty($video['thumbnail_url']) ? htmlspecialchars($video['thumbnail_url']) : 'assets/images/placeholder.jpg';
                ?>
                <div class="col">
                    <div class="card h-100">
                        <a href="watch.php?id=<?php echo $video['id']; ?>"> <img src="<?php echo $thumbnail; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($video['title']); ?>">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-2"><a href="watch.php?id=<?php echo $video['id']; ?>"><?php echo htmlspecialchars($video['title']); ?></a></h5> <p class="card-text flex-grow-1"><?php echo htmlspecialchars($video['description']); ?></p>
                            <div class="mt-auto">
                                <a href="watch.php?id=<?php echo $video['id']; ?>" class="btn btn-primary btn-sm">Watch Video</a> </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="col-12"><p class="lead text-center">No videos found in this category.</p></div>';
        }
        $stmt->close();
    } else {
        echo '<div class="col-12"><p class="lead text-center">Invalid category selected.</p></div>';
    }
    ?>
</div>

<?php include 'includes/footer.php'; ?>