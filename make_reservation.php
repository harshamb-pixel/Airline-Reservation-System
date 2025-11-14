<?php
session_start();
include 'db.php';
include 'header.php';

if (!isset($_SESSION['user']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['user']['Cname'] ?? $_SESSION['user'];
$email = $_SESSION['email'];

if (isset($_POST['reserve'])) {
    $flight_number = $_POST['flight_number'];
    $leg_no = (int)$_POST['leg_no'];
    $date = $_POST['date'];
    $seat_no = $_POST['seat_no'];
    $customer_name = $_POST['customer_name'];
    $cphone = $_POST['cphone'];

    $check = $conn->prepare("SELECT 1 FROM reservation WHERE Flight_number=? AND Leg_no=? AND Date=? AND Seat_no=?");
    $check->bind_param("siss", $flight_number, $leg_no, $date, $seat_no);
    $check->execute();
    $exists = $check->get_result();
    $check->close();

    if ($exists->num_rows > 0) {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>Swal.fire({icon:'error',title:'Seat Already Reserved!',text:'Seat $seat_no is already booked.'}).then(()=>window.location='make_reservation.php');</script>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO reservation (Flight_number, Leg_no, Date, Seat_no, Customer_name, Cphone, Email) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssss", $flight_number, $leg_no, $date, $seat_no, $customer_name, $cphone, $email);
    $stmt->execute();
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>Swal.fire({icon:'success',title:'Reservation Successful!',text:'Redirecting...',showConfirmButton:false,timer:2000}).then(()=>window.location='payment.php?flight_number=$flight_number');</script>";
    exit();
}

$flights = $conn->query("SELECT Flight_number FROM flight");
$seats = $conn->query("SELECT Seat_no FROM seat");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>✈️ Make Reservation</title>
<style>
body {
  background: url('images/flight-bg.jpg') no-repeat center center fixed;
  background-size: cover;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding-top: calc(var(--nav-height) + 40px);
  font-family: 'Poppins', sans-serif;
}

.glass-form {
  width: 520px;
  background: rgba(255,255,255,0.15);
  backdrop-filter: blur(12px);
  border-radius: 20px;
  padding: 35px;
  box-shadow: 0 8px 32px rgba(31,38,135,0.3);
  color: #0d47a1;
  text-align: center;
}

h3 {
  color: #0d47a1;
  margin-bottom: 20px;
  font-weight: 600;
}
.form-control, .form-select {
  border: none;
  background: rgba(255,255,255,0.9);
  border-radius: 8px;
}
.btn-success {
  background: linear-gradient(90deg,#43cea2,#185a9d);
  border: none;
}
.btn-success:hover {
  transform: scale(1.05);
  background: linear-gradient(90deg,#185a9d,#43cea2);
}
</style>
</head>
<body>
<div class="glass-form">
  <h3>✈️ Make a Reservation</h3>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Flight Number</label>
      <select name="flight_number" class="form-select" required>
        <option value="">Select Flight</option>
        <?php while($row=$flights->fetch_assoc()): ?>
        <option value="<?= htmlspecialchars($row['Flight_number']) ?>"><?= htmlspecialchars($row['Flight_number']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="row g-2">
      <div class="col-md-6 mb-3"><label>Leg No</label><input type="number" name="leg_no" class="form-control" required></div>
      <div class="col-md-6 mb-3"><label>Date</label><input type="date" name="date" class="form-control" required></div>
    </div>
    <div class="mb-3">
      <label>Seat</label>
      <select name="seat_no" class="form-select" required>
        <option value="">Select Seat</option>
        <?php while($row=$seats->fetch_assoc()): ?>
        <option value="<?= htmlspecialchars($row['Seat_no']) ?>"><?= htmlspecialchars($row['Seat_no']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <input type="hidden" name="customer_name" value="<?= htmlspecialchars($username) ?>">
    <div class="mb-3"><label>Contact Number</label><input type="text" name="cphone" class="form-control" required></div>
    <button type="submit" name="reserve" class="btn btn-success w-100">🛫 Reserve Now</button>
  </form>
</div>
</body>
</html>
