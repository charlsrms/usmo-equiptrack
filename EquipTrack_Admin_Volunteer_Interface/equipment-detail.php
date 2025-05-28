<?php
require_once 'db.php';
session_start();

// Authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Get equipment ID
$equipment_id = $_GET['id'] ?? 0;

// Fetch equipment details
$stmt = $pdo->prepare("SELECT * FROM equipment_list WHERE id = ?");
$stmt->execute([$equipment_id]);
$equipment = $stmt->fetch();

// Fetch related activities with more details
$activities_stmt = $pdo->prepare("SELECT 
                                ea.*, 
                                u.name as user_name,
                                u.user_id,
                                el.name as equipment_name,
                                el.serial_number
                             FROM equipment_activities ea
                             JOIN users u ON ea.user_id = u.user_id
                             JOIN equipment_list el ON ea.equipment_id = el.id
                             WHERE ea.equipment_id = ?
                             ORDER BY ea.created_at DESC");
$activities_stmt->execute([$equipment_id]);
$activities = $activities_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #f8f9fa;
            --accent-color: #e74c3c;
            --text-color: #2c3e50;
        }
        
        body {
            background-color: #f5f7fa;
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: none;
            overflow: hidden;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1.5rem;
            border-bottom: none;
        }
        
        .equipment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
        }
        
        .status-active {
            background-color: #2ecc71;
            color: white;
        }
        
        .status-inactive {
            background-color: #e74c3c;
            color: white;
        }
        
        .status-maintenance {
            background-color: #f39c12;
            color: white;
        }
        
        .detail-item {
            margin-bottom: 1.2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #7f8c8d;
            margin-bottom: 0.3rem;
        }
        
        .detail-value {
            font-size: 1.1rem;
        }
        
        /* Enhanced Activity History Styles */
        .activity-item {
            border-left: 3px solid var(--primary-color);
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .activity-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .activity-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }
        
        .activity-type {
            font-weight: 600;
            text-transform: capitalize;
            color: var(--primary-color);
        }
        
        .activity-time {
            font-size: 0.85rem;
            color: #7f8c8d;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .activity-user {
            font-weight: 600;
            color: var(--text-color);
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 0.5rem;
        }
        
        .activity-description {
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .activity-details {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 0.5rem;
            font-size: 0.9rem;
        }
        
        .detail-badge {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 0.25rem 0.5rem;
            background-color: #e9ecef;
            border-radius: 4px;
        }
        
        .no-activity {
            color: #95a5a6;
            font-style: italic;
            text-align: center;
            padding: 2rem;
        }
        
        .section-title {
            position: relative;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        /* Activity type colors */
        .activity-added {
            border-left-color: #2ecc71;
        }
        
        .activity-updated {
            border-left-color: #3498db;
        }
        
        .activity-borrowed {
            border-left-color: #9b59b6;
        }
        
        .activity-returned {
            border-left-color: #f39c12;
        }
        
        .activity-maintenance {
            border-left-color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="card mb-4">
            <div class="card-header">
                <div class="equipment-header">
                    <h2 class="mb-0"><i class="bi bi-pc-display-horizontal"></i> Equipment Details</h2>
                    <a href="equipment-list.php" class="btn btn-light"><i class="bi bi-arrow-left"></i> Back to List</a>
                </div>
            </div>
            <div class="card-body">
                <?php if ($equipment): ?>
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h3 class="mb-3"><?= htmlspecialchars($equipment['name']) ?></h3>
                            <span class="status-badge 
                                <?= $equipment['status'] === 'Active' ? 'status-active' : 
                                   ($equipment['status'] === 'In Maintenance' ? 'status-maintenance' : 'status-inactive') ?>">
                                <?= htmlspecialchars($equipment['status']) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label"><i class="bi bi-upc-scan"></i> Serial Number</div>
                                <div class="detail-value"><?= htmlspecialchars($equipment['serial_number']) ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label"><i class="bi bi-heart-pulse"></i> Condition</div>
                                <div class="detail-value"><?= htmlspecialchars($equipment['conditions']) ?></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="detail-item">
                                <div class="detail-label"><i class="bi bi-geo-alt"></i> Location</div>
                                <div class="detail-value"><?= htmlspecialchars($equipment['location']) ?></div>
                            </div>
                            
                            <div class="detail-item">
                                <div class="detail-label"><i class="bi bi-calendar-check"></i> Last Updated</div>
                                <div class="detail-value">
                                    <?= isset($equipment['updated_at']) && $equipment['updated_at'] ? 
                                        date('M j, Y H:i', strtotime($equipment['updated_at'])) : 'Never' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> Equipment not found
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($equipment): ?>
        <div class="card">
            <div class="card-header bg-white">
                <h4 class="section-title mb-0"><i class="bi bi-clock-history"></i> Activity History</h4>
            </div>
            <div class="card-body">
                <?php if ($activities): ?>
                    <div class="activity-list">
                        <?php foreach ($activities as $activity): ?>
                        <div class="activity-item activity-<?= htmlspecialchars($activity['activity_type']) ?>">
                            <div class="activity-header">
                                <span class="activity-type">
                                    <?= ucfirst(htmlspecialchars($activity['activity_type'])) ?>
                                </span>
                                <span class="activity-time">
                                    <i class="bi bi-clock"></i>
                                    <?= date('M j, Y H:i', strtotime($activity['created_at'])) ?>
                                </span>
                            </div>
                            
                            <div class="activity-user">
                                <i class="bi bi-person"></i>
                                <?= htmlspecialchars($activity['user_name']) ?>
                            </div>
                            
                            <div class="activity-description">
                                <?= htmlspecialchars($activity['description']) ?>
                            </div>
                            
                            <div class="activity-details">
                                <div class="detail-badge">
                                    <i class="bi bi-tag"></i>
                                    <span>Equipment: <?= htmlspecialchars($activity['equipment_name']) ?></span>
                                </div>
                                <div class="detail-badge">
                                    <i class="bi bi-upc"></i>
                                    <span>Serial: <?= htmlspecialchars($activity['serial_number']) ?></span>
                                </div>
                                <?php if (!empty($activity['duration'])): ?>
                                <div class="detail-badge">
                                    <i class="bi bi-calendar-range"></i>
                                    <span>Duration: <?= htmlspecialchars($activity['duration']) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($activity['notes'])): ?>
                                <div class="detail-badge">
                                    <i class="bi bi-journal-text"></i>
                                    <span>Notes: <?= htmlspecialchars($activity['notes']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-activity">
                        <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                        <p class="mt-2">No activity recorded for this equipment</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
