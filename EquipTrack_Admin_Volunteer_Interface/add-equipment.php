
<?php
include 'db.php'; // database connection (PDO)
error_log("Form submitted: " . print_r($_POST, true));

session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Sanitize and get POST data
  $name = trim($_POST['name'] ?? '');
  $equipment_type = trim($_POST['equipment_type'] ?? '');
  $serial_number = trim($_POST['serial_number'] ?? '');
  $status = trim($_POST['status'] ?? '');
  $conditions = trim($_POST['conditions'] ?? '');
  $location = trim($_POST['location'] ?? '');
  $model = trim($_POST['model'] ?? '');

  // Basic validation
  $errors = [];

  if (empty($name)) $errors[] = "Name is required.";
  if (empty($equipment_type)) $errors[] = "Equipment type is required.";
  if (empty($serial_number)) $errors[] = "Serial number is required.";
  if (empty($status)) $errors[] = "Status is required.";
  if (empty($conditions)) $errors[] = "Condition is required.";
  if (empty($location)) $errors[] = "Location is required.";
  if (empty($model)) $errors[] = "Model is required.";

  if (empty($errors)) {
    try {
      $stmt = $pdo->prepare("INSERT INTO equipment_list 
        (name, equipment_type, model, serial_number, status, conditions, location) 
        VALUES 
        (:name, :equipment_type, :model, :serial_number, :status, :conditions, :location)");

      $stmt->execute([
        ':name' => $name,
        ':equipment_type' => $equipment_type,
        ':model' => $model,
        ':serial_number' => $serial_number,
        ':status' => $status,
        ':conditions' => $conditions,
        ':location' => $location,
      ]);

      $_SESSION['success_message'] = "Equipment successfully added.";
      //header("Location: equipment-list.php");
      // exit;
    } catch (PDOException $e) {
      $_SESSION['error_message'] = "Error saving equipment: " . $e->getMessage();
    }
  } else {
    $_SESSION['error_message'] = implode('<br>', $errors);
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Equipment</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
  .form-container {
    background: white;
    padding: 40px; /* increased padding */
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.12); /* slightly stronger shadow */
    max-width: 1100px; /* increased max width */
    margin: 30px auto;
  }

  .form-row {
    display: flex;
    gap: 30px; /* more spacing between fields */
    margin-bottom: 25px;
  }

  .form-group {
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .form-group label {
    margin-bottom: 10px;
    font-weight: 500;
    font-size: 1.05em; /* slightly bigger label */
  }

  .input-group {
    position: relative;
  }

  .input-group i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #777;
    pointer-events: none;
    font-size: 1.1em;
  }

  input, select, textarea {
    padding: 14px 14px 14px 45px; /* bigger padding and left space for icon */
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 1.05em;
    width: 100%;
    box-sizing: border-box;
  }

  textarea {
    resize: vertical;
    min-height: 100px;
  }

  #status {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23666' viewBox='0 0 24 24'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 40px;
  }

  .status-note, .description {
    margin-top: 6px;
    font-size: 0.9em;
    color: #666;
    font-style: italic;
  }

  .alert {
    padding: 14px 18px;
    margin-bottom: 25px;
    border-radius: 6px;
  }

  .alert-success {
    background-color: #d1fae5;
    color: #047857;
  }

  .alert-error {
    background-color: #fee2e2;
    color: #b91c1c;
  }

  .btn-row {
    display: flex;
    justify-content: flex-start;
    gap: 12px;
    margin-top: 30px;
  }

  button {
    padding: 10px 20px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }

  button:hover {
    background: #0056b3;
  }
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main">
  <div class="topbar"><h3>Add New Equipment</h3></div>

  <?php if (isset($_SESSION['success_message'])): ?>
  <div class="alert alert-success">
    <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
  </div>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
  <div class="alert alert-error">
    <?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
  </div>
