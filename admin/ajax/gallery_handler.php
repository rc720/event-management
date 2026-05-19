<?php
// Include db configurations
require_once __DIR__ . '/../../config/db.php';
checkAdminSession();

$action = isset($_GET['action']) ? trim($_GET['action']) : '';

// 1. GET SINGLE GALLERY ITEM BY ID
if ($action === 'get') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        sendAjaxResponse(false, 'Invalid Gallery ID.');
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM `gallery` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $gallery = $stmt->fetch();

        if ($gallery) {
            sendAjaxResponse(true, 'Gallery item loaded successfully.', ['data' => $gallery]);
        } else {
            sendAjaxResponse(false, 'Gallery item not found.');
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'Database error: ' . $e->getMessage());
    }
}

// 2. CREATE OR UPDATE GALLERY ITEM WITH STRICT WEBOP (800x600)
if ($action === 'save') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendAjaxResponse(false, 'Invalid request method.');
    }

    $id = isset($_POST['gallery_id']) && is_numeric($_POST['gallery_id']) ? (int)$_POST['gallery_id'] : 0;
    
    // Sanitize inputs
    $title = sanitizeInput($_POST['title']);
    $status = sanitizeInput($_POST['status']);

    if (empty($title)) {
        sendAjaxResponse(false, 'Title is required.');
    }

    $imagePath = '';

    // Check if new image is uploaded (must be webp, exactly 800x600)
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/gallery/';
        $uploadedFile = uploadWebPImage($_FILES['featured_image'], $uploadDir, $title, 800, 600);
        
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
                // Delete old image
                $oldStmt = $pdo->prepare("SELECT `featured_image` FROM `gallery` WHERE `id` = :id LIMIT 1");
                $oldStmt->execute(['id' => $id]);
                $oldImg = $oldStmt->fetchColumn();
                if ($oldImg && file_exists(__DIR__ . '/../../uploads/gallery/' . $oldImg)) {
                    unlink(__DIR__ . '/../../uploads/gallery/' . $oldImg);
                }

                $stmt = $pdo->prepare("UPDATE `gallery` SET `title` = :title, `featured_image` = :image, `status` = :status WHERE `id` = :id");
                $params = ['image' => $imagePath];
            } else {
                $stmt = $pdo->prepare("UPDATE `gallery` SET `title` = :title, `status` = :status WHERE `id` = :id");
                $params = [];
            }

            $params = array_merge($params, [
                'title' => $title,
                'status' => $status,
                'id' => $id
            ]);

            $result = $stmt->execute($params);

            if ($result) {
                sendAjaxResponse(true, 'Gallery item updated successfully!');
            } else {
                sendAjaxResponse(false, 'Failed to update gallery item.');
            }

        } else {
            // Insert operation
            if (empty($imagePath)) {
                sendAjaxResponse(false, 'Image upload is mandatory for new gallery items.');
            }

            $stmt = $pdo->prepare("INSERT INTO `gallery` (`title`, `featured_image`, `status`) VALUES (:title, :image, :status)");

            $result = $stmt->execute([
                'title' => $title,
                'image' => $imagePath,
                'status' => $status
            ]);

            if ($result) {
                sendAjaxResponse(true, 'New gallery item uploaded successfully!');
            } else {
                sendAjaxResponse(false, 'Failed to upload gallery item.');
            }
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'Database insert/update error: ' . $e->getMessage());
    }
}

// 3. DELETE GALLERY ITEM
if ($action === 'delete') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        sendAjaxResponse(false, 'Invalid Gallery ID.');
    }

    try {
        // Delete image file first
        $stmtImg = $pdo->prepare("SELECT `featured_image` FROM `gallery` WHERE `id` = :id LIMIT 1");
        $stmtImg->execute(['id' => $id]);
        $img = $stmtImg->fetchColumn();
        if ($img && file_exists(__DIR__ . '/../../uploads/gallery/' . $img)) {
            unlink(__DIR__ . '/../../uploads/gallery/' . $img);
        }

        $stmt = $pdo->prepare("DELETE FROM `gallery` WHERE `id` = :id");
        $result = $stmt->execute(['id' => $id]);

        if ($result) {
            sendAjaxResponse(true, 'Gallery item deleted successfully.');
        } else {
            sendAjaxResponse(false, 'Failed to delete the gallery item.');
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'Database deletion error: ' . $e->getMessage());
    }
}

// 4. BATCH IMPORT GALLERY FROM CSV
if ($action === 'import') {
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        sendAjaxResponse(false, 'Please select a valid CSV file.');
    }

    $fileTmp = $_FILES['csv_file']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));

    if ($ext !== 'csv') {
        sendAjaxResponse(false, 'Invalid file format. Please upload a .csv file.');
    }

    try {
        $handle = fopen($fileTmp, "r");
        if ($handle === false) {
            sendAjaxResponse(false, 'Failed to read uploaded CSV.');
        }

        // Read header
        $headers = fgetcsv($handle, 1000, ",");
        
        $importedCount = 0;
        $failedCount = 0;

        while (($row = fgetcsv($handle, 1000, ",")) !== false) {
            if (count($row) < 1) {
                $failedCount++;
                continue;
            }

            // Columns mapping: Title, Status
            $title = sanitizeInput($row[0]);
            $status = isset($row[1]) ? sanitizeInput($row[1]) : 'active';

            if (empty($title)) {
                $failedCount++;
                continue;
            }

            // Default image placeholder
            $defaultImage = 'placeholder-gallery.webp';

            // Insert securely
            $stmt = $pdo->prepare("INSERT INTO `gallery` (`title`, `featured_image`, `status`) VALUES (:title, :image, :status)");

            $res = $stmt->execute([
                'title' => $title,
                'image' => $defaultImage,
                'status' => $status
            ]);

            if ($res) {
                $importedCount++;
            } else {
                $failedCount++;
            }
        }
        fclose($handle);

        sendAjaxResponse(true, "CSV Import completed! Successfully imported: {$importedCount} gallery items. Failed rows: {$failedCount}.", [
            'imported' => $importedCount,
            'failed' => $failedCount
        ]);

    } catch (Exception $e) {
        sendAjaxResponse(false, 'CSV parsing database error: ' . $e->getMessage());
    }
}

sendAjaxResponse(false, 'Invalid Action parameter.');
