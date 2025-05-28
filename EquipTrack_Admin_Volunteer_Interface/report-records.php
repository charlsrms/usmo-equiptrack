<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit;
}
?>
<style>
    :root {
  --primary-color: #3498db;       /* Blue */
  --primary-dark: #103e5b;       /* Darker blue */
  --primary-light: #4aabec;      /* Lighter blue */
  --good-color: #2ecc71;    /* Green */
  --defect-color: #f04a4a;      /* Red */
  --bad-color: #ff0000;
}
*
{
    padding: 0px 0px 0px 0px;
    margin: 0px 0px 0px 0px;
}

#title-holder p
{
    padding: 15px 15px 15px 15px;
    font-size: 30px;
    font-family: 'Segoe UI';
    color: #13364e;
}

#dropdown-section
{
    position: relative;
    padding: 10px 0px 10px 0px;
}

#dropdown-holder
{
    margin-left: 20px;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: left;
    gap: 15px;
}
#dropdown-holder div
{
    color: #13364e;
    font-size: 20px;
    padding: 15px 15px 15px 15px;
    border-width: 1px;
    font-family: 'Segoe UI';
    font-weight: 500;
    display: flex;
    flex-direction: column;
}

select {
  width: 150px;
  padding: 2px;
  font-size: 17px;
  border: 2px solid var(--primary-dark);
  border-radius: 5px;
  background-color: var(--primary-dark);
  color: white;
  margin-top: 5px;
  
  /* Dropdown arrow */
  appearance: none; /* Removes default arrow in modern browsers */
  -webkit-appearance: none; /* For Safari */
  -moz-appearance: none; /* For Firefox */
  
  /* Custom arrow */
  background-image: url('data:image/svg+xml;utf8,<svg fill="%233498db" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>');
  background-repeat: no-repeat;
  background-position: right 10px center;
  background-size: 16px;
}

select:focus {
  outline: none;
  border-color: #2ecc71;
  box-shadow: 0 0 5px rgba(46, 204, 113, 0.5);
}

#date
{
    width: 150px;
    margin-top: 4px;
    border: 2px solid var(--primary-dark);
    border-radius: 5px;
    background-color: var(--primary-dark);
    padding: 3px;
    color: white;
    font-size: 15px;
    font-family: Arial;
}

input[type="date"]::-webkit-calendar-picker-indicator {
  cursor: pointer;
}

/* print button */

.print-btn {
  font-size: 20px;
  background: none;
  border: none;
  color: #333;
  cursor: pointer;
  transition: color 0.3s;
  position: absolute;
  right: 10px;
  top: 10px;
}

.print-btn:hover {
  color: #007bff;
}
#display-section
{
    position: relative;
    padding-bottom: 50px;
}
/* table 1 */
table.informationTable
{
    margin-top: 2%;
    border: 1px solid #000000;
    border-collapse:collapse;
    width: 95%;
    margin-left: auto;
    margin-right: auto;
    table-layout: fixed;
}

table.informationTable td
{
    border: 1px solid #000000;
    padding: 3px 2px;
    text-align: center;   
    font-family: 'Segoe UI';
    height: 20px;
    width: auto;
    background-color: white;
}
col.first-column {
  width: 35%; /* Set specific width */
}

/* table 2*/
table.blueTable {
  border: 1px solid #000000;
  background-color: #f4f6f9;
  width: 95%;
  text-align: center;
  border-collapse:collapse;
  margin-top: 25px;
  table-layout:fixed;
  position: relative;
  margin-left: auto;
  margin-right: auto;
}
table.blueTable td, table.blueTable th {
  border: 1px solid #000000;
  padding: 3px 2px;
  text-align: center;   
  font-family: 'Segoe UI';
  height: 20px;
}
table.blueTable tr:nth-child(1) {
  font-weight: bold;
}

table.blueTable tbody td {
  font-size: 13px;
}
table.blueTable thead {
    border-bottom: 1px solid #000000
}
table.blueTable thead th {
  font-size: 14px;
  font-weight: bold;
  color: #000000;
  text-align: center;
  font-family: 'Segoe UI';
  border-left: 1px solid #000000;
}
.good-cell {
background-color: #2ecc71 !important;
color: black !important;
-webkit-print-color-adjust: exact;
print-color-adjust: exact;
}

.defect-cell {
background-color: #f04a4a !important;
color: black !important;
-webkit-print-color-adjust: exact;
print-color-adjust: exact;
}

.bad-cell {
background-color: red !important;
color: black !important;
-webkit-print-color-adjust: exact;
print-color-adjust: exact;
}

