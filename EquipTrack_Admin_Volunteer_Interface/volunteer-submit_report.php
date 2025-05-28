<?php
require 'db.php';
require 'login-user_verification.php'; // Start session for user ID (adjust as needed)

// === Handle full form submission (insert volunteer report) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['equipment_id'])) {
    // Assuming user ID is stored in session
    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        die('You must be logged in to submit a report.');
    }

    $checkType = $_POST['check_type'] ?? null;
    $conditions = $_POST['condition'] ?? [];
    $remarks = $_POST['remarks'] ?? [];

    if (!$checkType) {
        die('Please select a check type.');
    }
    if (empty($conditions)) {
        die('No equipment conditions submitted.');
    }

    $stmt = $pdo->prepare("INSERT INTO volunteer_submitted_report 
        (user_id, equipment_id, check_type, condition_status, remarks, submitted_at) 
        VALUES (:user_id, :equipment_id, :check_type, :condition_status, :remarks, NOW())");

    $pdo->beginTransaction();
    try {
        foreach ($conditions as $equipmentId => $conditionStatus) {
            $remark = $remarks[$equipmentId] ?? '';
            $stmt->execute([
                ':user_id' => $userId,
                ':equipment_id' => $equipmentId,
                ':check_type' => $checkType,
                ':condition_status' => $conditionStatus,
                ':remarks' => $remark,
            ]);
        }
        $pdo->commit();
        echo "<script>alert('Report submitted successfully!'); window.location.href=window.location.href;</script>";
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        die('Error submitting report: ' . $e->getMessage());
    }
}

// === Handle AJAX POST: Update equipment condition ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['equipment_id'])) {
    $id = $_POST['equipment_id'];
    $newCondition = $_POST['new_condition'];

    $query = "UPDATE equipment_list SET conditions = :condition WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['condition' => $newCondition, 'id' => $id]);

    echo json_encode(["status" => "success", "message" => "Condition updated"]);
    exit;
}

