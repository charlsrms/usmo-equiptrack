<?php
include 'db.php';
session_start();

// Check if user is logged in and is admin/volunteer
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// Display success/error messages
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']);
}

// Handle search and filter
$search = $_GET['search'] ?? '';
$filterLocation = $_GET['location'] ?? '';
$filterStatus = $_GET['status'] ?? '';
$filterCondition = $_GET['conditions'] ?? '';

// Build query with filters
// Build query with filters
$query = "SELECT * FROM equipment_list WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (name LIKE :search OR serial_number LIKE :search)";
}
if (!empty($filterLocation)) {
    $query .= " AND location = :location";
}
if (!empty($filterStatus)) {
    $query .= " AND status = :status";
}
if (!empty($filterCondition)) {
    $query .= " AND conditions = :condition";
}

// Add ORDER BY at the end
$query .= " ORDER BY id DESC";

// Prepare and execute query
$stmt = $pdo->prepare($query);

if (!empty($search)) {
    $searchParam = "%$search%";
    $stmt->bindParam(':search', $searchParam);
}
if (!empty($filterLocation)) {
    $stmt->bindParam(':location', $filterLocation);
}
if (!empty($filterStatus)) {
    $stmt->bindParam(':status', $filterStatus);
}
if (!empty($filterCondition)) {
    $stmt->bindParam(':condition', $filterCondition);
}

$stmt->execute();
$equipment = $stmt->fetchAll();

// Get unique values for filter dropdowns
$locationsQuery = $pdo->query("SELECT DISTINCT location FROM equipment_list WHERE location IS NOT NULL ORDER BY location");
$locations = $locationsQuery->fetchAll(PDO::FETCH_COLUMN);

$conditionsQuery = $pdo->query("SELECT DISTINCT conditions FROM equipment_list WHERE conditions IS NOT NULL ORDER BY conditions");
$conditions = $conditionsQuery->fetchAll(PDO::FETCH_COLUMN);

