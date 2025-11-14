<?php
// Path: /header.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>✈️ Airline System</title>

<!-- Google Fonts & Bootstrap CSS -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
:root {
  --nav-height: 72px;
}

/* Full page & base styles */
html, body {
  margin: 0;
  padding: 0;
  font-family: 'Poppins', sans-serif;
  height: 100%;
}

/* ✅ Fixed Navbar */
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: var(--nav-height);
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  z-index: 1100;
}

/* ✅ Ensure page content appears below navbar */
body {
  padding-top: var(--nav-height);
}

/* Navbar content styling */
.navbar-brand {
  font-weight: 700;
  color: #667eea !important;
  font-size: 1.25rem;
}

.nav-link {
  color: #333 !important;
  font-weight: 500;
  margin: 0 8px;
  transition: 0.3s;
}

.nav-link:hover {
  color: #667eea !important;
}

.nav-link.text-danger:hover {
  color: #e53935 !important;
}

.nav-link.fw-semibold {
  color: #764ba2 !important;
}

.navbar-toggler {
  border: none;
}
</style>
</head>

<body>

<!-- ✈️ Fixed Top Navigation -->
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid px-4">
    <a class="navbar-brand" href="index.php">✈️ Airline System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <?php if (isset($_SESSION['user'])): ?>
          <li class="nav-item"><a class="nav-link" href="index.php">🏠 Home</a></li>
          <li class="nav-item"><a class="nav-link" href="make_reservation.php">🛫 Book</a></li>
          <li class="nav-item"><a class="nav-link" href="view_reservations.php">📋 View</a></li>
          <li class="nav-item"><a class="nav-link" href="cancel_reservation.php">❌ Cancel</a></li>
          <li class="nav-item"><a class="nav-link" href="query_executor.php">🧠 SQL</a></li>
          <li class="nav-item"><a class="nav-link" href="all_tables.php">📊 Tables</a></li>

          <?php if ($_SESSION['email'] === 'admin@airline.com'): ?>
            <li class="nav-item"><a class="nav-link text-warning fw-bold" href="admin_dashboard.php">⚙ Admin</a></li>
          <?php endif; ?>

          <li class="nav-item"><span class="nav-link fw-semibold">👤 <?= htmlspecialchars($_SESSION['user']['Cname'] ?? 'User') ?></span></li>
          <li class="nav-item"><a class="nav-link text-danger" href="logout.php">🚪 Logout</a></li>

        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
