<?php
/**
 * Process Calculator Form
 * COMP3700 - Part 4
 */

require_once 'dbConfig.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: calculate.html");
    exit();
}

// Get form data
$bookingType = sanitizeInput($_POST['bookingType'] ?? 'hotel');
$itemSelection = sanitizeInput($_POST['hotelSelect'] ?? $_POST['eventSelect'] ?? '');
$quantity = intval($_POST['nights'] ?? $_POST['tickets'] ?? 1);
$roomType = floatval($_POST['roomType'] ?? 1.0);
$age = intval($_POST['age'] ?? 30);
$loyaltyMember = isset($_POST['loyaltyMember']);
$seasonalPromo = isset($_POST['seasonalPromo']);

// Get base price
$basePrice = floatval($itemSelection);

// Calculate subtotal
$subtotal = $basePrice * $quantity * $roomType;

// Calculate discounts
$discountPercentage = 0;
$discountBreakdown = [];

if ($age >= 60) {
    $discountPercentage += 10;
    $discountBreakdown[] = 'Senior Citizen (10%)';
}

if ($loyaltyMember) {
    $discountPercentage += 5;
    $discountBreakdown[] = 'Loyalty Member (5%)';
}

if ($seasonalPromo) {
    $discountPercentage += 15;
    $discountBreakdown[] = 'Seasonal Promotion (15%)';
}

$discount = $subtotal * ($discountPercentage / 100);
$priceAfterDiscount = $subtotal - $discount;

// Add tax
$tax = $priceAfterDiscount * 0.05;
$finalTotal = $priceAfterDiscount + $tax;

// Special bonus
$specialBonus = 0;
$hasBonus = false;
if (($bookingType === 'hotel' && $quantity >= 7) || ($bookingType === 'event' && $quantity >= 10)) {
    $specialBonus = $finalTotal * 0.05;
    $finalTotal -= $specialBonus;
    $hasBonus = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculation Result</title>
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
                    <h2>💰 Cost Calculation Complete!</h2>
                    <p class="lead mb-0">Your booking cost breakdown is ready</p>
                </div>
                
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Detailed Cost Breakdown</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th width="40%">Booking Type:</th>
                                    <td><span class="badge bg-info"><?php echo ucfirst($bookingType); ?></span></td>
                                </tr>
                                <tr>
                                    <th>Base Price:</th>
                                    <td><?php echo number_format($basePrice, 2); ?> OMR per <?php echo $bookingType === 'hotel' ? 'night' : 'ticket'; ?></td>
                                </tr>
                                <tr>
                                    <th>Quantity:</th>
                                    <td><?php echo $quantity; ?> <?php echo $bookingType === 'hotel' ? 'night(s)' : 'ticket(s)'; ?></td>
                                </tr>
                                <tr>
                                    <th>Type Multiplier:</th>
                                    <td><?php echo number_format($roomType, 1); ?>x</td>
                                </tr>
                                <tr class="table-active">
                                    <th>Subtotal:</th>
                                    <td><strong><?php echo number_format($subtotal, 2); ?> OMR</strong></td>
                                </tr>
                                
                                <?php if ($discountPercentage > 0): ?>
                                <tr class="table-success">
                                    <th>Discounts Applied (<?php echo $discountPercentage; ?>%):</th>
                                    <td>
                                        <?php foreach ($discountBreakdown as $disc): ?>
                                            <span class="badge bg-success me-1"><?php echo $disc; ?></span>
                                        <?php endforeach; ?>
                                    </td>
                                </tr>
                                <tr class="table-success">
                                    <th>Total Discount:</th>
                                    <td><strong class="text-success">-<?php echo number_format($discount, 2); ?> OMR</strong></td>
                                </tr>
                                <tr>
                                    <th>Price After Discount:</th>
                                    <td><?php echo number_format($priceAfterDiscount, 2); ?> OMR</td>
                                </tr>
                                <?php endif; ?>
                                
                                <tr>
                                    <th>VAT (5%):</th>
                                    <td>+<?php echo number_format($tax, 2); ?> OMR</td>
                                </tr>
                                
                                <?php if ($hasBonus): ?>
                                <tr class="table-warning">
                                    <th>🎉 Special Bonus:</th>
                                    <td><strong class="text-warning">-<?php echo number_format($specialBonus, 2); ?> OMR (Large booking discount)</strong></td>
                                </tr>
                                <?php endif; ?>
                                
                                <tr class="table-primary">
                                    <th><h4 class="mb-0">Final Total:</h4></th>
                                    <td><h3 class="text-primary mb-0"><?php echo number_format($finalTotal, 3); ?> OMR</h3></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="alert alert-info mt-3">
                            <strong>💡 Calculation Summary:</strong><br>
                            Base: <?php echo number_format($basePrice, 2); ?> × <?php echo $quantity; ?> × <?php echo $roomType; ?> = <?php echo number_format($subtotal, 2); ?> OMR<br>
                            <?php if ($discountPercentage > 0): ?>
                            Discount: -<?php echo number_format($discount, 2); ?> OMR (<?php echo $discountPercentage; ?>%)<br>
                            <?php endif; ?>
                            Tax: +<?php echo number_format($tax, 2); ?> OMR (5%)<br>
                            <?php if ($hasBonus): ?>
                            Bonus: -<?php echo number_format($specialBonus, 2); ?> OMR<br>
                            <?php endif; ?>
                            <strong>Total: <?php echo number_format($finalTotal, 3); ?> OMR</strong>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="calculate.html" class="btn btn-primary">← New Calculation</a>
                        <a href="booking.html" class="btn btn-success">Book Now</a>
                        <a href="index.html" class="btn btn-secondary">Home</a>
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