// === Handle AJAX GET: Fetch locations based on equipment type ===
if (isset($_GET['fetch_locations']) && isset($_GET['equipment_type'])) {
    $type = $_GET['equipment_type'];
    $stmt = $pdo->prepare("SELECT DISTINCT location FROM equipment_list WHERE equipment_type = :type");
    $stmt->execute(['type' => $type]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
    exit;
}

// === Handle AJAX GET: Fetch equipment list ===
if (isset($_GET['ajax']) && $_GET['ajax'] === 'true') {
    $selectedType = $_GET['equipment_type'] ?? '';
    $selectedLocation = $_GET['location'] ?? '';

    $query = "SELECT id, name, model, equipment_type, serial_number, quantity, conditions 
              FROM equipment_list 
              WHERE equipment_type = :type AND location = :location";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['type' => $selectedType, 'location' => $selectedLocation]);
    echo json_encode($stmt->fetchAll());
    exit;
}



// Initial page load - filter equipment
$selectedType = $_GET['equipment_type'] ?? '';
$selectedLocation = $_GET['location'] ?? '';

$equipments = [];
if ($selectedType && $selectedLocation) {
    $stmt = $pdo->prepare("SELECT id, name, model, equipment_type, serial_number, quantity, conditions 
                           FROM equipment_list 
                           WHERE equipment_type = :type AND location = :location");
    $stmt->execute(['type' => $selectedType, 'location' => $selectedLocation]);
    $equipments = $stmt->fetchAll();
}

// Fetch dropdown values
$equipmentTypes = $pdo->query("SELECT DISTINCT equipment_type FROM equipment_list")->fetchAll(PDO::FETCH_COLUMN);
if ($selectedType) {
    $stmt = $pdo->prepare("SELECT DISTINCT location FROM equipment_list WHERE equipment_type = :type");
    $stmt->execute(['type' => $selectedType]);
    $locations = $stmt->fetchAll(PDO::FETCH_COLUMN);
} else {
    $locations = $pdo->query("SELECT DISTINCT location FROM equipment_list")->fetchAll(PDO::FETCH_COLUMN);
}
?>
<style>
  button[type="submit"] {
  background-color: #1b4332; /* your requested deep green */
  color: white;
  border: none;
  padding: 12px 24px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

button[type="submit"]:hover {
  background-color: #14532d; /* slightly darker green on hover */
}

button[type="submit"]:focus {
  outline: 3px solid #74c69d; /* lighter green outline for accessibility */
  outline-offset: 2px;
}

button[type="submit"] i.fas {
  font-size: 18px;
}
</style>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Volunteer Submit Report</title>
  <link rel="stylesheet" href="volunteer-styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    .hidden { display: none; }
  </style>
</head>
<body>

<?php include 'volunteer-sidebar.php'; ?>

<div class="main">
  <div class="topbar">
    <h3>Submit Equipment Report</h3>
    <div><i class="fas fa-user-circle"></i> Volunteer Profile</div>
  </div>

  <div id="submit_report" class="section">
    <h2>Submit Equipment Condition Report</h2>
    <form id="submit_report_form" class="section" method="POST" action="">

        <!-- Check Type -->
        <label>Select Check Type:</label>
        <div class="radio-group">
          <input type="radio" id="first_check" name="check_type" value="First Check" required />
          <label for="first_check">First Check</label>

          <input type="radio" id="final_check" name="check_type" value="Final Check" required />
          <label for="final_check">Final Check</label>
        </div>

        <!-- Filters -->
        <label for="equipment_type">Equipment Type:</label>
        <select id="equipment_type" name="equipment_type">
          <option value="">-- Select Type --</option>
          <?php foreach ($equipmentTypes as $type): ?>
            <option value="<?= htmlspecialchars($type) ?>" <?= ($selectedType == $type ? 'selected' : '') ?>>
              <?= htmlspecialchars($type) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label for="location">Location:</label>
        <select id="location" name="location">
          <option value="">-- Select Location --</option>
          <?php foreach ($locations as $loc): ?>
            <option value="<?= htmlspecialchars($loc) ?>" <?= ($selectedLocation == $loc ? 'selected' : '') ?>>
              <?= htmlspecialchars($loc) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <!-- Equipment Table -->
        <?php if (!empty($equipments)): ?>
          <table class="table-container">
            <thead>
              <tr>
                <th>Equipment ID</th>
                <th>Item Name</th>
                <th>Model</th>
                <th>Serial Number</th>
                <th>Quantity</th>
                <th>Condition</th>
                <th>Remarks</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($equipments as $equipment): ?>
                <tr>
                  <td><?= htmlspecialchars($equipment['id']) ?></td>
                  <td><?= htmlspecialchars($equipment['name']) ?></td>
                  <td><?= htmlspecialchars($equipment['model']) ?></td>
                  <td><?= htmlspecialchars($equipment['serial_number']) ?></td>
                  <td><?= htmlspecialchars($equipment['quantity']) ?></td>
                  <td>
                    <?php $eid = $equipment['id']; $curr = $equipment['conditions']; ?>
                    <label><input type="radio" class="condition" name="condition[<?= $eid ?>]" value="Good(Working)" data-id="<?= $eid ?>" <?= $curr === 'Good(Working)' ? 'checked' : '' ?> /> Good (Working)</label><br />
                    <label><input type="radio" class="condition" name="condition[<?= $eid ?>]" value="Defect(Needs Repair)" data-id="<?= $eid ?>" <?= $curr === 'Defect(Needs Repair)' ? 'checked' : '' ?> /> Defect (Needs Repair)</label><br />
                    <label><input type="radio" class="condition" name="condition[<?= $eid ?>]" value="Bad(Not Working)" data-id="<?= $eid ?>" <?= $curr === 'Bad(Not Working)' ? 'checked' : '' ?> /> Bad (Not Working)</label>
                  </td>
                  <td>
                    <textarea id="remarks-<?= $eid ?>" name="remarks[<?= $eid ?>]" placeholder="Enter remarks..."></textarea>
                    <button class="incident-btn hidden" id="incident-btn-<?= $eid ?>">Report Incident</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p style="margin-top: 20px; color: red;">Please select an Equipment Type and Location to view equipment.</p>
        <?php endif; ?>
          <br>
        <button type="submit"><i class="fas fa-paper-plane"></i> Submit Report</button>
    </form>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // === Condition radios ===
  document.querySelectorAll('.condition').forEach(radio => {
    const id = radio.dataset.id;

    if (radio.checked && (radio.value.includes('Defect') || radio.value.includes('Bad'))) {
      document.getElementById(`incident-btn-${id}`).classList.remove('hidden');
    }

    radio.addEventListener('change', function () {
      const newCondition = this.value;
      const equipmentId = this.dataset.id;
      const incidentBtn = document.getElementById(`incident-btn-${equipmentId}`);

      if (newCondition.includes('Defect') || newCondition.includes('Bad')) {
        incidentBtn.classList.remove('hidden');
      } else {
        incidentBtn.classList.add('hidden');
      }

      fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `equipment_id=${equipmentId}&new_condition=${encodeURIComponent(newCondition)}`
      })
      .then(res => res.json())
      .then(data => console.log('Updated:', data))
      .catch(err => console.error('Update error:', err));
    });
  });

  // === Dropdown filters ===
  const equipmentTypeSelect = document.getElementById('equipment_type');
  const locationSelect = document.getElementById('location');

  equipmentTypeSelect.addEventListener('change', function () {
    const type = this.value;
    
    // Fetch dynamic location list
    if (!type) {
      locationSelect.innerHTML = '<option value="">-- Select Location --</option>';
      return;
    }

    fetch(`?fetch_locations=true&equipment_type=${encodeURIComponent(type)}`)
      .then(res => res.json())
      .then(locations => {
        locationSelect.innerHTML = '<option value="">-- Select Location --</option>';
        locations.forEach(loc => {
          const option = document.createElement('option');
          option.value = loc;
          option.textContent = loc;
          locationSelect.appendChild(option);
        });
      })
      .catch(err => console.error('Failed to fetch locations:', err));
  });

  locationSelect.addEventListener('change', function () {
    const location = this.value;
    const type = equipmentTypeSelect.value;
    if (type && location) {
      window.location.href = `?equipment_type=${encodeURIComponent(type)}&location=${encodeURIComponent(location)}`;
    }
  });
  const form = document.getElementById('submit_report_form');

form.addEventListener('submit', function (e) {
  // Check if a check_type is selected
  const checkTypeSelected = form.querySelector('input[name="check_type"]:checked');
  if (!checkTypeSelected) {
    alert('Please select a Check Type.');
    e.preventDefault();
    return;
  }

  // Check if at least one condition radio is selected
  // Since conditions are an array keyed by equipment id, 
  // check if at least one radio in the conditions group is checked
  const conditionRadios = form.querySelectorAll('input.condition');
  let conditionSelected = false;
  for (const radio of conditionRadios) {
    if (radio.checked) {
      conditionSelected = true;
      break;
    }
  }
  if (!conditionSelected) {
    alert('Please select condition for at least one equipment.');
    e.preventDefault();
    return;
  }

  // (Optional) Add confirmation before submit
  if (!confirm('Are you sure you want to submit this report?')) {
    e.preventDefault();
  }
  });

});

</script>
</body>
</html>
