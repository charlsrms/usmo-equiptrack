<?php
include 'db_connect.php';

$sql = "SELECT user_id, name, username, password FROM users WHERE role = 'Volunteer'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>" . htmlspecialchars($row['name']) . "</td>
            <td>" . htmlspecialchars($row['username']) . "</td>
            <td>" . htmlspecialchars($row['password']) . "</td>
            <td>
              <a href='edit_volunteer.php?id=" . $row['user_id'] . "'><button>Edit</button></a>
              <a href='remove_volunteer.php?id=" . $row['user_id'] . "' onclick='return confirm(\"Are you sure?\");'><button>Remove</button></a>
            </td>
          </tr>";
  }
} else {
  echo "<tr><td colspan='3'>No volunteers found.</td></tr>";
}

$conn->close();
?>
