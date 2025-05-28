<?php
include 'db.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: index.php');
  exit;
}

// Get equipment stats
$stats_query = $pdo->query("
  SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'Available' THEN 1 ELSE 0 END) as available,
    SUM(CASE WHEN status = 'Borrowed' THEN 1 ELSE 0 END) as borrowed,
    SUM(CASE WHEN conditions = 'Good(Working)' THEN 1 ELSE 0 END) as good,
    SUM(CASE WHEN conditions = 'Bad(Not Working)' THEN 1 ELSE 0 END) as bad,
    SUM(CASE WHEN conditions = 'Defect(Needs Repair)' THEN 1 ELSE 0 END) as defect
  FROM equipment_list
");
$stats = $stats_query->fetch(PDO::FETCH_ASSOC);

// Get volunteer count
$volunteer_query = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'volunteer' AND disabled_at IS NULL");
$volunteer_count = $volunteer_query->fetch(PDO::FETCH_ASSOC)['count'];

// Get recent activities
$activities_query = $pdo->query("
  SELECT 
    a.id, 
    a.activity_type, 
    a.description, 
    a.created_at, 
    e.name as equipment_name,
    e.id as equipment_id,
    u.name as user_name
  FROM equipment_activities a
  JOIN equipment_list e ON a.equipment_id = e.id
  JOIN users u ON a.user_id = u.user_id
  ORDER BY a.created_at DESC
  LIMIT 10
");
$recent_activities = $activities_query->fetchAll(PDO::FETCH_ASSOC);

// Handle condition filter if clicked
$filter_condition = $_GET['condition'] ?? '';
if (!empty($filter_condition)) {
  $filtered_query = $pdo->prepare("
    SELECT * FROM equipment_list 
    WHERE conditions = :condition
    ORDER BY name
    LIMIT 6
  ");
  $filtered_query->execute(['condition' => $filter_condition]);
  $filtered_equipment = $filtered_query->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
  <style>
    /* Add your existing styles here */
    
    /* Dashboard Cards Styling */
    .dashboard-cards {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      gap: 20px;
      margin-bottom: 30px;
      margin-top: 30px;
    }
    
    .card {
      flex: 1;
      padding: 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }
    
    .card i {
      font-size: 2em;
      margin-bottom: 10px;
      color: #0066cc;
    }
    
    .card h4 {
      font-size: 1em;
      color: #6B7280;
      margin: 0 0 10px 0;
    }
    
    .card p {
      font-size: 1.8em;
      font-weight: bold;
      color: #111827;
      margin: 0;
    }
    
    /* Main Content Layout */
    .main-content {
      display: flex;
      gap: 30px;
      margin-bottom: 30px;
    }
    
    .left-column {
      flex: 2;
    }
    
    .right-column {
      flex: 1;
    }
    
    /* Chart Container Styling */
    .chart-container {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      height: 100%;
    }
    
    .chart-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .chart-title {
      font-size: 1.2em;
      color: #374151;
      margin: 0;
    }
    
    .condition-filters {
      display: flex;
      justify-content: center;
      margin-top: 15px;
      flex-wrap: wrap;
      gap: 10px;
    }
    
    .condition-filter {
      padding: 8px 15px;
      border-radius: 20px;
      font-size: 0.9em;
      font-weight: 500;
      color: white;
      display: flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
      transition: transform 0.2s, opacity 0.2s;
      text-decoration: none;
    }
    
    .condition-filter:hover {
      transform: translateY(-2px);
      opacity: 0.9;
    }
    
    .filter-good {
      background-color: #8BC34A;
    }
    
    .filter-bad {
      background-color: #F44336;
    }
    
    .filter-defect {
      background-color: #FF9800;
    }
    
    .condition-badge {
      padding: 5px 10px;
      border-radius: 5px;
      font-size: 0.85em;
      font-weight: 500;
      color: white;
    }
    
    .condition-good {
      background-color: #8BC34A;
    }
    
    .condition-bad {
      background-color: #F44336;
    }
    
    .condition-defect {
      background-color: #FF9800;
    }
    
    /* Activity Feed Styling */
    .activity-container {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      height: 100%;
    }
    
    .activity-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      padding-bottom: 15px;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .activity-title {
      font-size: 1.2em;
      color: #374151;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .activity-title i {
      color: #0066cc;
    }
    
    .view-all-link {
      color: #0066cc;
      text-decoration: none;
      font-size: 0.9em;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .activity-feed {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    
    .activity-item {
      display: flex;
      gap: 15px;
      padding-bottom: 15px;
      border-bottom: 1px solid #f3f4f6;
    }
    
    .activity-item:last-child {
      border-bottom: none;
    }
    
    .activity-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #f3f4f6;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    
    .activity-icon.added {
      background-color: #d1fae5;
      color: #047857;
    }
    
    .activity-icon.updated {
      background-color: #dbeafe;
      color: #1e40af;
    }
    
    .activity-icon.borrowed {
      background-color: #ede9fe;
      color: #5b21b6;
    }
    
    .activity-icon.returned {
      background-color: #fef3c7;
      color: #92400e;
    }
    
    .activity-icon.maintenance {
      background-color: #fee2e2;
      color: #b91c1c;
    }
    
    .activity-content {
      flex: 1;
    }
    
    .activity-text {
      margin: 0 0 5px 0;
      color: #374151;
    }
    
    .activity-text strong {
      font-weight: 600;
      color: #111827;
    }
    
    .activity-text a {
      color: #0066cc;
      text-decoration: none;
      font-weight: 500;
    }
    
    .activity-meta {
      display: flex;
      gap: 15px;
      color: #6b7280;
      font-size: 0.85em;
    }
    
    .activity-time, .activity-user {
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .activity-time i, .activity-user i {
      font-size: 0.95em;
    }
    
    .no-activity {
      text-align: center;
      padding: 30px 0;
      color: #6b7280;
      font-style: italic;
    }
    
    /* Filtered Equipment Table Styles */
    .filtered-equipment {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-top: 20px;
    }
    
    .filtered-equipment h3 {
      margin-top: 0;
      color: #374151;
      padding-bottom: 15px;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .filtered-equipment table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .filtered-equipment th {
      background-color: #f3f4f6;
      padding: 12px 15px;
      text-align: left;
      font-weight: 600;
      color: #374151;
    }
    
    .filtered-equipment td {
      padding: 12px 15px;
      border-bottom: 1px solid #e5e7eb;
      color: #4b5563;
    }
    
    .filtered-equipment tr:last-child td {
      border-bottom: none;
    }
    
    .status-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.85em;
      font-weight: 500;
    }
    
    .status-available {
      background-color: #d1fae5;
      color: #065f46;
    }
    
    .status-borrowed {
      background-color: #dbeafe;
      color: #1e40af;
    }
    
    .action-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      color: white;
      margin-right: 5px;
      transition: transform 0.2s;
    }
    
    .action-btn:hover {
      transform: scale(1.1);
    }
    
    .action-btn.edit {
      background-color: #3b82f6;
    }
    
    .action-btn.view {
      background-color: #10b981;
    }
    
    .view-all-link {
      display: inline-block;
      margin-top: 15px;
      color: #3b82f6;
      text-decoration: none;
      font-weight: 500;
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .view-all-link:hover {
      text-decoration: underline;
    }
    
    .no-results {
      text-align: center;
      padding: 20px;
      color: #6b7280;
      font-style: italic;
    }
    
    /* Responsive styles */
    @media (max-width: 1200px) {
      .main-content {
        flex-direction: column;
      }
      
      .left-column, .right-column {
        flex: 1;
      }
    }
    
    @media (max-width: 768px) {
      .dashboard-cards {
        flex-direction: column;
      }
      
      .filtered-equipment table {
        display: block;
        overflow-x: auto;
      }
    }
  </style>
</head>
<body>

<?php include 'sidebar.php'; ?>


<div class="main">
  <div class="topbar">
    <h3>Welcome, <?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></h3>
  </div>
  
  <!-- Dashboard Cards -->
  <div class="dashboard-cards">
    <div class="card">
      <i class="fas fa-box"></i>
      <h4>Total Equipments</h4>
      <p><?php echo number_format($stats['total'] ?? 0); ?></p>
    </div>
    <div class="card">
      <i class="fas fa-check-circle"></i>
      <h4>Available</h4>
      <p><?php echo number_format($stats['available'] ?? 0); ?></p>
    </div>
    <div class="card">
      <i class="fas fa-hourglass-half"></i>
      <h4>Borrowed</h4>
      <p><?php echo number_format($stats['borrowed'] ?? 0); ?></p>
    </div>
    <div class="card">
      <i class="fas fa-users"></i>
      <h4>Volunteers</h4>
      <p><?php echo number_format($volunteer_count ?? 0); ?></p>
    </div>
  </div>
  
  <!-- Main Content Area -->
  <div class="main-content">
    <!-- Left Column - Chart -->
    <div class="left-column">
      <div class="chart-container">
        <div class="chart-header">
          <h3 class="chart-title">Equipment Condition Overview</h3>
        </div>
        <canvas id="conditionChart"></canvas>
      </div>
      
      <!-- Filtered Equipment List (if condition is selected) -->
      <?php if (!empty($filter_condition) && isset($filtered_equipment)): ?>
        <div class="filtered-equipment">
          <h3>Equipment in <?php echo htmlspecialchars($filter_condition); ?> Condition</h3>
          
          <?php if (count($filtered_equipment) > 0): ?>
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Serial Number</th>
                  <th>Status</th>
                  <th>Location</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($filtered_equipment as $item): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($item['id']); ?></td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['serial_number']); ?></td>
                    <td>
                      <span class="status-badge status-<?php echo strtolower(htmlspecialchars($item['status'])); ?>">
                        <?php echo htmlspecialchars($item['status']); ?>
                      </span>
                    </td>
                    <td><?php echo htmlspecialchars($item['location']); ?></td>
                    <td>
                      <a href="edit-equipment.php?id=<?php echo $item['id']; ?>" class="action-btn edit">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="view-equipment.php?id=<?php echo $item['id']; ?>" class="action-btn view">
                        <i class="fas fa-eye"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <a href="equipment-list.php?condition=<?php echo urlencode($filter_condition); ?>" class="view-all-link">
              View all in Equipment List <i class="fas fa-arrow-right"></i>
            </a>
          <?php else: ?>
            <div class="no-results">No equipment found in this condition</div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
    
    <!-- Right Column - Recent Activities -->
    <div class="right-column">
      <div class="activity-container">
        <div class="activity-header">
          <h3 class="activity-title">
            <i class="fas fa-history"></i> Recent Activities
          </h3>
          <a href="activity-log.php" class="view-all-link">
            View all activities <i class="fas fa-arrow-right"></i>
          </a>
        </div>
        
        <div class="activity-feed">
          <?php if (count($recent_activities) > 0): ?>
            <?php foreach ($recent_activities as $activity): ?>
              <div class="activity-item">
                <div class="activity-icon <?php echo htmlspecialchars($activity['activity_type']); ?>">
                  <?php
                  $icon = '';
                  switch ($activity['activity_type']) {
                    case 'added':
                      $icon = 'fa-plus';
                      break;
                    case 'updated':
                      $icon = 'fa-pen';
                      break;
                    case 'borrowed':
                      $icon = 'fa-handshake';
                      break;
                    case 'returned':
                      $icon = 'fa-arrow-right-to-bracket';
                      break;
                    case 'maintenance':
                      $icon = 'fa-wrench';
                      break;
                  }
                  ?>
                  <i class="fas <?php echo $icon; ?>"></i>
                </div>
                <div class="activity-content">
                  <p class="activity-text">
                    <strong><?php echo ucfirst(htmlspecialchars($activity['activity_type'])); ?>:</strong> 
                    <?php echo htmlspecialchars($activity['description']); ?>
                    <a href="view-equipment.php?id=<?php echo $activity['equipment_id']; ?>">
                      <?php echo htmlspecialchars($activity['equipment_name']); ?>
                    </a>
                  </p>
                  <div class="activity-meta">
                    <div class="activity-time">
                      <i class="far fa-clock"></i>
                      <?php 
                      $timestamp = strtotime($activity['created_at']);
                      $now = time();
                      $diff = $now - $timestamp;
                      
                      if ($diff < 60) {
                        echo 'Just now';
                      } elseif ($diff < 3600) {
                        echo floor($diff / 60) . ' min ago';
                      } elseif ($diff < 86400) {
                        echo floor($diff / 3600) . ' hours ago';
                      } elseif ($diff < 604800) {
                        echo floor($diff / 86400) . ' days ago';
                      } else {
                        echo date('M j, Y', $timestamp);
                      }
                      ?>
                    </div>
                    <div class="activity-user">
                      <i class="far fa-user"></i>
                      <?php echo htmlspecialchars($activity['user_name']); ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="no-activity">
              <p>No recent activities found.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Equipment condition chart
const conditionCtx = document.getElementById('conditionChart').getContext('2d');
const conditionChart = new Chart(conditionCtx, {
  type: 'bar',
  data: {
    labels: ['Good(Working)', 'Bad(Not Working)', 'Defect(Needs Repair)'],
    datasets: [{
      label: 'Number of Equipment',
      data: [
        <?php echo $stats['good'] ?? 0; ?>,
        <?php echo $stats['bad'] ?? 0; ?>,
        <?php echo $stats['defect'] ?? 0; ?>
      ],
      backgroundColor: [
        '#8BC34A', // Green
        '#F44336', // Red
        '#FF9800'  // Orange
      ],
      borderColor: [
        '#7CB342', // Darker Green
        '#E53935', // Darker Red
        '#FB8C00'  // Darker Orange
      ],
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    aspectRatio: 2,
    plugins: {
      legend: {
        display: false // Hide the default legend
      },
      tooltip: {
        callbacks: {
          label: function(context) {
            return `${context.dataset.label}: ${context.parsed.y}`;
          }
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          precision: 0
        }
      }
    },
    onClick: function(e, elements) {
      if(elements.length > 0) {
        const index = elements[0].index;
        const conditions = ['Good(Working)', 'Bad(Not Working)', 'Defect(Needs Repair)'];
        const condition = conditions[index];
        window.location.href = 'equipment-list.php?condition=' + encodeURIComponent(condition);
      }
    }
  }
});
// Make the chart clickable
document.getElementById('conditionChart').style.cursor = 'pointer';
</script>
</body>
</html>