// Get condition counts
$conditionCountsQuery = $pdo->query("
  SELECT 
    conditions,
    COUNT(*) as count
  FROM equipment_list
  WHERE conditions IS NOT NULL
  GROUP BY conditions
  ORDER BY conditions
");
$conditionCounts = $conditionCountsQuery->fetchAll(PDO::FETCH_ASSOC);

// Format condition data
$conditionData = [];
foreach ($conditionCounts as $condition) {
  $conditionData[$condition['conditions']] = $condition['count'];
}

// Define condition colors
$conditionColors = [
  'Good(Working)' => '#8BC34A',       // Green
  'Bad(Not Working)' => '#F44336',    // Red
  'Defect(Needs Repair)' => '#FF9800' // Orange
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Equipment List</title>
  <link rel="stylesheet" href="style.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <script>
    // JavaScript for intelligence-enhanced filtering
    document.addEventListener('DOMContentLoaded', function() {
      // Enable real-time filtering
      const filterInputs = document.querySelectorAll('.filter-control');
      filterInputs.forEach(input => {
        input.addEventListener('change', function() {
          document.getElementById('filter-form').submit();
        });
      });
      
      // Enable instant search as you type with debounce
      let typingTimer;
      const searchInput = document.getElementById('search-input');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          clearTimeout(typingTimer);
          typingTimer = setTimeout(function() {
            document.getElementById('filter-form').submit();
          }, 300); // Wait 300ms after user stops typing to search (reduced from 500ms for faster response)
        });
        
        // Toggle clear button visibility
        searchInput.addEventListener('input', function() {
          const clearButton = document.getElementById('clear-search');
          if (this.value.length > 0) {
            clearButton.style.display = 'block';
          } else {
            clearButton.style.display = 'none';
          }
        });
        
        // Initialize clear button visibility on page load
        const clearButton = document.getElementById('clear-search');
        if (searchInput.value.length > 0) {
          clearButton.style.display = 'block';
        } else {
          clearButton.style.display = 'none';
        }
      }
      
      // Add clear search functionality
      const clearSearch = document.getElementById('clear-search');
      if (clearSearch) {
        clearSearch.addEventListener('click', function(e) {
          e.preventDefault();
          searchInput.value = '';
          clearSearch.style.display = 'none';
          document.getElementById('filter-form').submit();
        });
      }
      
      // Enable condition filter cards
      const conditionCards = document.querySelectorAll('.condition-card');
      conditionCards.forEach(card => {
        card.addEventListener('click', function() {
          const conditionValue = this.getAttribute('data-condition');
          document.getElementById('condition-filter').value = conditionValue;
          document.getElementById('filter-form').submit();
        });
      });

      // Edit Equipment Popup Functionality
      const editButtons = document.querySelectorAll('.action-btn.edit');
      const popup = document.getElementById('editEquipmentPopup');
      const closeButtons = document.querySelectorAll('.close-popup');
      
      // Open popup when edit button is clicked
      editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();
          
          // Get equipment data from the row
          const row = this.closest('tr');
          const id = row.querySelector('td:nth-child(1)').textContent;
          const name = row.querySelector('td:nth-child(2)').textContent;
          const equipmentType = row.querySelector('td:nth-child(3)').textContent;
          const model = row.querySelector('td:nth-child(4)').textContent;
          const serialNumber = row.querySelector('td:nth-child(5)').textContent;
          const status = row.querySelector('.status-badge').textContent.trim();
          const condition = row.querySelector('.condition-badge') ? 
            row.querySelector('.condition-badge').textContent.trim() : '';
          const location = row.querySelector('td:nth-child(8)').textContent;
          
          // Fill the form
          document.getElementById('editEquipmentId').value = id;
          document.getElementById('editName').value = name;
          document.getElementById('editEquipmentType').value = equipmentType;
          document.getElementById('editModel').value = model;
          document.getElementById('editSerialNumber').value = serialNumber;
          document.getElementById('editStatus').value = status;
          document.getElementById('editCondition').value = condition;
          document.getElementById('editLocation').value = location;
          
          // Show popup
          popup.style.display = 'flex';
        });
      });
      
      // Close popup when close button or overlay is clicked
      closeButtons.forEach(button => {
        button.addEventListener('click', function() {
          popup.style.display = 'none';
        });
      });
      
      // Close popup when clicking outside content
      popup.addEventListener('click', function(e) {
        if (e.target === popup) {
          popup.style.display = 'none';
        }
      });
      
      // Handle form submission
      document.getElementById('editEquipmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Submit form via AJAX
        const formData = new FormData(this);
        
        fetch(this.action, {
          method: 'POST',
          body: formData
        })
        .then(response => {
          if (response.ok) {
            // Reload page to see changes
            window.location.reload();
          } else {
            alert('Error updating equipment');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error updating equipment');
        });
      });
    });

    function confirmDelete(equipmentName) {
        return confirm(`Are you sure you want to delete "${equipmentName}"?\nThis action cannot be undone.`);
    }
  </script>
</head>
<body>

<?php include 'sidebar.php'; ?>


