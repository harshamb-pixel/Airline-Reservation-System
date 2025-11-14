<?php
header('Content-Type: application/json; charset=UTF-8');
ini_set('display_errors', 0);
error_reporting(E_ALL);

include 'db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

function sendJson($reply) {
    echo json_encode(['reply' => $reply]);
    exit;
}

// Catch errors
set_error_handler(function($severity, $message) {
    sendJson("⚠️ PHP Error: $message");
});
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error) sendJson("⚠️ Fatal Error: {$error['message']}");
});

// Input
$data = json_decode(file_get_contents("php://input"), true);
$prompt = strtolower(trim($data['prompt'] ?? ''));
$email = $_SESSION['email'] ?? '';
if (!$prompt) sendJson("❌ No prompt provided.");

// -------------------
// Greetings
// -------------------
if (strpos($prompt, 'hello') !== false || strpos($prompt, 'hi') !== false) {
    sendJson("👋 Hello! How can I assist you with your flights or reservations?");
}

// -------------------
// Reservation Count
// -------------------
if (strpos($prompt, 'my bookings') !== false || strpos($prompt, 'how many reservations') !== false) {
    if (!$email) sendJson("❌ Please log in to see your reservations.");
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM Reservation WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $count = (int)($row['total'] ?? 0);
    sendJson("📊 You have $count active reservation(s).");
}

// -------------------
// Reservation List
// -------------------
if (strpos($prompt, 'show my flights') !== false || strpos($prompt, 'my reservation') !== false) {
    if (!$email) sendJson("❌ Please log in to view your reservations.");
    $stmt = $conn->prepare("SELECT Flight_number, Leg_no, Date, Seat_no FROM Reservation WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) sendJson("📭 You currently have no reservations.");

    // Format as HTML table
    $html = "<table class='table table-bordered table-sm'><thead><tr>";
    while ($field = $result->fetch_field()) {
        $html .= "<th>" . htmlspecialchars($field->name) . "</th>";
    }
    $html .= "</tr></thead><tbody>";

    // Reset pointer
    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        $html .= "<tr>";
        foreach ($row as $val) $html .= "<td>" . htmlspecialchars($val) . "</td>";
        $html .= "</tr>";
    }
    $html .= "</tbody></table>";

    sendJson($html);
}

// -------------------
// SQL Query Execution (SELECT only)
// -------------------
if (strpos($prompt, 'sql') !== false || strpos($prompt, 'query') !== false) {
    preg_match('/SELECT .*/i', $prompt, $matches);
    if (!isset($matches[0])) sendJson("❌ Please provide a valid SELECT query.");

    $query = $matches[0];
    if (!preg_match('/^\s*SELECT/i', $query)) sendJson("❌ Only SELECT queries are allowed.");

    $result = $conn->query($query);
    if (!$result) sendJson("❌ SQL Error: " . $conn->error);
    if ($result->num_rows === 0) sendJson("✅ Query executed successfully, but no rows found.");

    // Format as HTML table
    $html = "<table class='table table-bordered table-striped table-sm'><thead><tr>";
    while ($field = $result->fetch_field()) $html .= "<th>" . htmlspecialchars($field->name) . "</th>";
    $html .= "</tr></thead><tbody>";

    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        $html .= "<tr>";
        foreach ($row as $val) $html .= "<td>" . htmlspecialchars($val) . "</td>";
        $html .= "</tr>";
    }
    $html .= "</tbody></table>";

    sendJson($html);
}

// -------------------
// Fallback
// -------------------
sendJson("🤖 Sorry, I didn't understand your request. You can ask about reservations, bookings, or SQL queries.");
