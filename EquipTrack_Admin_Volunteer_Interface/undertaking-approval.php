<?php
include('db.php'); // your PDO connection here

// Handle approval or reject actions, example:
if (isset($_POST['approve'])) {
  $id = $_POST['approval_id'];

  // 1. Fetch the full row data
  $stmt = $pdo->prepare("SELECT * FROM equipment_undertaking WHERE borrower_id = ?");
  $stmt->execute([$id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($row) {
      // 2. Insert the data into the undertaking_approval table
      $insert = $pdo->prepare("INSERT INTO undertaking_approval (
          borrower_id, equipment_code, equipment_name, model, serial_number, condition_status,
          recipient_name, position, office_location, contact_info, event_purpose, usage_start, usage_end,
          date_submitted, status
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

      $insert->execute([
          $row['borrower_id'], $row['equipment_code'], $row['equipment_name'], $row['model'],
          $row['serial_number'], $row['condition_status'], $row['recipient_name'], $row['position'],
          $row['office_location'], $row['contact_info'], $row['event_purpose'], $row['usage_start'], $row['usage_end'],
          $row['date_submitted'], 'Approved'
      ]);

      // 3. Delete the row from the equipment_undertaking table
      $delete = $pdo->prepare("DELETE FROM equipment_undertaking WHERE borrower_id = ?");
      $delete->execute([$id]);
  }

  header("Location: undertaking-approval.php");
  exit;
}

if (isset($_POST['reject'])) {
  $id = $_POST['approval_id'];
  $reason = $_POST['rejection_reason'] ?? '';
  $stmt = $pdo->prepare("UPDATE equipment_undertaking SET status = 'Rejected', rejection_reason = ? WHERE borrower_id = ?");
  $stmt->execute([$reason, $id]);
  header("Location: undertaking-approval.php");
  exit;
}
//REJECTED COLUMN
if (isset($_POST['reapprove'])) {
  $id = $_POST['approval_id'];

  $stmt = $pdo->prepare("SELECT * FROM equipment_undertaking WHERE borrower_id = ?");
  $stmt->execute([$id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($row) {
      $insert = $pdo->prepare("INSERT INTO undertaking_approval (
          borrower_id, equipment_code, equipment_name, model, serial_number, condition_status,
          recipient_name, position, office_location, contact_info, event_purpose, usage_start, usage_end,
          date_submitted, status
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

      $insert->execute([
          $row['borrower_id'], $row['equipment_code'], $row['equipment_name'], $row['model'],
          $row['serial_number'], $row['condition_status'], $row['recipient_name'], $row['position'],
          $row['office_location'], $row['contact_info'], $row['event_purpose'], $row['usage_start'], $row['usage_end'],
          $row['date_submitted'], 'Approved'
      ]);

      // Delete from rejected list
      $delete = $pdo->prepare("DELETE FROM equipment_undertaking WHERE borrower_id = ?");
      $delete->execute([$id]);
  }

  header("Location: undertaking-approval.php");
  exit;
}

if (isset($_POST['delete'])) {
  $id = $_POST['approval_id'];
  $stmt = $pdo->prepare("DELETE FROM equipment_undertaking WHERE borrower_id = ?");
  $stmt->execute([$id]);
  header("Location: undertaking-approval.php");
  exit;
}


// Fetch all undertakings that need approval
$sql = "SELECT * FROM equipment_undertaking WHERE status = 'Pending'";
$stmt = $pdo->query($sql);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all rejected undertakings
$sqlRejected = "SELECT * FROM equipment_undertaking WHERE status = 'Rejected'";
$stmtRejected = $pdo->query($sqlRejected);
$rejectedRequests = $stmtRejected->fetchAll(PDO::FETCH_ASSOC);


function displayRequestRow($row) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['borrower_id']) . "</td>";
    echo "<td>{$row['equipment_code']}</td>";
    echo "<td>{$row['equipment_name']}</td>";
    echo "<td>{$row['model']}</td>";
    echo "<td>{$row['serial_number']}</td>";
    echo "<td>{$row['condition_status']}</td>";
    echo "<td>{$row['recipient_name']}</td>";
    echo "<td>{$row['position']}</td>";
    echo "<td>{$row['office_location']}</td>";
    echo "<td>{$row['contact_info']}</td>";
    echo "<td>{$row['event_purpose']}</td>";
    echo "<td>" . date("F j, Y", strtotime($row['usage_start'])) . "</td>";
    echo "<td>" . date("F j, Y", strtotime($row['usage_end'])) . "</td>";
    echo "<td>{$row['date_submitted']}</td>";
    echo "<td>";
    echo "<form method='POST' style='display:inline-block;'>";
    echo "<input type='hidden' name='approval_id' value='{$row['borrower_id']}'>";
    echo "<button type='submit' name='approve' onclick=\"return confirm('Are you sure you want to approve this request?')\" style='background:#005f73;color:white;padding:5px 10px;border:none;border-radius:5px;'>Approve</button>";
    echo "</form>";

    echo "<form method='POST' style='display:inline-block; margin-left:5px;'>";
    echo "<input type='hidden' name='approval_id' value='{$row['borrower_id']}'>";
    echo "<button type='button' onclick=\"handleReject('{$row['borrower_id']}')\" style='background:#e63946;color:white;padding:5px 10px;border:none;border-radius:5px;'>Reject</button>";
    echo "</form>";
    echo "</td>";
    echo "</tr>";
}
?>

<style>
 .previous-section {
  margin-top: 50px; /* push down from above sections */
  color: #777; /* lighter text */
  background-color: #f9f9f9; /* very light background */
  border-radius: 8px;
  box-shadow: inset 0 0 10px #ddd; /* subtle inner shadow for depth */
  }

  /* Make table text and borders lighter */
  .previous-section table {
   color: #777;
  border-color: #ccc;
   }

   /* Table rows lighter */
   .previous-section table th,
   .previous-section table td {
    border-color: #ddd;
    opacity: 0.7; /* semi-transparent for a faded look */
    }

    /* Optional: make the whole section slightly smaller font */
    .previous-section table {
    font-size: 0.9em;
    }
    .section-title {
            background-color: #f1f1f1;
            padding: 8px 12px;
            font-weight: bold;
        }
  
  table {
  width: 100%;
  border-collapse: collapse;
  min-width: 1200px; /* Forces table to be wide for scroll */
}

th, td {
  border: 1px solid #ccc;
  padding: 8px 10px;
  text-align: left;
  font-size: 14px;
}

th {
  background-color: #f0f0f0;
}

.card {
  padding: 10px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

@media screen and (max-width: 768px) {
  .card table {
    font-size: 12px;
  }
}
</style>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Undertaking Approval</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    /* Paste your entire CSS from equipment-tracker.php here or keep linked */
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
  <div class="topbar">
    <h3>Undertaking Approval</h3>
  </div>

  <br>

  <div class="card">
  <h4>Pending Approvals</h4>

  <!-- Scroll wrapper -->
  <div style="overflow-x:auto;">
    <table>
      <thead>
        <tr>
          <th>Request Code</th>
          <th>Equipment Code</th>
          <th>Equipment Type</th>
          <th>Equipment Name</th>
          <th>Serial Number</th>
          <th>Equipment Condition</th>
          <th>Requester Name</th>
          <th>Position</th>
          <th>Principal Office Location</th>
          <th>Contact</th>
          <th>Purpose</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Request Submitted on</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
          if ($requests) {
            foreach ($requests as $row) {
              displayRequestRow($row);
            }
          } else {
            echo "<tr><td colspan='14'>No pending requests</td></tr>";
          }
        ?>
      </tbody>
    </table>
  </div> <!-- end of scroll wrapper -->
</div>

 <br>

<!---REJECTED COLUMN-->
<div class="card">
  <div class="previous-section">
    <div class="section-title">Rejected Contract</div>
      <div style="overflow-x:auto;">
        <table>
          <thead>
            <tr>
              <th>Request Code</th>
              <th>Equipment Code</th>
              <th>Equipment Type</th>
              <th>Equipment Name</th>
              <th>Serial Number</th>
              <th>Condition</th>
              <th>Requester</th>
              <th>Position</th>
              <th>Location</th>
              <th>Contact</th>
              <th>Purpose</th>
              <th>Start</th>
              <th>End</th>
              <th>Submitted</th>
              <th>Reason</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
              if ($rejectedRequests) {
                foreach ($rejectedRequests as $row) {
                  echo "<tr>";
                  echo "<td>{$row['borrower_id']}</td>";
                  echo "<td>{$row['equipment_code']}</td>";
                  echo "<td>{$row['equipment_name']}</td>";
                  echo "<td>{$row['model']}</td>";
                  echo "<td>{$row['serial_number']}</td>";
                  echo "<td>{$row['condition_status']}</td>";
                  echo "<td>{$row['recipient_name']}</td>";
                  echo "<td>{$row['position']}</td>";
                  echo "<td>{$row['office_location']}</td>";
                  echo "<td>{$row['contact_info']}</td>";
                  echo "<td>{$row['event_purpose']}</td>";
                  echo "<td>" . date("F j, Y", strtotime($row['usage_start'])) . "</td>";
                  echo "<td>" . date("F j, Y", strtotime($row['usage_end'])) . "</td>";
                  echo "<td>{$row['date_submitted']}</td>";
                  echo "<td>{$row['rejection_reason']}</td>";
                  echo "<td>";
                  echo "<form method='POST' style='display:inline-block;'>";
                  echo "<input type='hidden' name='approval_id' value='{$row['borrower_id']}'>";
                  echo "<button type='submit' name='reapprove' onclick=\"return confirm('Are you sure you want to approve this rejected request?')\" style='background:#2a9d8f;color:white;padding:5px 10px;border:none;border-radius:5px;'>Approve</button>";
                  echo "</form>";

                  echo "<form method='POST' style='display:inline-block; margin-left:5px;'>";
                  echo "<input type='hidden' name='approval_id' value='{$row['borrower_id']}'>";
                  echo "<button type='submit' name='delete' onclick=\"return confirm('Are you sure you want to delete this request permanently?')\" style='background:#6d0202;color:white;padding:5px 10px;border:none;border-radius:5px;'>Delete</button>";
                  echo "</form>";
                  echo "</td>";
                  echo "</tr>";
                }
                  } else {
                echo "<tr><td colspan='14'>No rejected requests</td></tr>";
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script>
function handleReject(id) {
  const reason = prompt("Please enter the reason for rejection:");
  if (reason) {
    const form = document.createElement("form");
    form.method = "POST";
    form.style.display = "none";

    const idInput = document.createElement("input");
    idInput.name = "approval_id";
    idInput.value = id;
    form.appendChild(idInput);

    const reasonInput = document.createElement("input");
    reasonInput.name = "rejection_reason";
    reasonInput.value = reason;
    form.appendChild(reasonInput);

    const actionInput = document.createElement("input");
    actionInput.name = "reject";
    actionInput.value = "1";
    form.appendChild(actionInput);

    document.body.appendChild(form);
    form.submit();
  }
}
</script>
</body>
</html>
