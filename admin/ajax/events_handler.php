<?php
// Load database connection and check session integrity
require_once __DIR__ . '/../../config/db.php';
checkAdminSession();

$action = isset($_GET['action']) ? trim($_GET['action']) : '';

// 1. GET SINGLE EVENT BY ID (For Modal Loading)
if ($action === 'get') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        sendAjaxResponse(false, 'Invalid Event ID.');
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM `events` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $event = $stmt->fetch();

        if ($event) {
            sendAjaxResponse(true, 'Event loaded successfully.', ['data' => $event]);
        } else {
            sendAjaxResponse(false, 'Event not found.');
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'Database error: ' . $e->getMessage());
    }
}

// 2. CREATE OR UPDATE EVENT WITH WEBOP VALIDATIONS (1200x700)
if ($action === 'save') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendAjaxResponse(false, 'Invalid request method.');
    }

    $id = isset($_POST['event_id']) && is_numeric($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
    
    // Sanitize and validate inputs
    $title = sanitizeInput($_POST['title']);
    $slug = sanitizeInput($_POST['slug']);
    $short_desc = sanitizeInput($_POST['short_description']);
    $full_desc = trim($_POST['full_description']); // contains CKEditor tags
    
    $event_date = sanitizeInput($_POST['event_date']);
    $event_time = sanitizeInput($_POST['event_time']);
    $location = sanitizeInput($_POST['location']);
    
    $meta_title = sanitizeInput($_POST['meta_title']);
    $meta_keywords = sanitizeInput($_POST['meta_keywords']);
    $meta_description = sanitizeInput($_POST['meta_description']);
    $status = sanitizeInput($_POST['status']);

    if (empty($title) || empty($short_desc) || empty($full_desc) || empty($event_date) || empty($event_time) || empty($location)) {
        sendAjaxResponse(false, 'Title, Date, Time, Venue, Short Description, and Full Description are mandatory.');
    }

    // Slug dynamic generation
    if (empty($slug)) {
        $slug = generateSlug($title, 'events', 'slug', $id > 0 ? $id : null);
    } else {
        $slug = generateSlug($slug, 'events', 'slug', $id > 0 ? $id : null);
    }

    // Set fallback SEO fields
    if (empty($meta_title)) $meta_title = $title;
    if (empty($meta_description)) $meta_description = $short_desc;

    $imagePath = '';

    // Check if new image is uploaded (Must be WebP, exactly 1200x700)
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/events/';
        $uploadedFile = uploadWebPImage($_FILES['featured_image'], $uploadDir, $title, 1200, 700);
        
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
                // Delete old image file
                $oldStmt = $pdo->prepare("SELECT `featured_image` FROM `events` WHERE `id` = :id LIMIT 1");
                $oldStmt->execute(['id' => $id]);
                $oldImg = $oldStmt->fetchColumn();
                if ($oldImg && file_exists(__DIR__ . '/../../uploads/events/' . $oldImg)) {
                    unlink(__DIR__ . '/../../uploads/events/' . $oldImg);
                }

                $stmt = $pdo->prepare("UPDATE `events` SET `title` = :title, `slug` = :slug, `featured_image` = :image, `short_description` = :short_desc, `full_description` = :full_desc, `event_date` = :event_date, `event_time` = :event_time, `location` = :location, `meta_title` = :meta_title, `meta_keywords` = :meta_keywords, `meta_description` = :meta_description, `status` = :status WHERE `id` = :id");
                $params = ['image' => $imagePath];
            } else {
                $stmt = $pdo->prepare("UPDATE `events` SET `title` = :title, `slug` = :slug, `short_description` = :short_desc, `full_description` = :full_desc, `event_date` = :event_date, `event_time` = :event_time, `location` = :location, `meta_title` = :meta_title, `meta_keywords` = :meta_keywords, `meta_description` = :meta_description, `status` = :status WHERE `id` = :id");
                $params = [];
            }

            $params = array_merge($params, [
                'title' => $title,
                'slug' => $slug,
                'short_desc' => $short_desc,
                'full_desc' => $full_desc,
                'event_date' => $event_date,
                'event_time' => $event_time,
                'location' => $location,
                'meta_title' => $meta_title,
                'meta_keywords' => $meta_keywords,
                'meta_description' => $meta_description,
                'status' => $status,
                'id' => $id
            ]);

            $result = $stmt->execute($params);

            if ($result) {
                sendAjaxResponse(true, 'Event updated successfully!');
            } else {
                sendAjaxResponse(false, 'Failed to update the event.');
            }

        } else {
            // Insert operation
            if (empty($imagePath)) {
                sendAjaxResponse(false, 'Featured image is mandatory for new events.');
            }

            $stmt = $pdo->prepare("INSERT INTO `events` (`title`, `slug`, `featured_image`, `short_description`, `full_description`, `event_date`, `event_time`, `location`, `meta_title`, `meta_keywords`, `meta_description`, `status`) VALUES (:title, :slug, :image, :short_desc, :full_desc, :event_date, :event_time, :location, :meta_title, :meta_keywords, :meta_description, :status)");

            $result = $stmt->execute([
                'title' => $title,
                'slug' => $slug,
                'image' => $imagePath,
                'short_desc' => $short_desc,
                'full_desc' => $full_desc,
                'event_date' => $event_date,
                'event_time' => $event_time,
                'location' => $location,
                'meta_title' => $meta_title,
                'meta_keywords' => $meta_keywords,
                'meta_description' => $meta_description,
                'status' => $status
            ]);

            if ($result) {
                sendAjaxResponse(true, 'New event created successfully!');
            } else {
                sendAjaxResponse(false, 'Failed to schedule new event.');
            }
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'Database insert/update error: ' . $e->getMessage());
    }
}

