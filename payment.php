<?php
// Path: payment.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';
include 'header.php'; // optional, remove if header prints content before you want

// Ensure user logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Validate input
if (!isset($_GET['flight_number'])) {
    echo "<script>alert('Flight not specified'); window.location='view_reservations.php';</script>";
    exit();
}

$flight = $_GET['flight_number'];
$email = $_SESSION['email'];
$username = $_SESSION['user']['Cname'] ?? ($_SESSION['user'] ?? 'Guest');

// Fetch latest reservation for this flight and user
$stmt = $conn->prepare("
    SELECT Date, Seat_no, Leg_no, payment_status
    FROM reservation
    WHERE Flight_number = ? AND Email = ?
    ORDER BY Date DESC
    LIMIT 1
");
$stmt->bind_param("ss", $flight, $email);
$stmt->execute();
$res = $stmt->get_result();
$resData = $res->fetch_assoc();
$stmt->close();

if (!$resData) {
    echo "<script>alert('No reservation found for this flight'); window.location='make_reservation.php';</script>";
    exit();
}

$travel_date = $resData['Date'];
$seat_no = $resData['Seat_no'];
$leg_no = $resData['Leg_no'];
$payment_status = $resData['payment_status'] ?? 'pending';

// If already paid, redirect to success or view
if ($payment_status === 'paid') {
    // Optionally fetch the payment tx for this reservation and redirect; for simplicity redirect to view.
    header("Location: view_reservations.php");
    exit();
}

// Fallback fare (you can keep your dynamic fare logic)
$base_fare = 2500;
$final_fare = $base_fare;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Payment — Flight <?= htmlspecialchars($flight) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body{height:100vh;margin:0;display:flex;align-items:center;justify-content:center;background:linear-gradient(to bottom,#a1c4fd,#c2e9fb);font-family:'Poppins',sans-serif;}
    .card-pay{background:rgba(255,255,255,0.25);backdrop-filter:blur(12px);padding:32px;border-radius:14px;max-width:520px;width:95%;box-shadow:0 8px 32px rgba(0,0,0,0.15);}
    .btn-pay{background:linear-gradient(90deg,#667eea,#764ba2);color:#fff;border:none;padding:12px;border-radius:8px;width:100%;}
  </style>
</head>
<body>
  <div class="card-pay">
    <h4>💳 Payment for Flight <?= htmlspecialchars($flight) ?></h4>
    <div class="mt-3 mb-3">
      <p><strong>Seat:</strong> <?= htmlspecialchars($seat_no) ?></p>
      <p><strong>Date:</strong> <?= htmlspecialchars($travel_date) ?></p>
      <p><strong>Amount:</strong> ₹<?= number_format($final_fare, 2) ?></p>
    </div>

    <form id="paymentForm">
      <input type="hidden" name="flight" value="<?= htmlspecialchars($flight) ?>">
      <input type="hidden" name="leg" value="<?= htmlspecialchars($leg_no) ?>">
      <input type="hidden" name="date" value="<?= htmlspecialchars($travel_date) ?>">
      <input type="hidden" name="seat" value="<?= htmlspecialchars($seat_no) ?>">
      <input type="hidden" name="amount" value="<?= htmlspecialchars($final_fare) ?>">
      <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
      <input type="hidden" name="customer_name" value="<?= htmlspecialchars($username) ?>">

      <button type="button" class="btn-pay mt-2" onclick="handlePayment()">Pay Now (Simulated)</button>
    </form>
  </div>

<script>
async function handlePayment(){
  const fd = new FormData(document.getElementById('paymentForm'));

  // Add simulation metadata if you want (FormData supports these)
  fd.append('method', 'card');
  fd.append('currency', 'INR');
  fd.append('currency_symbol', '₹');
  fd.append('method_details', 'Simulated Card Payment');

  try {
    const res = await fetch('payment_process.php', {
      method: 'POST',
      body: fd
    });

    // Expect clean JSON response
    const result = await res.json();

    if (result.ok) {
      Swal.fire({
        icon: 'success',
        title: 'Payment Successful',
        text: 'Transaction ID: ' + result.transaction_id
      }).then(() => {
        // Redirect to success page (or view reservations)
        window.location = 'payment_success.php?tx=' + encodeURIComponent(result.transaction_id);
      });
    } else {
      Swal.fire({icon:'error',title:'Payment Failed',text: result.message || 'Unknown error'});
    }
  } catch (err) {
    Swal.fire({icon:'error',title:'Network Error',text: String(err)});
  }
}
</script>
</body>
</html>
