<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug point 1
echo "Script started<br>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debug point 2
    echo "POST request received<br>";
    echo "POST data: ";
    print_r($_POST);
    echo "<br>";
    
    if (isset($_POST['review']) && isset($_POST['rating']) && isset($_POST['pg_name'])) {
        // Debug point 3
        echo "All required fields are set<br>";
        
        $review = $_POST['review'];
        $rating = $_POST['rating'];
        $pg_name = $_POST['pg_name'];
        
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'pg_finder');
        
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        echo "Database connected successfully<br>";
        
        try {
            // Prepare statement
            $stmt = $conn->prepare("INSERT INTO reviews (pg_name, review_text, rating, created_at) VALUES (?, ?, ?, NOW())");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            echo "Statement prepared successfully<br>";
            
            // Bind parameters
            if (!$stmt->bind_param("ssi", $pg_name, $review, $rating)) {
                throw new Exception("Binding parameters failed: " . $stmt->error);
            }
            echo "Parameters bound successfully<br>";
            
            // Execute
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            echo "Query executed successfully<br>";
            
            $stmt->close();
            $conn->close();
            
            // Redirect back to details page
            header("Location: details.php?pg_name=" . urlencode($pg_name) . "&success=1");
            exit();
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "Missing required fields<br>";
        echo "POST data received: ";
        print_r($_POST);
    }
} else {
    echo "Not a POST request. Request method was: " . $_SERVER["REQUEST_METHOD"];
}
?>