<?php
include 'db.php';

$submit = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'recipient_name' => $_POST['recipient_name'],
        'contact_info' => $_POST['contact_info'],
        'position' => $_POST['position'],
        'office_location' => $_POST['office_location'],
        'event_purpose' => $_POST['event_purpose'],
        'equipment_name' => $_POST['equipment_name'],
        'model' => $_POST['model'],
        'serial_number' => $_POST['serial_number'],
        'usage_start' => $_POST['usage_start'],
        'usage_end' => $_POST['usage_end'],
        'condition_status' => 'Working',
        'date_submitted' => date('Y-m-d H:i:s'),
        'status' => 'Pending'
    ];

    $stmt = $pdo->prepare("INSERT INTO equipment_undertaking 
        (recipient_name, contact_info, position, office_location, event_purpose,
         equipment_name, model, serial_number,
         condition_status, usage_start, usage_end, date_submitted, status)
        VALUES 
        (:recipient_name, :contact_info, :position, :office_location, :event_purpose,
         :equipment_name, :model, :serial_number,
         :condition_status, :usage_start, :usage_end, :date_submitted, :status)");

    $stmt->execute($data);
    $submit = true;
}
?>

<?php
include 'db.php';

// Get Equipment Names based on Equipment Type
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_names') {
    $equipment_type = $_GET['equipment_type'] ?? '';
    $stmt = $pdo->prepare("SELECT DISTINCT name FROM equipment_list WHERE equipment_type = ? AND status = 'Available' AND conditions = 'Good(Working)'");
    $stmt->execute([$equipment_type]);  // <-- fixed here
    $names = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($names);
    exit; // stop further HTML output
}

// Get Models based on Equipment Name
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_models') {
    $equipment_name = $_GET['equipment_name'] ?? '';
    $stmt = $pdo->prepare("SELECT DISTINCT model FROM equipment_list WHERE name = ? AND status = 'Available' AND conditions = 'Good(Working)'");
    $stmt->execute([$equipment_name]);
    $models = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($models);
    exit; // stop further HTML output
}

if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_serial') {
    $model = $_GET['model'] ?? '';
    $stmt = $pdo->prepare("SELECT serial_number, conditions FROM equipment_list WHERE model = ? AND status = 'Available' LIMIT 1");
    $stmt->execute([$model]);
    $serial = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($serial);
    exit;
}
?>  

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Equipment Undertaking</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    <?php include 'admin-form-style.css'; ?>
  </style>
    <?php include 'sidebar.php'?>
