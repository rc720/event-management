<?php
// Load db connection and session safeguards
require_once __DIR__ . '/../../config/db.php';
checkAdminSession();

$action = isset($_GET['action']) ? trim($_GET['action']) : '';

// 1. FETCH DYNAMIC SINGLE BLOG POST BY ID (For Edit modal rendering)
if ($action === 'get') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        sendAjaxResponse(false, 'Invalid Blog ID.');
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM `blogs` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $blog = $stmt->fetch();

        if ($blog) {
            sendAjaxResponse(true, 'Blog loaded successfully.', ['data' => $blog]);
        } else {
            sendAjaxResponse(false, 'Blog article not found.');
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'Database error: ' . $e->getMessage());
    }
}

// 2. CREATE OR UPDATE BLOG POST WITH WEBOP VALIDATIONS
if ($action === 'save') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendAjaxResponse(false, 'Invalid request method.');
    }

    $id = isset($_POST['blog_id']) && is_numeric($_POST['blog_id']) ? (int)$_POST['blog_id'] : 0;
    
    // Sanitize and validate inputs
    $title = sanitizeInput($_POST['title']);
    $slug = sanitizeInput($_POST['slug']);
    $short_desc = sanitizeInput($_POST['short_description']);
    
    // Description contains CKEditor data - keep HTML but sanitize lightly or accept standard
    $full_desc = trim($_POST['full_description']); 
    
    $meta_title = sanitizeInput($_POST['meta_title']);
    $meta_keywords = sanitizeInput($_POST['meta_keywords']);
    $meta_description = sanitizeInput($_POST['meta_description']);
    $status = sanitizeInput($_POST['status']);

    if (empty($title) || empty($short_desc) || empty($full_desc)) {
        sendAjaxResponse(false, 'Title, Short Description, and Full Description are mandatory.');
    }

    // Auto-generate slug if empty
    if (empty($slug)) {
        $slug = generateSlug($title, 'blogs', 'slug', $id > 0 ? $id : null);
    } else {
        // Enforce clean slug format
        $slug = generateSlug($slug, 'blogs', 'slug', $id > 0 ? $id : null);
    }

    // Set fallback SEO fields
    if (empty($meta_title)) $meta_title = $title;
    if (empty($meta_description)) $meta_description = $short_desc;

    $imagePath = '';
    
    // Check if new image is uploaded
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        // Strict WebP Only, 1200x630
        $uploadDir = __DIR__ . '/../../uploads/blogs/';
        $uploadedFile = uploadWebPImage($_FILES['featured_image'], $uploadDir, $title, 1200, 630);
        
        if ($uploadedFile === false) {
            $err = isset($_SESSION['upload_error']) ? $_SESSION['upload_error'] : 'Image upload validation failed.';
            sendAjaxResponse(false, $err);
        }
        $imagePath = $uploadedFile;
    }

    try {
        if ($id > 0) {
            // Update operation
            if (!empty($imagePath)) {
                // Delete old image file first
                $oldStmt = $pdo->prepare("SELECT `featured_image` FROM `blogs` WHERE `id` = :id LIMIT 1");
                $oldStmt->execute(['id' => $id]);
                $oldImg = $oldStmt->fetchColumn();
                if ($oldImg && file_exists(__DIR__ . '/../../uploads/blogs/' . $oldImg)) {
                    unlink(__DIR__ . '/../../uploads/blogs/' . $oldImg);
                }

                $stmt = $pdo->prepare("UPDATE `blogs` SET `title` = :title, `slug` = :slug, `featured_image` = :image, `short_description` = :short_desc, `full_description` = :full_desc, `meta_title` = :meta_title, `meta_keywords` = :meta_keywords, `meta_description` = :meta_description, `status` = :status WHERE `id` = :id");
                $params = ['image' => $imagePath];
            } else {
                $stmt = $pdo->prepare("UPDATE `blogs` SET `title` = :title, `slug` = :slug, `short_description` = :short_desc, `full_description` = :full_desc, `meta_title` = :meta_title, `meta_keywords` = :meta_keywords, `meta_description` = :meta_description, `status` = :status WHERE `id` = :id");
                $params = [];
            }
            
            $params = array_merge($params, [
                'title' => $title,
                'slug' => $slug,
                'short_desc' => $short_desc,
                'full_desc' => $full_desc,
                'meta_title' => $meta_title,
                'meta_keywords' => $meta_keywords,
                'meta_description' => $meta_description,
                'status' => $status,
                'id' => $id
            ]);
            
            $result = $stmt->execute($params);
            
            if ($result) {
                sendAjaxResponse(true, 'Blog article updated successfully!');
            } else {
                sendAjaxResponse(false, 'Failed to update blog article.');
            }
            
        } else {
            // Insert operation
            if (empty($imagePath)) {
                sendAjaxResponse(false, 'Featured image is mandatory for new blog articles.');
            }

            $stmt = $pdo->prepare("INSERT INTO `blogs` (`title`, `slug`, `featured_image`, `short_description`, `full_description`, `meta_title`, `meta_keywords`, `meta_description`, `status`) VALUES (:title, :slug, :image, :short_desc, :full_desc, :meta_title, :meta_keywords, :meta_description, :status)");
            
            $result = $stmt->execute([
                'title' => $title,
                'slug' => $slug,
                'image' => $imagePath,
                'short_desc' => $short_desc,
                'full_desc' => $full_desc,
                'meta_title' => $meta_title,
                'meta_keywords' => $meta_keywords,
                'meta_description' => $meta_description,
                'status' => $status
            ]);
            
            if ($result) {
                sendAjaxResponse(true, 'New blog article published successfully!');
            } else {
                sendAjaxResponse(false, 'Failed to publish new blog article.');
            }
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'System database error: ' . $e->getMessage());
    }
}

