<?php
// Path: payment_success.php
session_start();
include 'db.php';
include 'header.php'; // optional

$tx = $_GET['tx'] ?? '';
$payment = null;

if ($tx) {
    $stmt = $conn->prepare("SELECT * FROM payments WHERE transaction_id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $tx);
        $stmt->execute();
        $res = $stmt->get_result();
        $payment = $res ? $res->fetch_assoc() : null;
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Payment Successful</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:linear-gradient(180deg,#e6f7ff,#fff);font-family:Poppins, sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;}
    .card{padding:28px;border-radius:14px;box-shadow:0 10px 30px rgba(8,30,80,0.06);max-width:700px;width:95%;}
    .badge-success{background:linear-gradient(135deg,#4caf50,#66bb6a);color:#fff;padding:12px;border-radius:50%;display:inline-block;width:90px;height:90px;text-align:center;line-height:66px;font-size:28px;margin-bottom:16px;}
  </style>
</head>
<body>
  <div class="card text-center">
    <div class="mb-3 badge-success">✓</div>
    <h3>Payment Successful</h3>

    <?php if ($payment): ?>
      <p class="text-muted">Transaction ID: <strong><?= htmlspecialchars($payment['transaction_id']) ?></strong></p>
      <h4><?= htmlspecialchars(($payment['currency_symbol'] ?? '₹') . ' ' . number_format($payment['amount'], 2)) ?></h4>
      <p>Flight: <?= htmlspecialchars($payment['flight_number'] ?? '') ?> — Seat: <?= htmlspecialchars($payment['seat_no'] ?? '') ?> — Date: <?= htmlspecialchars($payment['date'] ?? '') ?></p>
      <p>Paid by: <?= htmlspecialchars($payment['customer_name'] ?? $payment['email'] ?? '') ?></p>
    <?php else: ?>
      <p class="text-muted">No payment found for this transaction ID.</p>
    <?php endif; ?>

    <div class="mt-3">
      <a href="view_reservations.php" class="btn btn-primary">View Reservations</a>
      <a href="index.php" class="btn btn-outline-secondary">Home</a>
    </div>
  </div>
</body>
</html>
