<?php
session_start();
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>✈️ Airline Reservation System</title>

<style>
/* 🌄 Full-screen background */
body {
  background: url('images/flight-bg.jpg') no-repeat center center fixed;
  background-size: cover;
  margin: 0;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;  /* ✅ Centers the welcome window */
  text-align: center;
  font-family: 'Poppins', sans-serif;
}

/* 💎 Glass welcome box */
.welcome-box {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(15px);
  border-radius: 20px;
  padding: 40px 60px;
  color: #0d47a1;
  box-shadow: 0 8px 32px rgba(0,0,0,0.2);
  animation: fadeIn 1.5s ease;
  max-width: 600px;
  width: 90%;
}

@keyframes fadeIn {
  from {opacity: 0; transform: translateY(25px);}
  to {opacity: 1; transform: translateY(0);}
}

h1 {
  font-weight: 700;
  font-size: 2.2rem;
  color: #0d47a1;
}
h5 {
  font-weight: 400;
  color: #1565c0;
  margin-bottom: 25px;
}

/* 🟢 Buttons */
.btn-custom {
  background: linear-gradient(90deg, #43cea2, #185a9d);
  border: none;
  color: white;
  padding: 12px 25px;
  margin: 8px;
  border-radius: 10px;
  font-weight: 600;
  transition: all 0.3s ease;
}
.btn-custom:hover {
  transform: scale(1.05);
  background: linear-gradient(90deg, #185a9d, #43cea2);
}
</style>
</head>

<body>

<div class="welcome-box">
  <h1>✈️ Welcome to Airline Reservation System</h1>
  <h5>Book your flights, view tickets, and manage reservations easily.</h5>

  <?php if (isset($_SESSION['user'])): ?>
      <div>
          <a href="make_reservation.php" class="btn btn-custom">🛫 Book Flight</a>
          <a href="view_reservations.php" class="btn btn-custom">📋 View Reservations</a>
          <a href="cancel_reservation.php" class="btn btn-custom">❌ Cancel Booking</a>
      </div>
  <?php else: ?>
      <div>
          <a href="login.php" class="btn btn-custom">🔑 Login</a>
          <a href="signup.php" class="btn btn-custom">🧾 Sign Up</a>
      </div>
  <?php endif; ?>
</div>

</body>
</html>