// 3. DELETE SPECIFIC BLOG POST
if ($action === 'delete') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        sendAjaxResponse(false, 'Invalid Blog ID.');
    }

    try {
        // Delete image file first
        $stmtImg = $pdo->prepare("SELECT `featured_image` FROM `blogs` WHERE `id` = :id LIMIT 1");
        $stmtImg->execute(['id' => $id]);
        $img = $stmtImg->fetchColumn();
        if ($img && file_exists(__DIR__ . '/../../uploads/blogs/' . $img)) {
            unlink(__DIR__ . '/../../uploads/blogs/' . $img);
        }

        $stmt = $pdo->prepare("DELETE FROM `blogs` WHERE `id` = :id");
        $result = $stmt->execute(['id' => $id]);

        if ($result) {
            sendAjaxResponse(true, 'Blog article deleted successfully.');
        } else {
            sendAjaxResponse(false, 'Failed to delete the blog.');
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'System database error: ' . $e->getMessage());
    }
}

// 4. BATCH IMPORT BLOG ARTICLES FROM CSV
if ($action === 'import') {
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        sendAjaxResponse(false, 'Please select a valid CSV file.');
    }

    $fileTmp = $_FILES['csv_file']['tmp_name'];
    
    // Check file extension
    $ext = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));
    if ($ext !== 'csv') {
        sendAjaxResponse(false, 'Invalid file format. Please upload a .csv file.');
    }

    try {
        $handle = fopen($fileTmp, "r");
        if ($handle === false) {
            sendAjaxResponse(false, 'Failed to read the uploaded CSV file.');
        }

        // Read header row
        $headers = fgetcsv($handle, 1000, ",");
        
        $importedCount = 0;
        $failedCount = 0;
        
        // Loop through rows
        while (($row = fgetcsv($handle, 1000, ",")) !== false) {
            if (count($row) < 3) {
                $failedCount++;
                continue; // Skip incomplete rows
            }

            // Map standard columns
            $title = sanitizeInput($row[0]);
            $short_desc = sanitizeInput($row[1]);
            $full_desc = trim($row[2]); // keeps standard styling
            
            $meta_title = isset($row[3]) ? sanitizeInput($row[3]) : $title;
            $meta_keywords = isset($row[4]) ? sanitizeInput($row[4]) : 'imported, blogs';
            $meta_description = isset($row[5]) ? sanitizeInput($row[5]) : $short_desc;
            $status = isset($row[6]) ? sanitizeInput($row[6]) : 'active';

            if (empty($title) || empty($short_desc) || empty($full_desc)) {
                $failedCount++;
                continue; // skip empty required columns
            }

            // Generate dynamic unique slug
            $slug = generateSlug($title, 'blogs', 'slug');
            
            // Set a placeholder default featured image since we can't upload file blobs inside CSV
            $defaultImage = 'placeholder-blog.webp';
            
            // Insert securely
            $stmt = $pdo->prepare("INSERT INTO `blogs` (`title`, `slug`, `featured_image`, `short_description`, `full_description`, `meta_title`, `meta_keywords`, `meta_description`, `status`) VALUES (:title, :slug, :image, :short_desc, :full_desc, :meta_title, :meta_keywords, :meta_description, :status)");
            
            $res = $stmt->execute([
                'title' => $title,
                'slug' => $slug,
                'image' => $defaultImage,
                'short_desc' => $short_desc,
                'full_desc' => $full_desc,
                'meta_title' => $meta_title,
                'meta_keywords' => $meta_keywords,
                'meta_description' => $meta_description,
                'status' => $status
            ]);
            
            if ($res) {
                $importedCount++;
            } else {
                $failedCount++;
            }
        }
        fclose($handle);

        sendAjaxResponse(true, "CSV Import completed! Successfully imported: {$importedCount} articles. Failed rows: {$failedCount}.", [
            'imported' => $importedCount,
            'failed' => $failedCount
        ]);
        
    } catch (Exception $e) {
        sendAjaxResponse(false, 'CSV parsing database error: ' . $e->getMessage());
    }
}

// Fallback error
sendAjaxResponse(false, 'Invalid Action parameter.');
