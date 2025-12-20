<?php
/**
 * Booking Class - Represents a booking record
 * COMP3700 - Part 4
 */

class Booking {
    private $bookingId;
    private $bookingReference;
    private $customerName;
    private $customerEmail;
    private $customerPhone;
    private $bookingType;
    private $itemName;
    private $checkInDate;
    private $checkOutDate;
    private $eventDate;
    private $numberOfGuests;
    private $specialRequests;
    private $bookingStatus;
    private $totalAmount;
    private $bookingDate;
    
    /**
     * Constructor
     */
    public function __construct($data = null) {
        if (is_array($data)) {
            $this->bookingId = $data['booking_id'] ?? null;
            $this->bookingReference = $data['booking_reference'] ?? '';
            $this->customerName = $data['customer_name'] ?? '';
            $this->customerEmail = $data['customer_email'] ?? '';
            $this->customerPhone = $data['customer_phone'] ?? '';
            $this->bookingType = $data['booking_type'] ?? '';
            $this->itemName = $data['item_name'] ?? '';
            $this->checkInDate = $data['check_in_date'] ?? null;
            $this->checkOutDate = $data['check_out_date'] ?? null;
            $this->eventDate = $data['event_date'] ?? null;
            $this->numberOfGuests = $data['number_of_guests'] ?? 0;
            $this->specialRequests = $data['special_requests'] ?? '';
            $this->bookingStatus = $data['booking_status'] ?? 'pending';
            $this->totalAmount = $data['total_amount'] ?? 0.00;
            $this->bookingDate = $data['booking_date'] ?? date('Y-m-d H:i:s');
        }
    }
    
    // Getters
    public function getBookingId() { return $this->bookingId; }
    public function getBookingReference() { return $this->bookingReference; }
    public function getCustomerName() { return $this->customerName; }
    public function getCustomerEmail() { return $this->customerEmail; }
    public function getCustomerPhone() { return $this->customerPhone; }
    public function getBookingType() { return $this->bookingType; }
    public function getItemName() { return $this->itemName; }
    public function getCheckInDate() { return $this->checkInDate; }
    public function getCheckOutDate() { return $this->checkOutDate; }
    public function getEventDate() { return $this->eventDate; }
    public function getNumberOfGuests() { return $this->numberOfGuests; }
    public function getSpecialRequests() { return $this->specialRequests; }
    public function getBookingStatus() { return $this->bookingStatus; }
    public function getTotalAmount() { return $this->totalAmount; }
    public function getBookingDate() { return $this->bookingDate; }
    
    // Setters
    public function setBookingId($id) { $this->bookingId = $id; }
    public function setBookingReference($ref) { $this->bookingReference = $ref; }
    public function setCustomerName($name) { $this->customerName = $name; }
    public function setCustomerEmail($email) { $this->customerEmail = $email; }
    public function setCustomerPhone($phone) { $this->customerPhone = $phone; }
    public function setBookingType($type) { $this->bookingType = $type; }
    public function setItemName($name) { $this->itemName = $name; }
    public function setCheckInDate($date) { $this->checkInDate = $date; }
    public function setCheckOutDate($date) { $this->checkOutDate = $date; }
    public function setEventDate($date) { $this->eventDate = $date; }
    public function setNumberOfGuests($guests) { $this->numberOfGuests = $guests; }
    public function setSpecialRequests($requests) { $this->specialRequests = $requests; }
    public function setBookingStatus($status) { $this->bookingStatus = $status; }
    public function setTotalAmount($amount) { $this->totalAmount = $amount; }
    public function setBookingDate($date) { $this->bookingDate = $date; }
    
    /**
     * Get formatted date range
     */
    public function getFormattedDateRange() {
        if ($this->bookingType === 'hotel' && $this->checkInDate && $this->checkOutDate) {
            $checkIn = new DateTime($this->checkInDate);
            $checkOut = new DateTime($this->checkOutDate);
            return $checkIn->format('M d, Y') . ' - ' . $checkOut->format('M d, Y');
        } else if ($this->eventDate) {
            $eventDate = new DateTime($this->eventDate);
            return $eventDate->format('M d, Y');
        }
        return 'N/A';
    }
    
    /**
     * Get status badge
     */
    public function getStatusBadge() {
        $badges = [
            'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
            'confirmed' => '<span class="badge bg-success">Confirmed</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>'
        ];
        return $badges[$this->bookingStatus] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
    
    /**
     * Get type badge
     */
    public function getTypeBadge() {
        return $this->bookingType === 'hotel' 
            ? '<span class="badge bg-info">Hotel</span>' 
            : '<span class="badge bg-warning text-dark">Event</span>';
    }
    
    /**
     * Display as table row
     */
    public function displayAsTableRow() {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($this->bookingReference) . "</td>";
        echo "<td>" . htmlspecialchars($this->customerName) . "</td>";
        echo "<td>" . $this->getTypeBadge() . "</td>";
        echo "<td>" . htmlspecialchars($this->itemName) . "</td>";
        echo "<td>" . $this->getFormattedDateRange() . "</td>";
        echo "<td>" . htmlspecialchars($this->numberOfGuests) . "</td>";
        echo "<td>" . $this->getStatusBadge() . "</td>";
        echo "<td>" . number_format($this->totalAmount, 2) . " OMR</td>";
        echo "</tr>";
    }
    
    /**
     * Display as card
     */
    public function displayAsCard() {
        echo '<div class="card mb-3 shadow-sm">';
        echo '<div class="card-header bg-primary text-white">';
        echo '<strong>Booking: ' . htmlspecialchars($this->bookingReference) . '</strong> ';
        echo $this->getStatusBadge();
        echo '</div>';
        echo '<div class="card-body">';
        echo '<div class="row">';
        echo '<div class="col-md-6">';
        echo '<p><strong>Customer:</strong> ' . htmlspecialchars($this->customerName) . '</p>';
        echo '<p><strong>Email:</strong> ' . htmlspecialchars($this->customerEmail) . '</p>';
        if (!empty($this->customerPhone)) {
            echo '<p><strong>Phone:</strong> ' . htmlspecialchars($this->customerPhone) . '</p>';
        }
        echo '</div>';
        echo '<div class="col-md-6">';
        echo '<p><strong>Type:</strong> ' . $this->getTypeBadge() . '</p>';
        echo '<p><strong>Item:</strong> ' . htmlspecialchars($this->itemName) . '</p>';
        echo '<p><strong>Date:</strong> ' . $this->getFormattedDateRange() . '</p>';
        echo '<p><strong>Guests:</strong> ' . htmlspecialchars($this->numberOfGuests) . '</p>';
        echo '<p><strong>Total:</strong> ' . number_format($this->totalAmount, 2) . ' OMR</p>';
        echo '</div>';
        echo '</div>';
        if (!empty($this->specialRequests)) {
            echo '<hr>';
            echo '<p><strong>Special Requests:</strong> ' . htmlspecialchars($this->specialRequests) . '</p>';
        }
        echo '</div>';
        echo '</div>';
    }
}
?>