</head>
<body>
  <div class="main">
    <div class="topbar">
      <h3>Equipment Undertaking</h3>
    </div>

    <div class="undertaking-container">
      <div class="undertaking-header">Borrowing Form</div>

      <div class="undertaking-buttons">
        <button>Add Borrowed Equipment</button>
        <button>Bring Out Permit</button>
        <button>Transfer Permit</button>
        <button>LED Wall Undertaking</button>
      </div>

      <form method="POST">
        <div class="form-grid">
          <?php
          $inputs = [
            ['name' => 'recipient_name', 'placeholder' => 'Name of the recipient representative', 'note' => 'Full name of the person receiving the equipment'],
            ['name' => 'contact_info', 'placeholder' => 'Contact Information', 'note' => 'Phone number or email of the recipient'],
            ['name' => 'position', 'placeholder' => 'Position/Designation in the organization', 'note' => 'Role of the recipient in the organization'],
            ['name' => 'office_location', 'placeholder' => 'Principal Office Location', 'note' => 'Main office address of the organization'],
            ['name' => 'event_purpose', 'placeholder' => 'Event/Purpose of Equipment Use', 'note' => 'Reason or event where the equipment will be used'],
          ];

          foreach ($inputs as $input) {
            echo "<div class='input-group'>
                    <input type='text' name='{$input['name']}' placeholder='{$input['placeholder']}' required />
                    <small>{$input['note']}</small>
                  </div>";
          }
          ?>
        </div>

        <div id="borrowForm" class="form-grid">
          <div class="input-group">
            <select name="equipment_type" id="equipment_type" required>
              <option value="" disabled selected>Select Equipment Type</option>
              <?php
              $stmt = $pdo->prepare("SELECT DISTINCT equipment_type FROM equipment_list WHERE status = 'Available' AND conditions = 'Good(Working)'");
              $stmt->execute();
              foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                  echo "<option value=\"{$row['equipment_type']}\">{$row['equipment_type']}</option>";
              }
              ?>
            </select>
            <small>DSLR Camera for professional shoots</small>
          </div>

          <div class="input-group">
              <select name="equipment_name" id="equipment_name" required>
                <option value="" disabled selected >Select Equipment Name</option>
              </select>
            <small>Equipment Name or details here</small>
          </div>

          <div class="input-group">
              <select name="model" id="model" required>
                <option value="" disabled selected>Select Model</option>
              </select>
            <small>Model description or details here</small>
          </div>

          <div class="input-group">
                <input type="text" name="serial_number" id="serial_number" readonly />
                <small>Unique serial number of the equipment (readonly)</small>
            <small>Equipment Code</small>
          </div>

          <div class="condition-section">
              <div class="condition-badge" id="condition_badge">Condition</div>
              <small class="description-text">Equipment Condition</small>
              <input type="hidden" name="condition_status" id="condition_status" />
          </div>
        </div>

        <div class="form-row">
          <div class="usage-period">
            <label for="usage_start">Usage Period:</label>
            <input type="date" id="usage_start" name="usage_start" required />
            <span class="usage-separator">to</span>
            <input type="date" id="usage_end" name="usage_end" required />
          </div>

        <div class="form-actions">
          <button type="submit" class="button-style">Submit</button>
          <button id="clear_all" type="reset" class="button-style">Clear All</button>
        </div>
      </form>
    </div>
  </div>


    <script>
      document.addEventListener("DOMContentLoaded", function () {
    const today = new Date().toISOString().split("T")[0];
    document.getElementById("usage_start").setAttribute("min", today);
    document.getElementById("usage_end").setAttribute("min", today);
  });
  document.getElementById("equipment_type").addEventListener("change", function () {
  const type = this.value;

  fetch("?ajax=get_names&equipment_type=" + encodeURIComponent(type))
    .then(response => response.json())
    .then(data => {
      const nameSelect = document.getElementById("equipment_name");
      nameSelect.innerHTML = '<option value="" disabled selected>Select Equipment Name</option>';

      data.forEach(item => {
        const option = document.createElement("option");
        option.value = item.name;
        option.textContent = item.name;
        nameSelect.appendChild(option);
      });

      // Clear model and serial when equipment type changes
      const modelSelect = document.getElementById("model");
      modelSelect.innerHTML = '<option value="" disabled selected>Select Model</option>';

      document.getElementById("serial_number").value = '';
      document.getElementById("condition_badge").textContent = 'Condition';
      document.getElementById("condition_status").value = '';
    });
});

document.getElementById("equipment_name").addEventListener("change", function () {
  const name = this.value;

  fetch("?ajax=get_models&equipment_name=" + encodeURIComponent(name))
    .then(response => response.json())
    .then(data => {
      const modelSelect = document.getElementById("model");
      modelSelect.innerHTML = '<option value="" disabled selected>Select Model</option>';

      data.forEach(model => {
        const option = document.createElement("option");
        option.value = model.model;
        option.textContent = model.model;
        modelSelect.appendChild(option);
      });

      // Clear serial when equipment name changes
      document.getElementById("serial_number").value = '';
      document.getElementById("condition_badge").textContent = 'Condition';
      document.getElementById("condition_status").value = '';
    });
});

document.getElementById("model").addEventListener("change", function () {
  const model = this.value;

  fetch(`?ajax=get_serial&model=${encodeURIComponent(model)}`)
    .then(response => response.json())
    .then(data => {
      const serialInput = document.querySelector('input[name="serial_number"]');
      if (serialInput) {
        serialInput.value = data.serial_number || '';
      }

      const conditionBadge = document.querySelector('.condition-badge');
      const conditionInput = document.querySelector('input[name="condition_status"]');
      if (conditionBadge && conditionInput) {
        conditionBadge.textContent = data.conditions || 'Unknown';
        conditionInput.value = data.conditions || '';
      }
    })
    .catch(error => {
      console.error('Error fetching serial and condition:', error);
    });
});

document.getElementById("clear_all").addEventListener("click", function () {
  // Reset equipment_type dropdown (optional)
  document.getElementById("equipment_type").value = "";

  // Reset equipment_name dropdown
  const nameSelect = document.getElementById("equipment_name");
  nameSelect.innerHTML = '<option value="" disabled selected>Select Equipment Name</option>';

  // Reset model dropdown
  const modelSelect = document.getElementById("model");
  modelSelect.innerHTML = '<option value="" disabled selected>Select Model</option>';

  // Reset serial number and condition
  document.getElementById("serial_number").value = '';
  document.getElementById("condition_badge").textContent = 'Condition';
  document.getElementById("condition_status").value = '';
});
  </script>
</body>
</html>
