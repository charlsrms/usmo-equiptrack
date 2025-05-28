<?php
include 'db.php';

// Handle enable/disable
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['disable_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET disabled_at = NOW(), schedule_start = NULL, schedule_end = NULL WHERE user_id = ?");
    $stmt->execute([$_POST['disable_id']]);
    header("Location: manage-volunteers.php");
    exit;
  }

  if (isset($_POST['enable_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET disabled_at = NULL WHERE user_id = ?");
    $stmt->execute([$_POST['enable_id']]);
    header("Location: manage-volunteers.php");
    exit;
  }

  // Handle inline edit save
  if (isset($_POST['edit_user_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET name = ?, username = ?, password = ?, schedule_start = ?, schedule_end = ? WHERE user_id = ?");
    $stmt->execute([
      $_POST['edit_name'],
      $_POST['edit_username'],
      $_POST['edit_password'],
      $_POST['edit_schedule_start'],
      $_POST['edit_schedule_end'],
      $_POST['edit_user_id']
    ]);
    header("Location: manage-volunteers.php");
    exit;
  }

  // Handle add new volunteer
  if (
    isset($_POST['name']) && isset($_POST['username']) && isset($_POST['password']) &&
    isset($_POST['schedule_start']) && isset($_POST['schedule_end'])
  ) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $schedule_start = $_POST['schedule_start'];
    $schedule_end   = $_POST['schedule_end'];

    $stmt   = $pdo->query("SELECT user_id FROM users WHERE user_id LIKE 'V%' ORDER BY user_id DESC LIMIT 1");
    $lastId = $stmt->fetchColumn();

    if ($lastId) {
      $num   = (int)substr($lastId, 1) + 1;
      $newId = 'V' . str_pad($num, 3, '0', STR_PAD_LEFT);
    } else {
      $newId = 'V001';
    }

    $insert = $pdo->prepare("
      INSERT INTO users
        (user_id, username, password, name, role, schedule_start, schedule_end)
      VALUES
        (?, ?, ?, ?, 'volunteer', ?, ?)
    ");
    $insert->execute([$newId, $username, $password, $name, $schedule_start, $schedule_end]);

    header("Location: manage-volunteers.php");
    exit;
  }
}

$editId = $_GET['edit_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Volunteers</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    
    /* Form Section */
    .form-section {
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin-bottom: 30px;
      max-width: 600px;
    }
    
    .form-section h4 {
      margin-top: 0;
      color: #004080;
      border-bottom: 1px solid #eee;
      padding-bottom: 10px;
    }
    
    .form-section input[type="text"],
    .form-section input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-sizing: border-box;
    }
    
    /* PERFECT DATE PICKER POSITIONING */
    .schedule-row {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .schedule-label {
      font-weight: bold;
      margin-right: 10px;
    }
    
    .schedule-date {
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
      width: 140px;
    }
    
    .schedule-to {
      margin: 0 8px;
      color: #666;
    }
    
    
    /* Table Styling */
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    table th {
      background-color: #004080;
      color: white;
      padding: 12px;
      text-align: left;
    }
    
    table td {
      padding: 12px;
      border-bottom: 1px solid #eee;
    }
    
    table tr:last-child td {
      border-bottom: none;
    }
    
    table tr:nth-child(even) {
      background-color: #f9f9f9;
    }
    
    table tr:hover {
      background-color: #f0f0f0;
    }
    
    /* Action Buttons */
    .action-buttons {
      display: flex;
      gap: 5px;
    }
    
    .action-buttons button {
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
      border: none;
      font-size: 13px;
    }
    
    .action-buttons .edit {
      background-color: #004080;
      color: white;
    }
    
    .action-buttons .disable {
      background-color: #d9534f;
      color: white;
    }
    
    .action-buttons .enable {
      background-color: #5cb85c;
      color: white;
    }
    
    /* Status Indicators */
    .status-active {
      color: #5cb85c;
      font-weight: bold;
    }
    
    .status-inactive {
      color: #d9534f;
      font-weight: bold;
    }
    
    /* Inline Edit Form */
    table input[type="text"],
    table input[type="date"] {
      width: 100%;
      padding: 8px;
      border: 1px solid #ddd;
      border-radius: 4px;
      box-sizing: border-box;
    }
    
    .inline-schedule {
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    hr {
      border: 0;
      height: 1px;
      background: #ddd;
      margin: 20px 0;
    }

    .btn-blue {
    background-color: #1565c0;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
  }

  .btn-blue:hover {
    background-color: #0d47a1; /* mas madilim na blue */
    box-shadow: 0 4px 12px rgba(13, 71, 161, 0.6); /* glow effect */
  }

  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>


<div class="main">
  <div class="topbar"><h3>Volunteer Management</h3></div>

  <br>

  <!-- Add Volunteer Form with Perfect Date Picker Positioning -->
  <form method="POST" class="form-section">
    <h4>Add Volunteer</h4>
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="text" name="username" placeholder="Username" required>
    <input type="text" name="password" placeholder="Password" required>
    
    <div class="schedule-row">
      <span class="schedule-label">Schedule:</span>
      <input type="date" name="schedule_start" class="schedule-date" required>
      <span class="schedule-to">to</span>
      <input type="date" name="schedule_end" class="schedule-date" required>
    </div>
    
    <button type="submit" class="btn-blue">Add Volunteer</button>
  </form>

  <hr>

  <table>
    <tr>
      <th>Name</th>
      <th>Username</th>
      <th>Password</th>
      <th>Created At</th>
      <th>Status</th>
      <th>Schedule</th>
      <th>Action</th>
    </tr>
    <?php
    $volunteers = $pdo->query("SELECT * FROM users WHERE role = 'volunteer'");
    foreach ($volunteers as $v) {
      $isEditing = $editId === $v['user_id'];
      $createdAt  = date("F j, Y", strtotime($v['created_at']));
      $schedule   = ($v['schedule_start'] && $v['schedule_end'])
                  ? date("M d, Y", strtotime($v['schedule_start'])) . " - " . date("M d, Y", strtotime($v['schedule_end']))
                  : 'Not Set';
      $status     = $v['disabled_at'] ? 'Inactive' : 'Active';
      $statusClass = $v['disabled_at'] ? 'status-inactive' : 'status-active';

      echo "<tr>";
      if ($isEditing) {
        // Inline edit row
        $scheduleStart = $v['schedule_start'] ? date('Y-m-d', strtotime($v['schedule_start'])) : '';
        $scheduleEnd = $v['schedule_end'] ? date('Y-m-d', strtotime($v['schedule_end'])) : '';
        
        echo "
          <form method='POST'>
            <td><input type='text' name='edit_name' value='{$v['name']}' required></td>
            <td><input type='text' name='edit_username' value='{$v['username']}' required></td>
            <td><input type='text' name='edit_password' value='{$v['password']}' required></td>
            <td>{$createdAt}</td>
            <td><span class='{$statusClass}'>{$status}</span></td>
            <td>
              <div class='inline-schedule'>
                <input type='date' name='edit_schedule_start' value='{$scheduleStart}' required>
                <span>to</span>
                <input type='date' name='edit_schedule_end' value='{$scheduleEnd}' required>
              </div>
            </td>
            <td class='action-buttons'>
              <input type='hidden' name='edit_user_id' value='{$v['user_id']}'>
              <button type='submit' class='edit'>Save</button>
              <a href='manage-volunteers.php'><button type='button' class='disable'>Cancel</button></a>
            </td>
          </form>
        ";
      } else {
        // Normal row
        $actionForm = $v['disabled_at']
          ? "<form method='POST' style='display:inline;'><input type='hidden' name='enable_id' value='{$v['user_id']}'><button type='submit' class='enable'>Activate</button></form>"
          : "<form method='POST' style='display:inline;'><input type='hidden' name='disable_id' value='{$v['user_id']}'><button type='submit' class='disable'>Deactivate</button></form>";

        echo "
          <td>{$v['name']}</td>
          <td>{$v['username']}</td>
          <td>{$v['password']}</td>
          <td>{$createdAt}</td>
          <td><span class='{$statusClass}'>{$status}</span></td>
          <td>{$schedule}</td>
          <td class='action-buttons'>
            <form method='GET' action='manage-volunteers.php' style='display:inline;'>
              <input type='hidden' name='edit_id' value='{$v['user_id']}'>
              <button type='submit' class='edit'>Edit</button>
            </form>
            {$actionForm}
          </td>
        ";
      }
      echo "</tr>";
    }
    ?>
  </table>
</div>

</body>
</html>