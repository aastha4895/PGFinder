<?php
// Initialize the session
session_start();

// Include database connection file
$servername = "localhost";
$username = "root"; // Update this if your username is different
$password = ""; // Update this if you have a password
$dbname = "pg_finder";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    // Modified query to also get the user ID
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($inputPassword, $user['password'])) {
            // Start the session and set both username and user_id
            $_SESSION['username'] = $inputUsername;
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit();
        } else {
            $message = "Invalid username or password.";
        }
    } else {
        $message = "Invalid username or password.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="css/style.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
    <style>
         body {
    background-color: #f4f4f4; /* Light background */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Modern font */
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}
.login-container {
    background-color: #fff; /* White container */
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    padding: 30px;
    width: 100%;
    max-width: 400px;
    text-align: center;
}
.login-container h2 {
    color: #333; /* Dark gray heading */
    margin-bottom: 20px;
}
.input-group label {
    display: block; /* Labels on their own line */
    margin-bottom: 5px;
    font-weight: bold;
    color: #555; /* Slightly darker gray */
}

.input-group input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd; /* Light gray border */
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 16px;
}

.input-group input:focus {
    outline: none; /* Remove default focus outline */
    border-color: #007bff; /* Blue focus color */
}

.login-container button {
    width: 100%;
    padding: 12px 20px;
    background-color: #007bff; /* Blue button */
    color: #fff;
    border: none;
    border-radius : 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.login-container button:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

.login-container p {
    margin-top: 15px;
    font-size: 14px;
    color: #666; /* Gray text */
}

.password-container {
    position: relative;
    width: 100%;
}

#toggle-password {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 20px;
}
</style>

</head>
<body>
    <div class="login-container">
        <h2>Login to PG Finder</h2>
        <form action="testphp.php" method="POST" autocomplete="off">
    <div class="input-group">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required autocomplete="username">
    </div>
    <div class="input-group">
        <label for="password">Password:</label>
        <div class="password-container">
            <input type="password" name="password" id="password" required autocomplete="current-password">
            <span onclick="togglePassword()" id="toggle-password">👁️</span>
        </div>
    </div>
    <button type="submit">Login</button>
    <p class="message">Not registered? <a href="register.php">Create an account</a></p>
</form>
    </div>
    <script>
    function togglePassword() {
        const passwordField = document.getElementById("password");
        const toggleIcon = document.getElementById("toggle-password");
        if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleIcon.textContent = "🙈"; // Closed eye icon
        } else {
            passwordField.type = "password";
            toggleIcon.textContent = "👁️"; // Open eye icon
        }
    }
</script>


</body>
</html>



