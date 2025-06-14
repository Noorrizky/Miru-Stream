<?php
// search.php
include 'includes/header.php'; // Includes session_start() and config.php
?>

<?php
$search_query = isset($_GET['q']) ? trim($_GET['q']) : ''; // Get query from $_GET
?>

<h1 class="text-center mb-5 display-5">Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php
    if (!empty($search_query)) {
        $search_param = '%' . $search_query . '%';
        $stmt = $conn->prepare("SELECT id, title, description, thumbnail_url FROM videos WHERE title LIKE ? OR description LIKE ? ORDER BY created_at DESC");
        $stmt->bind_param("ss", $search_param, $search_param);
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
            echo '<div class="col-12"><p class="lead text-center">No videos found matching your search criteria.</p></div>';
        }
        $stmt->close();
    } else {
        echo '<div class="col-12"><p class="lead text-center">Please enter a search query in the navigation bar.</p></div>';
    }
    ?>
</div>

<?php include 'includes/footer.php'; ?>