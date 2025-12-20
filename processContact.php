<?php
/**
 * Process Contact Form
 */

require_once 'dbConfig.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: contactUs.html");
    exit();
}

$name = sanitizeInput($_POST['name'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$subject = sanitizeInput($_POST['subject'] ?? '');
$reason = sanitizeInput($_POST['reason'] ?? '');
$message = sanitizeInput($_POST['message'] ?? '');

$errors = [];
if (empty($name) || strlen($name) < 3) $errors[] = "Name must be at least 3 characters";
if (empty($email) || !validateEmail($email)) $errors[] = "Valid email required";
if (empty($subject) || strlen($subject) < 5) $errors[] = "Subject must be at least 5 characters";
if (empty($reason)) $errors[] = "Please select a reason";
if (empty($message) || strlen($message) < 20) $errors[] = "Message must be at least 20 characters";

if (!empty($errors)) {
    ?>
    <!DOCTYPE html>
    <html><head><meta charset="UTF-8"><title>Error</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"></head>
    <body><div class="container my-5"><div class="alert alert-danger"><h4>❌ Errors</h4><ul>
    <?php foreach ($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?>
    </ul><a href="contactUs.html" class="btn btn-primary">← Back</a></div></div></body></html>
    <?php
    exit();
}

$reasonMap = [
    'booking' => 'Booking Question',
    'hotel' => 'Hotel Information',
    'event' => 'Event Information',
    'technical' => 'Technical Issue',
    'feedback' => 'Feedback',
    'other' => 'Other'
];
$reasonText = $reasonMap[$reason] ?? 'Unknown';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Received</title>
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
                    <h2>✓ Message Received!</h2>
                    <p class="lead">We'll respond within 24 hours.</p>
                </div>
                
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Contact Submission</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-striped">
                            <tbody>
                                <tr>
                                    <th width="30%">Name:</th>
                                    <td><?php echo htmlspecialchars($name); ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?php echo htmlspecialchars($email); ?></td>
                                </tr>
                                <tr>
                                    <th>Subject:</th>
                                    <td><?php echo htmlspecialchars($subject); ?></td>
                                </tr>
                                <tr>
                                    <th>Reason:</th>
                                    <td><span class="badge bg-info"><?php echo $reasonText; ?></span></td>
                                </tr>
                                <tr>
                                    <th>Date:</th>
                                    <td><?php echo date('F d, Y - h:i A'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="mt-3">
                            <h5 class="text-primary border-bottom pb-2">Your Message:</h5>
                            <div class="card">
                                <div class="card-body">
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($message)); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h6 class="text-muted">Characters</h6>
                                        <h3 class="text-primary"><?php echo strlen($message); ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h6 class="text-muted">Words</h6>
                                        <h3 class="text-success"><?php echo str_word_count($message); ?></h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card text-center">
                                    <div class="card-body">
                                        <h6 class="text-muted">Status</h6>
                                        <h3 class="text-info">Received</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="index.html" class="btn btn-primary">← Home</a>
                        <a href="contactUs.html" class="btn btn-success">Send Another</a>
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
?>