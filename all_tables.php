<?php
include 'header.php';
include 'db.php';


if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$tables = [];
$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] === 'create_record') {
    $table = $_POST['table'];
    $fields = $_POST['fields'];
    $columns = implode(",", array_keys($fields));
    $placeholders = implode(",", array_fill(0, count($fields), "?"));
    $values = array_values($fields);
    $types = str_repeat('s', count($values));

    $stmt = $conn->prepare("INSERT INTO `$table` ($columns) VALUES ($placeholders)");
    $stmt->bind_param($types, ...$values);

    $msg = $stmt->execute()
        ? "✅ Record successfully added to '$table'."
        : "❌ Error adding record: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] === 'update_record') {
    $table = $_POST['table'];
    $pk = $_POST['primary_key'];
    $pk_value = $_POST['pk_value'];
    $fields = $_POST['fields'];

    $set_clause = implode(", ", array_map(fn($k) => "$k=?", array_keys($fields)));
    $values = array_values($fields);
    $types = str_repeat('s', count($values)) . "s";

    $stmt = $conn->prepare("UPDATE `$table` SET $set_clause WHERE $pk = ?");
    $stmt->bind_param($types, ...array_merge($values, [$pk_value]));

    $msg = $stmt->execute()
        ? "✏️ Record successfully updated in '$table'."
        : "❌ Error updating record: " . $conn->error;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST['action'] === 'delete_record') {
    $table = $_POST['table'];
    $pk = $_POST['primary_key'];
    $pk_value = $_POST['pk_value'];

    $stmt = $conn->prepare("DELETE FROM `$table` WHERE $pk = ?");
    $stmt->bind_param("s", $pk_value);

    $msg = $stmt->execute()
        ? "🗑️ Record successfully deleted from '$table'."
        : "❌ Error deleting record: " . $conn->error;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>📊 Manage All Tables | Airline Reservation System</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow-x: hidden;
      font-family: 'Poppins', sans-serif;
      animation: skyGradient 45s ease-in-out infinite;
      background-size: cover;
      background-repeat: no-repeat;
    }

    @keyframes skyGradient {
      0% { background: linear-gradient(to bottom, #a1c4fd, #c2e9fb); }
      50% { background: linear-gradient(to bottom, #89f7fe, #66a6ff); }
      100% { background: linear-gradient(to bottom, #a1c4fd, #c2e9fb); }
    }

    .cloud-layer {
      position: absolute;
      width: 200%;
      height: 100%;
      background: url('images/cloud.png') repeat-x;
      background-size: contain;
      opacity: 0.45;
      top: 0;
      left: -50%;
      animation: moveClouds 180s linear infinite;
      z-index: 1;
    }
    .cloud-layer:nth-child(1) { top: 5%; opacity: 0.6; animation-duration: 150s; }
    .cloud-layer:nth-child(2) { top: 40%; opacity: 0.5; animation-duration: 180s; }
    .cloud-layer:nth-child(3) { top: 75%; opacity: 0.4; animation-duration: 210s; }

    @keyframes moveClouds {
      from { background-position-x: 0; }
      to { background-position-x: 10000px; }
    }
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


    .main-content {
      position: relative;
      z-index: 5;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: calc(100vh - var(--nav-height));
      padding-top: calc(var(--nav-height) + 30px);
      padding-bottom: 40px;
    }

    .glass-box {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(15px);
      border-radius: 20px;
      padding: 35px;
      width: 90%;
      max-width: 1100px;
      margin: 0 auto;
      box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
      color: #0d47a1;
    }

    .table {
      background: rgba(255, 255, 255, 0.85);
      border-radius: 10px;
    }

    h2 {
      text-align: center;
      font-weight: 700;
      margin-bottom: 25px;
      color: #0d47a1;
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

    /* ✅ Modal Styling */
    .modal.fade.show {
      display: block;
      background: rgba(0, 0, 0, 0.6);
    }

    @media (max-width: 768px) {
      .glass-box {
        width: 95%;
        padding: 20px;
      }
      h2 {
        font-size: 1.2rem;
      }
    }
  </style>
</head>
<body>

  <!-- ☁️ Background Layers -->
  <div class="cloud-layer"></div>
  <div class="cloud-layer"></div>
  <div class="cloud-layer"></div>
  <img src="images/airplane.png" alt="plane" class="plane">

  <!-- 💎 Main Glass Panel -->
  <div class="main-content">
    <div class="glass-box">
      <h2>📊 Manage All Tables</h2>

      <!-- CRUD Form Section -->
      <form method="GET" class="row g-2 align-items-center mb-4">
        <div class="col-md-5">
          <select name="table" class="form-select" required>
            <option value="">🔽 Select Table</option>
            <?php foreach ($tables as $table): ?>
              <option value="<?= htmlspecialchars($table) ?>" <?= (isset($_GET['table']) && $_GET['table'] === $table) ? 'selected' : '' ?>>
                <?= ucfirst($table) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <select name="action" class="form-select" required>
            <option value="">⚡ Select Action</option>
            <option value="create" <?= ($_GET['action'] ?? '') === 'create' ? 'selected' : '' ?>>➕ Create</option>
            <option value="read" <?= ($_GET['action'] ?? '') === 'read' ? 'selected' : '' ?>>📋 Read</option>
            <option value="update" <?= ($_GET['action'] ?? '') === 'update' ? 'selected' : '' ?>>✏️ Update</option>
            <option value="delete" <?= ($_GET['action'] ?? '') === 'delete' ? 'selected' : '' ?>>❌ Delete</option>
          </select>
        </div>
        <div class="col-md-3">
          <button type="submit" class="btn btn-primary w-100">Go</button>
        </div>
      </form>

      <?php
      if (isset($_GET['table']) && in_array($_GET['table'], $tables)) {
          $table_name = $_GET['table'];
          $action = $_GET['action'] ?? 'read';

          echo "<div class='card border-0 shadow-sm'>";
          echo "<div class='card-header bg-primary text-white fw-bold fs-5'>".ucfirst($table_name)."</div>";
          echo "<div class='card-body table-responsive'>";

          // READ TABLE CONTENTS
          $data = $conn->query("SELECT * FROM `$table_name`");
          if ($data && $data->num_rows > 0) {
              echo "<table class='table table-bordered table-striped text-center'>";
              echo "<thead class='table-light'><tr>";
              $fields = $data->fetch_fields();
              foreach ($fields as $field) echo "<th>{$field->name}</th>";
              echo "</tr></thead><tbody>";
              while ($row = $data->fetch_assoc()) {
                  echo "<tr>";
                  foreach ($row as $v) echo "<td>" . ($v === NULL || $v === '' ? '—' : htmlspecialchars($v)) . "</td>";
                  echo "</tr>";
              }
              echo "</tbody></table>";
          } else {
              echo "<p class='text-muted'>No data found in this table.</p>";
          }

          // CRUD FORMS
          if (in_array($action, ['create', 'update', 'delete'])) {
              $columns = $conn->query("DESCRIBE `$table_name`");
              $first_col = $columns->fetch_assoc();
              $pk = $first_col['Field'];
              $columns->data_seek(0);

              echo "<hr><form method='POST' class='mt-3'>";
              echo "<input type='hidden' name='table' value='$table_name'>";

              if ($action === 'create') {
                  echo "<input type='hidden' name='action' value='create_record'>";
                  echo "<h5 class='text-success'>➕ Add New Record</h5>";
                  while ($col = $columns->fetch_assoc()) {
                      $field = htmlspecialchars($col['Field']);
                      echo "<div class='mb-2'><input type='text' name='fields[$field]' placeholder='$field' class='form-control'></div>";
                  }
                  echo "<button type='submit' class='btn btn-success mt-2'>Add Record</button>";
              }

              if ($action === 'update') {
                  echo "<input type='hidden' name='action' value='update_record'>";
                  echo "<input type='hidden' name='primary_key' value='$pk'>";
                  echo "<h5 class='text-warning'>✏️ Update Record</h5>";
                  echo "<input type='text' name='pk_value' placeholder='Enter $pk to Update' class='form-control mb-2'>";
                  while ($col = $columns->fetch_assoc()) {
                      $field = htmlspecialchars($col['Field']);
                      echo "<div class='mb-2'><input type='text' name='fields[$field]' placeholder='New value for $field' class='form-control'></div>";
                  }
                  echo "<button type='submit' class='btn btn-warning mt-2'>Update Record</button>";
              }

              if ($action === 'delete') {
                  echo "<input type='hidden' name='action' value='delete_record'>";
                  echo "<input type='hidden' name='primary_key' value='$pk'>";
                  echo "<h5 class='text-danger'>❌ Delete Record</h5>";
                  echo "<input type='text' name='pk_value' placeholder='Enter $pk to Delete' class='form-control mb-2'>";
                  echo "<button type='submit' class='btn btn-danger'>Delete Record</button>";
              }

              echo "</form>";
          }

          echo "</div></div>";
      }
      ?>
    </div>
  </div>

  <!-- ✅ Popup Modal -->
  <?php if (!empty($msg)): ?>
  <div class="modal fade show" id="statusModal" tabindex="-1" style="display:block;">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content text-center p-4">
        <?php if (str_starts_with($msg, '✅') || str_starts_with($msg, '✏️') || str_starts_with($msg, '🗑️')): ?>
          <h5 class="text-success"><?= $msg ?></h5>
        <?php else: ?>
          <h5 class="text-danger"><?= $msg ?></h5>
        <?php endif; ?>
        <button class="btn btn-primary mt-3" onclick="closeModal()">OK</button>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <script>
    function closeModal() {
      document.getElementById('statusModal').style.display = 'none';
      window.location.href = "all_tables.php";
    }
  </script>
</body>
</html>
