<div class="sidebar">
  <h2>Equip Track</h2>
  <a href="admin-dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
  <a href="equipment-list.php"><i class="fas fa-boxes"></i> Equipment List</a>
  <a href="add-equipment.php"><i class="fas fa-plus-circle"></i> Add Equipment</a>
  <a href="report-records.php"><i class="fas fa-file-alt"></i> Reports & Records</a>
  <a href="equipment-undertaking.php"><i class="fas fa-tasks"></i> Equip Undertaking</a>
  <a href="undertaking-approval.php" class="active"><i class="fas fa-check-circle"></i> Undertaking Approval</a>
  <a href="equipment-tracker.php"><i class="fas fa-signal"></i> Equipment Tracker</a>
  <a href="manage-volunteers.php"><i class="fas fa-users-cog"></i> Manage Volunteers</a>
  <div class="sidebar-profile">
  <button id="profileToggle" class="profile-btn" aria-haspopup="true" aria-expanded="false" aria-controls="profileMenu">
    <div class="profile-info">
      <i class="fas fa-user-circle"></i>
      <p style="font-size: 1.1em;">Account</p>
      <i class="fas fa-caret-down dropdown-caret"></i>
    </div>
  </button>

  <div id="profileMenu" class="profile-dropdown-menu" role="menu" aria-labelledby="profileToggle" tabindex="-1">
    <a href="#" onclick="openAccountModal()" role="menuitem" tabindex="0">Profile Settings</a>
    <a href="login.php" role="menuitem" tabindex="0" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
  </div>
</div>
</div>

<!-- Account Settings Modal -->
<div id="accountModal" class="account-modal" style="display: none;">
    <div class="account-modal-overlay"></div>
    <div class="account-modal-content">
        <div class="account-modal-header">
            <h3>Account Settings</h3>
            <button type="button" class="account-modal-close">&times;</button>
        </div>
        <div class="account-modal-body">
            <form id="accountForm">
                <div class="account-form-group">
                    <label for="adminName">Full Name:</label>
                    <input type="text" id="adminName" name="name" value="Admin User" required>
                </div>
                
                <div class="account-form-group">
                    <label for="adminUsername">Username:</label>
                    <input type="text" id="adminUsername" name="username" value="admin" required>
                </div>
                
                <div class="account-form-group">
                    <label for="currentPassword">Current Password:</label>
                    <input type="password" id="currentPassword" name="current_password" placeholder="Enter current password" required>
                </div>
                
                <div class="account-form-group">
                    <label for="newPassword">New Password:</label>
                    <input type="password" id="newPassword" name="new_password" placeholder="Leave blank to keep current">
                </div>
                
                <div class="account-form-group">
                    <label for="confirmPassword">Confirm New Password:</label>
                    <input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirm new password">
                </div>
                
                <div class="account-form-buttons">
                    <button type="button" id="cancelAccountBtn">Cancel</button>
                    <button type="button" id="saveAccountBtn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Modal Styles - Scoped to avoid affecting your site */
.account-modal .account-modal-content {
    position: fixed !important;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
}


.account-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}

.account-modal-content {
    position: relative;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    width: 90%;
    max-width: 400px;
    max-height: 90vh;
    overflow-y: auto;
    z-index: 1;
    pointer-events: auto; /* Re-enable clicks for the modal content */
    margin: 20px; /* Ensures some spacing from viewport edges */
}

.account-modal-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.account-modal-header h3 {
    margin: 0;
    font-size: 18px;
}

.account-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
}

.account-modal-close:hover {
    color: #333;
}

.account-modal-body {
    padding: 20px;
}

.account-form-group {
    margin-bottom: 15px;
}

.account-form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.account-form-group input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.account-form-group input:focus {
    outline: none;
    border-color: #007cba;
}

.account-form-buttons {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
}

.account-form-buttons button {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

#cancelAccountBtn {
    background: #999;
    color: white;
}

#cancelAccountBtn:hover {
    background: #777;
}

#saveAccountBtn {
    background: #007cba;
    color: white;
}

#saveAccountBtn:hover {
    background: #005a87;
}

#saveAccountBtn:disabled {
    background: #ccc;
    cursor: not-allowed;
}

/* Alternative centering method (if flexbox doesn't work) */
.account-modal-alt {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
}

.account-modal-content-alt {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    width: 90%;
    max-width: 400px;
    max-height: 90vh;
    overflow-y: auto;
}

/* Alert styles */
.alert {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 5px;
    color: white;
    z-index: 10000;
    font-weight: bold;
    max-width: 300px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    cursor: pointer;
}

