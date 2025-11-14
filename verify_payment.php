<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $flight = $_POST['flight_number'];
  $amount = $_POST['amount'];
  $email = $_SESSION['email'] ?? $_SESSION['user'] ?? 'guest@example.com';

  // Simulate random success/failure
  $paymentSuccess = rand(1, 100) > 10; // 90% success chance

  echo "<!DOCTYPE html>
  <html lang='en'>
  <head>
    <meta charset='UTF-8'>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
  </head>
  <body>";

  if ($paymentSuccess) {
      // ✅ Update DB
      $stmt = $conn->prepare("UPDATE reservation SET payment_status = 'Paid' WHERE Flight_number = ? AND Email = ?");
      $stmt->bind_param('ss', $flight, $email);
      $stmt->execute();

      echo "<script>
        Swal.fire({
          icon: 'success',
          title: 'Payment Successful!',
          text: 'You paid ₹$amount for Flight $flight.',
          confirmButtonText: 'View Reservations'
        }).then(() => window.location.href = 'view_reservations.php');
      </script>";
  } else {
      // ❌ Payment failed
      $stmt = $conn->prepare("UPDATE reservation SET payment_status = 'Failed' WHERE Flight_number = ? AND Email = ?");
      $stmt->bind_param('ss', $flight, $email);
      $stmt->execute();

      echo "<script>
        Swal.fire({
          icon: 'error',
          title: 'Payment Failed!',
          text: 'Please try again later.',
          confirmButtonText: 'Retry Payment'
        }).then(() => window.location.href = 'payment.php?flight_number=$flight');
      </script>";
  }

  echo "</body></html>";
}
?>
