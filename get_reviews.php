<?php
$mysqli = new mysqli("localhost", "root", "", "pg_finder");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$pg_id = $_GET['pg_id'];
$sql = "SELECT * FROM reviews WHERE pg_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $pg_id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode($reviews);
$mysqli->close();
?>
<?php
$conn = new mysqli("localhost", "root", "", "pg_finder");

$pg_id = $_GET['pg_id'];
$result = $conn->query("SELECT review_text, rating FROM reviews WHERE pg_id = $pg_id");

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode($reviews);
?>
