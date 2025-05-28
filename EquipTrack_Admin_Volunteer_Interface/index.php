<?php
session_start();
include 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->execute([$username]);
  $user = $stmt->fetch();

  if ($user && $user['password'] === $password) {
    if ($user['disabled_at'] !== null) {
      $error = 'Account is disabled.';
    } else {
      $_SESSION['user_id'] = $user['user_id'];
      $_SESSION['role'] = $user['role'];
      $_SESSION['name'] = $user['name'];

      if ($user['role'] === 'admin') {
        header('Location: admin-dashboard.php');
        exit;
      } elseif ($user['role'] === 'volunteer') {
        header('Location: volunteer-dashboard.php');
        exit;
      } else {
        $error = 'Invalid role.';
      }
    }
  } else {
    $error = 'Invalid username or password.';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Equip Track - Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      margin: 0;
      background: #f2f6fc;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .login-container {
      background: #fff;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }

    .login-container h2 {
      text-align: center;
      margin-bottom: 30px;
      color: #1a237e;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 6px;
      font-weight: 500;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      outline: none;
      transition: 0.3s;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #1565c0;
    }

    .login-btn {
      width: 100%;
      padding: 12px;
      background-color: #1565c0; /* UMak Blue */
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }

    .login-btn:hover {
      background-color: #0d47a1;
    }

    .footer-text {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
      color: #555;
    }

    .footer-text a {
      color: #1565c0;
      text-decoration: none;
    }

    .footer-text a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div style="text-align: center; margin-bottom: 20px;">
      <img src="USMO Logo.png" alt="USMO Logo" style="max-width: 150px; height: auto;" />
    </div>
    <h2>Equip Track Login</h2>
    <?php if ($error): ?>
      <div style="color: red; margin-bottom: 20px; text-align: center;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label for="username">Email or Username</label>
        <input type="text" id="username" name="username" required />
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required />
      </div>
      <button type="submit" class="login-btn">Login</button>
    </form>
    <div class="footer-text">
      Only registered Admins and Volunteers can log in.
    </div>
  </div>
</body>

</html>
