<?php
// Load global configuration and connection utilities
require_once __DIR__ . '/../../config/db.php';

$action = isset($_GET['action']) ? trim($_GET['action']) : '';

// 1. FRONTEND ENQUIRY INGESTION - Accessible without log in session
if ($action === 'create') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendAjaxResponse(false, 'Invalid request method.');
    }

    // Sanitize inputs
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $subject = sanitizeInput($_POST['subject']);
    $message = sanitizeInput($_POST['message']);

    // Validate fields
    if (empty($name) || empty($email) || empty($phone) || empty($subject) || empty($message)) {
        sendAjaxResponse(false, 'All fields are mandatory. Please fill in the details.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendAjaxResponse(false, 'Invalid email format.');
    }

    try {
        // Insert record securely using PDO Prepared Statements
        $stmt = $pdo->prepare("INSERT INTO `enquiries` (`name`, `email`, `phone`, `subject`, `message`) VALUES (:name, :email, :phone, :subject, :message)");
        $result = $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message
        ]);

        if ($result) {
            sendAjaxResponse(true, 'Your enquiry has been delivered successfully. Thank you!');
        } else {
            sendAjaxResponse(false, 'Unable to process your enquiry. Database error.');
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'Server error occurred: ' . $e->getMessage());
    }
}

// --------------------------------------------------------
// STRICT SECURITY CHECK - SESSIONS ARE REQUIRED BEYOND THIS POINT
// --------------------------------------------------------
checkAdminSession();

// 2. VIEW SPECIFIC ENQUIRY & MARK AS READ
if ($action === 'view') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        sendAjaxResponse(false, 'Invalid Enquiry ID.');
    }

    try {
        // Mark as read dynamically
        $updateStmt = $pdo->prepare("UPDATE `enquiries` SET `is_read` = 1 WHERE `id` = :id");
        $updateStmt->execute(['id' => $id]);

        // Fetch details
        $stmt = $pdo->prepare("SELECT * FROM `enquiries` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $enquiry = $stmt->fetch();

        if ($enquiry) {
            sendAjaxResponse(true, 'Enquiry details loaded successfully.', ['data' => $enquiry]);
        } else {
            sendAjaxResponse(false, 'Enquiry not found.');
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'System error: ' . $e->getMessage());
    }
}

// 3. DELETE ENQUIRY
if ($action === 'delete') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) {
        sendAjaxResponse(false, 'Invalid Enquiry ID.');
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM `enquiries` WHERE `id` = :id");
        $result = $stmt->execute(['id' => $id]);

        if ($result) {
            sendAjaxResponse(true, 'Enquiry deleted successfully.');
        } else {
            sendAjaxResponse(false, 'Failed to delete the enquiry.');
        }
    } catch (Exception $e) {
        sendAjaxResponse(false, 'System error: ' . $e->getMessage());
    }
}

// Default fallback
sendAjaxResponse(false, 'Invalid Action parameter.');
