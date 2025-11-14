<?php
include 'db.php';
session_start();

// Restrict access to admin only
if (!isset($_SESSION['email']) || $_SESSION['email'] !== 'admin@airline.com') {
  echo "<script>alert('Access denied! Admins only.'); window.location='login.php';</script>";
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>🛫 Airline Admin Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
html, body {
  height: 100%;
  margin: 0;
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(to bottom, #a1c4fd, #c2e9fb);
  overflow-x: hidden;
  position: relative;
}

/* Clouds Animation */
.cloud {
  position: absolute;
  opacity: 0.85;
  animation: moveClouds linear infinite;
  z-index: 0;
}

.cloud1 { top: 10%; left: -300px; width: 250px; animation-duration: 70s; }
.cloud2 { top: 40%; left: -400px; width: 300px; animation-duration: 90s; animation-delay: 10s; }
.cloud3 { top: 70%; left: -350px; width: 230px; animation-duration: 110s; animation-delay: 25s; }

@keyframes moveClouds {
  from { transform: translateX(0); }
  to { transform: translateX(130vw); }
}

/*Airplane Animation */
.plane {
  position: absolute;
  width: 2000px;
  top: 0%;
  left: -400px;
  opacity: 0.8;
  animation: flyPlane 60s ease-in-out infinite;
  z-index: 1;
}

@keyframes flyPlane {
  0% { transform: translate(0, 0) rotate(4deg); }
  50% { transform: translate(120vw, -8vh) rotate(-4deg); }
  100% { transform: translate(-100px, 4vh) rotate(4deg); }
}

/* Admin Panel Glass UI */
.admin-panel {
  position: relative;
  z-index: 5;
  background: rgba(255, 255, 255, 0.2);
  backdrop-filter: blur(14px);
  border-radius: 20px;
  padding: 30px;
  width: 95%;
  max-width: 1200px;
  margin: 100px auto;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
}

h1 {
  color: #0d47a1;
  font-weight: 700;
  text-align: center;
}

.nav-tabs .nav-link.active {
  background-color: #0d47a1;
  color: #fff;
  border-radius: 10px;
}

.table {
  background: rgba(255,255,255,0.95);
  border-radius: 10px;
}
</style>
</head>

<body>
  <!--  Animated Background -->
  <img src="images/cloud.png" class="cloud cloud1">
  <img src="images/cloud.png" class="cloud cloud2">
  <img src="images/cloud.png" class="cloud cloud3">
  <img src="images/airplane.png" class="plane">

  <!-- 💎 Admin Glass Panel -->
  <div class="admin-panel">
    <h1>🛫 Airline Admin Dashboard</h1>

    <ul class="nav nav-tabs mt-4" id="adminTabs" role="tablist">
      <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#flights">✈️ Flights</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#reservations">🧾 Reservations</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#payments">💳 Payments</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#fares">💹 Dynamic Fares</a></li>
      <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#query">🧠 Run Query</a></li>
    </ul>

    <div class="tab-content mt-3">
      <!-- Flights -->
      <div class="tab-pane fade show active" id="flights">
        <h4>✈️ Flight Details</h4>
        <table class="table table-bordered table-sm">
          <thead><tr><th>Flight Number</th><th>Airline</th><th>Departure</th><th>Arrival</th></tr></thead>
          <tbody>
          <?php
          $res = $conn->query("SELECT * FROM flight LIMIT 10");
          if ($res->num_rows > 0) {
            while ($r = $res->fetch_assoc()) {
              echo "<tr>
                      <td>{$r['Flight_number']}</td>
                      <td>{$r['Airline']}</td>
                      <td>{$r['Departure_airport']}</td>
                      <td>{$r['Arrival_airport']}</td>
                    </tr>";
            }
          } else echo "<tr><td colspan='4'>No flights found</td></tr>";
          ?>
          </tbody>
        </table>
      </div>

      <!-- Reservations -->
      <div class="tab-pane fade" id="reservations">
        <h4>🧾 Reservations</h4>
        <table class="table table-bordered table-sm">
          <thead><tr><th>Email</th><th>Flight</th><th>Date</th><th>Seat</th></tr></thead>
          <tbody>
          <?php
          $res = $conn->query("SELECT Email, Flight_number, Date, Seat_no FROM reservation ORDER BY Date DESC LIMIT 10");
          if ($res->num_rows > 0) {
            while ($r = $res->fetch_assoc()) {
              echo "<tr>
                      <td>{$r['Email']}</td>
                      <td>{$r['Flight_number']}</td>
                      <td>{$r['Date']}</td>
                      <td>{$r['Seat_no']}</td>
                    </tr>";
            }
          } else echo "<tr><td colspan='4'>No reservations yet</td></tr>";
          ?>
          </tbody>
        </table>
      </div>

      <!-- Payments -->
      <div class="tab-pane fade" id="payments">
        <h4>💳 Payment Transactions</h4>
        <table class="table table-bordered table-sm">
          <thead><tr><th>Email</th><th>Flight</th><th>Amount</th><th>Status</th></tr></thead>
          <tbody>
          <?php
          $res = $conn->query("SELECT * FROM payments ORDER BY id DESC LIMIT 10");
          if ($res->num_rows > 0) {
            while ($r = $res->fetch_assoc()) {
              echo "<tr>
                      <td>{$r['email']}</td>
                      <td>{$r['flight_number']}</td>
                      <td>₹{$r['amount']}</td>
                      <td>{$r['status']}</td>
                    </tr>";
            }
          } else echo "<tr><td colspan='4'>No payments yet</td></tr>";
          ?>
          </tbody>
        </table>
      </div>

      <!-- Dynamic Fares -->
      <div class="tab-pane fade" id="fares">
        <h4>💹 Dynamic Fares</h4>
        <table class="table table-bordered table-sm">
          <thead><tr><th>Flight</th><th>Date</th><th>Class</th><th>Base</th><th>Factor</th><th>Final</th></tr></thead>
          <tbody>
          <?php
          $res = $conn->query("SELECT df.flight_number, df.travel_date, sc.class_name, df.base_fare, df.demand_factor, df.final_fare
                               FROM dynamic_fare df JOIN seat_class sc ON df.seat_class = sc.class_id
                               ORDER BY df.travel_date DESC LIMIT 10");
          if ($res->num_rows > 0) {
            while ($r = $res->fetch_assoc()) {
              echo "<tr>
                      <td>{$r['flight_number']}</td>
                      <td>{$r['travel_date']}</td>
                      <td>{$r['class_name']}</td>
                      <td>₹{$r['base_fare']}</td>
                      <td>x{$r['demand_factor']}</td>
                      <td><b>₹{$r['final_fare']}</b></td>
                    </tr>";
            }
          } else echo "<tr><td colspan='6'>No dynamic fares found</td></tr>";
          ?>
          </tbody>
        </table>
      </div>

      <!-- Custom SQL Query -->
      <div class="tab-pane fade" id="query">
        <h4>🧠 Execute SQL Query</h4>
        <form method="POST" class="mb-3">
          <textarea name="custom_query" class="form-control" rows="4" placeholder="SELECT * FROM users LIMIT 5;" required></textarea>
          <button type="submit" name="run_query" class="btn btn-primary mt-2">Run Query</button>
        </form>
        <?php
        if (isset($_POST['run_query'])) {
          $q = trim($_POST['custom_query']);
          if (preg_match('/^\s*SELECT/i', $q)) {
            $result = $conn->query($q);
            if ($result && $result->num_rows > 0) {
              echo "<div class='table-responsive'><table class='table table-bordered table-sm'><thead><tr>";
              while ($field = $result->fetch_field()) echo "<th>{$field->name}</th>";
              echo "</tr></thead><tbody>";
              while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $val) echo "<td>" . htmlspecialchars($val) . "</td>";
                echo "</tr>";
              }
              echo "</tbody></table></div>";
            } else echo "<div class='alert alert-warning'>No results found or invalid query.</div>";
          } else {
            echo "<div class='alert alert-danger'>Only SELECT queries are allowed.</div>";
          }
        }
        ?>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
