<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$mysqli = new mysqli("localhost", "root", "", "pg_finder");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Example query to fetch room information
$query = "SELECT * FROM rooms"; // Adjust this query based on your requirements
$result = $mysqli->query($query);

// HTML structure
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h1>Welcome to the Dashboard</h1>

    <h2>Available Rooms</h2>
    <?php
    if ($result->num_rows > 0) {
        // Output data of each room
        echo "<table border='1'>
                <tr>
                    <th>Room ID</th>
                    <th>Room Number</th>
                    <th>Room Type</th>
                    <th>Availability</th>
                    <th>Owner ID</th>
                </tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["room_id"] . "</td>
                    <td>" . $row["room_number"] . "</td>
                    <td>" . $row["room_type"] . "</td>
                    <td>" . ($row["availability"] ? 'Available' : 'Not Available') . "</td>
                    <td>" . $row["owner_id"] . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No rooms available.</p>";
    }

    // Close the database connection
    $mysqli->close();
    ?>
</body>
</html>
