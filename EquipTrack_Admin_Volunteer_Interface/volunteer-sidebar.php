<div class="sidebar">
  <h2>Equip Track</h2>

  <a href="volunteer-dashboard.php" class="sidebar-link">
    <i class="fas fa-home"></i> Dashboard
  </a>

  <a href="volunteer-equipment_undertaking.php" class="sidebar-link">
    <i class="fas fa-box-open"></i> Equipment Undertaking
  </a>

  <a href="volunteer-equipment_tracker.php" class="sidebar-link">
    <i class="fas fa-history"></i> Equipment Tracker
  </a>

  <a href="index.php" onclick="return confirm('Are you sure you want to logout?')">
    <i class="fas fa-sign-out-alt"></i> Logout
  </a>
</div>

<style>
.sidebar {
  /* your sidebar styles */
}

.sidebar-link {
  display: block;
  padding: 10px 15px;
  color: #fff;
  text-decoration: none;
  font-size: 16px;
  margin: 5px 0;
  border-radius: 4px;
  transition: background-color 0.3s ease;
}

.sidebar-link:hover {
  background-color: #444;
}
</style>
