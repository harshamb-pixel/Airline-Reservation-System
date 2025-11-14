<?php
include 'db.php';
session_start();

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

   // Use 'name' for the username/name field, and 'password_hash' for the hashed password field
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    // The bind_param order should match the new columns: name, email, password_hash
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        $msg = "<div class='alert alert-success'>✅ Registration successful! You can now <a href='login.php'>login</a>.</div>";
    } else {
        $msg = "<div class='alert alert-danger'>❌ Error: " . htmlspecialchars($conn->error) . "</div>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Signup | Airline System</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
  <?php include 'header.php'; ?>
  <div class="col-md-6 offset-md-3 mt-4">
    <h2>✈️ Create Account</h2>
    <?= $msg ?>
    <form method="POST">
      <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
      <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
      <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
      <button type="submit" class="btn btn-primary w-100">Sign Up</button>
    </form>
    <p class="mt-3 text-center">Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>
