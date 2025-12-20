

-- Drop database if exists (for clean reinstall)
DROP DATABASE IF EXISTS smartbooking_db;

-- Create database
CREATE DATABASE smartbooking_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE smartbooking_db;

-- ================================================================
-- TABLE 1: HOTELS
-- ================================================================

CREATE TABLE hotels (
    hotel_id INT PRIMARY KEY AUTO_INCREMENT,
    hotel_name VARCHAR(100) NOT NULL,
    location VARCHAR(150) NOT NULL,
    price_per_night DECIMAL(10, 2) NOT NULL,
    rating INT NOT NULL,
    room_type VARCHAR(50) NOT NULL,
    image_url VARCHAR(255) DEFAULT 'f1.webp',
    available_rooms INT DEFAULT 0,
    amenities TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Constraints
    CONSTRAINT chk_hotels_price CHECK (price_per_night > 0),
    CONSTRAINT chk_hotels_rating CHECK (rating >= 1 AND rating <= 5),
    CONSTRAINT chk_hotels_rooms CHECK (available_rooms >= 0),
    
    -- Indexes for performance
    INDEX idx_hotel_name (hotel_name),
    INDEX idx_location (location),
    INDEX idx_price (price_per_night),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample hotel data
INSERT INTO hotels (hotel_name, location, price_per_night, rating, room_type, image_url, available_rooms, amenities) VALUES
('Crowne Plaza Muscat', 'Muscat, Oman', 45.00, 4, 'Standard Room', 'f1.webp', 120, 'WiFi, Pool, Restaurant, Spa, Gym, Conference Rooms'),
('Crowne Plaza Muscat', 'Muscat, Oman', 65.00, 4, 'Deluxe Room', 'f1.webp', 80, 'WiFi, Pool, Restaurant, Spa, City View, Mini Bar'),
('Mercure Muscat', 'Al Khuwair, Muscat', 32.00, 3, 'Standard Room', 'f2.webp', 95, 'WiFi, Restaurant, Parking, Business Center'),
('Mercure Muscat', 'Al Khuwair, Muscat', 50.00, 3, 'Suite', 'f2.webp', 45, 'WiFi, Restaurant, Parking, Kitchenette, Living Room'),
('Ibis Muscat', 'Muscat, Oman', 28.00, 3, 'Standard Room', 'f3.webp', 60, 'WiFi, Breakfast, 24/7 Reception'),
('Ibis Muscat', 'Muscat, Oman', 38.00, 3, 'Deluxe Room', 'f3.webp', 40, 'WiFi, Breakfast, Mini Bar, Work Desk'),
('InterContinental Muscat', 'Al Qurum, Muscat', 85.00, 5, 'Deluxe Suite', 'f1.webp', 30, 'WiFi, Pool, Spa, Beach Access, Fine Dining, Butler Service'),
('Hyatt Regency Muscat', 'Shatti Al Qurum', 75.00, 5, 'Executive Room', 'f2.webp', 50, 'WiFi, Pool, Gym, Beach Access, Business Center, Lounge Access');

-- ================================================================
-- TABLE 2: EVENTS
-- ================================================================

CREATE TABLE events (
    event_id INT PRIMARY KEY AUTO_INCREMENT,
    event_name VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    ticket_price DECIMAL(10, 2) NOT NULL,
    venue VARCHAR(150) NOT NULL,
    available_tickets INT DEFAULT 0,
    event_category VARCHAR(50),
    description TEXT,
    organizer VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Constraints
    CONSTRAINT chk_events_price CHECK (ticket_price >= 0),
    CONSTRAINT chk_events_tickets CHECK (available_tickets >= 0),
    
    
    -- Indexes for performance
    INDEX idx_event_name (event_name),
    INDEX idx_event_date (event_date),
    INDEX idx_venue (venue),
    INDEX idx_category (event_category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample event data
INSERT INTO events (event_name, event_date, event_time, ticket_price, venue, available_tickets, event_category, description, organizer) VALUES
('Winter Music Festival', '2025-12-25', '19:00:00', 10.00, 'Grand Arena', 500, 'Music', 'Annual winter music festival featuring local and international artists with live performances', 'Muscat Events Co.'),
('Winter Music Festival', '2025-12-26', '19:00:00', 10.00, 'Grand Arena', 500, 'Music', 'Day 2 of the winter music festival with different lineup of artists', 'Muscat Events Co.'),
('Art Expo & Market', '2026-01-15', '16:00:00', 3.00, 'City Gallery', 300, 'Art', 'Contemporary art exhibition featuring local artists with market stalls', 'Oman Art Society'),
('Food Carnival', '2026-02-20', '12:00:00', 5.00, 'Waterfront Park', 1000, 'Food', 'International food festival with live cooking demonstrations and food stalls', 'Muscat Tourism Board'),
('New Year Gala Night', '2025-12-31', '20:00:00', 25.00, 'Royal Ballroom', 250, 'Celebration', 'Exclusive New Year celebration with dinner, entertainment, and fireworks', 'Royal Events'),
('National Day Concert', '2026-11-18', '18:00:00', 8.00, 'Outdoor Stadium', 2000, 'Music', 'National Day celebration concert with traditional and modern Omani music', 'Ministry of Culture'),
('Tech Innovation Summit', '2026-03-15', '09:00:00', 15.00, 'Convention Center', 400, 'Conference', 'Annual technology and innovation summit with workshops and keynote speakers', 'Tech Oman'),
('Cultural Heritage Festival', '2026-04-20', '14:00:00', 0.00, 'Heritage Village', 1500, 'Culture', 'Free cultural festival celebrating Omani heritage with traditional crafts and performances', 'Heritage Foundation');

-- ================================================================
-- TABLE 3: BOOKINGS
-- ================================================================

CREATE TABLE bookings (
    booking_id INT PRIMARY KEY AUTO_INCREMENT,
    booking_reference VARCHAR(20) UNIQUE NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    booking_type ENUM('hotel', 'event') NOT NULL,
    item_name VARCHAR(150) NOT NULL,
    check_in_date DATE,
    check_out_date DATE,
    event_date DATE,
    number_of_guests INT NOT NULL,
    special_requests TEXT,
    booking_status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    total_amount DECIMAL(10, 2),
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Constraints
    CONSTRAINT chk_bookings_guests CHECK (number_of_guests > 0 AND number_of_guests <= 20),
    CONSTRAINT chk_bookings_amount CHECK (total_amount >= 0),
    CONSTRAINT chk_bookings_dates CHECK (
        (booking_type = 'hotel' AND check_in_date IS NOT NULL AND check_out_date IS NOT NULL AND check_out_date > check_in_date) OR
        (booking_type = 'event' AND event_date IS NOT NULL)
    ),
    
    -- Indexes for performance
    INDEX idx_booking_reference (booking_reference),
    INDEX idx_customer_email (customer_email),
    INDEX idx_booking_type (booking_type),
    INDEX idx_booking_status (booking_status),
    INDEX idx_booking_date (booking_date),
    INDEX idx_check_in_date (check_in_date),
    INDEX idx_event_date (event_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample booking data
INSERT INTO bookings (booking_reference, customer_name, customer_email, customer_phone, booking_type, item_name, check_in_date, check_out_date, event_date, number_of_guests, special_requests, booking_status, total_amount) VALUES
('B001', 'Ahmed Al-Alawi', 'ahmed.alawi@example.com', '+968-91234567', 'hotel', 'Crowne Plaza Muscat', '2025-12-22', '2025-12-24', NULL, 2, 'Late check-in requested after 8 PM', 'confirmed', 90.00),
('B002', 'Sara Al-Maamari', 'sara.maamari@example.com', '+968-92345678', 'event', 'New Year Gala Night', NULL, NULL, '2025-12-31', 3, 'VIP seating preferred near stage', 'pending', 75.00),
('B003', 'Hamza Al-Rashdi', 'hamza.rashdi@example.com', '+968-93456789', 'hotel', 'Ibis Muscat', '2026-01-05', '2026-01-07', NULL, 3, 'Family room needed, one child age 8', 'cancelled', 56.00),
('B004', 'Fatima Al-Hinai', 'fatima.hinai@example.com', '+968-94567890', 'event', 'Winter Music Festival', NULL, NULL, '2025-12-25', 4, 'Group booking for friends', 'confirmed', 40.00),
('B005', 'Ali Al-Balushi', 'ali.balushi@example.com', '+968-95678901', 'hotel', 'Mercure Muscat', '2025-12-28', '2025-12-30', NULL, 2, 'Quiet room on higher floor preferred', 'confirmed', 64.00),
('B006', 'Mariam Al-Lawati', 'mariam.lawati@example.com', '+968-96789012', 'event', 'Food Carnival', NULL, NULL, '2026-02-20', 5, 'Wheelchair access needed for one person', 'confirmed', 25.00),
('B007', 'Yousif Al-Habsi', 'yousif.habsi@example.com', '+968-97890123', 'hotel', 'InterContinental Muscat', '2026-01-20', '2026-01-22', NULL, 2, 'Honeymoon package, beach view room', 'pending', 170.00);

-- ================================================================
-- VIEWS FOR COMMON QUERIES
-- ================================================================

-- View 1: Available Hotels
CREATE OR REPLACE VIEW available_hotels AS
SELECT 
    hotel_id,
    hotel_name,
    location,
    price_per_night,
    rating,
    room_type,
    available_rooms,
    amenities,
    image_url
FROM hotels
WHERE available_rooms > 0
ORDER BY rating DESC, price_per_night ASC;

-- View 2: Upcoming Events
CREATE OR REPLACE VIEW upcoming_events AS
SELECT 
    event_id,
    event_name,
    event_date,
    event_time,
    ticket_price,
    venue,
    available_tickets,
    event_category,
    description,
    organizer
FROM events
WHERE event_date >= CURDATE()
ORDER BY event_date ASC, event_time ASC;

-- View 3: Active Bookings (Non-Cancelled)
CREATE OR REPLACE VIEW active_bookings AS
SELECT 
    booking_id,
    booking_reference,
    customer_name,
    customer_email,
    customer_phone,
    booking_type,
    item_name,
    check_in_date,
    check_out_date,
    event_date,
    number_of_guests,
    booking_status,
    total_amount,
    booking_date
FROM bookings
WHERE booking_status != 'cancelled'
ORDER BY booking_date DESC;

-- View 4: Hotel Bookings Summary
CREATE OR REPLACE VIEW hotel_bookings_summary AS
SELECT 
    booking_reference,
    customer_name,
    customer_email,
    item_name AS hotel_name,
    check_in_date,
    check_out_date,
    DATEDIFF(check_out_date, check_in_date) AS nights,
    number_of_guests,
    total_amount,
    booking_status
FROM bookings
WHERE booking_type = 'hotel'
ORDER BY check_in_date DESC;

-- View 5: Event Bookings Summary
CREATE OR REPLACE VIEW event_bookings_summary AS
SELECT 
    booking_reference,
    customer_name,
    customer_email,
    item_name AS event_name,
    event_date,
    number_of_guests AS tickets,
    total_amount,
    booking_status
FROM bookings
WHERE booking_type = 'event'
ORDER BY event_date DESC;

-- View 6: Revenue Report
CREATE OR REPLACE VIEW revenue_report AS
SELECT 
    booking_type,
    booking_status,
    COUNT(*) AS total_bookings,
    SUM(number_of_guests) AS total_guests,
    SUM(total_amount) AS total_revenue,
    AVG(total_amount) AS average_booking_value
FROM bookings
GROUP BY booking_type, booking_status
ORDER BY booking_type, booking_status;

-- ================================================================
-- STORED PROCEDURES (Optional - for advanced functionality)
-- ================================================================

-- Procedure 1: Get Booking Details
DELIMITER //
CREATE PROCEDURE GetBookingDetails(IN booking_ref VARCHAR(20))
BEGIN
    SELECT 
        b.*,
        CASE 
            WHEN b.booking_type = 'hotel' THEN CONCAT(b.item_name, ' - ', DATEDIFF(b.check_out_date, b.check_in_date), ' nights')
            ELSE CONCAT(b.item_name, ' - ', b.number_of_guests, ' tickets')
        END AS booking_summary
    FROM bookings b
    WHERE b.booking_reference = booking_ref;
END //
DELIMITER ;

-- Procedure 2: Search Bookings
DELIMITER //
CREATE PROCEDURE SearchBookings(
    IN search_term VARCHAR(100),
    IN search_type VARCHAR(20)
)
BEGIN
    IF search_type = 'customer' THEN
        SELECT * FROM bookings 
        WHERE customer_name LIKE CONCAT('%', search_term, '%') 
           OR customer_email LIKE CONCAT('%', search_term, '%')
        ORDER BY booking_date DESC;
    ELSEIF search_type = 'reference' THEN
        SELECT * FROM bookings 
        WHERE booking_reference LIKE CONCAT('%', search_term, '%')
        ORDER BY booking_date DESC;
    ELSEIF search_type = 'item' THEN
        SELECT * FROM bookings 
        WHERE item_name LIKE CONCAT('%', search_term, '%')
        ORDER BY booking_date DESC;
    ELSE
        SELECT * FROM bookings ORDER BY booking_date DESC;
    END IF;
END //
DELIMITER ;

-- Procedure 3: Update Booking Status
DELIMITER //
CREATE PROCEDURE UpdateBookingStatus(
    IN booking_ref VARCHAR(20),
    IN new_status VARCHAR(20)
)
BEGIN
    UPDATE bookings 
    SET booking_status = new_status,
        updated_at = CURRENT_TIMESTAMP
    WHERE booking_reference = booking_ref;
    
    SELECT ROW_COUNT() AS rows_affected;
END //
DELIMITER ;

-- ================================================================
-- TRIGGERS (Optional - for automatic data management)
-- ================================================================

-- Trigger 1: Validate email format before insert
DELIMITER //
CREATE TRIGGER validate_email_before_insert
BEFORE INSERT ON bookings
FOR EACH ROW
BEGIN
    IF NEW.customer_email NOT LIKE '%@%.%' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid email format';
    END IF;
END //
DELIMITER ;

-- ================================================================
-- SAMPLE QUERIES FOR TESTING
-- ================================================================

-- Query 1: Count records in each table
SELECT 'Hotels' AS table_name, COUNT(*) AS record_count FROM hotels
UNION ALL
SELECT 'Events' AS table_name, COUNT(*) AS record_count FROM events
UNION ALL
SELECT 'Bookings' AS table_name, COUNT(*) AS record_count FROM bookings;

-- Query 2: Show all hotels ordered by rating
SELECT hotel_name, location, price_per_night, rating, room_type 
FROM hotels 
ORDER BY rating DESC, price_per_night ASC;

-- Query 3: Show upcoming events
SELECT event_name, event_date, event_time, venue, ticket_price, available_tickets 
FROM events 
WHERE event_date >= CURDATE() 
ORDER BY event_date ASC;

-- Query 4: Show all bookings with formatted dates
SELECT 
    booking_reference,
    customer_name,
    booking_type,
    item_name,
    CASE 
        WHEN booking_type = 'hotel' THEN CONCAT(DATE_FORMAT(check_in_date, '%b %d'), ' - ', DATE_FORMAT(check_out_date, '%b %d, %Y'))
        ELSE DATE_FORMAT(event_date, '%b %d, %Y')
    END AS date_display,
    number_of_guests,
    booking_status,
    CONCAT(total_amount, ' OMR') AS amount
FROM bookings
ORDER BY booking_date DESC;

-- Query 5: Revenue by booking type and status
SELECT * FROM revenue_report;

-- Query 6: Hotel occupancy summary
SELECT 
    hotel_name,
    room_type,
    available_rooms,
    COUNT(b.booking_id) AS total_bookings
FROM hotels h
LEFT JOIN bookings b ON h.hotel_name LIKE CONCAT(b.item_name, '%') AND b.booking_type = 'hotel'
GROUP BY h.hotel_id, h.hotel_name, h.room_type, h.available_rooms
ORDER BY h.hotel_name, h.room_type;


-- Display all data to verify successful creation
-- ================================================================

SELECT '=====================================' AS info;
SELECT '  DATABASE CREATION COMPLETE' AS info; 
SELECT '=====================================' AS info;

SELECT '' AS info;
SELECT '--- HOTELS TABLE ---' AS info;
SELECT * FROM hotels;

SELECT '' AS info;
SELECT '--- EVENTS TABLE ---' AS info;
SELECT * FROM events;

SELECT '' AS info;
SELECT '--- BOOKINGS TABLE ---' AS info;
SELECT * FROM bookings;

SELECT '' AS info;
SELECT '--- AVAILABLE HOTELS VIEW ---' AS info;
SELECT * FROM available_hotels LIMIT 5;

SELECT '' AS info;
SELECT '--- UPCOMING EVENTS VIEW ---' AS info;
SELECT * FROM upcoming_events LIMIT 5;

SELECT '' AS info;
SELECT '--- ACTIVE BOOKINGS VIEW ---' AS info;
SELECT * FROM active_bookings;

SELECT '' AS info;
SELECT '--- REVENUE REPORT ---' AS info;
SELECT * FROM revenue_report;

SELECT '' AS info;
SELECT '=====================================' AS info;
SELECT '  TABLE STATISTICS' AS info;
SELECT '=====================================' AS info;

SELECT 
    'hotels' AS table_name,
    COUNT(*) AS total_records,
    SUM(available_rooms) AS total_rooms
FROM hotels
UNION ALL
SELECT 
    'events' AS table_name,
    COUNT(*) AS total_records,
    SUM(available_tickets) AS total_tickets
FROM events
UNION ALL
SELECT 
    'bookings' AS table_name,
    COUNT(*) AS total_records,
    SUM(total_amount) AS total_revenue
FROM bookings;
