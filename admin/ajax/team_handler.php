<?php
// Load db configs
require_once __DIR__ . '/../../config/db.php';
checkAdminSession();

$action = isset($_GET['action']) ? trim($_GET['action']) : '';

// 1. GET SINGLE TEAM MEMBER BY ID
if ($action === 'get') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        sendAjaxResponse(false, 'Invalid Member ID.');
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM `team_members` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $team = $stmt->fetch();

        if ($team) {
            sendAjaxResponse(true, 'Member loaded successfully.', ['data' => $team]);
        } else {
            sendAjaxResponse(false, 'Member not found.');
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'Database error: ' . $e->getMessage());
    }
}

// 2. CREATE OR UPDATE TEAM MEMBER WITH STRICT WEBOP (500x500)
if ($action === 'save') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendAjaxResponse(false, 'Invalid request method.');
    }

    $id = isset($_POST['member_id']) && is_numeric($_POST['member_id']) ? (int)$_POST['member_id'] : 0;
    
    // Sanitize inputs
    $name = sanitizeInput($_POST['name']);
    $designation = sanitizeInput($_POST['designation']);
    $description = trim($_POST['description']); // bio contains CKEditor tags
    
    $facebook = sanitizeInput($_POST['facebook']);
    $twitter = sanitizeInput($_POST['twitter']);
    $instagram = sanitizeInput($_POST['instagram']);
    $linkedin = sanitizeInput($_POST['linkedin']);
    $status = sanitizeInput($_POST['status']);

    if (empty($name) || empty($designation) || empty($description)) {
        sendAjaxResponse(false, 'Name, Designation, and biography description are required.');
    }

    $imagePath = '';

    // Check if new image is uploaded (must be webp, exactly 500x500)
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/team/';
        $uploadedFile = uploadWebPImage($_FILES['featured_image'], $uploadDir, $name, 500, 500);
        
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
                $oldStmt = $pdo->prepare("SELECT `featured_image` FROM `team_members` WHERE `id` = :id LIMIT 1");
                $oldStmt->execute(['id' => $id]);
                $oldImg = $oldStmt->fetchColumn();
                if ($oldImg && file_exists(__DIR__ . '/../../uploads/team/' . $oldImg)) {
                    unlink(__DIR__ . '/../../uploads/team/' . $oldImg);
                }

                $stmt = $pdo->prepare("UPDATE `team_members` SET `name` = :name, `designation` = :designation, `description` = :description, `featured_image` = :image, `facebook` = :facebook, `twitter` = :twitter, `instagram` = :instagram, `linkedin` = :linkedin, `status` = :status WHERE `id` = :id");
                $params = ['image' => $imagePath];
            } else {
                $stmt = $pdo->prepare("UPDATE `team_members` SET `name` = :name, `designation` = :designation, `description` = :description, `facebook` = :facebook, `twitter` = :twitter, `instagram` = :instagram, `linkedin` = :linkedin, `status` = :status WHERE `id` = :id");
                $params = [];
            }

            $params = array_merge($params, [
                'name' => $name,
                'designation' => $designation,
                'description' => $description,
                'facebook' => $facebook,
                'twitter' => $twitter,
                'instagram' => $instagram,
                'linkedin' => $linkedin,
                'status' => $status,
                'id' => $id
            ]);

            $result = $stmt->execute($params);

            if ($result) {
                sendAjaxResponse(true, 'Member profile updated successfully!');
            } else {
                sendAjaxResponse(false, 'Failed to update member profile.');
            }

        } else {
            // Insert operation
            if (empty($imagePath)) {
                sendAjaxResponse(false, 'Profile image upload is mandatory for new member creation.');
            }

            $stmt = $pdo->prepare("INSERT INTO `team_members` (`name`, `designation`, `description`, `featured_image`, `facebook`, `twitter`, `instagram`, `linkedin`, `status`) VALUES (:name, :designation, :description, :image, :facebook, :twitter, :instagram, :linkedin, :status)");

            $result = $stmt->execute([
                'name' => $name,
                'designation' => $designation,
                'description' => $description,
                'image' => $imagePath,
                'facebook' => $facebook,
                'twitter' => $twitter,
                'instagram' => $instagram,
                'linkedin' => $linkedin,
                'status' => $status
            ]);

            if ($result) {
                sendAjaxResponse(true, 'New member profile published successfully!');
            } else {
                sendAjaxResponse(false, 'Failed to publish new member profile.');
            }
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'Database insert/update error: ' . $e->getMessage());
    }
}

// 3. DELETE TEAM MEMBER
if ($action === 'delete') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        sendAjaxResponse(false, 'Invalid Member ID.');
    }

    try {
        // Delete image file first
        $stmtImg = $pdo->prepare("SELECT `featured_image` FROM `team_members` WHERE `id` = :id LIMIT 1");
        $stmtImg->execute(['id' => $id]);
        $img = $stmtImg->fetchColumn();
        if ($img && file_exists(__DIR__ . '/../../uploads/team/' . $img)) {
            unlink(__DIR__ . '/../../uploads/team/' . $img);
        }

        $stmt = $pdo->prepare("DELETE FROM `team_members` WHERE `id` = :id");
        $result = $stmt->execute(['id' => $id]);

        if ($result) {
            sendAjaxResponse(true, 'Member profile deleted successfully.');
        } else {
            sendAjaxResponse(false, 'Failed to delete the member profile.');
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'Database deletion error: ' . $e->getMessage());
    }
}

// 4. BATCH IMPORT TEAM FROM CSV
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
            if (count($row) < 3) {
                $failedCount++;
                continue;
            }

            // Columns mapping: Name, Designation, Description, Facebook, Twitter, Instagram, LinkedIn, Status
            $name = sanitizeInput($row[0]);
            $designation = sanitizeInput($row[1]);
            $description = trim($row[2]);

            $facebook = isset($row[3]) ? sanitizeInput($row[3]) : '';
            $twitter = isset($row[4]) ? sanitizeInput($row[4]) : '';
            $instagram = isset($row[5]) ? sanitizeInput($row[5]) : '';
            $linkedin = isset($row[6]) ? sanitizeInput($row[6]) : '';
            $status = isset($row[7]) ? sanitizeInput($row[7]) : 'active';

            if (empty($name) || empty($designation) || empty($description)) {
                $failedCount++;
                continue;
            }

            // Default image placeholder
            $defaultImage = 'placeholder-team.webp';

            // Insert securely
            $stmt = $pdo->prepare("INSERT INTO `team_members` (`name`, `designation`, `description`, `featured_image`, `facebook`, `twitter`, `instagram`, `linkedin`, `status`) VALUES (:name, :designation, :description, :image, :facebook, :twitter, :instagram, :linkedin, :status)");

            $res = $stmt->execute([
                'name' => $name,
                'designation' => $designation,
                'description' => $description,
                'image' => $defaultImage,
                'facebook' => $facebook,
                'twitter' => $twitter,
                'instagram' => $instagram,
                'linkedin' => $linkedin,
                'status' => $status
            ]);

            if ($res) {
                $importedCount++;
            } else {
                $failedCount++;
            }
        }
        fclose($handle);

        sendAjaxResponse(true, "CSV Import completed! Successfully imported: {$importedCount} profiles. Failed rows: {$failedCount}.", [
            'imported' => $importedCount,
            'failed' => $failedCount
        ]);

    } catch (Exception $e) {
        sendAjaxResponse(false, 'CSV parsing database error: ' . $e->getMessage());
    }
}

sendAjaxResponse(false, 'Invalid Action parameter.');
