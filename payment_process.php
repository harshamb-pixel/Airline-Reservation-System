<?php
// Path: payment_process.php
session_start();
// Buffer output to avoid stray whitespace breaking JSON
if (ob_get_level() === 0) ob_start();

header('Content-Type: application/json; charset=utf-8');
include 'db.php';

// Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (ob_get_level()) ob_end_clean();
    echo json_encode(['ok' => false, 'message' => 'Invalid request method']);
    exit;
}

// Read POST (FormData)
$flight = $_POST['flight'] ?? null;
$leg    = $_POST['leg'] ?? null;
$date   = $_POST['date'] ?? null;
$seat   = $_POST['seat'] ?? null;
$amount = $_POST['amount'] ?? null;
$email  = $_POST['email'] ?? null;
$cust   = $_POST['customer_name'] ?? null;
$method = $_POST['method'] ?? 'card';
$currency = $_POST['currency'] ?? 'INR';
$symbol = $_POST['currency_symbol'] ?? '₹';
$details = $_POST['method_details'] ?? 'Simulated Payment';

// Basic validation
if (!$flight || !$leg || !$date || !$seat || !$amount || !$email) {
    if (ob_get_level()) ob_end_clean();
    echo json_encode(['ok' => false, 'message' => 'Missing required fields']);
    exit;
}

// Sanitize/normalize (example)
$flight = trim($flight);
$leg = trim($leg);
$date = trim($date);
$seat = trim($seat);
$amount = trim($amount);
$email = trim($email);
$cust = trim($cust);

// Generate transaction id
$tx = 'TXN' . bin2hex(random_bytes(5)); // 10 hex chars

// Insert into payments table
// You should ensure payments table exists with these columns.
$stmt = $conn->prepare("INSERT INTO payments 
    (transaction_id, flight_number, leg_no, date, seat_no, email, customer_name, amount, currency, currency_symbol, method, method_details, status, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'completed', NOW())"
);

if (!$stmt) {
    if (ob_get_level()) ob_end_clean();
    echo json_encode(['ok' => false, 'message' => 'DB prepare error: ' . $conn->error]);
    exit;
}

$stmt->bind_param(
    "sssssssdssss",
    $tx,
    $flight,
    $leg,
    $date,
    $seat,
    $email,
    $cust,
    $amount,
    $currency,
    $symbol,
    $method,
    $details
);

if (!$stmt->execute()) {
    if (ob_get_level()) ob_end_clean();
    echo json_encode(['ok' => false, 'message' => 'DB execute error: ' . $stmt->error]);
    $stmt->close();
    exit;
}
$stmt->close();

// Update reservation to paid (only one)
$upd = $conn->prepare("UPDATE reservation SET payment_status = 'paid' WHERE Flight_number = ? AND Email = ? AND Seat_no = ? AND Date = ? LIMIT 1");
if ($upd) {
    $upd->bind_param("ssss", $flight, $email, $seat, $date);
    $upd->execute();
    $upd->close();
}

// Clean output buffer and respond
if (ob_get_level()) ob_end_clean();
echo json_encode(['ok' => true, 'transaction_id' => $tx]);
exit;
