<?php
$conn = new mysqli("localhost", "root", "", "pg_finder");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sort = $_GET['sort'];
$orderBy = '';

if ($sort === 'location') {
    $orderBy = 'ORDER BY location';
} elseif ($sort === 'rent') {
    $orderBy = 'ORDER BY rent';
}

$sql = "SELECT * FROM rooms $orderBy";
$result = $conn->query($sql);

$pgs = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $pgs[] = $row;
    }
}

echo json_encode($pgs);
$conn->close();
?>
