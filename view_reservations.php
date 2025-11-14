<?php
include 'header.php';
include 'db.php';

// 🔒 Ensure login
if (!isset($_SESSION['user']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['email'];

// ✅ Fetch reservations joined with Leg table
$stmt = $conn->prepare("
    SELECT 
        R.Flight_number, 
        R.Leg_no, 
        R.Date, 
        R.Seat_no, 
        R.Customer_name, 
        R.Cphone,
        L.Airplane_id 
    FROM 
        Reservation R
    JOIN 
        Leg L ON R.Flight_number = L.Flight_number AND R.Leg_no = L.Leg_no
    WHERE 
        R.Email = ?
    ORDER BY 
        R.Date DESC
");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>📋 View Reservations | Airline System</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<style>
/* 🌄 Animated Sky Background */
@keyframes skyMove {
  0% { background: linear-gradient(to bottom, #a1c4fd, #c2e9fb); }
  50% { background: linear-gradient(to bottom, #89f7fe, #66a6ff); }
  100% { background: linear-gradient(to bottom, #a1c4fd, #c2e9fb); }
}

html, body {
  margin: 0;
  padding: 0;
  height: 100%;
  overflow-x: hidden;
  animation: skyMove 45s ease-in-out infinite;
  font-family: 'Poppins', sans-serif;
  background-size: cover;
  background-repeat: no-repeat;
}

/* ☁️ Cloud Animation */
.cloud-layer {
  position: absolute;
  width: 250%;
  height: 100%;
  background: url('images/cloud.png') repeat-x;
  background-size: contain;
  opacity: 0.4;
  animation: drift 180s linear infinite;
  z-index: 1;
}
.cloud-layer:nth-child(1) { top: 10%; opacity: 0.5; animation-duration: 130s; }
.cloud-layer:nth-child(2) { top: 40%; opacity: 0.35; animation-duration: 160s; }
.cloud-layer:nth-child(3) { top: 70%; opacity: 0.25; animation-duration: 200s; }

@keyframes drift {
  from { background-position-x: 0; }
  to { background-position-x: 10000px; }
}

/* ✈️ Plane */
.plane {
  position: absolute;
  width: 340px;
  top: 60%;
  left: -350px;
  opacity: 0.85;
  animation: flyPlane 45s ease-in-out infinite;
  filter: drop-shadow(0 10px 15px rgba(0,0,0,0.3));
  z-index: 2;
}
@keyframes flyPlane {
  0% { transform: translate(0, 0) rotate(3deg); }
  50% { transform: translate(110vw, -8vh) rotate(-3deg); }
  100% { transform: translate(-400px, 5vh) rotate(3deg); }
}

/* 💎 Glass Reservation Table */
.main-content {
  position: relative;
  z-index: 5;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  flex-direction: column;
  min-height: calc(100vh - var(--nav-height));
  padding-top: calc(var(--nav-height) + 20px); /* Space for navbar */
  padding-bottom: 40px;
}

.glass-panel {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(15px);
  border-radius: 20px;
  padding: 35px;
  margin: 0 auto;
  width: 90%;
  max-width: 1100px;
  box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
  color: #0d47a1;
  text-align: center;
}

h2 {
  font-weight: 700;
  color: #0d47a1;
  margin-bottom: 25px;
}

/* 📋 Table Style */
.table {
  background: rgba(255,255,255,0.9);
  border-radius: 10px;
  overflow: hidden;
}
.table th {
  background: linear-gradient(90deg, #667eea, #764ba2);
  color: white;
  text-align: center;
}
.table td {
  text-align: center;
  vertical-align: middle;
}

/* 🎨 Buttons */
.btn-danger {
  background: linear-gradient(90deg, #ff6a6a, #c0392b);
  border: none;
  transition: all 0.3s;
}
.btn-danger:hover {
  transform: scale(1.05);
  background: linear-gradient(90deg, #c0392b, #ff6a6a);
}
.btn-primary {
  background: linear-gradient(90deg, #43cea2, #185a9d);
  border: none;
  font-weight: 600;
  transition: all 0.3s;
}
.btn-primary:hover {
  transform: scale(1.05);
  background: linear-gradient(90deg, #185a9d, #43cea2);
}

/* Responsive */
@media (max-width: 768px) {
  .glass-panel {
    width: 95%;
    padding: 20px;
  }
  .table {
    font-size: 0.85rem;
  }
}
</style>
</head>
<body>

<!-- ☁️ Animated Background Layers -->
<div class="cloud-layer"></div>
<div class="cloud-layer"></div>
<div class="cloud-layer"></div>
<img src="images/airplane.png" alt="plane" class="plane">

<!-- 💎 Glass Table Panel -->
<div class="main-content">
  <div class="glass-panel container">
    <h2>📋 Your Current Reservations</h2>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
      <div class="alert alert-success">✅ Reservation cancelled successfully!</div>
    <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
      <div class="alert alert-danger">❌ Cancellation failed. Please try again.</div>
    <?php endif; ?>

    <div class="table-responsive mt-3">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Flight</th>
            <th>Leg</th>
            <th>Date</th>
            <th>Airplane</th>
            <th>Seat</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['Flight_number']}</td>
                      <td>{$row['Leg_no']}</td>
                      <td>{$row['Date']}</td>
                      <td>{$row['Airplane_id']}</td>
                      <td>{$row['Seat_no']}</td>
                      <td>{$row['Customer_name']}</td>
                      <td>{$row['Cphone']}</td>
                      <td>
                        <form method='POST' action='cancel_reservation.php'>
                          <input type='hidden' name='flight_number' value='{$row['Flight_number']}'>
                          <input type='hidden' name='leg_no' value='{$row['Leg_no']}'>
                          <input type='hidden' name='date' value='{$row['Date']}'>
                          <input type='hidden' name='seat_no' value='{$row['Seat_no']}'>
                          <button type='submit' class='btn btn-danger btn-sm'>Cancel</button>
                        </form>
                      </td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='8'>No reservations found for your account.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-4">
      <a href="make_reservation.php" class="btn btn-primary px-4">➕ Make New Reservation</a>
    </div>
  </div>
</div>

</body>
</html>
