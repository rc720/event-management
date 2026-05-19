<?php
/**
 * Core Database Connection & Helper Functions
 * Using PDO for advanced security and prepared statements.
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    // Enable secure session cookie settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    // If running on HTTPS, set secure cookie
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}

// Database Credentials - Easily adjustable
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'creative_events_db');

try {
    // Establish secure PDO Connection
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    // If the database doesn't exist, we can output a clean message
    // but in production, show a generic message
    die("Database Connection Failed. Please ensure 'creative_events_db' database is imported using the 'database.sql' file and credentials in 'config/db.php' are correct.<br><br>Error details: " . htmlspecialchars($e->getMessage()));
}

/**
 * Sanitize user input to prevent XSS (Cross Site Scripting)
 * @param mixed $data Input data (string or array)
 * @return mixed Sanitized output
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitizeInput($value);
        }
        return $data;
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate an SEO-friendly URL Slug from a string
 * Automatically removes special characters, lowercases, and inserts hyphens.
 * Also checks DB table for uniqueness and appends numeric counter if slug exists.
 * 
 * @param string $string Raw title or text
 * @param string $table Target database table to check for uniqueness
 * @param string $column Column name storing the slug
 * @param int|null $excludeId ID of record to exclude from uniqueness check (for edits)
 * @return string Generated unique SEO-friendly slug
 */
function generateSlug($string, $table, $column = 'slug', $excludeId = null) {
    global $pdo;
    
    // Convert to lowercase, replace spaces and non-alphanumeric with hyphens
    $slug = preg_replace('/[^a-z0-9\-]/', '', strtolower(str_replace(' ', '-', trim($string))));
    // Replace multiple consecutive hyphens with a single hyphen
    $slug = preg_replace('/-+/', '-', $slug);
    // Trim hyphens from beginning and end
    $slug = trim($slug, '-');
    
    if (empty($slug)) {
        $slug = 'post';
    }
    
    // Check for uniqueness in the database
    $queryStr = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = :slug";
    if ($excludeId !== null) {
        $queryStr .= " AND `id` != :exclude_id";
    }
    
    $stmt = $pdo->prepare($queryStr);
    
    $originalSlug = $slug;
    $counter = 1;
    
    while (true) {
        $params = ['slug' => $slug];
        if ($excludeId !== null) {
            $params['exclude_id'] = $excludeId;
        }
        
        $stmt->execute($params);
        $count = $stmt->fetchColumn();
        
        if ($count == 0) {
            break; // Slug is unique
        }
        
        // Append counter to maintain uniqueness
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}

/**
 * Send unified JSON AJAX Response and terminate script execution
 * @param bool $success Operation status
 * @param string $message Notification or error message
 * @param array $extra Optional key-value variables to include in payload
 */
function sendAjaxResponse($success, $message, $extra = []) {
    header('Content-Type: application/json');
    $response = array_merge([
        'success' => $success,
        'message' => $message
    ], $extra);
    echo json_encode($response);
    exit;
}

/**
 * Restrict page access to logged-in administrator users only
 */
function checkAdminSession() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true || !isset($_SESSION['admin_id'])) {
        // If it's an AJAX request, return unauthorized JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            sendAjaxResponse(false, 'Session expired. Please log in again.');
        } else {
            header('Location: login.php');
            exit;
        }
    }
}

/**
 * Fetch dynamic system settings from settings table
 * @return array Associative array of settings
 */
function getSystemSettings() {
    global $pdo;
    static $settings = null;
    if ($settings === null) {
        $stmt = $pdo->query("SELECT * FROM `settings` LIMIT 1");
        $settings = $stmt->fetch();
        if (!$settings) {
            // Return empty layout fallback
            $settings = [
                'site_title' => 'AuraEvents',
                'logo' => null,
                'phone' => '',
                'email' => '',
                'address' => '',
                'facebook_link' => '',
                'facebook_enable' => 0,
                'twitter_link' => '',
                'twitter_enable' => 0,
                'instagram_link' => '',
                'instagram_enable' => 0,
                'linkedin_link' => '',
                'linkedin_enable' => 0,
            ];
        }
    }
    return $settings;
}

/**
 * Upload and validate WebP images based on strict dimensions
 * Reject all other mime types/extensions (png, jpg, jpeg).
 * Enforces exact required dimensions.
 *
 * @param array $fileObj PHP $_FILES element
 * @param string $uploadDir Absolute or relative directory path (must end with /)
 * @param string $slugBase Name base for slugifying the file
 * @param int $reqWidth Required image width
 * @param int $reqHeight Required image height
 * @return string|bool Uploaded filename string on success, false on failure (errors stored in $_SESSION['upload_error'])
 */
function uploadWebPImage($fileObj, $uploadDir, $slugBase, $reqWidth, $reqHeight) {
    unset($_SESSION['upload_error']);
    
    if (!isset($fileObj) || $fileObj['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['upload_error'] = "File upload failed or no file selected.";
        return false;
    }
    
    $filename = $fileObj['name'];
    $tmpPath = $fileObj['tmp_name'];
    $fileSize = $fileObj['size'];
    
    // Check file extension
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if ($ext !== 'webp') {
        $_SESSION['upload_error'] = "Invalid file type. Only WebP (.webp) images are allowed.";
        return false;
    }
    
    // Validate MIME type securely
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);
    } else {
        $mimeType = $fileObj['type'];
    }
    
    if ($mimeType !== 'image/webp') {
        $_SESSION['upload_error'] = "Invalid image content. Only real WebP images are allowed.";
        return false;
    }
    
    // Check image dimensions
    $imageSizes = getimagesize($tmpPath);
    if ($imageSizes === false) {
        $_SESSION['upload_error'] = "Invalid image file. Could not read dimensions.";
        return false;
    }
    
    $width = $imageSizes[0];
    $height = $imageSizes[1];
    
    if ($width != $reqWidth || $height != $reqHeight) {
        $_SESSION['upload_error'] = "Invalid dimensions! Required dimensions are {$reqWidth}x{$reqHeight} pixels. Your image is {$width}x{$height} pixels.";
        return false;
    }
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // SEO-friendly slugified file naming
    $cleanSlug = preg_replace('/[^a-z0-9\-]/', '', strtolower(str_replace(' ', '-', trim($slugBase))));
    $cleanSlug = preg_replace('/-+/', '-', $cleanSlug);
    $cleanSlug = trim($cleanSlug, '-');
    if (empty($cleanSlug)) {
        $cleanSlug = 'image';
    }
    
    $newFilename = $cleanSlug . '-' . time() . '-' . rand(1000, 9999) . '.webp';
    $destination = $uploadDir . $newFilename;
    
    if (move_uploaded_file($tmpPath, $destination)) {
        return $newFilename;
    } else {
        $_SESSION['upload_error'] = "Failed to save uploaded image. Check directory permissions.";
        return false;
    }
}
