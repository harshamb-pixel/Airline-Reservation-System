<?php
include 'header.php';
include 'db.php';

// 🔒 Ensure user login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// 🧹 Handle Delete Request
$status = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $flight = $_POST['flight_number'];
    $leg = $_POST['leg_no'];
    $date = $_POST['date'];
    $seat = $_POST['seat_no'];

    $stmt = $conn->prepare("
        DELETE FROM reservation
        WHERE Flight_number = ? AND Leg_no = ? AND Date = ? AND Seat_no = ?
    ");
    $stmt->bind_param("siss", $flight, $leg, $date, $seat);
    $status = $stmt->execute() ? 'success' : 'error';
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>❌ Cancel Reservation | Airline System</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    /* 🌤️ Animated Sky Gradient */
    @keyframes skyGradient {
      0% { background: linear-gradient(to bottom, #a1c4fd, #c2e9fb); }
      50% { background: linear-gradient(to bottom, #89f7fe, #66a6ff); }
      100% { background: linear-gradient(to bottom, #a1c4fd, #c2e9fb); }
    }

    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Poppins', sans-serif;
      animation: skyGradient 45s ease-in-out infinite;
      background-size: cover;
      overflow-x: hidden;
      overflow-y: auto;
    }

    /* ☁️ Clouds Animation */
    .cloud-layer {
      position: absolute;
      width: 250%;
      height: 100%;
      background: url('images/cloud.png') repeat-x;
      background-size: contain;
      opacity: 0.4;
      top: 0;
      left: -50%;
      animation: moveClouds 180s linear infinite;
      z-index: 1;
    }
    .cloud-layer:nth-child(1) { top: 10%; opacity: 0.5; animation-duration: 140s; }
    .cloud-layer:nth-child(2) { top: 45%; opacity: 0.35; animation-duration: 170s; }
    .cloud-layer:nth-child(3) { top: 70%; opacity: 0.25; animation-duration: 200s; }

    @keyframes moveClouds {
      from { background-position-x: 0; }
      to { background-position-x: 10000px; }
    }

    /* ✈️ Airplane Animation */
    .plane {
      position: absolute;
      width: 300px;
      top: 55%;
      left: -350px;
      z-index: 2;
      opacity: 0.9;
      animation: flyPlane 45s ease-in-out infinite;
      filter: drop-shadow(0 8px 12px rgba(0,0,0,0.3));
    }
    @keyframes flyPlane {
      0% { transform: translate(0, 0) rotate(3deg); }
      50% { transform: translate(110vw, -8vh) rotate(-3deg); }
      100% { transform: translate(-400px, 5vh) rotate(3deg); }
    }

    /* 💎 Glass Cancel Form */
    .main-content {
      position: relative;
      z-index: 5;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      flex-direction: column;
      min-height: calc(100vh - var(--nav-height));
      padding-top: calc(var(--nav-height) + 40px);
      padding-bottom: 60px;
    }

    .glass-card {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(15px);
      border-radius: 20px;
      padding: 40px;
      box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
      max-width: 600px;
      width: 90%;
      margin: 0 auto;
      color: #0d47a1;
    }

    h2 {
      font-weight: 700;
      color: #b71c1c;
      text-align: center;
      margin-bottom: 25px;
    }

    .form-control, .form-select {
      border-radius: 8px;
      background: rgba(255,255,255,0.9);
      border: none;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .btn-danger {
      background: linear-gradient(90deg, #e53935, #b71c1c);
      border: none;
      font-weight: 600;
      transition: 0.3s;
    }
    .btn-danger:hover {
      background: linear-gradient(90deg, #b71c1c, #e53935);
      transform: scale(1.05);
    }

    .btn-outline-primary, .btn-outline-secondary {
      border-radius: 10px;
      padding: 8px 18px;
      font-weight: 600;
    }

    /* 📱 Responsive */
    @media (max-width: 576px) {
      .glass-card {
        padding: 25px;
        width: 95%;
      }
    }
  </style>
</head>

<body>
  <!-- ☁️ Background Layers -->
  <div class="cloud-layer"></div>
  <div class="cloud-layer"></div>
  <div class="cloud-layer"></div>
  <img src="images/airplane.png" alt="airplane" class="plane">

  <!-- 💎 Glass Cancel Reservation Form -->
  <div class="main-content">
    <div class="glass-card">
      <h2>❌ Cancel a Reservation</h2>

      <form method="POST" class="mt-3">
        <div class="mb-3">
          <label class="form-label fw-bold text-dark">Select Reservation</label>
          <select class="form-select" id="reservationDropdown" onchange="fillForm(this)">
            <option value="">🔽 Choose a reservation</option>
            <?php
            $email = $_SESSION['email'];
            $res = $conn->prepare("SELECT Flight_number, Leg_no, Date, Seat_no, Customer_name FROM reservation WHERE Email = ?");
            $res->bind_param("s", $email);
            $res->execute();
            $result = $res->get_result();
            while ($row = $result->fetch_assoc()) {
                $val = htmlspecialchars(json_encode($row));
                echo "<option value='$val'>{$row['Customer_name']} - {$row['Flight_number']} (Seat {$row['Seat_no']})</option>";
            }
            $res->close();
            ?>
          </select>
        </div>

        <input type="text" name="flight_number" id="flight_number" class="form-control mb-2" placeholder="Flight Number" required>
        <input type="number" name="leg_no" id="leg_no" class="form-control mb-2" placeholder="Leg No" required>
        <input type="date" name="date" id="date" class="form-control mb-2" required>
        <input type="text" name="seat_no" id="seat_no" class="form-control mb-3" placeholder="Seat No" required>

        <button type="submit" class="btn btn-danger w-100">Cancel Reservation</button>
      </form>

      <div class="mt-4 d-flex justify-content-between">
        <a href="index.php" class="btn btn-outline-secondary">🏠 Home</a>
        <a href="view_reservations.php" class="btn btn-outline-primary">📋 View Reservations</a>
      </div>
    </div>
  </div>

  <!-- ✅ Status Modal -->
  <?php if ($status): ?>
  <div class="modal fade show" id="statusModal" tabindex="-1" style="display:block; background:rgba(0,0,0,0.5);" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <?php if ($status === 'success'): ?>
          <h4 class="text-success">✅ Reservation Cancelled!</h4>
          <p>Your reservation has been successfully removed.</p>
        <?php else: ?>
          <h4 class="text-danger">❌ Error!</h4>
          <p>Something went wrong. Please try again.</p>
        <?php endif; ?>
        <button class="btn btn-primary mt-3" onclick="closeModal()">OK</button>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <script>
    function fillForm(select) {
      if (!select.value) return;
      const data = JSON.parse(select.value);
      document.getElementById('flight_number').value = data.Flight_number;
      document.getElementById('leg_no').value = data.Leg_no;
      document.getElementById('date').value = data.Date;
      document.getElementById('seat_no').value = data.Seat_no;
    }

    function closeModal() {
      document.getElementById('statusModal').style.display = 'none';
      window.location.href = "cancel_reservation.php";
    }
  </script>
</body>
</html>
