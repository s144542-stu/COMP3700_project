<?php
/**
 * Search Bookings - SELECT Query Demo
 * COMP3700 - Part 4
 */

require_once 'dbConfig.php';
require_once 'Booking.php';

$searchResults = [];
$searchPerformed = false;
$criteria = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" || isset($_GET['search'])) {
    $searchPerformed = true;
    
    $searchBy = $_POST['searchBy'] ?? $_GET['searchBy'] ?? '';
    $searchValue = sanitizeInput($_POST['searchValue'] ?? $_GET['searchValue'] ?? '');
    $type = $_POST['bookingType'] ?? $_GET['bookingType'] ?? 'all';
    $status = $_POST['status'] ?? $_GET['status'] ?? 'all';
    
    $conn = getDatabaseConnection();
    if ($conn) {
        $sql = "SELECT * FROM bookings WHERE 1=1";
        $params = [];
        $types = "";
        
        if (!empty($searchValue) && !empty($searchBy)) {
            switch ($searchBy) {
                case 'reference':
                    $sql .= " AND booking_reference LIKE ?";
                    $params[] = "%$searchValue%";
                    $types .= "s";
                    $criteria = "Reference: $searchValue";
                    break;
                    
                case 'customer':
                    $sql .= " AND (customer_name LIKE ? OR customer_email LIKE ?)";
                    $params[] = "%$searchValue%";
                    $params[] = "%$searchValue%";
                    $types .= "ss";
                    $criteria = "Customer: $searchValue";
                    break;
                    
                case 'item':
                    $sql .= " AND item_name LIKE ?";
                    $params[] = "%$searchValue%";
                    $types .= "s";
                    $criteria = "Item: $searchValue";
                    break;
                    
                case 'date':
                    $sql .= " AND (check_in_date = ? OR event_date = ?)";
                    $params[] = $searchValue;
                    $params[] = $searchValue;
                    $types .= "ss";
                    $criteria = "Date: $searchValue";
                    break;
            }
        }
        
        if ($type !== 'all') {
            $sql .= " AND booking_type = ?";
            $params[] = $type;
            $types .= "s";
            if ($criteria) $criteria .= ", ";
            $criteria .= "Type: $type";
        }
        
        if ($status !== 'all') {
            $sql .= " AND booking_status = ?";
            $params[] = $status;
            $types .= "s";
            if ($criteria) $criteria .= ", ";
            $criteria .= "Status: $status";
        }
        
        $sql .= " ORDER BY booking_date DESC";
        
        if (!empty($params)) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $searchResults[] = new Booking($row);
            }
            $stmt->close();
        } else {
            $result = $conn->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $searchResults[] = new Booking($row);
                }
            }
        }
        
        closeDatabaseConnection($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Bookings</title>
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
                    <li class="nav-item"><a class="nav-link" href="viewAllBookings.php">View All</a></li>
                    <li class="nav-item"><a class="nav-link" href="manageBookings.php">Manage</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="mb-4">🔍 Search Bookings</h1>
        
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Search Criteria</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="searchBookings.php">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Search By</label>
                            <select class="form-select" name="searchBy">
                                <option value="">-- Select --</option>
                                <option value="reference">Booking Reference</option>
                                <option value="customer">Customer Name/Email</option>
                                <option value="item">Hotel/Event Name</option>
                                <option value="date">Date</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Search Value</label>
                            <input type="text" class="form-control" name="searchValue" 
                                   placeholder="Enter search term...">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="bookingType">
                                <option value="all">All Types</option>
                                <option value="hotel">Hotel</option>
                                <option value="event">Event</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="all">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">🔍 Search</button>
                            <a href="searchBookings.php" class="btn btn-secondary">Clear</a>
                            <a href="viewAllBookings.php" class="btn btn-info">View All</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if ($searchPerformed): ?>
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Search Results <?php if ($criteria) echo "($criteria)"; ?></h5>
            </div>
            <div class="card-body">
                <?php if (empty($searchResults)): ?>
                    <div class="alert alert-warning">No bookings match your search.</div>
                <?php else: ?>
                    <div class="alert alert-success">
                        Found <strong><?php echo count($searchResults); ?></strong> booking(s).
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Reference</th><th>Customer</th><th>Email</th>
                                    <th>Type</th><th>Item</th><th>Date(s)</th>
                                    <th>Guests</th><th>Status</th><th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($searchResults as $booking): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($booking->getBookingReference()); ?></td>
                                        <td><?php echo htmlspecialchars($booking->getCustomerName()); ?></td>
                                        <td><?php echo htmlspecialchars($booking->getCustomerEmail()); ?></td>
                                        <td><?php echo $booking->getTypeBadge(); ?></td>
                                        <td><?php echo htmlspecialchars($booking->getItemName()); ?></td>
                                        <td><?php echo $booking->getFormattedDateRange(); ?></td>
                                        <td><?php echo $booking->getNumberOfGuests(); ?></td>
                                        <td><?php echo $booking->getStatusBadge(); ?></td>
                                        <td><?php echo number_format($booking->getTotalAmount(), 2); ?> OMR</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <footer class="container-fluid bg-light py-3 mt-5">
        <div class="container text-center text-muted small">
            ©2025 Smart Booking | by Leader Team
        </div>
    </footer>
</body>
</html>