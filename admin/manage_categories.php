<?php
// admin/manage_categories.php
include 'includes/admin_header.php'; // Includes session_start(), auth check, and config.php

$message = '';
$category_to_edit = null;

// Handle Add/Update Category
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['category_name']) && !empty($_POST['category_name'])) {
        $category_name = trim($_POST['category_name']);
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

        if ($category_id > 0) {
            // Update existing category
            $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("si", $category_name, $category_id);
                if ($stmt->execute()) {
                    $message = '<div class="alert alert-success">Category updated successfully!</div>';
                } else {
                    $message = '<div class="alert alert-danger">Error updating category: ' . htmlspecialchars($stmt->error) . '</div>';
                }
                $stmt->close();
            } else {
                $message = '<div class="alert alert-danger">Database error: Could not prepare statement for update.</div>';
            }
        } else {
            // Add new category
            $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
            if ($stmt) {
                $stmt->bind_param("s", $category_name);
                if ($stmt->execute()) {
                    $message = '<div class="alert alert-success">Category added successfully!</div>';
                } else {
                    $message = '<div class="alert alert-danger">Error adding category: ' . htmlspecialchars($stmt->error) . '</div>';
                }
                $stmt->close();
            } else {
                $message = '<div class="alert alert-danger">Database error: Could not prepare statement for insert.</div>';
            }
        }
    } else {
        $message = '<div class="alert alert-warning">Category name cannot be empty.</div>';
    }
}

// Handle Delete Category
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $category_id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $category_id);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Category deleted successfully!</div>';
        } else {
            $message = '<div class="alert alert-danger">Error deleting category: ' . htmlspecialchars($stmt->error) . '</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="alert alert-danger">Database error: Could not prepare statement for delete.</div>';
    }
}

// Handle Edit Category (populate form)
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $category_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT id, name FROM categories WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $category_to_edit = $result->fetch_assoc();
        } else {
            $message = '<div class="alert alert-warning">Category not found for editing.</div>';
        }
        $stmt->close();
    } else {
        $message = '<div class="alert alert-danger">Database error: Could not prepare statement for edit.</div>';
    }
}
?>

<h1 class="mb-4 display-5 text-center">Manage Categories</h1>

<?php echo $message; ?>

<div class="card mb-4 shadow-sm">
    <div class="card-header"><?php echo $category_to_edit ? 'Edit Category' : 'Add New Category'; ?></div>
    <div class="card-body">
        <form action="manage_categories.php" method="POST">
            <div class="mb-3">
                <label for="category_name" class="form-label">Category Name:</label>
                <input type="text" class="form-control" id="category_name" name="category_name"
                       value="<?php echo $category_to_edit ? htmlspecialchars($category_to_edit['name']) : ''; ?>" required>
                <?php if ($category_to_edit): ?>
                    <input type="hidden" name="category_id" value="<?php echo $category_to_edit['id']; ?>">
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $category_to_edit ? 'Update Category' : 'Add Category'; ?></button>
            <?php if ($category_to_edit): ?>
                <a href="manage_categories.php" class="btn btn-secondary ms-2">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<h2 class="mb-3">Existing Categories</h2>
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                    echo '<td>';
                    echo '<a href="manage_categories.php?action=edit&id=' . $row['id'] . '" class="btn btn-warning btn-sm me-2">Edit</a>';
                    echo '<a href="manage_categories.php?action=delete&id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this category and its associated videos?\');">Delete</a>';
                    echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="3" class="text-center">No categories found.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'includes/admin_footer.php'; ?>