<?php
// equipment-tracker.php

include('db.php');

// Handle 'Done' action
if (isset($_POST['mark_done'])) {
    $id = $_POST['borrower_id'];
    $stmt = $pdo->prepare("UPDATE undertaking_approval SET returned = 'Yes' WHERE borrower_id = ?");
    $stmt->execute([$id]);
}


// Handle 'Delete' action
if (isset($_POST['delete_record'])) {
    $id = $_POST['borrower_id'];
    $stmt = $pdo->prepare("DELETE FROM undertaking_approval WHERE borrower_id = ?");
    $stmt->execute([$id]);
}

// Get today's date
$today = date('Y-m-d');

// Fetch data
$sql = "SELECT * FROM undertaking_approval";
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$expiring = [];
$ongoing = [];
$previous = [];

foreach ($rows as $row) {
    // your logic here
    $end = $row['usage_end'];
    $status = $row['returned'];

    if ($status === 'Yes') {
        $previous[] = $row;
    } elseif ($end == $today) {
        $expiring[] = $row;
    } elseif ($end > $today) {
        $ongoing[] = $row;
    } else {
        $previous[] = $row; // if already past due and not returned
    }
}


function displayRow($row, $withActions = false, $isPrevious = false) {
    echo "<tr>";
    echo "<td>{$row['equipment_code']}</td>";
    echo "<td>{$row['equipment_name']}</td>";
    echo "<td>{$row['recipient_name']}</td>";
    echo "<td>{$row['contact_info']}</td>";
    echo "<td>{$row['position']}</td>";
    echo "<td>{$row['office_location']}</td>";
    echo "<td>{$row['event_purpose']}</td>";
    echo "<td>" . date("F j, Y", strtotime($row['usage_start'])) . " - " . date("F j, Y", strtotime($row['usage_end'])) . "</td>";
    
    echo "<td>";
    

    if ($withActions) {
    echo "<form method='POST' style='display:inline-block;' onsubmit=\"return confirm('Are you sure this item has been returned?');\">";
    echo "<input type='hidden' name='borrower_id' value='{$row['borrower_id']}'>";
    echo "<button type='submit' name='mark_done' style='background:#005f73;color:white;padding:5px 10px;border:none;border-radius:5px;'>Done</button>";
    echo "</form>";

    // EDIT BUTTON
    echo "<button onclick=\"if(confirm('Are you sure you want to edit this?')) { openEditModal('{$row['borrower_id']}', '{$row['equipment_name']}', '{$row['recipient_name']}', '{$row['usage_start']}', '{$row['usage_end']}'); }\" style='margin-left:5px;background:#f4a261;color:white;padding:5px 10px;border:none;border-radius:5px;'>Edit</button>";
}

    if ($isPrevious) {
        echo "<form method='POST' style='display:inline-block; margin-left: 5px;' onsubmit=\"return confirm('Are you sure you want to delete this record?');\">";
        echo "<input type='hidden' name='borrower_id' value='{$row['borrower_id']}'>";
        echo "<button type='submit' name='delete_record' style='background:#e63946;color:white;padding:5px 10px;border:none;border-radius:5px;'>🗑</button>";
        echo "</form>";
    }

    echo "</td>";
    echo "</tr>";
}

?>

<style>
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  padding-top: 80px;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.5);
}
.modal-content {
  background-color: #fff;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 50%;
  border-radius: 10px;
}
.close {
  color: #aaa;
  float: right;
  font-size: 24px;
  font-weight: bold;
}
.close:hover,
.close:focus {
  color: black;
  cursor: pointer;
}
</style>


<!--EDIT FORM--->
<script>
function openEditModal(borrowerId, equipmentName, recipientName, usageStart, usageEnd) {
    document.getElementById('edit_borrower_id').value = borrowerId;
    document.getElementById('edit_equipment_name').value = equipmentName;
    document.getElementById('edit_recipient_name').value = recipientName;
    document.getElementById('edit_usage_start').value = usageStart;
    document.getElementById('edit_usage_end').value = usageEnd;
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>

<!DOCTYPE html>
<html lang="en"></html>
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Equipment Undertaking</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #1d3557;
            color: white;
        }
        .section-title {
            background-color: #f1f1f1;
            padding: 8px 12px;
            font-weight: bold;
        }

        /* Fade effect for Previous Contract section */
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

    </style>
</head>
<body>


  <?php