<?php endif; ?>

  <div class="form-container">
    <form method="POST">

      <div class="form-row">
        <div class="form-group">
          <label for="name">Name</label>
          <div class="input-group">
            <i class="fas fa-tag"></i>
            <input id="name" name="name" type="text" required />
          </div>
          <div class="description">General name or label of the equipment (e.g., Laptop, Camera).</div>
        </div>

        <div class="form-group">
          <label for="equipment_type">Equipment Type</label>
          <div class="input-group">
            <i class="fas fa-cogs"></i>
            <input id="equipment_type" name="equipment_type" type="text" required />
          </div>
          <div class="description">Type or category of the equipment (e.g., Input Device, Audio Device).</div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="model">Model</label>
          <div class="input-group">
            <i class="fas fa-mobile-alt"></i>
            <input id="model" name="model" type="text" required />
          </div>
          <div class="description">Model name or number of the equipment.</div>
        </div>

        <div class="form-group">
          <label for="serial_number">Serial Number</label>
          <div class="input-group">
            <i class="fas fa-barcode"></i>
            <input id="serial_number" name="serial_number" type="text" required />
          </div>
          <div class="description">Unique serial number for tracking.</div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label for="status">Status</label>
          <div class="input-group">
            <i class="fas fa-info-circle"></i>
            <select id="status" name="status" required>
              <option value="Available">Available</option>
              <option value="Under Maintenance">Under Maintenance</option>
              <option value="Borrowed">Borrowed</option>
            </select>
          </div>
          <div class="description">Current usage status of the equipment.</div>
        </div>

        <div class="form-group">
          <label for="conditions">Condition</label>
          <div class="input-group">
            <i class="fas fa-tools"></i>
            <select id="conditions" name="conditions" required>
              <option value="Good(Working)">Good (Working)</option>
              <option value="Defect(Needs Repair)">Defect (Needs Repair)</option>
              <option value="Bad(Not Working)">Bad (Not Working)</option>
            </select>
          </div>
          <div class="description">Physical or operational condition of the item.</div>
        </div>

        <div class="form-group">
          <label for="location">Location</label>
          <div class="input-group">
            <i class="fas fa-map-marker-alt"></i>
            <input id="location" name="location" type="text" required />
          </div>
          <div class="description">Storage or usage location of the equipment.</div>
        </div>
      </div>

      <div class="btn-row">
        <button type="submit">Save</button>
        <button type="reset">Clear</button>
      </div>

    </form>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Equipment name suggestions
    const nameInput = document.getElementById('name');
    const nameSuggestions = document.getElementById('name-suggestions');
    const equipmentNames = <?php echo json_encode($equipment_names); ?>;
    
    // Auto-complete for name field
    nameInput.addEventListener('input', function() {
      const inputValue = this.value.toLowerCase();
      
      // Clear previous suggestions
      nameSuggestions.innerHTML = '';
      
      if (inputValue.length < 2) {
        nameSuggestions.style.display = 'none';
        return;
      }
      
      // Filter matching equipment names
      const matches = equipmentNames.filter(name => 
        name.toLowerCase().includes(inputValue)
      );
      
      if (matches.length > 0) {
        matches.forEach(match => {
          const div = document.createElement('div');
          div.className = 'suggestion-item';
          div.textContent = match;
          div.addEventListener('click', function() {
            nameInput.value = match;
            nameSuggestions.style.display = 'none';
          });
          nameSuggestions.appendChild(div);
        });
        nameSuggestions.style.display = 'block';
      } else {
        nameSuggestions.style.display = 'none';
      }
    });
    
    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
      if (e.target !== nameInput && e.target !== nameSuggestions) {
        nameSuggestions.style.display = 'none';
      }
    });
    
    // Auto-generate serial number suggestion based on name
    nameInput.addEventListener('change', function() {
      const serialInput = document.getElementById('serial_number');
      if (serialInput.value === '') {
        // Only suggest if field is empty
        const nameValue = this.value.trim();
        if (nameValue) {
          // Generate serial based on name + date (YYYY-MM-DD)
          const today = new Date();
          const dateStr = today.toISOString().split('T')[0];
          const namePart = nameValue.replace(/\s+/g, '-').toUpperCase().substring(0, 5);
          serialInput.value = `${namePart}-${dateStr}`;
        }
      }
    });
    
    // Update status note based on selected status
    const statusSelect = document.getElementById('status');
    const statusNote = document.getElementById('status-note');
    
    function updateStatusNote() {
      const selectedStatus = statusSelect.value;
      
      switch(selectedStatus) {
        case 'Available':
          statusNote.textContent = 'Equipment will be marked as available in the system.';
          break;
        case 'Borrowed':
          statusNote.textContent = 'The system will log this equipment as borrowed during creation. Additional borrower details can be added in the notes.';
          break;
        case 'Under Maintenance':
          statusNote.textContent = 'Equipment will be marked for maintenance. Please provide maintenance details in the notes section.';
          break;
      }
    }
    
    // Set initial status note
    updateStatusNote();
    
    // Update note when status changes
    statusSelect.addEventListener('change', updateStatusNote);
  });
</script>
</body>
</html>