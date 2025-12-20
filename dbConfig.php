<?php
/**
 * Database Configuration File
 * COMP3700 - Part 4 - SmartBooking Project
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smartbooking_db');

/**
 * Get database connection
 */
function getDatabaseConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

/**
 * Close database connection
 */
function closeDatabaseConnection($conn) {
    if ($conn) {
        $conn->close();
    }
}

/**
 * Sanitize input
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Format date
 */
function formatDate($date) {
    if (empty($date) || $date === '0000-00-00') return 'N/A';
    $dateObj = new DateTime($date);
    return $dateObj->format('M d, Y');
}

/**
 * Generate booking reference
 */
function generateBookingReference() {
    $conn = getDatabaseConnection();
    $query = "SELECT MAX(CAST(SUBSTRING(booking_reference, 2) AS UNSIGNED)) as max_ref FROM bookings";
    $result = $conn->query($query);
    
    if ($result && $row = $result->fetch_assoc()) {
        $nextNum = ($row['max_ref'] ?? 0) + 1;
        closeDatabaseConnection($conn);
        return 'B' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
    }
    
    closeDatabaseConnection($conn);
    return 'B001';
}

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 0 for production
?>