<?php
include 'db.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: equipment-list.php');
    exit;
}

$id = $_GET['id'];

try {
    // Begin transaction
    $pdo->beginTransaction();

    // First, log the deletion activity before deleting
    $activity_stmt = $pdo->prepare("
        INSERT INTO equipment_activities 
        (equipment_id, user_id, activity_type, description) 
        VALUES (?, ?, 'deleted', 'Equipment deleted from system')
    ");
    $activity_stmt->execute([$id, $_SESSION['user_id']]);

    // Then delete the equipment
    $stmt = $pdo->prepare("DELETE FROM equipment_list WHERE id = ?");
    $stmt->execute([$id]);

    // Commit transaction
    $pdo->commit();

    // Redirect back to equipment list with success message
    $_SESSION['success_message'] = "Equipment deleted successfully";
    header('Location: equipment-list.php');
    exit;

} catch (PDOException $e) {
    // Rollback transaction on error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Redirect back with error message
    $_SESSION['error_message'] = "Error deleting equipment: " . $e->getMessage();
    header('Location: equipment-list.php');
    exit;
}
