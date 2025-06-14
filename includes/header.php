<?php
// includes/header.php
session_start(); // Start the session at the very beginning
require_once 'config.php'; // Include database connection and site settings

// No .htaccess means we revert to traditional PHP URLs
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple iFrame Video Streamer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand me-auto" href="index.php">Miru</a> <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" aria-current="page" href="index.php">Home</a></li> <?php
                    // Fetch categories dynamically for the navigation
                    $categories_query = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
                    if ($categories_query) {
                        while ($cat = $categories_query->fetch_assoc()) {
                            // Link adjusted back to traditional PHP URL
                            echo '<li class="nav-item"><a class="nav-link" href="category.php?id=' . $cat['id'] . '">' . htmlspecialchars($cat['name']) . '</a></li>';
                        }
                    }
                    ?>
                </ul>
                <form class="d-flex" action="search.php" method="GET"> <input class="form-control me-2" type="search" placeholder="Search videos" aria-label="Search" name="q">
                    <button class="btn btn-outline-light" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>
    <main class="flex-shrink-0">
        <div class="container py-4">