<div class="main">
  <div class="topbar">
    <h3>Equipment List</h3>
  </div>

  <form id="filter-form" method="GET" action="equipment-list.php" class="search-bar">
      <div class="search-input-container">
        <input type="text" id="search-input" name="search" placeholder="Search equipment..." class="search-input" value="<?php echo htmlspecialchars($search); ?>" autocomplete="off">
        <button type="button" id="clear-search" class="clear-search">×</button>
      </div>
      
      <!-- Hidden condition filter input to maintain functionality but without the dropdown UI -->
      <input type="hidden" name="conditions" id="condition-filter" value="<?php echo htmlspecialchars($filterCondition); ?>">
    </form>
  <div id="search-results" class="search-results"></div>
  
  <!-- Condition Filter Cards -->
  <div class="condition-cards">
    <div class="condition-card<?php echo $filterCondition === 'Good(Working)' ? ' active' : ''; ?>" data-condition="Good(Working)">
      <div class="condition-icon" style="background-color: #8BC34A">
        <i class="fas fa-check"></i>
      </div>
      <div class="condition-info">
        <div class="condition-name">Good</div>
        <div class="condition-count"><?php echo $conditionData['Good(Working)'] ?? 0; ?> items</div>
      </div>
    </div>
    
    <div class="condition-card<?php echo $filterCondition === 'Bad(Not Working)' ? ' active' : ''; ?>" data-condition="Bad(Not Working)">
      <div class="condition-icon" style="background-color: #F44336">
        <i class="fas fa-times"></i>
      </div>
      <div class="condition-info">
        <div class="condition-name">Bad</div>
        <div class="condition-count"><?php echo $conditionData['Bad(Not Working)'] ?? 0; ?> items</div>
      </div>
    </div>
    
    <div class="condition-card<?php echo $filterCondition === 'Defect(Needs Repair)' ? ' active' : ''; ?>" data-condition="Defect(Needs Repair)">
      <div class="condition-icon" style="background-color: #FF9800">
        <i class="fas fa-wrench"></i>
      </div>
      <div class="condition-info">
        <div class="condition-name">Defective</div>
        <div class="condition-count"><?php echo $conditionData['Defect(Needs Repair)'] ?? 0; ?> items</div>
      </div>
    </div>
    
    <div class="condition-card<?php echo empty($filterCondition) ? ' active' : ''; ?>" data-condition="">
      <div class="condition-icon" style="background-color: #757575">
        <i class="fas fa-list"></i>
      </div>
      <div class="condition-info">
        <div class="condition-name">All</div>
        <div class="condition-count"><?php echo array_sum($conditionData); ?> items</div>
      </div>
    </div>
  </div>
  
  <div class="equipment-list">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Equipment Type</th>
          <th>Equipment Model</th>
          <th>Serial Number</th>
          <th>Status</th>
          <th>Condition</th>
          <th>Location</th>
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <th>Actions</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (count($equipment) > 0): ?>
          <?php foreach ($equipment as $item): ?>
            <tr>
              <td><?php echo htmlspecialchars($item['id']); ?></td>
              <td><?php echo htmlspecialchars($item['name']); ?></td>
              <td><?php echo htmlspecialchars($item['equipment_type']); ?></td>
              <td><?php echo htmlspecialchars($item['model']); ?></td>
              <td><?php echo htmlspecialchars($item['serial_number']); ?></td>
              <td>
                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $item['status'])); ?>">
                  <?php echo htmlspecialchars($item['status']); ?>
                </span>
              </td>
              <td>
                <?php if (!empty($item['conditions'])): ?>
                  <span class="condition-badge condition-<?php echo strtolower(str_replace(['(', ')', ' '], ['', '', '-'], $item['conditions'])); ?>">
                    <?php echo htmlspecialchars($item['conditions']); ?>
                  </span>
                <?php else: ?>
                  <span class="not-specified">Not specified</span>
                <?php endif; ?>
              </td>
              <td><?php echo htmlspecialchars($item['location']); ?></td>
              <?php if ($_SESSION['role'] === 'admin'): ?>
                <td>
                  <a href="#" class="action-btn edit">
                    <i class="fas fa-edit"></i>
                  </a>
                </td>
              <?php endif; ?>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="<?php echo $_SESSION['role'] === 'admin' ? '7' : '6'; ?>" class="no-results">
              No equipment found matching your criteria
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

<!-- Edit Equipment Popup -->
<div id="editEquipmentPopup" class="popup-overlay">
  <div class="popup-content">
    <div class="popup-header">
      <h4>Edit Equipment</h4>
      <button class="close-popup">&times;</button>
    </div>
    <form id="editEquipmentForm" method="POST" action="update-equipment.php">
      <input type="hidden" name="id" id="editEquipmentId">
      <div class="form-group">
        <label for="editName">Name:</label>
        <input type="text" id="editName" name="name" class="form-control" required>
      </div>
      <div class="form-group">
  <label for="editEquipmentType">Equipment Type:</label>
  <input type="text" id="editEquipmentType" name="equipment_type" class="form-control">
</div>

<div class="form-group">
  <label for="editModel">Model:</label>
  <input type="text" id="editModel" name="model" class="form-control">
