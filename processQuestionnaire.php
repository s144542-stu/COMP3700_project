<?php
/**
 * Process Feedback Questionnaire Form
 * COMP3700 - Part 4
 */

require_once 'dbConfig.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: questionnaire.html");
    exit();
}

// Get and sanitize form data
$fullName = sanitizeInput($_POST['fullName'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');
$rating = intval($_POST['rating'] ?? 0);
$bookingId = sanitizeInput($_POST['bookingId'] ?? '');
$visitDate = sanitizeInput($_POST['visitDate'] ?? '');
$feedback = sanitizeInput($_POST['feedback'] ?? '');
$recommend = sanitizeInput($_POST['recommend'] ?? '');

// Get services used (checkboxes)
$services = [];
if (isset($_POST['serviceHotel'])) $services[] = 'Hotel Booking';
if (isset($_POST['serviceEvent'])) $services[] = 'Event Booking';
if (isset($_POST['serviceSupport'])) $services[] = 'Customer Support';
if (isset($_POST['serviceOther'])) $services[] = 'Other Services';
$servicesUsed = implode(', ', $services);

// Validation
$errors = [];

if (empty($fullName)) $errors[] = "Full name is required";
if (empty($email) || !validateEmail($email)) $errors[] = "Valid email is required";
if ($rating < 1 || $rating > 5) $errors[] = "Please select a rating";
if (empty($visitDate)) $errors[] = "Service date is required";
if (empty($feedback)) $errors[] = "Feedback is required";
if (empty($recommend)) $errors[] = "Please indicate if you'd recommend us";

if (!empty($errors)) {
    ?>
    <!DOCTYPE html>
    <html><head><meta charset="UTF-8"><title>Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"></head>
    <body><div class="container my-5"><div class="alert alert-danger"><h4>❌ Validation Errors</h4><ul>
    <?php foreach ($errors as $error): ?><li><?php echo htmlspecialchars($error); ?></li><?php endforeach; ?>
    </ul><a href="questionnaire.html" class="btn btn-primary">← Back to Form</a></div></div></body></html>
    <?php
    exit();
}

// Map recommendation values
$recommendMap = [
    'definitely' => 'Definitely Yes',
    'probably' => 'Probably Yes',
    'maybe' => 'Maybe',
    'probably_not' => 'Probably Not',
    'definitely_not' => 'Definitely Not'
];
$recommendText = $recommendMap[$recommend] ?? 'Unknown';

// Rating stars
$ratingStars = str_repeat('⭐', $rating);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Received</title>
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
                    <h2>✓ Thank You for Your Feedback!</h2>
                    <p class="lead">We appreciate you taking the time to share your experience.</p>
                </div>
                
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">📝 Feedback Summary</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-striped">
                            <tbody>
                                <tr>
                                    <th width="35%">Customer Name:</th>
                                    <td><?php echo htmlspecialchars($fullName); ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?php echo htmlspecialchars($email); ?></td>
                                </tr>
                                <?php if (!empty($phone)): ?>
                                <tr>
                                    <th>Phone:</th>
                                    <td><?php echo htmlspecialchars($phone); ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <th>Overall Rating:</th>
                                    <td>
                                        <strong><?php echo $ratingStars; ?> (<?php echo $rating; ?>/5)</strong>
                                        <?php if ($rating >= 4): ?>
                                            <span class="badge bg-success ms-2">Excellent</span>
                                        <?php elseif ($rating >= 3): ?>
                                            <span class="badge bg-primary ms-2">Good</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning ms-2">Needs Improvement</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if (!empty($servicesUsed)): ?>
                                <tr>
                                    <th>Services Used:</th>
                                    <td><?php echo htmlspecialchars($servicesUsed); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($bookingId)): ?>
                                <tr>
                                    <th>Booking ID:</th>
                                    <td><code><?php echo htmlspecialchars($bookingId); ?></code></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <th>Service Date:</th>
                                    <td><?php echo date('F d, Y', strtotime($visitDate)); ?></td>
                                </tr>
                                <tr>
                                    <th>Would Recommend:</th>
                                    <td>
                                        <strong><?php echo $recommendText; ?></strong>
                                        <?php if (in_array($recommend, ['definitely', 'probably'])): ?>
                                            <span class="badge bg-success ms-2">👍</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Submission Date:</th>
                                    <td><?php echo date('F d, Y - h:i A'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="mt-4">
                            <h5 class="text-primary border-bottom pb-2">💬 Your Feedback:</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-0" style="white-space: pre-wrap;"><?php echo htmlspecialchars($feedback); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h6 class="text-muted">Response Time</h6>
                                        <h4 class="text-success">24-48 hrs</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h6 class="text-muted">Feedback Status</h6>
                                        <h4 class="text-info">Received</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h6 class="text-muted">Priority</h6>
                                        <h4 class="<?php echo $rating <= 2 ? 'text-danger' : 'text-primary'; ?>">
                                            <?php echo $rating <= 2 ? 'High' : 'Normal'; ?>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-4">
                            <strong>📧 What's Next?</strong><br>
                            • We've received your feedback and will review it carefully<br>
                            • Our team will respond within 24-48 hours<br>
                            • You'll receive a follow-up email at <?php echo htmlspecialchars($email); ?><br>
                            <?php if ($rating <= 2): ?>
                            • Due to your rating, a senior team member will contact you personally<br>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="index.html" class="btn btn-primary">← Home</a>
                        <a href="questionnaire.html" class="btn btn-success">Submit More Feedback</a>
                        <a href="contactUs.html" class="btn btn-info">Contact Us</a>
                    </div>
                </div>
                
                <?php if ($rating >= 4): ?>
                <div class="alert alert-success mt-4 text-center">
                    <h5>🎉 Thank You for the Positive Review!</h5>
                    <p class="mb-0">We're thrilled to hear you had a great experience. Your feedback motivates our team!</p>
                </div>
                <?php elseif ($rating <= 2): ?>
                <div class="alert alert-warning mt-4 text-center">
                    <h5>😔 We're Sorry for Your Experience</h5>
                    <p class="mb-0">Your feedback is very important to us. A senior team member will contact you within 24 hours to address your concerns.</p>
                </div>
                <?php endif; ?>
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