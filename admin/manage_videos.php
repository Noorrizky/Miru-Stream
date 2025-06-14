<?php
// admin/manage_videos.php
include 'includes/admin_header.php'; // Includes session_start(), auth check, and config.php

$message = '';
$video_to_edit = null;

// Helper function to extract YouTube video ID from various URL formats
function getYoutubeVideoId($url) {
    $parts = parse_url($url);
    $video_id = false;

    if (isset($parts['query'])) {
        parse_str($parts['query'], $query);
        if (isset($query['v'])) {
            $video_id = $query['v'];
        }
    } else if (isset($parts['path'])) {
        $path_parts = explode('/', rtrim($parts['path'], '/'));
        if (end($path_parts) && !in_array(end($path_parts), ['embed', 'v'])) { // Basic check to avoid 'embed' or 'v' if it's the last part
            $video_id = end($path_parts);
        }
    }
    // Handle http://youtu.be/ short URLs
    if ($video_id === false && strpos($url, 'http://youtu.be/') !== false) {
        $path_parts = explode('http://youtu.be/', $url);
        if (isset($path_parts[1])) {
            $video_id = explode('?', $path_parts[1])[0]; // Remove any query string
            $video_id = explode('&', $video_id)[0];     // Remove any hash
        }
    }
    // Final regex for more robust ID extraction
    if ($video_id === false && preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
        $video_id = $matches[1];
    }

    // Basic validation for YouTube ID length (standard IDs are 11 chars)
    if ($video_id && strlen($video_id) == 11) {
        return $video_id;
    }
    return false; // Not a valid YouTube URL for ID extraction
}


// Handle Add/Update Video
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['title'], $_POST['youtube_url'], $_POST['category_id'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $youtube_url = trim($_POST['youtube_url']);
        $category_id = intval($_POST['category_id']);
        $video_id_to_process = isset($_POST['video_id']) ? intval($_POST['video_id']) : 0;

        $youtube_video_id = getYoutubeVideoId($youtube_url);
        // High quality thumbnail URL from YouTube
        $thumbnail_url = $youtube_video_id ? "https://img.youtube.com/vi/{$youtube_video_id}/hqdefault.jpg" : '';

        if (empty($title) || empty($youtube_url) || $category_id <= 0) {
            $message = '<div class="alert alert-warning">Title, YouTube URL, and Category are required fields.</div>';
        } elseif (!$youtube_video_id) {
            $message = '<div class="alert alert-danger">Invalid YouTube URL. Could not extract video ID. Please check the URL format.</div>';
        } else {
            if ($video_id_to_process > 0) {
                // Update existing video
                $stmt = $conn->prepare("UPDATE videos SET title = ?, description = ?, youtube_url = ?, thumbnail_url = ?, category_id = ? WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("ssssii", $title, $description, $youtube_url, $thumbnail_url, $category_id, $video_id_to_process);
                    if ($stmt->execute()) {
                        $message = '<div class="alert alert-success">Video updated successfully!</div>';
                    } else {
                        $message = '<div class="alert alert-danger">Error updating video: ' . htmlspecialchars($stmt->error) . '</div>';
                    }
                    $stmt->close();
                } else {
                    $message = '<div class="alert alert-danger">Database error: Could not prepare statement for update.</div>';
                }
            } else {
                // Add new video
                $stmt = $conn->prepare("INSERT INTO videos (title, description, youtube_url, thumbnail_url, category_id) VALUES (?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ssssi", $title, $description, $youtube_url, $thumbnail_url, $category_id);
                    if ($stmt->execute()) {
                        $message = '<div class="alert alert-success">Video added successfully!</div>';
                    } else {
                        $message = '<div class="alert alert-danger">Error adding video: ' . htmlspecialchars($stmt->error) . '</div>';
                    }
                    $stmt->close();
                } else {
                    $message = '<div class="alert alert-danger">Database error: Could not prepare statement for insert.</div>';
                }
            }
        }
    } else {
        $message = '<div class="alert alert-warning">Please fill all required fields for the video.</div>';
    }
}

// Handle Delete Video
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $video_id_to_delete = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM videos WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $video_id_to_delete);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Video deleted successfully!</div>';
        } else {
            $message = '<div class="alert alert-danger">Error deleting video: ' . htmlspecialchars($stmt->error) . '</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="alert alert-danger">Database error: Could not prepare statement for delete.</div>';
    }
}

// Handle Edit Video (populate form)
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $video_id_to_edit = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id, category_id, title, description, youtube_url FROM videos WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $video_id_to_edit);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $video_to_edit = $result->fetch_assoc();
        } else {
            $message = '<div class="alert alert-warning">Video not found for editing.</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="alert alert-danger">Database error: Could not prepare statement for edit.</div>';
    }
}

// Fetch all categories for the dropdown menu
$categories_result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = [];
if ($categories_result) {
    while ($row = $categories_result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<h1 class="mb-4 display-5 text-center">Manage Videos</h1>

<?php echo $message; ?>

<div class="card mb-4 shadow-sm">
    <div class="card-header"><?php echo $video_to_edit ? 'Edit Video' : 'Add New Video'; ?></div>
    <div class="card-body">
        <form action="manage_videos.php" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Video Title:</label>
                <input type="text" class="form-control" id="title" name="title"
                       value="<?php echo $video_to_edit ? htmlspecialchars($video_to_edit['title']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?php echo $video_to_edit ? htmlspecialchars($video_to_edit['description']) : ''; ?></textarea>
            </div>
            <div class="mb-3">
                <label for="youtube_url" class="form-label">YouTube URL:</label>
                <input type="url" class="form-control" id="youtube_url" name="youtube_url" placeholder="e.g., http://www.youtube.com/watch?v=dQw4w9WgXcQ"
                       value="<?php echo $video_to_edit ? htmlspecialchars($video_to_edit['youtube_url']) : ''; ?>" required>
                <small class="form-text text-muted">Enter the full YouTube video URL (e.g., `http://www.youtube.com/watch?v=VIDEO_ID` or `http://youtu.be/VIDEO_ID`).</small>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Category:</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Select a Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"
                            <?php echo ($video_to_edit && $video_to_edit['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($video_to_edit): ?>
                <input type="hidden" name="video_id" value="<?php echo $video_to_edit['id']; ?>">
            <?php endif; ?>
            <button type="submit" class="btn btn-primary"><?php echo $video_to_edit ? 'Update Video' : 'Add Video'; ?></button>
            <?php if ($video_to_edit): ?>
                <a href="manage_videos.php" class="btn btn-secondary ms-2">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<h2 class="mb-3">Existing Videos</h2>
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Title</th>
                <th scope="col">Category</th>
                <th scope="col">YouTube URL</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT v.id, v.title, v.youtube_url, c.name AS category_name FROM videos v JOIN categories c ON v.category_id = c.id ORDER BY v.created_at DESC");
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td>' . htmlspecialchars($row['title']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['category_name']) . '</td>';
                    echo '<td><a href="' . htmlspecialchars($row['youtube_url']) . '" target="_blank" class="text-truncate d-inline-block" style="max-width: 150px;">' . htmlspecialchars($row['youtube_url']) . '</a></td>';
                    echo '<td>';
                    echo '<a href="manage_videos.php?action=edit&id=' . $row['id'] . '" class="btn btn-warning btn-sm me-2">Edit</a>';
                    echo '<a href="manage_videos.php?action=delete&id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this video?\');">Delete</a>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="5" class="text-center">No videos found. Please add some.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'includes/admin_footer.php'; ?>