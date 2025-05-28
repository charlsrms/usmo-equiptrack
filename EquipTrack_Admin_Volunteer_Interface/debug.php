<?php
include 'db.php'; // Make sure this has $pdo properly set up

// 🔁 Handle AJAX request for model list
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_models') {
    $type = $_GET['equipment_type'] ?? '';
    $stmt = $pdo->prepare("SELECT DISTINCT model FROM equipment_list WHERE equipment_type = ? AND status = 'Available' AND conditions = 'Good(Working)'");
    $stmt->execute([$type]);
    $models = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($models);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Equipment Undertaking</title>
</head>
<body>

<!-- Equipment Type -->
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

<!-- Model (Dynamically Loaded) -->
<select name="model" id="model" required>
    <option value="" disabled selected>Select Model</option>
</select>

<!-- JS for dynamic model loading -->
<script>
document.getElementById("equipment_type").addEventListener("change", function () {
    const type = this.value;
    fetch("?ajax=get_models&equipment_type=" + encodeURIComponent(type))
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
        })
        .catch(err => {
            console.error("Failed to load models:", err);
        });
});
</script>

</body>
</html>