.alert-success { background-color: #28a745; }
.alert-error { background-color: #dc3545; }
.alert-warning { background-color: #ffc107; color: #000; }
.alert-info { background-color: #17a2b8; }
</style>

<script>
// Enhanced Account Modal Functions with Database Integration
function openAccountModal() {
    document.getElementById('accountModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Load current user data
    loadCurrentUserData();
}

function closeAccountModal() {
    document.getElementById('accountModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    // Reset form
    resetAccountForm();
}

function resetAccountForm() {
    document.getElementById('accountForm').reset();
    // Clear password fields
    document.getElementById('currentPassword').value = '';
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';
}

function loadCurrentUserData() {
    fetch('get_user_data.php')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('adminName').value = data.data.name;
            document.getElementById('adminUsername').value = data.data.username;
        }
    })
    .catch(error => {
        console.error('Error loading user data:', error);
        // Fallback to default values if fetch fails
        document.getElementById('adminName').value = 'Admin User';
        document.getElementById('adminUsername').value = 'admin';
    });
}

function saveAccountSettings() {
    const name = document.getElementById('adminName').value.trim();
    const username = document.getElementById('adminUsername').value.trim();
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Client-side validation
    if (!name || !username || !currentPassword) {
        showAlert('Please fill in all required fields.', 'error');
        return;
    }
    
    if (newPassword && newPassword !== confirmPassword) {
        showAlert('New passwords do not match.', 'error');
        return;
    }
    
    if (newPassword && newPassword.length < 6) {
        showAlert('New password must be at least 6 characters long.', 'error');
        return;
    }
    
    // Disable save button to prevent double submission
    const saveBtn = document.getElementById('saveAccountBtn');
    const originalText = saveBtn.textContent;
    saveBtn.textContent = 'Saving...';
    saveBtn.disabled = true;
    
    // Send data to server
    fetch('update_account.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            name: name,
            username: username,
            current_password: currentPassword,
            new_password: newPassword
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeAccountModal();
            
            // Update displayed name if it changed
            updateDisplayedUserInfo(data.data);
        } else {
            showAlert('Error: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        // Re-enable save button
        saveBtn.textContent = originalText;
        saveBtn.disabled = false;
    });
}

function updateDisplayedUserInfo(userData) {
    // Update any displayed user information on the page
    const profileElements = document.querySelectorAll('.user-name, .admin-name');
    profileElements.forEach(element => {
        if (element) {
            element.textContent = userData.name;
        }
    });
}

function showAlert(message, type = 'info') {
    // Create a simple alert notification
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
    
    // Remove on click
    alertDiv.addEventListener('click', () => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    });
}

// Enhanced Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Profile dropdown functionality
    const profileToggle = document.getElementById('profileToggle');
    const profileMenu = document.getElementById('profileMenu');

    if (profileToggle && profileMenu) {
        profileToggle.addEventListener('click', () => {
            const isExpanded = profileToggle.getAttribute('aria-expanded') === 'true';
            profileToggle.setAttribute('aria-expanded', String(!isExpanded));
            profileMenu.classList.toggle('show');
        });

        // Close dropdown if clicked outside
        window.addEventListener('click', (e) => {
            if (!profileToggle.contains(e.target) && !profileMenu.contains(e.target)) {
                profileToggle.setAttribute('aria-expanded', 'false');
                profileMenu.classList.remove('show');
            }
        });
    }
    
    // Modal event listeners
    const modalOverlay = document.querySelector('.account-modal-overlay');
    const modalClose = document.querySelector('.account-modal-close');
    const cancelBtn = document.getElementById('cancelAccountBtn');
    const saveBtn = document.getElementById('saveAccountBtn');
    
    if (modalOverlay) {
        modalOverlay.addEventListener('click', closeAccountModal);
    }
    
    if (modalClose) {
        modalClose.addEventListener('click', closeAccountModal);
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeAccountModal);
    }
    
    if (saveBtn) {
        saveBtn.addEventListener('click', saveAccountSettings);
    }
    
    // Close with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('accountModal').style.display === 'block') {
            closeAccountModal();
        }
    });
    
    // Form submission with Enter key
    const accountForm = document.getElementById('accountForm');
    if (accountForm) {
        accountForm.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                saveAccountSettings();
            }
        });
    }
    
    // Real-time password confirmation validation
    const newPasswordField = document.getElementById('newPassword');
    const confirmPasswordField = document.getElementById('confirmPassword');
    
    if (newPasswordField && confirmPasswordField) {
        function validatePasswordMatch() {
            const newPass = newPasswordField.value;
            const confirmPass = confirmPasswordField.value;
            
            if (confirmPass && newPass !== confirmPass) {
                confirmPasswordField.setCustomValidity('Passwords do not match');
                confirmPasswordField.style.borderColor = '#dc3545';
            } else {
                confirmPasswordField.setCustomValidity('');
                confirmPasswordField.style.borderColor = '#ddd';
            }
        }
        
        newPasswordField.addEventListener('input', validatePasswordMatch);
        confirmPasswordField.addEventListener('input', validatePasswordMatch);
    }
});
</script>