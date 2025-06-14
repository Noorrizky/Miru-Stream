<?php
// admin/index.php
include 'includes/admin_header.php'; // Includes session_start(), auth check, and config.php
?>

<h1 class="mb-4 display-5 text-center">Welcome to the Admin Dashboard</h1>
<p class="lead text-center">Use the navigation bar above to manage your video categories and video entries.</p>

<div class="row mt-5">
    <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title display-6 mb-4">Categories</h5>
                <p class="card-text mb-4">Add, edit, or delete video categories to organize your content.</p>
                <a href="manage_categories.php" class="btn btn-primary btn-lg">Manage Categories</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body text-center">
                <h5 class="card-title display-6 mb-4">Videos</h5>
                <p class="card-text mb-4">Add, edit, or delete video entries and link them to categories.</p>
                <a href="manage_videos.php" class="btn btn-success btn-lg">Manage Videos</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>