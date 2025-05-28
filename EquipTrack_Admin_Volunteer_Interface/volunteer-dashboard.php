<?php
require 'login-user_verification.php';
// Check if the user is logged in
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    die('You must be logged in to view this page.');
}

// Fetch the volunteer's name, schedule start, and schedule end from the database
$stmt = $pdo->prepare("SELECT name, schedule_start, schedule_end FROM users WHERE user_id = :user_id");
$stmt->execute([':user_id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If user data is not found, stop the execution
if (!$user) {
    die('User information not found.');
}

// Extract user details
$volunteerName = htmlspecialchars($user['name']);
$schedule_start = htmlspecialchars($user['schedule_start']);
$schedule_end = htmlspecialchars($user['schedule_end']);

// Check if current date is within the schedule period
$currentDate = date('Y-m-d'); // Get today's date
$isScheduled = (strtotime($currentDate) >= strtotime($schedule_start) && strtotime($currentDate) <= strtotime($schedule_end));

// Fetch rejected undertakings for the logged-in volunteer
$stmtRejected = $pdo->prepare("
    SELECT eu.recipient_name, eu.equipment_name, eu.rejection_reason, eu.date_submitted
    FROM equipment_undertaking eu
    WHERE eu.status = 'Rejected'
");
$stmtRejected->execute();
$rejectedList = $stmtRejected->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Equip Track Volunteer</title>
  <link rel="stylesheet" href="volunteer-styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <?php include "volunteer-sidebar.php" ?>

    <div class="main">
      <?php include "volunteer-header.php"?>

      <!-- Header: Display volunteer's name and schedule -->
      <div class="header">
        <p>Schedule: From <?= $schedule_start ?> to <?= $schedule_end ?></p>
      </div>

      <div id="assignment_board" class="section">
          <h1>You're scheduled to report on:</h1>
          <h3>Date: <span id="report_date"><?= htmlspecialchars($schedule_start) ?></span></h3>
          <h3>Assigned by: <span id="assigned_by">Admin</span></h3>

          <!-- Submit Report Button -->
          <button class="submit-btn" id="submit_report_btn" 
              <?php echo !$isScheduled ? 'disabled' : ''; ?> 
              onclick="handleSubmitReport();">
              <i class="fas fa-edit"></i> 
              <?php 
                  echo $isScheduled ? 'Submit Report' : 'Schedule is Over';
              ?>
          </button>
      </div>

      <br>
  
      <div id="report_history" class="section">
    <div class="header">
      <h1>Rejected Undertaking:</h1>
    </div>

    <?php if (!empty($rejectedList)): ?>
    <table class="rejected-table">
        <thead>
            <tr>
                <th>Date Submitted</th>
                <th>Equipment Name</th>
                <th>Recipient Name</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rejectedList as $item): ?>
            <tr>
                <td><?= htmlspecialchars(date('F j, Y', strtotime($item['date_submitted']))) ?></td>
                <td><?= htmlspecialchars($item['equipment_name']) ?></td>
                <td><?= htmlspecialchars($item['recipient_name']) ?></td>
                <td><?= htmlspecialchars($item['rejection_reason']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No rejected undertakings found.</p>
    <?php endif; ?>
</div>



    <script>
        // Handle Submit Report Button Click
        function handleSubmitReport() {
            // Get the submit button element
            const submitButton = document.getElementById("submit_report_btn");

            // Check if button is enabled (not disabled)
            if (!submitButton.disabled) {
                console.log('Button clicked, redirecting to submit report page...');
                window.location.href = "volunteer-submit_report.php"; // Redirect to the report page
            } else {
                console.log('Button is disabled. Cannot proceed.');
            }
        }
    </script>
</body>
</html>