.blue-cell {
background-color: #103e5b !important;
color: white !important;
-webkit-print-color-adjust: exact;
print-color-adjust: exact;
}

table, th, td {
-webkit-print-color-adjust: exact;
print-color-adjust: exact;
}

#display-section p
{
    padding-top: 15px;
    position: relative;
    font-size: 35px;
    font-family: Verdana;
    color: #f5951a;
    font-weight: 600;
    print-color-adjust: exact;
}

/* submit button */
#submit-btn
{
    position: relative;
    display: block;
    background-color: var(--primary-dark);
    border: none;
    width: 100px;
    height: 30px;
    top: 50%;
    font-size: 15px;
    color: white;
    border-radius: 5px;
}
#submit-btn:hover
{
    cursor: pointer;
    scale: 1.1;
}

#table1-header tr
{
    display: none;
    background-color: #ffbf6d;
    font-family: 'Segoe UI';
    font-weight: bold;
}

/* usmo div */
#usmo-div 
{
    display: none;  
    position: relative;
    height: 400px;
  
    align-items: center;
    justify-content: center;
}
#usmo-div p
{
    font-size: 40px;
    font-family: 'Times New Roman';
    color: black;
}
#usmo-div img
{
    width: 120px;
    height: 120px;
    print-color-adjust: exact;
}
#usmo-logo 
{
    position: absolute;
    top: 15px;
    left: 15px;
}
.form-container {
    background: white;
    padding: flex;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.12);
    max-width: 1100px;
    margin: 30px auto;
}
@page {
  size: landscape;
}
@media print {


    /* Hide non-essential UI like buttons, sidebars, dropdowns */
    .print-btn, #title-holder,
    .sidebar,
    #dropdown-section, #display-section-text,
    #submit-btn {
    display: none !important;
    }

    /* Use full width */
    #content-holder {
    padding: 0 !important;
    }

    .content {
    margin: 0 !important;
    width: 100% !important;
    background: white !important;
    }

    body {
    background: white !important;
    color: black !important;
    }

    /* Improve table print layout */
    table {
    width: 100% !important;
    font-size: 12px !important;
    }

    th, td {
    padding: 5px !important;
    }

    /* Avoid page breaks inside rows */
    tr 
    {
        page-break-inside: avoid;
    }

    /* Optional: Add page breaks after major sections */
    #display-section 
    {
        page-break-before: auto;
        page-break-after: always;
    }
    #display-section p 
    {
        font-size: 30px;
        font-family: Verdana;
        color: #f5951a;
        font-weight: 600;
    }
    #table1-header tr
    { 
        display: table-row !important;
    }
    #table1-header td
    {
        background-color: #ffbf6d;
        font-family: 'Segoe UI';
        font-weight: bold;
    }
    #usmo-div 
    {
        display: flex;  
        position: relative;
        height: 240px;
        align-items: center;
        justify-content: center;
    }

    #usmo-div p
    {
        position: relative;
        top: 70px;
        font-size: 40px;
        font-family: 'Times New Roman';
        color: black;
    }
    #usmo-div img
    {
        width: 120px;
        height: 120px;
        print-color-adjust: exact;
    }
    #usmo-logo 
    {
        position: absolute;
        top: 15px;
        left: 15px;
    }
}
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
<?php
// Database connection
$host = 'localhost';
$db   = 'equiptrack';
$user = 'root'; 
$pass = '';     

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch volunteers
    $volunteersQuery = $pdo->query("SELECT DISTINCT name, user_id FROM users WHERE role = 'volunteer' ORDER BY name");
    $volunteers = $volunteersQuery->fetchAll(PDO::FETCH_ASSOC);

    // Fetch equipment types
    $equipmentQuery = $pdo->query("SELECT DISTINCT equipment_type FROM equipment_list WHERE equipment_type IS NOT NULL AND equipment_type != '' ORDER BY equipment_type");
    $equipmentTypes = $equipmentQuery->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize variables
$reportData = [];
$dateData = [];
$table2Data = [];
$equipment_type = '';
$volunteer_name = '';
$date = '';


// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $volunteer_name = $_POST['volunteer'] ?? '';
    $equipment_type = $_POST['equipment'] ?? '';
    $date = $_POST['date'] ?? '';
    
    // Get volunteer ID from name
    $volunteer_id = '';
    foreach ($volunteers as $volunteer) {
        if ($volunteer['name'] == $volunteer_name) {
            $volunteer_id = $volunteer['user_id'];
            break;
        }
    }
    
    // Build WHERE conditions based on selections
    $whereConditions = [];
    $params = [];
    
    if (!empty($volunteer_id)) {
        $whereConditions[] = "v.user_id = :user_id";
        $params[':user_id'] = $volunteer_id;
    }
    
    if (!empty($equipment_type)) {
        $whereConditions[] = "e.equipment_type = :equipment_type";
        $params[':equipment_type'] = $equipment_type;
    }
    
    if (!empty($date)) {
        $yearMonth = explode('-', $date);
        $whereConditions[] = "YEAR(v.submitted_at) = :year AND MONTH(v.submitted_at) = :month";
        $params[':year'] = $yearMonth[0];
        $params[':month'] = $yearMonth[1];
    }
    
    $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
    
    // Get report summary data
    $reportQuery = $pdo->prepare("
        SELECT 
            u.name AS volunteer_name,
            v.check_type,
            v.condition_status,
            v.submitted_at,
            COUNT(DISTINCT v.equipment_id) AS incident_count
        FROM 
            volunteer_submitted_report v
        JOIN 
            users u ON v.user_id = u.user_id
        JOIN
            equipment_list e ON v.equipment_id = e.id
        $whereClause
        GROUP BY 
            v.user_id, v.check_type
        ORDER BY 
            v.submitted_at DESC
        LIMIT 1
    ");
    $reportQuery->execute($params);
    $reportData = $reportQuery->fetch(PDO::FETCH_ASSOC);
    
    // Get first and final check dates
    if (!empty($volunteer_id) && !empty($date)) {
        $yearMonth = explode('-', $date);
        $dateQuery = $pdo->prepare("
            SELECT 
                MIN(submitted_at) AS first_check,
                MAX(submitted_at) AS final_check
            FROM 
                volunteer_submitted_report
            WHERE 
                user_id = :user_id
            AND 
                YEAR(submitted_at) = :year 
            AND 
                MONTH(submitted_at) = :month
        ");
        $dateQuery->execute([
            ':user_id' => $volunteer_id,
            ':year' => $yearMonth[0],
            ':month' => $yearMonth[1]
        ]);
        $dateData = $dateQuery->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get detailed equipment data for table 2
    $table2Query = $pdo->prepare("
    SELECT 
        e.name AS item_name,
        e.serial_number,
        COUNT(*) AS quantity,
        SUM(CASE WHEN v.check_type = 'First Check' AND v.condition_status = 'Good(Working)' THEN 1 ELSE 0 END) AS first_good,
        SUM(CASE WHEN v.check_type = 'First Check' AND v.condition_status = 'Defect(Needs Repair)' THEN 1 ELSE 0 END) AS first_defect,
        SUM(CASE WHEN v.check_type = 'First Check' AND v.condition_status = 'Bad(Not Working)' THEN 1 ELSE 0 END) AS first_bad,
        SUM(CASE WHEN v.check_type = 'Final Check' AND v.condition_status = 'Good(Working)' THEN 1 ELSE 0 END) AS final_good,
        SUM(CASE WHEN v.check_type = 'Final Check' AND v.condition_status = 'Defect(Needs Repair)' THEN 1 ELSE 0 END) AS final_defect,
        SUM(CASE WHEN v.check_type = 'Final Check' AND v.condition_status = 'Bad(Not Working)' THEN 1 ELSE 0 END) AS final_bad,
        GROUP_CONCAT(DISTINCT v.remarks SEPARATOR '; ') AS remarks
    FROM 
        volunteer_submitted_report v
    JOIN 
        equipment_list e ON v.equipment_id = e.id
    $whereClause
    GROUP BY 
        e.id, e.name, e.serial_number
    ");

    $table2Query->execute($params);
    $table2Data = $table2Query->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php include 'sidebar.php'; ?>
<div id="content-holder">
     <div class="topbar"><h3>Reports and Records</h3></div>
    </div>
    <div class="main">
    <div id="dropdown-section" class="content">
        <form action="" method="POST">
            <div id="dropdown-holder">
                <div>
                    <label for="volunteer">Volunteer:</label>
                    <select name="volunteer" id="volunteer">
                        <option value="">-- Select Volunteer --</option>
                        <?php foreach ($volunteers as $volunteer): ?>
                            <option value="<?= htmlspecialchars($volunteer['name']) ?>" <?= isset($_POST['volunteer']) && $_POST['volunteer'] == $volunteer['name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($volunteer['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="equipment">Type of Equipment:</label>
                    <select name="equipment" id="equipment">
                        <option value="">-- Select Equipment --</option>
                        <?php foreach ($equipmentTypes as $equipment): ?>
                            <option value="<?= htmlspecialchars($equipment['equipment_type']) ?>" <?= isset($_POST['equipment']) && $_POST['equipment'] == $equipment['equipment_type'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($equipment['equipment_type']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="date">Date: </label>
                    <input type="month" id="date" name="date" value="<?= $_POST['date'] ?? '' ?>">
                </div>
                <div>
                    <input type="submit" name="submit" id="submit-btn" value="Search">
                </div>
            </div>
        </form>
    </div>
    
    <div class="form-container">
    <div id="display-section" class="content">
        <center><p id="display-section-text">BIWEEKLY CONDITION CHECKUP</p></center>
        <button class="print-btn" onclick="window.print()" title="Print / Save as PDF">
            <i class="fas fa-print"></i>
        </button>
        
        <div id="usmo-div">
            <img src="USMO Logo.png" id="usmo-logo">
            <center><p>University Student Multimedia Organization</p></center>
        </div>
        <!-- table 1 -->
        <table class="informationTable">
            <colgroup>
                <col class="first-column">
                <col>
            </colgroup>
            <thead>
                <tr id="table1-tr">
                    <td colspan="2" >
                        BIWEEKLY CONDITION CHECKUPS
                    </td>
                </tr>
                
            </thead>
            <tr>
                <td>Name of Volunteer Submitted the Report:</td>
                <td><?= htmlspecialchars($reportData['volunteer_name'] ?? 'N/A') ?></td>
            </tr>
            <tr>
                <td>For the Month of:</td>
                <td><?= !empty($date) ? date('F Y', strtotime($date . '-01')) : date('F Y') ?></td>
            </tr>
            <tr>
                <td>Date of First Check:</td>
                <td><?= isset($dateData['first_check']) ? date('m/d/Y', strtotime($dateData['first_check'])) : 'N/A' ?></td>
            </tr>
            <tr>
                <td>Date of Final Check:</td>
                <td><?= isset($dateData['final_check']) ? date('m/d/Y', strtotime($dateData['final_check'])) : 'N/A' ?></td>
            </tr>
            <tr>
                <td>No. of Incident Reports Collected:</td>
                <td><?= $reportData['incident_count'] ?? 0 ?></td>
            </tr>
        </table>

        <!-- table 2 -->
    <table class="blueTable">
        <thead>
        <tr>
            <th>Item Name</th>
            <th>Serial Number</th>
            <th style="width: 5%">Qty</th>
            <th colspan="3" style="width: 15%">First Check</th>
            <th colspan="3" style="width: 15%">Final Check</th>
            <th colspan="4" style="width: 35%">Remarks</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="3" class="blue-cell"><?php echo strtoupper($equipment_type) ?></td>
                <td class="good-cell">Good</td>
                <td class="defect-cell">Defect</td>
                <td class="bad-cell">Bad</td>
                <td class="good-cell">Good</td>
                <td class="defect-cell">Defect</td>
                <td class="bad-cell">Bad</td>
                <td colspan="4" class="blue-cell"></td>
            </tr>
            <?php foreach ($table2Data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['item_name']) ?></td>
                    <td><?= htmlspecialchars($row['serial_number']) ?></td>
                    <td><?= htmlspecialchars($row['quantity']) ?></td>

                    <!-- First Check -->
                    <td class="good-cell"><?= $row['first_good'] > 0 ? '✔' : '' ?></td>
                    <td class="defect-cell"><?= $row['first_defect'] > 0 ? '✔' : '' ?></td>
                    <td class="bad-cell"><?= $row['first_bad'] > 0 ? '✔' : '' ?></td>

                    <!-- Final Check -->
                    <td class="good-cell"><?= $row['final_good'] > 0 ? '✔' : '' ?></td>
                    <td class="defect-cell"><?= $row['final_defect'] > 0 ? '✔' : '' ?></td>
                    <td class="bad-cell"><?= $row['final_bad'] > 0 ? '✔' : '' ?></td>

                    <!-- Remarks -->
                    <td colspan="4"><?= htmlspecialchars($row['remarks']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>    
        </div>
    </div>
</div>
</div>
</body>
<script>
window.onload = function () {
    const hasSubmitted = <?= isset($_POST['submit']) ? 'true' : 'false' ?>;

    if (!hasSubmitted) {
        // Set default dropdown values
        document.getElementById("volunteer").value = "<?= isset($volunteers[0]['name']) ? $volunteers[0]['name'] : '' ?>";
        document.getElementById("equipment").value = "<?= isset($equipmentTypes[0]['equipment_type']) ? $equipmentTypes[0]['equipment_type'] : '' ?>";
        
        // Optionally set default date to current month
        const dateInput = document.getElementById("date");
        const now = new Date();
        dateInput.value = now.toISOString().slice(0, 7); // "YYYY-MM"
    }
};
</script>

</html>
