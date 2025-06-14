<?php
// watch.php
include 'includes/header.php'; // Includes session_start() and config.php
?>

<?php
// Get video_id from $_GET
$video_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$video = null;

if ($video_id > 0) {
    $stmt = $conn->prepare("SELECT title, description, youtube_url FROM videos WHERE id = ?");
    $stmt->bind_param("i", $video_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $video = $result->fetch_assoc();
    }
    $stmt->close();
}
?>

<?php if ($video):
    // Extract YouTube video ID for embed URL
    $youtube_video_id = '';
    $parts = parse_url($video['youtube_url']);
    if (isset($parts['query'])) {
        parse_str($parts['query'], $query);
        if (isset($query['v'])) {
            $youtube_video_id = $query['v'];
        }
    }
    // Handle short URLs like http://youtu.be/ and http://youtube.com/embed/
    if (empty($youtube_video_id) && isset($parts['path'])) {
         $path_parts = explode('/', rtrim($parts['path'], '/'));
         // Common cases: /watch?v=ID, /embed/ID, /v/ID, or direct ID like youtu.be/ID
         if (end($path_parts) && !in_array(end($path_parts), ['embed', 'v', 'watch'])) {
             $youtube_video_id = end($path_parts);
         }
    }
    // Final check for youtube.com/ID format
    if (empty($youtube_video_id) && preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $video['youtube_url'], $matches)) {
        $youtube_video_id = $matches[1];
    }


    $youtube_embed_url = $youtube_video_id ? "https://www.youtube.com/embed/{$youtube_video_id}?autoplay=1" : '';
?>
    <div class="video-details">
        <h1 class="text-center mb-4 display-5"><?php echo htmlspecialchars($video['title']); ?></h1>

        <?php if ($youtube_embed_url): ?>
            <div class="embed-responsive embed-responsive-16by9 mb-5">
                <iframe class="embed-responsive-item" src="<?php echo htmlspecialchars($youtube_embed_url); ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        <?php else: ?>
            <div class="alert alert-danger text-center">Invalid YouTube video URL. Could not embed the video.</div>
        <?php endif; ?>

        <h3 class="mb-3">Description</h3>
        <p class="lead"><?php echo nl2br(htmlspecialchars($video['description'])); ?></p>
    </div>

<?php else: ?>
    <div class="alert alert-warning text-center">Video not found. It may have been removed or the ID is incorrect.</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>