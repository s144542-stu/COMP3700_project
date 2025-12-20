<?php
/**
 * View All Bookings - Array of Objects Demo
 * COMP3700 - Part 4
 */

require_once 'dbConfig.php';
require_once 'Booking.php';

$conn = getDatabaseConnection();
$bookings = [];

if ($conn) {
    $result = $conn->query("SELECT * FROM bookings ORDER BY booking_date DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = new Booking($row);
        }
    }
    closeDatabaseConnection($conn);
}

/**
 * Display bookings table - Required function with iteration
 */
function displayBookingsTable($bookingsArray) {
    if (empty($bookingsArray)) {
        echo '<div class="alert alert-info">No bookings found.</div>';
        return;
    }
    
    echo '<div class="table-responsive">';
    echo '<table class="table table-striped table-bordered">';
    echo '<thead class="table-dark"><tr>';
    echo '<th>Reference</th><th>Customer</th><th>Type</th><th>Item</th>';
    echo '<th>Date(s)</th><th>Guests</th><th>Status</th><th>Amount</th>';
    echo '</tr></thead><tbody>';
    
    foreach ($bookingsArray as $booking) {
        $booking->displayAsTableRow();
    }
    
    echo '</tbody></table></div>';
    
    // Statistics
    $confirmed = $pending = $cancelled = 0;
    $totalRevenue = 0;
    
    foreach ($bookingsArray as $booking) {
        $status = $booking->getBookingStatus();
        if ($status === 'confirmed') {
            $confirmed++;
            $totalRevenue += $booking->getTotalAmount();
        } elseif ($status === 'pending') $pending++;
        elseif ($status === 'cancelled') $cancelled++;
    }
    
    echo '<div class="row mt-4">';
    echo '<div class="col-md-3"><div class="card text-center"><div class="card-body">';
    echo '<h6 class="text-muted">Total</h6><h2 class="text-primary">' . count($bookingsArray) . '</h2>';
    echo '</div></div></div>';
    
    echo '<div class="col-md-3"><div class="card text-center"><div class="card-body">';
    echo '<h6 class="text-muted">Confirmed</h6><h2 class="text-success">' . $confirmed . '</h2>';
    echo '</div></div></div>';
    
    echo '<div class="col-md-3"><div class="card text-center"><div class="card-body">';
    echo '<h6 class="text-muted">Pending</h6><h2 class="text-warning">' . $pending . '</h2>';
    echo '</div></div></div>';
    
    echo '<div class="col-md-3"><div class="card text-center"><div class="card-body">';
    echo '<h6 class="text-muted">Revenue</h6><h2 class="text-info">' . number_format($totalRevenue, 2) . ' OMR</h2>';
    echo '</div></div></div>';
    echo '</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.html">
                <img src="Picture1.png" alt="logo" height="60">
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="booking.html">New Booking</a></li>
                    <li class="nav-item"><a class="nav-link" href="searchBookings.php">Search</a></li>
                    <li class="nav-item"><a class="nav-link" href="manageBookings.php">Manage</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1>All Bookings</h1>
        <p class="text-muted">Demonstrates: Booking class, array of objects, iteration function</p>
        
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📋 All Bookings (Total: <?php echo count($bookings); ?>)</h5>
            </div>
            <div class="card-body">
                <?php displayBookingsTable($bookings); ?>
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