</div>
      <div class="form-group">
        <label for="editSerialNumber">Serial Number:</label>
        <input type="text" id="editSerialNumber" name="serial_number" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="editStatus">Status:</label>
        <select id="editStatus" name="status" class="form-control" required>
          <option value="Available">Available</option>
          <option value="Borrowed">Borrowed</option>
          <option value="Under Maintenance">Under Maintenance</option>
        </select>
      </div>
      <div class="form-group">
        <label for="editCondition">Condition:</label>
        <select id="editCondition" name="conditions" class="form-control" required>
          <option value="Good(Working)">Good (Working)</option>
          <option value="Bad(Not Working)">Bad (Not Working)</option>
          <option value="Defect(Needs Repair)">Defect (Needs Repair)</option>
        </select>
      </div>
      <div class="form-group">
        <label for="editLocation">Location:</label>
        <input type="text" id="editLocation" name="location" class="form-control">
      </div>
      <div class="popup-footer">
        <button type="submit" class="btn primary-btn">Save Changes</button>
        <button type="button" class="btn secondary-btn close-popup">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Add AJAX search functionality -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');

    // Function to fetch search suggestions
    function fetchSearchSuggestions(query) {
      if (query.length < 5) {
        searchResults.innerHTML = '';
        searchResults.style.display = 'none';
        return;
      }

      // Create a new XMLHttpRequest
      const xhr = new XMLHttpRequest();
      xhr.open('GET', `search-suggestions.php?query=${encodeURIComponent(query)}`, true);

      xhr.onload = function() {
        if (this.status === 200) {
          try {
            const suggestions = JSON.parse(this.responseText);

            if (suggestions.length > 0) {
              let html = '<ul>';
              suggestions.forEach(item => {
                html += `<li data-id="${item.id}">${item.name} (${item.serial_number})</li>`;
              });
              html += '</ul>';

              searchResults.innerHTML = html;
              searchResults.style.display = 'block';

              // Add click event to suggestions
              const suggestionItems = searchResults.querySelectorAll('li');
              suggestionItems.forEach(item => {
                item.addEventListener('click', function() {
                  searchInput.value = this.textContent;
                  searchResults.style.display = 'none';
                  document.getElementById('filter-form').submit();
                });
              });
            } else {
              searchResults.innerHTML = '';
              searchResults.style.display = 'none';
            }
          } catch (e) {
            console.error('Error parsing JSON:', e);
          }
        }
      };

      xhr.send();
    }

    // Add event listener to search input
    if (searchInput) {
      searchInput.addEventListener('input', function() {
        fetchSearchSuggestions(this.value);
      });

      // Hide search results when clicking outside
      document.addEventListener('click', function(e) {
        if (e.target !== searchInput && e.target !== searchResults) {
          searchResults.style.display = 'none';
        }
      });
    }
  });
</script>


