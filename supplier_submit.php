<?php
// ==================== SECURITY HEADERS ====================
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://cdn.jsdelivr.net; frame-ancestors 'none'; base-uri 'self'; form-action 'self';");
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// ==================== HTTPS ENFORCEMENT ====================
// Uncomment for production (HTTPS required)
// if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off'){
//     header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
//     exit;
// }

// Start session before any output
if(session_status() === PHP_SESSION_NONE){
    // Secure session configuration
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', '0'); // Set to '1' in production with HTTPS
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.gc_maxlifetime', '1800'); // 30 minutes
    
    session_start();
    session_regenerate_id(true); // Regenerate on each access
    
    // Session timeout (30 minutes of inactivity)
    if(isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)){
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['last_activity'] = time();
}

include 'init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_error){
    error_log("Supplier Submit - DB Connection Failed: " . $db->connect_error);
    http_response_code(500);
    die("Server error. Please contact support.");
}

date_default_timezone_set('Asia/Manila');

// ==================== RATE LIMITING ====================
$ip = $_SERVER['REMOTE_ADDR'];
$rate_limit_key = 'supplier_submit_' . md5($ip);
$attempts = $_SESSION[$rate_limit_key] ?? 0;
$last_attempt = $_SESSION[$rate_limit_key . '_time'] ?? 0;
$current_time = time();

if($current_time - $last_attempt < 10 && $attempts > 3){
    error_log("Rate limit exceeded: " . $ip);
    http_response_code(429);
    die("Too many requests. Please wait 10 seconds before trying again.");
}

if($current_time - $last_attempt > 10){
    $_SESSION[$rate_limit_key] = 0;
}

// ==================== VALIDATE TOKEN ====================
$token = $_GET['token'] ?? '';

// Validate token format (hex string, min length)
if(empty($token) || !preg_match('/^[a-fA-F0-9]{64}$/', $token)){
    error_log("Invalid token format attempt: " . $ip);
    http_response_code(400);
    die("Invalid access.");
}

$_SESSION[$rate_limit_key]++;
$_SESSION[$rate_limit_key . '_time'] = $current_time;

/* ================= VALIDATE TOKEN FROM DATABASE ================= */
$stmt = $db->prepare("
    SELECT pcs.*, 
           pci.item_description, pci.quantity, pci.unit
    FROM purchase_canvassing_suppliers pcs
    LEFT JOIN purchase_canvassing_items pci 
        ON pcs.canvass_item_id = pci.id
    WHERE pcs.token = ?
    LIMIT 1
");

if(!$stmt){
    error_log("Prepare failed: " . $db->error);
    http_response_code(500);
    die("Server error. Please contact support.");
}

$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    error_log("Invalid token attempt: " . $ip . " - Token: " . substr($token, 0, 8) . "...");
    http_response_code(400);
    die("Invalid quotation link.");
}

$data = $result->fetch_assoc();

/* ================= CHECK TOKEN STATUS ================= */
if($data['token_used'] == 1){
    error_log("Token reuse attempt: " . $ip . " - Canvass: " . $data['canvass_no']);
    http_response_code(400);
    die("This quotation link was already submitted.");
}

if(strtotime($data['token_expires_at']) < time()){
    error_log("Expired token attempt: " . $ip . " - Canvass: " . $data['canvass_no']);
    http_response_code(400);
    die("This quotation link has expired. Please request a new canvass.");
}

// Store/update token in session (allow switching between different canvass requests)
$_SESSION['supplier_token'] = $token;

// ==================== PER-TOKEN RATE LIMITING ====================
$token_limit_key = 'token_submit_' . md5($token);
$token_attempts = $_SESSION[$token_limit_key] ?? 0;
$token_last_attempt = $_SESSION[$token_limit_key . '_time'] ?? 0;

if($current_time - $token_last_attempt < 30 && $token_attempts > 5){
    error_log("Token rate limit exceeded: " . $ip . " - Token: " . substr($token, 0, 8) . "...");
    http_response_code(429);
    die("Too many submission attempts for this quotation. Please wait 30 seconds.");
}

if($current_time - $token_last_attempt > 30){
    $_SESSION[$token_limit_key] = 0;
}

