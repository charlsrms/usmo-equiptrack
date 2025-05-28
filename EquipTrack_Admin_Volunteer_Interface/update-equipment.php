<?php
include 'db.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

// Only admin can update equipment
if ($_SESSION['role'] !== 'admin') {
  header('Location: equipment-list.php');
  exit;
}

// Get form data
$id = $_POST['id'] ?? null;
$name = $_POST['name'] ?? '';
$serial_number = $_POST['serial_number'] ?? '';
$status = $_POST['status'] ?? '';
$conditions = $_POST['conditions'] ?? '';
$location = $_POST['location'] ?? '';

if ($id) {
  try {
    // Update equipment in database
    $stmt = $pdo->prepare("
      UPDATE equipment_list 
      SET name = ?, serial_number = ?, status = ?, conditions = ?, location = ?
      WHERE id = ?
    ");
    $stmt->execute([$name, $serial_number, $status, $conditions, $location, $id]);
    
    // Log the activity
    $activity_stmt = $pdo->prepare("
      INSERT INTO equipment_activities 
      (equipment_id, user_id, activity_type, description, created_at)
      VALUES (?, ?, 'updated', ?, NOW())
    ");
    $description = "Updated equipment: $name (SN: $serial_number) - Status: $status, Condition: $conditions, Location: $location";
    $activity_stmt->execute([$id, $_SESSION['user_id'], $description]);
    
    echo json_encode(['success' => true]);
  } catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
  }
} else {
  echo json_encode(['success' => false, 'error' => 'Invalid equipment ID']);
}
?>