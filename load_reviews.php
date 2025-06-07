<?php
$conn = new mysqli('localhost', 'root', '', 'pg_finder');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$pg_id = $_GET['pg_id'];
$sql = "SELECT * FROM reviews WHERE pg_id = $pg_id";
$result = $conn->query($sql);

$reviews = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
}
echo json_encode($reviews);
$conn->close();
?>