/* ================= HANDLE SUBMISSION ================= */
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    // Increment token rate limit
    $_SESSION[$token_limit_key]++;
    $_SESSION[$token_limit_key . '_time'] = $current_time;
    
    // ==================== HONEYPOT CHECK ====================
    if(!empty($_POST['website'])){
        error_log("Bot detected (honeypot triggered): " . $ip);
        http_response_code(400);
        die("Invalid submission.");
    }
    
    // ==================== CSRF TOKEN CHECK ====================
    if(empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')){
        error_log("CSRF token mismatch: " . $ip);
        http_response_code(403);
        die("Security validation failed. Please refresh the page and try again.");
    }

    // ==================== INPUT VALIDATION & SANITIZATION ====================
    $brand   = trim($_POST['brand'] ?? '');
    $price   = trim($_POST['price'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');

    // Check for SQL injection and XSS patterns
    $suspicious_patterns = [
        '/union.*select/i', '/insert.*into/i', '/delete.*from/i',
        '/drop.*table/i', '/update.*set/i', '/<script/i', '/javascript:/i',
        '/on(load|error|click)=/i', '/eval\(/i', '/base64_decode/i'
    ];
    
    foreach($suspicious_patterns as $pattern){
        if(preg_match($pattern, $brand . $price . $remarks)){
            error_log("Suspicious input detected: " . $ip . " - Pattern: " . $pattern);
            http_response_code(400);
            die("Invalid input detected.");
        }
    }

    // Brand validation & sanitization
    if(empty($brand) || strlen($brand) > 100){
        die("Invalid brand. Must be 1-100 characters.");
    }
    if(!preg_match('/^[a-zA-Z0-9\s\-\.&,()]+$/i', $brand)){
        die("Brand contains invalid characters.");
    }
    $brand = htmlspecialchars($brand, ENT_QUOTES, 'UTF-8');

    // Price validation (stricter decimal check)
    if(empty($price)){
        die("Price is required.");
    }
    if(!preg_match('/^\d+(\.\d{1,2})?$/', $price)){
        die("Invalid price format. Use decimal numbers only (e.g., 100.50).");
    }
    $price = floatval($price);
    if($price <= 0 || $price > 9999999){
        die("Invalid price. Must be between 0.01 and 9,999,999.");
    }

    // Remarks validation & sanitization
    if(strlen($remarks) > 500){
        die("Remarks too long. Maximum 500 characters.");
    }
    if($remarks && !preg_match('/^[a-zA-Z0-9\s\-\.&,():\'\"\\/\n\r]+$/i', $remarks)){
        die("Remarks contains invalid characters.");
    }
    $remarks = htmlspecialchars($remarks, ENT_QUOTES, 'UTF-8');

    // ==================== UPDATE DATABASE ====================
    $update = $db->prepare("
        UPDATE purchase_canvassing_suppliers
        SET brand=?, price=?, remarks=?, token_used=1
        WHERE token=?
    ");

    if(!$update){
        error_log("Update prepare failed: " . $db->error);
        http_response_code(500);
        die("Server error. Please contact support.");
    }

    $update->bind_param("sdss", $brand, $price, $remarks, $token);
    
    if(!$update->execute()){
        error_log("Update execution failed: " . $db->error);
        http_response_code(500);
        die("Server error. Please contact support.");
    }

    // ==================== LOG SUCCESS ====================
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    error_log("✓ Quotation submitted: Canvass=" . $data['canvass_no'] . 
              " Supplier_ID=" . $data['supplier_id'] . 
              " Brand=" . substr($brand, 0, 30) .
              " Price=" . $price . 
              " IP=" . $ip . 
              " UserAgent=" . substr($user_agent, 0, 50));

    // Clear session data
    unset($_SESSION['supplier_token']);
    unset($_SESSION['csrf_token']);

    echo "
    <div style='text-align:center;margin-top:100px;font-family:Arial'>
        <h3 style='color:green;'>✓ Quotation Submitted Successfully</h3>
        <p>Thank you for your response. We will review your quotation shortly.</p>
    </div>
    ";
    exit;
}

// ==================== GENERATE CSRF TOKEN ====================
if(empty($_SESSION['csrf_token'])){
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Supplier Quotation Submission - Jathnier Corporation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: #f5f5f5;
            min-height: 100vh;
            padding: 40px 20px;
            font-family: 'Arial', 'Helvetica', sans-serif;
        }

        .quote-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            background: white;
        }

        .card-header {
            background: #003366;
            padding: 25px 30px;
            border-bottom: 3px solid #002244;
            border-radius: 0;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-logo {
            height: 60px;
            background: white;
            padding: 8px;
            border-radius: 4px;
        }

        .header-text {
            flex: 1;
        }

        .header-text h4 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
            color: white;
            letter-spacing: 0.5px;
        }

        .header-text small {
            display: block;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.85);
            margin-top: 4px;
        }

        .card-body {
            padding: 35px 40px;
        }

        .info-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 20px 25px;
            margin-bottom: 30px;
        }

        .info-section .row {
            margin-bottom: 12px;
        }

        .info-section .row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .info-value {
            color: #555;
            font-size: 14px;
        }

        .form-section {
            margin-top: 25px;
        }

        .form-section h6 {
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            font-size: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid #003366;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
            display: block;
        }

        .form-group label .required {
            color: #dc3545;
            margin-left: 2px;
        }

        .form-group .form-control {
            border: 1px solid #ced4da;
            border-radius: 0;
            padding: 10px 12px;
            font-size: 14px;
        }

        .form-group .form-control:focus {
            border-color: #003366;
            box-shadow: 0 0 0 0.2rem rgba(0, 51, 102, 0.1);
        }

        .form-group textarea.form-control {
            resize: vertical;
            min-height: 90px;
        }

        .form-text {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
        }

        .btn-submit {
            background: #28a745;
            border: none;
            border-radius: 0;
            padding: 12px 40px;
            font-weight: 600;
            font-size: 15px;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background: #218838;
        }

        .card-footer {
            background: #f8f9fa;
            padding: 15px 30px;
            text-align: center;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
            border-radius: 0;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .card-body {
                padding: 25px 20px;
            }

            .btn-submit {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="quote-container">
    <div class="card">
        <div class="card-header">
            <div class="header-content">
                <img src="Images/jathnier_logo.png" alt="Jathnier Corporation" class="header-logo">
                <div class="header-text">
                    <h4>SUPPLIER QUOTATION SUBMISSION</h4>
                    <small>Jathnier Corporation Purchasing System</small>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Information Section -->
            <div class="info-section">
                <div class="row">
                    <div class="col-4 info-label">Canvass No:</div>
                    <div class="col-8 info-value"><?= htmlspecialchars($data['canvass_no']) ?></div>
                </div>
                <div class="row">
                    <div class="col-4 info-label">Item Description:</div>
                    <div class="col-8 info-value"><?= htmlspecialchars($data['item_description']) ?></div>
                </div>
                <div class="row">
                    <div class="col-4 info-label">Quantity:</div>
                    <div class="col-8 info-value"><?= htmlspecialchars($data['quantity'] . ' ' . $data['unit']) ?></div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="form-section">
                <h6>QUOTATION DETAILS</h6>
                
                <form method="POST" novalidate>
                    <!-- CSRF Token -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    
                    <!-- Honeypot Field (hidden from users, catches bots) -->
                    <input type="text" name="website" style="display:none;position:absolute;left:-9999px;" tabindex="-1" autocomplete="off">
                    
                    <div class="form-group">
                        <label for="brand">
                            Brand<span class="required">*</span>
                        </label>
                        <input type="text" id="brand" name="brand" class="form-control" required maxlength="100" placeholder="Enter brand name">
                    </div>

                    <div class="form-group">
                        <label for="price">
                            Unit Price<span class="required">*</span>
                        </label>
                        <input type="number" id="price" name="price" step="0.01" class="form-control" required min="0.01" max="9999999" placeholder="0.00">
                    </div>

                    <div class="form-group">
                        <label for="remarks">
                            Remarks (Optional)
                        </label>
                        <textarea id="remarks" name="remarks" class="form-control" rows="4" maxlength="500" placeholder="Additional information or comments..."></textarea>
                        <div class="form-text">Maximum 500 characters</div>
                    </div>

                    <div class="text-end" style="margin-top: 30px;">
                        <button type="submit" class="btn-submit">
                            Submit Quotation
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-footer">
            &copy; <?= date('Y') ?> Jathnier Corporation. All rights reserved. | Purchasing Department
        </div>
    </div>
</div>

</body>
</html>

