<?php
// Initialize variables
$error_message = null;
$owner = null;
$conn = null;

// Check if owner_id is set in the URL
if (!isset($_GET['owner_id']) || empty($_GET['owner_id'])) {
    $error_message = "Error: No owner ID provided.";
} else {
    // Get the owner_id from the URL
    $owner_id = $_GET['owner_id'];

    try {
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'pg_finder');
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        // Fetch owner details from the database
        $sql = "SELECT owner_id, name, email, phone, address, pg_name FROM owners WHERE owner_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $conn->error);
        }

        $stmt->bind_param("i", $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $error_message = "Owner not found.";
        } else {
            $owner = $result->fetch_assoc();
        }

        // Close the statement
        $stmt->close();
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    } finally {
        // Close the connection if it exists
        if ($conn) {
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Owner - PG Finder</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h2 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            color: #7f8c8d;
            font-size: 16px;
        }

        .error-message {
            background-color: #fee2e2;
            border: 1px solid #ef4444;
            color: #b91c1c;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            background-color: #e9ecef;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .profile-img i {
            font-size: 48px;
            color: #95a5a6;
        }

        .contact-info {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 10px;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-item i {
            width: 40px;
            height: 40px;
            background-color: #3498db;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 500;
        }

        .contact-buttons {
    display: flex;
    flex-direction: column;  /* Change to column to stack buttons vertically */
    gap: 10px;
    margin-top: 25px;
}

.contact-btn {
    width: 100%;  /* Make buttons full width */
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: transform 0.2s;
    text-decoration: none;
}

        .contact-btn:hover {
            transform: translateY(-2px);
        }

        .call-btn {
            background-color: #2ecc71;
            color: white;
        }

        .email-btn {
            background-color: #e74c3c;
            color: white;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background-color: #34495e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        @media (max-width: 480px) {
            .container {
                padding: 20px;
            }

            .contact-buttons {
                flex-direction: column;
            }

            .back-btn {
                position: static;
                margin-bottom: 20px;
                display: inline-block;
            }
        }
    </style>
</head>
<body>
    <a href="javascript:history.back()" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back
    </a>

    <div class="container">
    <?php if ($error_message): ?>
        <div class="header">
            <div class="profile-img">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h2>Error</h2>
        </div>
        <div class="error-message">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php elseif ($owner): ?>
        <div class="header">
            <div class="profile-img">
                <i class="fas fa-user"></i>
            </div>
            <h2>Contact Owner</h2>
            <p><?php echo htmlspecialchars($owner['pg_name']); ?></p>
        </div>

        <div class="contact-info">
            <div class="info-item">
                <i class="fas fa-user"></i>
                <div class="info-content">
                    <div class="info-label">Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($owner['name']); ?></div>
                </div>
            </div>

            <div class="info-item">
                <i class="fas fa-envelope"></i>
                <div class="info-content">
                    <div class="info-label">Email Address</div>
                    <div class="info-value"><?php echo htmlspecialchars($owner['email']); ?></div>
                </div>
            </div>

            <div class="info-item">
                <i class="fas fa-phone"></i>
                <div class="info-content">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value"><?php echo htmlspecialchars($owner['phone']); ?></div>
                </div>
            </div>

            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <div class="info-content">
                    <div class="info-label">Address</div>
                    <div class="info-value"><?php echo htmlspecialchars($owner['address']); ?></div>
                </div>
            </div>
        </div>

        <div class="contact-buttons">
            <a href="mailto:<?php echo htmlspecialchars($owner['email']); ?>" class="contact-btn email-btn">
                <i class="fas fa-envelope"></i>
                Send Email
            </a>
        </div>

        <div class="contact-buttons">
            <a href="tel:<?php echo htmlspecialchars($owner['phone']); ?>" class="contact-btn call-btn">
                <i class="fas fa-phone"></i>
                Call Now
            </a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>