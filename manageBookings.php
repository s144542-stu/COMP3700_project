<?php
/**
 * Manage Bookings - INSERT, UPDATE, DELETE
 * COMP3700 - Part 4
 */

require_once 'dbConfig.php';
require_once 'Booking.php';

$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = getDatabaseConnection();
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'insert':
            $ref = generateBookingReference();
            $name = sanitizeInput($_POST['customer_name'] ?? '');
            $email = sanitizeInput($_POST['customer_email'] ?? '');
            $phone = sanitizeInput($_POST['customer_phone'] ?? '');
            $type = sanitizeInput($_POST['booking_type'] ?? '');
            $item = sanitizeInput($_POST['item_name'] ?? '');
            $guests = intval($_POST['number_of_guests'] ?? 1);
            $amount = floatval($_POST['total_amount'] ?? 0);
            $status = 'pending';
            
            $checkIn = $type === 'hotel' ? ($_POST['check_in_date'] ?? null) : null;
            $checkOut = $type === 'hotel' ? ($_POST['check_out_date'] ?? null) : null;
            $eventDate = $type === 'event' ? ($_POST['event_date'] ?? null) : null;
            
            $sql = "INSERT INTO bookings (booking_reference, customer_name, customer_email, customer_phone, 
                    booking_type, item_name, check_in_date, check_out_date, event_date, 
                    number_of_guests, booking_status, total_amount) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssssisd", $ref, $name, $email, $phone, $type, $item, 
                              $checkIn, $checkOut, $eventDate, $guests, $status, $amount);
            
            if ($stmt->execute()) {
                $message = "✓ Booking $ref created successfully!";
                $messageType = "success";
            } else {
                $message = "❌ Error: " . $stmt->error;
                $messageType = "danger";
            }
            $stmt->close();
            break;
            
        case 'update':
            $id = intval($_POST['booking_id'] ?? 0);
            $status = sanitizeInput($_POST['booking_status'] ?? '');
            $amount = floatval($_POST['total_amount'] ?? 0);
            $guests = intval($_POST['number_of_guests'] ?? 1);
            
            $sql = "UPDATE bookings SET booking_status = ?, total_amount = ?, number_of_guests = ? 
                    WHERE booking_id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdii", $status, $amount, $guests, $id);
            
            if ($stmt->execute()) {
                $message = "✓ Booking updated!";
                $messageType = "success";
            } else {
                $message = "❌ Error: " . $stmt->error;
                $messageType = "danger";
            }
            $stmt->close();
            break;
            
        case 'delete':
            $id = intval($_POST['booking_id'] ?? 0);
            
            $sql = "DELETE FROM bookings WHERE booking_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $message = "✓ Booking deleted!";
                $messageType = "success";
            } else {
                $message = "❌ Error: " . $stmt->error;
                $messageType = "danger";
            }
            $stmt->close();
            break;
    }
    
    closeDatabaseConnection($conn);
}

// Fetch all bookings
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
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
                    <li class="nav-item"><a class="nav-link" href="viewAllBookings.php">View All</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="mb-4">⚙️ Manage Bookings</h1>
        
        <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- INSERT -->
        <div class="card shadow mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">➕ Add New Booking (INSERT)</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="insert">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Customer Name *</label>
                            <input type="text" class="form-control" name="customer_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="customer_email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="customer_phone" placeholder="+968-XXXXXXXX">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type *</label>
                            <select class="form-select" name="booking_type" required>
                                <option value="">Select...</option>
                                <option value="hotel">Hotel</option>
                                <option value="event">Event</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Hotel/Event Name *</label>
                            <input type="text" class="form-control" name="item_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Check-in Date (hotel)</label>
                            <input type="date" class="form-control" name="check_in_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Check-out Date (hotel)</label>
                            <input type="date" class="form-control" name="check_out_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Event Date (event)</label>
                            <input type="date" class="form-control" name="event_date">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Guests *</label>
                            <input type="number" class="form-control" name="number_of_guests" value="1" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total (OMR) *</label>
                            <input type="number" class="form-control" name="total_amount" step="0.01" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">➕ Add</button>
                            <button type="reset" class="btn btn-secondary">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- UPDATE & DELETE -->
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">✏️ Edit / 🗑️ Delete (UPDATE & DELETE)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Ref</th><th>Customer</th><th>Type</th><th>Item</th>
                                <th>Guests</th><th>Amount</th><th>Status</th><th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bookings)): ?>
                            <tr><td colspan="8" class="text-center text-muted">No bookings</td></tr>
                            <?php else: ?>
                            <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking->getBookingReference()); ?></td>
                                <td><?php echo htmlspecialchars($booking->getCustomerName()); ?></td>
                                <td><?php echo $booking->getTypeBadge(); ?></td>
                                <td><?php echo htmlspecialchars($booking->getItemName()); ?></td>
                                <td><?php echo $booking->getNumberOfGuests(); ?></td>
                                <td><?php echo number_format($booking->getTotalAmount(), 2); ?></td>
                                <td><?php echo $booking->getStatusBadge(); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                            data-bs-target="#edit<?php echo $booking->getBookingId(); ?>">✏️</button>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" 
                                            data-bs-target="#delete<?php echo $booking->getBookingId(); ?>">🗑️</button>
                                </td>
                            </tr>
                            
                            <!-- Edit Modal -->
                            <div class="modal fade" id="edit<?php echo $booking->getBookingId(); ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Edit: <?php echo $booking->getBookingReference(); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking->getBookingId(); ?>">
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Guests</label>
                                                    <input type="number" class="form-control" name="number_of_guests" 
                                                           value="<?php echo $booking->getNumberOfGuests(); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Amount (OMR)</label>
                                                    <input type="number" class="form-control" name="total_amount" step="0.01"
                                                           value="<?php echo $booking->getTotalAmount(); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-select" name="booking_status" required>
                                                        <option value="pending" <?php echo $booking->getBookingStatus() === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="confirmed" <?php echo $booking->getBookingStatus() === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                        <option value="cancelled" <?php echo $booking->getBookingStatus() === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-warning">💾 Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Delete Modal -->
                            <div class="modal fade" id="delete<?php echo $booking->getBookingId(); ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Delete Booking</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking->getBookingId(); ?>">
                                            <div class="modal-body">
                                                <p>Delete this booking?</p>
                                                <div class="alert alert-warning">
                                                    <strong>Ref:</strong> <?php echo $booking->getBookingReference(); ?><br>
                                                    <strong>Customer:</strong> <?php echo htmlspecialchars($booking->getCustomerName()); ?>
                                                </div>
                                                <p class="text-danger"><strong>Cannot be undone!</strong></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">🗑️ Delete</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer class="container-fluid bg-light py-3 mt-5">
        <div class="container text-center text-muted small">
            ©2025 Smart Booking | by Leader Team
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>