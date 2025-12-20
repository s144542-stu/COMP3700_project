<?php
/**
 * Process Booking Form
 * COMP3700 - Part 4
 */

require_once 'dbConfig.php';
require_once 'Booking.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: booking.html");
    exit();
}

// Get and sanitize form data
$fullName = sanitizeInput($_POST['fullName'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');
$bookingType = sanitizeInput($_POST['bookingType'] ?? '');
$itemName = sanitizeInput($_POST['itemName'] ?? '');
$checkInDate = !empty($_POST['checkInDate']) ? sanitizeInput($_POST['checkInDate']) : null;
$checkOutDate = !empty($_POST['checkOutDate']) ? sanitizeInput($_POST['checkOutDate']) : null;
$eventDate = !empty($_POST['eventDate']) ? sanitizeInput($_POST['eventDate']) : null;
$guests = intval($_POST['guests'] ?? 1);
$specialRequests = sanitizeInput($_POST['specialRequests'] ?? '');

// Validation
$errors = [];

if (empty($fullName)) $errors[] = "Full name is required";
if (empty($email) || !validateEmail($email)) $errors[] = "Valid email is required";
if (empty($bookingType)) $errors[] = "Booking type is required";
if (empty($itemName)) $errors[] = "Hotel/Event selection is required";
if ($guests < 1 || $guests > 20) $errors[] = "Guests must be between 1 and 20";

if ($bookingType === 'hotel') {
    if (empty($checkInDate) || empty($checkOutDate)) {
        $errors[] = "Check-in and check-out dates required for hotels";
    }
} else if ($bookingType === 'event') {
    if (empty($eventDate)) {
        $errors[] = "Event date is required";
    }
}

if (!empty($errors)) {
    displayErrors($errors);
    exit();
}

// Database operations
$conn = getDatabaseConnection();
if (!$conn) {
    die("Database connection failed");
}

// Generate booking reference
$bookingReference = generateBookingReference();

// Calculate total amount
$totalAmount = 0;
if ($bookingType === 'hotel' && !empty($checkInDate) && !empty($checkOutDate)) {
    $stmt = $conn->prepare("SELECT price_per_night FROM hotels WHERE hotel_name LIKE ? LIMIT 1");
    $searchName = $itemName . '%';
    $stmt->bind_param("s", $searchName);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $pricePerNight = $row['price_per_night'];
        $nights = (strtotime($checkOutDate) - strtotime($checkInDate)) / (60 * 60 * 24);
        $totalAmount = $pricePerNight * $nights;
    }
    $stmt->close();
} else if ($bookingType === 'event') {
    $stmt = $conn->prepare("SELECT ticket_price FROM events WHERE event_name LIKE ? LIMIT 1");
    $searchName = $itemName . '%';
    $stmt->bind_param("s", $searchName);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $totalAmount = $row['ticket_price'] * $guests;
    }
    $stmt->close();
}

// Insert booking
$sql = "INSERT INTO bookings (booking_reference, customer_name, customer_email, customer_phone, 
        booking_type, item_name, check_in_date, check_out_date, event_date, number_of_guests, 
        special_requests, booking_status, total_amount) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssisd", 
    $bookingReference, $fullName, $email, $phone, $bookingType, $itemName, 
    $checkInDate, $checkOutDate, $eventDate, $guests, $specialRequests, $totalAmount);

if ($stmt->execute()) {
    $bookingId = $conn->insert_id;
    
    // Fetch complete booking
    $query = "SELECT * FROM bookings WHERE booking_id = ?";
    $stmt2 = $conn->prepare($query);
    $stmt2->bind_param("i", $bookingId);
    $stmt2->execute();
    $result = $stmt2->get_result();
    $bookingData = $result->fetch_assoc();
    
    $booking = new Booking($bookingData);
    displaySuccess($booking);
    
    $stmt2->close();
} else {
    displayErrors(["Failed to create booking: " . $stmt->error]);
}

$stmt->close();
closeDatabaseConnection($conn);

function displayErrors($errors) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Booking Error</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="index.html">
                    <img src="Picture1.png" alt="logo" height="60">
                </a>
            </div>
        </nav>
        
        <div class="container my-5">
            <div class="alert alert-danger">
                <h4>❌ Booking Errors</h4>
                <hr>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <hr>
                <a href="booking.html" class="btn btn-primary">← Back to Form</a>
            </div>
        </div>
    </body>
    </html>
    <?php
}

function displaySuccess($booking) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Booking Confirmed</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <nav class="navbar navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="index.html">
                    <img src="Picture1.png" alt="logo" height="60">
                </a>
            </div>
        </nav>
        
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="alert alert-success text-center">
                        <h2>✓ Booking Confirmed!</h2>
                        <p class="lead">Your booking reference: <strong><?php echo $booking->getBookingReference(); ?></strong></p>
                    </div>
                    
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Booking Details</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <th width="30%">Reference:</th>
                                        <td><strong><?php echo $booking->getBookingReference(); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <th>Customer:</th>
                                        <td><?php echo htmlspecialchars($booking->getCustomerName()); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td><?php echo htmlspecialchars($booking->getCustomerEmail()); ?></td>
                                    </tr>
                                    <?php if (!empty($booking->getCustomerPhone())): ?>
                                    <tr>
                                        <th>Phone:</th>
                                        <td><?php echo htmlspecialchars($booking->getCustomerPhone()); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Type:</th>
                                        <td><?php echo $booking->getTypeBadge(); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo $booking->getBookingType() === 'hotel' ? 'Hotel' : 'Event'; ?>:</th>
                                        <td><?php echo htmlspecialchars($booking->getItemName()); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date(s):</th>
                                        <td><?php echo $booking->getFormattedDateRange(); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Guests:</th>
                                        <td><?php echo $booking->getNumberOfGuests(); ?></td>
                                    </tr>
                                    <?php if (!empty($booking->getSpecialRequests())): ?>
                                    <tr>
                                        <th>Special Requests:</th>
                                        <td><?php echo htmlspecialchars($booking->getSpecialRequests()); ?></td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th>Total:</th>
                                        <td><strong><?php echo number_format($booking->getTotalAmount(), 2); ?> OMR</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td><?php echo $booking->getStatusBadge(); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer text-center">
                            <a href="index.html" class="btn btn-primary">← Home</a>
                            <a href="booking.html" class="btn btn-success">New Booking</a>
                            <a href="viewAllBookings.php" class="btn btn-info">View All</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <footer class="container-fluid bg-light py-3 mt-5">
            <div class="container text-center text-muted small">
                ©2025 Smart Booking | by Leader Team
            </div>
        </footer>
    </body>
    </html>
    <?php
}
?>