<style>
  .equipment-list {
    margin-top: 20px;
    overflow-x: auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }
  
  .status-badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: 500;
  }
  
  .status-available {
    background-color: #d1fae5;
    color: #047857;
  }
  
  .status-borrowed {
    background-color: #dbeafe;
    color: #1e40af;
  }
  
  .status-under-maintenance {
    background-color: #fee2e2;
    color: #b91c1c;
  }
  
  .filter-control {
    padding: 8px 14px;
    border: 1px solid #ccc;
    border-radius: 20px;
    font-size: 14px;
  }
  
  .action-btn {
    display: inline-block;
    padding: 5px 10px;
    margin-right: 5px;
    color: #fff;
    border-radius: 5px;
    text-decoration: none;
  }
  
  .action-btn.edit {
    background-color: #3b82f6;
  }
  
  .action-btn.delete {
    background-color: #ef4444;
  }
  
  .action-btn.checkin {
    background-color: #10b981;
  }
  
  .no-results {
    text-align: center;
    padding: 20px;
    color: #6b7280;
  }
  
  .action-buttons {
    margin-top: 20px;
    display: flex;
    gap: 10px;
  }
  
  .btn {
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 14px;
  }
  
  .primary-btn {
    background-color: #005baa;
    color: white;
  }
  
  .secondary-btn {
    background-color: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
  }
  
  /* Condition Cards */
  .condition-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 15px;
    margin: 20px 0;
  }
  
  .condition-card {
    display: flex;
    align-items: center;
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 2px solid transparent;
  }
  
  .condition-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
  }
  
  .condition-card.active {
    border-color: #005baa;
    box-shadow: 0 4px 10px rgba(0,91,170,0.3);
  }
  
  .condition-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    color: white;
    font-size: 18px;
  }
  
  .condition-info {
    flex: 1;
  }
  
  .condition-name {
    font-weight: 600;
    font-size: 16px;
    color: #374151;
  }
  
  .condition-count {
    font-size: 13px;
    color: #6b7280;
    margin-top: 2px;
  }
  
  /* Condition badges */
  .condition-badge {
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 0.85em;
    font-weight: 500;
    color: white;
  }
  
  .condition-goodworking {
    background-color: #8BC34A;
  }
  
  .condition-badnot-working {
    background-color: #F44336;
  }
  
  .condition-defectneeds-repair {
    background-color: #FF9800;
  }
  
  .not-specified {
    color: #9ca3af;
    font-style: italic;
    font-size: 0.85em;
  }
  
  
  /* Enhanced search styles */
  .search-input-container {
    position: relative;
    display: flex;
    align-items: center;
    width: 450px; /* Increased from 300px to 450px */
  }
  
  .search-input {
    padding: 8px 35px 8px 14px;
    border: 1px solid #ccc;
    border-radius: 10px;
    font-size: 14px;
    width: 100%;
    height: 48px; /* Increased from 38px to 42px for taller input */
    font-size: 16px; /* Increased font size for better readability */
    margin-top: 20px;
  }
  
  .clear-search {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: #d1d5db;
    width: 20px; /* Slightly larger clear button */
    height: 20px; /* Slightly larger clear button */
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: bold;
    color: #4b5563;
    border: none;
    cursor: pointer;
    display: none; /* Hidden by default */
    line-height: 0;
    padding: 0;
  }
  
  .search-results {
    display: none;
    position: absolute;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    width: 450px; /* Match the width of the search input container */
    margin-top: 5px;
    max-height: 300px;
    overflow-y: auto;
  }
  
  .search-results ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .search-results li {
    padding: 12px 16px; /* Slightly larger padding for better readability */
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    font-size: 15px; /* Slightly larger font for better readability */
  }
  
  .search-results li:hover {
    background-color: #f9fafb;
  }
  
  .search-results li:last-child {
    border-bottom: none;
  }
  
  /* Popup Styles */
  .popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }
  
  .popup-content {
    background-color: white;
    border-radius: 8px;
    width: 500px;
    max-width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  }
  
  .popup-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #e5e7eb;
  }
  
  .popup-header h4 {
    margin: 0;
    font-size: 1.2em;
    color: #374151;
  }
  
  .close-popup {
    background: none;
    border: none;
    font-size: 1.5em;
    cursor: pointer;
    color: #6b7280;
  }
  
  .close-popup:hover {
    color: #374151;
  }
  
  .form-group {
    margin-bottom: 15px;
    padding: 0 20px;
  }
  
  .form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #374151;
  }
  
  .form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 14px;
  }
  
  .popup-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 15px 20px;
    border-top: 1px solid #e5e7eb;
  }
  
  .secondary-btn {
    background-color: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
  }

  /* Alert styles */
  .alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    max-width: 300px;
    animation: fadeIn 0.5s, fadeOut 0.5s 2.5s forwards;
  }

  .alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
  }

  .alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
  }
  
  /* Responsive adjustments for search bar */
  @media (max-width: 768px) {
    .search-input-container {
      width: 100%;
      max-width: 450px;
    }
    
    .search-results {
      width: 100%;
      max-width: 450px;
    }
    
    .search-bar {
      flex-direction: column;
      align-items: flex-start;
      gap: 10px;
    }
    
    .filter-control, .search-button {
      width: 100%;
      max-width: 450px;
    }
  }
</style>
</body>
</html>