// 3. DELETE EVENT
if ($action === 'delete') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        sendAjaxResponse(false, 'Invalid Event ID.');
    }

    try {
        // Delete image file
        $stmtImg = $pdo->prepare("SELECT `featured_image` FROM `events` WHERE `id` = :id LIMIT 1");
        $stmtImg->execute(['id' => $id]);
        $img = $stmtImg->fetchColumn();
        if ($img && file_exists(__DIR__ . '/../../uploads/events/' . $img)) {
            unlink(__DIR__ . '/../../uploads/events/' . $img);
        }

        $stmt = $pdo->prepare("DELETE FROM `events` WHERE `id` = :id");
        $result = $stmt->execute(['id' => $id]);

        if ($result) {
            sendAjaxResponse(true, 'Event deleted successfully.');
        } else {
            sendAjaxResponse(false, 'Failed to delete the event.');
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'Database deletion error: ' . $e->getMessage());
    }
}

// 4. BATCH IMPORT EVENTS FROM CSV
if ($action === 'import') {
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        sendAjaxResponse(false, 'Please select a valid CSV file.');
    }

    $fileTmp = $_FILES['csv_file']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));
    
    if ($ext !== 'csv') {
        sendAjaxResponse(false, 'Invalid file format. Only .csv files are supported.');
    }

    try {
        $handle = fopen($fileTmp, "r");
        if ($handle === false) {
            sendAjaxResponse(false, 'Failed to open the uploaded CSV.');
        }

        // Read header
        $headers = fgetcsv($handle, 1000, ",");
        
        $importedCount = 0;
        $failedCount = 0;

        while (($row = fgetcsv($handle, 1000, ",")) !== false) {
            if (count($row) < 6) {
                $failedCount++;
                continue; // Skip incomplete lines
            }

            // Columns mapping: Title, ShortDesc, FullDesc, EventDate, EventTime, Location, MetaTitle, MetaKeywords, MetaDescription, Status
            $title = sanitizeInput($row[0]);
            $short_desc = sanitizeInput($row[1]);
            $full_desc = trim($row[2]);
            $event_date = sanitizeInput($row[3]);
            $event_time = sanitizeInput($row[4]);
            $location = sanitizeInput($row[5]);

            $meta_title = isset($row[6]) ? sanitizeInput($row[6]) : $title;
            $meta_keywords = isset($row[7]) ? sanitizeInput($row[7]) : 'imported, events';
            $meta_description = isset($row[8]) ? sanitizeInput($row[8]) : $short_desc;
            $status = isset($row[9]) ? sanitizeInput($row[9]) : 'active';

            if (empty($title) || empty($short_desc) || empty($full_desc) || empty($event_date) || empty($event_time) || empty($location)) {
                $failedCount++;
                continue;
            }

            // Generate slug
            $slug = generateSlug($title, 'events', 'slug');

            // Default image placeholder
            $defaultImage = 'placeholder-event.webp';

            // Insert securely
            $stmt = $pdo->prepare("INSERT INTO `events` (`title`, `slug`, `featured_image`, `short_description`, `full_description`, `event_date`, `event_time`, `location`, `meta_title`, `meta_keywords`, `meta_description`, `status`) VALUES (:title, :slug, :image, :short_desc, :full_desc, :event_date, :event_time, :location, :meta_title, :meta_keywords, :meta_description, :status)");

            $res = $stmt->execute([
                'title' => $title,
                'slug' => $slug,
                'image' => $defaultImage,
                'short_desc' => $short_desc,
                'full_desc' => $full_desc,
                'event_date' => $event_date,
                'event_time' => $event_time,
                'location' => $location,
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

        sendAjaxResponse(true, "CSV Import completed! Successfully imported: {$importedCount} events. Failed rows: {$failedCount}.", [
            'imported' => $importedCount,
            'failed' => $failedCount
        ]);

    } catch (Exception $e) {
        sendAjaxResponse(false, 'CSV parsing database error: ' . $e->getMessage());
    }
}

sendAjaxResponse(false, 'Invalid Action parameter.');