if (isset($_POST['update_record'])) {
    $id = $_POST['edit_borrower_id'];
    $equipment_name = $_POST['edit_equipment_name'];
    $recipient_name = $_POST['edit_recipient_name'];
    $usage_start = $_POST['edit_usage_start'];
    $usage_end = $_POST['edit_usage_end'];

    $stmt = $pdo->prepare("UPDATE undertaking_approval SET equipment_name = ?, recipient_name = ?, usage_start = ?, usage_end = ? WHERE borrower_id = ?");
    $stmt->execute([$equipment_name, $recipient_name, $usage_start, $usage_end, $id]);

    echo "<script>window.location.href='equipment-tracker.php';</script>";
}

?>


<?php include 'sidebar.php'; ?>


  <div class="main">
    <div class="topbar">
      <h3>Equipment Tracker</h3>
    </div>

      <br>
      
<!-- Expiring Contract Section -->
<div class="section-title">Expiring Contract</div>
<table>
    <tr>
        <th>Borrowed Item</th>
        <th>Equipment Name</th>
        <th>Borrower Name</th>
        <th>Contact</th>
        <th>Position</th>
        <th>Located</th>
        <th>Event Used</th>
        <th>Usage Period</th>
        <th>Action</th>
    </tr>
    <?php foreach ($expiring as $row) displayRow($row, true); ?>
</table>

<!-- Ongoing Contract Section -->
<div class="section-title">Ongoing Contract</div>
<table>
    <tr>
        <th>Borrowed Item</th>
        <th>Equipment Name</th>
        <th>Borrower Name</th>
        <th>Contact</th>
        <th>Position</th>
        <th>Located</th>
        <th>Event Used</th>
        <th>Usage Period</th>
        <th>Action</th>
    </tr>
    <?php foreach ($ongoing as $row) displayRow($row, true); ?>
</table>

<!-- Previous Contract Section -->
<div class="previous-section">
  <div class="section-title">Previous Contract</div>
  <table>
    <tr>
        <th>Borrowed Item</th>
        <th>Equipment Name</th>
        <th>Borrower Name</th>
        <th>Contact</th>
        <th>Position</th>
        <th>Located</th>
        <th>Event Used</th>
        <th>Usage Period</th>
        <th>Action</th>
    </tr>
    <?php foreach ($previous as $row) displayRow($row, false, true); ?>
  </table>
</div>


<!-- Edit Modal -->
<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); z-index:1000;">
  <div style="background:white; width:90%; max-width:500px; margin:100px auto; padding:20px; border-radius:10px; position:relative; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
    
    <!-- Close button -->
    <span onclick="closeEditModal()" style="position:absolute; top:10px; right:15px; font-size:20px; font-weight:bold; color:#888; cursor:pointer;">&times;</span>
    
    <h2 style="margin-bottom:20px; color:#1d3557;">Edit Equipment Data</h2>

    <form method="POST" onsubmit="return confirm('Are you sure you want to edit this?');">
      <input type="hidden" name="edit_borrower_id" id="edit_borrower_id" />

      <div style="margin-bottom:15px;">
        <label for="edit_equipment_name" style="font-weight:bold;">Equipment Name</label>
        <input type="text" name="edit_equipment_name" id="edit_equipment_name" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;" required />
      </div>

      <div style="margin-bottom:15px;">
        <label for="edit_recipient_name" style="font-weight:bold;">Borrower Name</label>
        <input type="text" name="edit_recipient_name" id="edit_recipient_name" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;" required />
      </div>

      <div style="margin-bottom:15px;">
        <label for="edit_usage_start" style="font-weight:bold;">Usage Start</label>
        <input type="date" name="edit_usage_start" id="edit_usage_start" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;" min="<?= date('Y-m-d') ?>" required />
      </div>

      <div style="margin-bottom:20px;">
        <label for="edit_usage_end" style="font-weight:bold;">Usage End</label>
        <input type="date" name="edit_usage_end" id="edit_usage_end" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;" min="<?= date('Y-m-d') ?>" required />
      </div>

      <div style="text-align:right;">
        <button type="button" onclick="closeEditModal()" style="background:#ccc; color:#333; padding:8px 12px; border:none; border-radius:5px; margin-right:10px;">Cancel</button>
        <button type="submit" name="update_record" style="background:#1d3557; color:white; padding:8px 14px; border:none; border-radius:5px;">Update</button>
      </div>
    </form>
  </div>
</div>


</body>
</html>
