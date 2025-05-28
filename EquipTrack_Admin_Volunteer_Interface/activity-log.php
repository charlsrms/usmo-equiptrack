<?php
require_once 'db.php';
session_start();

// Authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Process filters
$filters = [
    'equipment_id' => $_GET['equipment_id'] ?? 0,
    'user_id' => $_GET['user_id'] ?? '',
    'activity_type' => $_GET['activity_type'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? ''
];

// Build query for both pagination and export
$where = [];
$params = [];

foreach ($filters as $key => $value) {
    if (!empty($value)) {
        switch ($key) {
            case 'equipment_id':
                $where[] = "ea.equipment_id = ?";
                $params[] = (int)$value;
                break;
            case 'user_id':
                $where[] = "ea.user_id = ?";
                $params[] = $value;
                break;
            case 'activity_type':
                $where[] = "ea.activity_type = ?";
                $params[] = $value;
                break;
            case 'date_from':
                $where[] = "DATE(ea.created_at) >= ?";
                $params[] = $value;
                break;
            case 'date_to':
                $where[] = "DATE(ea.created_at) <= ?";
                $params[] = $value;
                break;
        }
    }
}

// Get export query for PDF
function getExportQuery($where) {
    return "SELECT ea.created_at, e.name as equipment_name, e.serial_number, 
                  ea.activity_type, u.name as user_name, u.role as user_role, ea.description
           FROM equipment_activities ea
           LEFT JOIN equipment_list e ON ea.equipment_id = e.id
           LEFT JOIN users u ON ea.user_id = u.user_id" .
           ($where ? " WHERE " . implode(" AND ", $where) : "") . 
           " ORDER BY ea.created_at DESC";
}

// Handle PDF Export using mpdf
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    // First check if we can use direct browser printing
    if (isset($_GET['print']) && $_GET['print'] === 'direct') {
        // Get all records for export
        $export_sql = getExportQuery($where);
        
        $export_stmt = $pdo->prepare($export_sql);
        $export_stmt->execute($params);
        $export_data = $export_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Build filter description text
        $filterText = 'Filters applied: ';
        $activeFilters = [];
        
        if (!empty($filters['equipment_id'])) {
            // Get equipment name for the filter description
            $stmt = $pdo->prepare("SELECT name FROM equipment_list WHERE id = ?");
            $stmt->execute([$filters['equipment_id']]);
            $eq_name = $stmt->fetchColumn();
            $activeFilters[] = "Equipment: " . $eq_name;
        }
        
        if (!empty($filters['user_id'])) {
            // Get user name for the filter description
            $stmt = $pdo->prepare("SELECT name FROM users WHERE user_id = ?");
            $stmt->execute([$filters['user_id']]);
            $user_name = $stmt->fetchColumn();
            $activeFilters[] = "User: " . $user_name;
        }
        
        if (!empty($filters['activity_type'])) {
            $activeFilters[] = "Activity: " . ucfirst($filters['activity_type']);
        }
        
        if (!empty($filters['date_from'])) {
            $activeFilters[] = "From: " . $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $activeFilters[] = "To: " . $filters['date_to'];
        }
        
        // Filter description text
        $filterDescription = !empty($activeFilters) ? implode(', ', $activeFilters) : 'None';
        
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Equipment Activity Log</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    color: #333;
                }
                h1 {
                    color: #2c3e50;
                    font-size: 24px;
                    margin-bottom: 10px;
                }
                .report-header {
                    margin-bottom: 20px;
                }
                .filters {
                    margin-bottom: 15px;
                    font-size: 14px;
                    color: #555;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                th {
                    background-color: #3498db;
                    color: white;
                    padding: 8px;
                    text-align: left;
                    font-weight: bold;
                    font-size: 14px;
                    border: 1px solid #ddd;
                }
                td {
                    padding: 8px;
                    border: 1px solid #ddd;
                    font-size: 12px;
                }
                tr:nth-child(even) {
                    background-color: #f2f2f2;
                }
                .activity-badge {
                    display: inline-block;
                    padding: 3px 8px;
                    border-radius: 3px;
                    color: white;
                    font-size: 12px;
                    font-weight: bold;
                    text-align: center;
                }
                .added { background-color: #28a745; }
                .updated { background-color: #17a2b8; }
                .borrowed { background-color: #ffc107; color: #000; }
                .returned { background-color: #007bff; }
                .maintenance { background-color: #6c757d; }
                .footer {
                    margin-top: 30px;
                    text-align: center;
                    font-size: 12px;
                    color: #777;
                }
                .admin-user {
                    color: #3498db; 
                    font-weight: bold;
                }
                @media print {
                    body {
                        padding: 0;
                        margin: 0;
                    }
                    .no-print {
                        display: none;
                    }
                }
            </style>
        </head>
        <body>
            <div class="report-header">
                <h1>Equipment Activity Log</h1>
                <div>Generated on: <?= date('Y-m-d H:i:s') ?></div>
                <div class="filters">Filters applied: <?= $filterDescription ?></div>
            </div>
            
            <button class="no-print" onclick="window.print();" style="padding: 8px 15px; background: #3498db; color: white; border: none; border-radius: 4px; cursor: pointer; margin-bottom: 15px;">
                Print/Save as PDF
            </button>
            
            <table>
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>Equipment</th>
                        <th>Serial Number</th>
                        <th>Activity Type</th>
                        <th>User</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($export_data)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">No activities found</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($export_data as $row): ?>
                    <tr>
                        <td><?= date('M j, Y H:i', strtotime($row['created_at'])) ?></td>
                        <td><?= htmlspecialchars($row['equipment_name']) ?></td>
                        <td><?= htmlspecialchars($row['serial_number']) ?></td>
                        <td>
                            <span class="activity-badge <?= $row['activity_type'] ?>">
                                <?= ucfirst($row['activity_type']) ?>
                            </span>
                        </td>
                        <td class="<?= $row['user_role'] === 'admin' ? 'admin-user' : '' ?>">
                            <?= htmlspecialchars($row['user_name']) ?>
                            <?= $row['user_role'] === 'admin' ? ' (Admin)' : '' ?>
                        </td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <div class="footer">
                EquipTrack System &copy; <?= date('Y') ?>
            </div>
            
            <script>
                // Auto-print when page loads
                window.onload = function() {
                    // Small delay to ensure page is fully loaded
                    setTimeout(function() {
                        window.print();
                    }, 500);
                };
            </script>
        </body>
        </html>
        <?php
        exit;
    } else {
        // Redirect to the print-friendly version
        $print_url = $_SERVER['REQUEST_URI'] . (strpos($_SERVER['REQUEST_URI'], '?') ? '&' : '?') . 'print=direct';
        header('Location: ' . $print_url);
        exit;
    }
}

// Pagination
$per_page = 20;
$page = max(1, $_GET['page'] ?? 1);
$offset = ($page - 1) * $per_page;

// Get total count
$count_sql = "SELECT COUNT(*) FROM equipment_activities ea" . 
             ($where ? " WHERE " . implode(" AND ", $where) : "");
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total = $count_stmt->fetchColumn();
$total_pages = ceil($total / $per_page);

// Get activities - FIXED VERSION
$sql = "SELECT ea.*, e.name as equipment_name, e.serial_number, 
               u.name as user_name, u.role as user_role
        FROM equipment_activities ea
        LEFT JOIN equipment_list e ON ea.equipment_id = e.id
        LEFT JOIN users u ON ea.user_id = u.user_id" .
        ($where ? " WHERE " . implode(" AND ", $where) : "") . 
        " ORDER BY ea.created_at DESC LIMIT $offset, $per_page";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$activities = $stmt->fetchAll();

// Get filter options
$equipment_options = $pdo->query("SELECT id, name FROM equipment_list ORDER BY name")->fetchAll();
$user_options = $pdo->query("SELECT user_id, name FROM users ORDER BY name")->fetchAll();
$activity_types = ['added', 'updated', 'borrowed', 'returned', 'maintenance'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .activity-icon {
            width: 30px;
            text-align: center;
        }
        .badge-added { background-color: #28a745; }
        .badge-updated { background-color: #17a2b8; }
        .badge-borrowed { background-color: #ffc107; color: #000; }
        .badge-returned { background-color: #007bff; }
        .badge-maintenance { background-color: #6c757d; }
        .clickable-row { cursor: pointer; }
        .clickable-row:hover { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-history me-2"></i>Equipment Activity Log
                    </h3>
                    <div>
                        <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'pdf'])) ?>" class="btn btn-sm btn-light">
                            <i class="fas fa-file-pdf me-1"></i>Export PDF
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <form method="get" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Equipment</label>
                            <select name="equipment_id" class="form-select">
                                <option value="">All Equipment</option>
                                <?php foreach ($equipment_options as $eq): ?>
                                <option value="<?= $eq['id'] ?>" <?= $eq['id'] == $filters['equipment_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($eq['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">User</label>
                            <select name="user_id" class="form-select">
                                <option value="">All Users</option>
                                <?php foreach ($user_options as $user): ?>
                                <option value="<?= $user['user_id'] ?>" <?= $user['user_id'] == $filters['user_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">Activity Type</label>
                            <select name="activity_type" class="form-select">
                                <option value="">All Types</option>
                                <?php foreach ($activity_types as $type): ?>
                                <option value="<?= $type ?>" <?= $type == $filters['activity_type'] ? 'selected' : '' ?>>
                                    <?= ucfirst($type) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">From Date</label>
                            <input type="date" name="date_from" value="<?= $filters['date_from'] ?>" class="form-control">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">To Date</label>
                            <input type="date" name="date_to" value="<?= $filters['date_to'] ?>" class="form-control">
                        </div>
                        
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Date/Time</th>
                                <th>Equipment</th>
                                <th>Serial</th>
                                <th>Activity</th>
                                <th>User</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($activities)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">No activities found</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($activities as $act): ?>
                            <tr class="clickable-row" onclick="window.location='equipment-detail.php?id=<?= $act['equipment_id'] ?>'">
                                <td><?= date('M j, Y H:i', strtotime($act['created_at'])) ?></td>
                                <td><?= htmlspecialchars($act['equipment_name']) ?></td>
                                <td><?= htmlspecialchars($act['serial_number']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $act['activity_type'] ?>">
                                        <i class="fas fa-<?= [
                                            'added' => 'plus',
                                            'updated' => 'edit',
                                            'borrowed' => 'handshake',
                                            'returned' => 'undo',
                                            'maintenance' => 'wrench'
                                        ][$act['activity_type']] ?> me-1"></i>
                                        <?= ucfirst($act['activity_type']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="<?= $act['user_role'] === 'admin' ? 'text-primary fw-bold' : '' ?>">
                                        <?= htmlspecialchars($act['user_name']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($act['description']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($page + 2, $total_pages); $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Make rows clickable
        document.querySelectorAll('.clickable-row').forEach(row => {
            row.addEventListener('click', () => {
                window.location = row.getAttribute('onclick').match(/'(.*?)'/)[1];
            });
        });
    </script>
</body>
</html>