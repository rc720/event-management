<?php
// Load db configurations and security check
require_once __DIR__ . '/../../config/db.php';
checkAdminSession();

$action = isset($_GET['action']) ? trim($_GET['action']) : '';

// 1. SAVE GLOBAL WEBSITE SETTINGS
if ($action === 'save') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendAjaxResponse(false, 'Invalid request method.');
    }

    // Sanitize contact and text details
    $site_title = sanitizeInput($_POST['site_title']);
    $phone = sanitizeInput($_POST['phone']);
    $email = sanitizeInput($_POST['email']);
    $address = sanitizeInput($_POST['address']);
    
    $facebook_link = sanitizeInput($_POST['facebook_link']);
    $facebook_enable = isset($_POST['facebook_enable']) ? 1 : 0;
    
    $twitter_link = sanitizeInput($_POST['twitter_link']);
    $twitter_enable = isset($_POST['twitter_enable']) ? 1 : 0;
    
    $instagram_link = sanitizeInput($_POST['instagram_link']);
    $instagram_enable = isset($_POST['instagram_enable']) ? 1 : 0;
    
    $linkedin_link = sanitizeInput($_POST['linkedin_link']);
    $linkedin_enable = isset($_POST['linkedin_enable']) ? 1 : 0;

    if (empty($site_title) || empty($phone) || empty($email) || empty($address)) {
        sendAjaxResponse(false, 'Site Title, Phone, Email, and Address are mandatory.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendAjaxResponse(false, 'Invalid email format.');
    }

    $logoFilename = '';

    // Handle Logo Upload (Strictly WebP as per general upload policy!)
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/logo/';
        $filename = $_FILES['logo']['name'];
        $tmpPath = $_FILES['logo']['tmp_name'];
        
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($ext !== 'webp') {
            sendAjaxResponse(false, 'Invalid file type. Only WebP (.webp) image format is allowed for logo!');
        }

        // Create folder if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $logoFilename = 'site-logo-' . time() . '.webp';
        
        if (move_uploaded_file($tmpPath, $uploadDir . $logoFilename)) {
            // Delete old logo if exists
            $stmtOld = $pdo->query("SELECT `logo` FROM `settings` LIMIT 1");
            $oldLogo = $stmtOld->fetchColumn();
            if ($oldLogo && file_exists($uploadDir . $oldLogo)) {
                unlink($uploadDir . $oldLogo);
            }
        } else {
            sendAjaxResponse(false, 'Failed to save the logo file.');
        }
    }

    try {
        // Prepare dynamic query depending on logo update
        if (!empty($logoFilename)) {
            $stmt = $pdo->prepare("UPDATE `settings` SET `site_title` = :site_title, `logo` = :logo, `phone` = :phone, `email` = :email, `address` = :address, `facebook_link` = :facebook_link, `facebook_enable` = :facebook_enable, `twitter_link` = :twitter_link, `twitter_enable` = :twitter_enable, `instagram_link` = :instagram_link, `instagram_enable` = :instagram_enable, `linkedin_link` = :linkedin_link, `linkedin_enable` = :linkedin_enable WHERE `id` = 1");
            $params = ['logo' => $logoFilename];
        } else {
            $stmt = $pdo->prepare("UPDATE `settings` SET `site_title` = :site_title, `phone` = :phone, `email` = :email, `address` = :address, `facebook_link` = :facebook_link, `facebook_enable` = :facebook_enable, `twitter_link` = :twitter_link, `twitter_enable` = :twitter_enable, `instagram_link` = :instagram_link, `instagram_enable` = :instagram_enable, `linkedin_link` = :linkedin_link, `linkedin_enable` = :linkedin_enable WHERE `id` = 1");
            $params = [];
        }

        $params = array_merge($params, [
            'site_title' => $site_title,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'facebook_link' => $facebook_link,
            'facebook_enable' => $facebook_enable,
            'twitter_link' => $twitter_link,
            'twitter_enable' => $twitter_enable,
            'instagram_link' => $instagram_link,
            'instagram_enable' => $instagram_enable,
            'linkedin_link' => $linkedin_link,
            'linkedin_enable' => $linkedin_enable
        ]);

        $result = $stmt->execute($params);

        if ($result) {
            sendAjaxResponse(true, 'Global settings updated successfully!');
        } else {
            sendAjaxResponse(false, 'Failed to update system settings.');
        }

    } catch (Exception $e) {
        sendAjaxResponse(false, 'System database error: ' . $e->getMessage());
    }
}

// 2. DELETE DYNAMIC LOGO FILE
if ($action === 'delete_logo') {
    try {
        $stmtOld = $pdo->query("SELECT `logo` FROM `settings` LIMIT 1");
        $oldLogo = $stmtOld->fetchColumn();
        
        if ($oldLogo) {
            $logoPath = __DIR__ . '/../../uploads/logo/' . $oldLogo;
            if (file_exists($logoPath)) {
                unlink($logoPath);
            }
            
            // Set logo col to NULL in database
            $pdo->query("UPDATE `settings` SET `logo` = NULL WHERE `id` = 1");
            sendAjaxResponse(true, 'Site logo removed successfully!');
        } else {
            sendAjaxResponse(false, 'No logo found to delete.');
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'System database error: ' . $e->getMessage());
    }
}

sendAjaxResponse(false, 'Invalid Action parameter.');
