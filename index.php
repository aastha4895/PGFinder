<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root"; // Default username for XAMPP
$password = ""; // Default password for XAMPP (empty)
$database = "pg_finder"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 


// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: testphp.php");
    exit();
}

// Fetch PG data from the database
$sql = "SELECT * FROM rooms WHERE location LIKE '%Ghaziabad%'";
$result = $conn->query($sql);
// Check if query was successful
if (!$result) {
    die("Error executing query: " . $conn->error);
}

// Get user data
$user_id = $_SESSION['user_id'];

// Check if user info exists
$check_query = "SELECT u.username, ui.* 
                FROM users u 
                LEFT JOIN user_info ui ON u.id = ui.user_id 
                WHERE u.id = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$user_info_exists = isset($user_data['full_name']);

// Add this at the very beginning of your index.php, right after the existing PHP code
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your existing head content -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PG Finder</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
    <style>
        /* Styling for the sorting section */
        #sortSection {
            margin: 20px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            display: flex;
            align-items: center;
        }

        #sortSection label {
            margin-right: 10px;
            font-weight: bold;
        }

        #sortOptions {
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
        }

        #sortSection button {
            padding: 6px 12px;
            background-color: #4c81af;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        #sortSection {
    margin: 20px auto; /* Centers the section horizontally */
    padding: 10px;
    background-color: #f9f9f9;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center; /* Ensures content is centered */
    width: 50%; /* Adjust width as needed */
}

        #sortSection button:hover {
            background-color: #4c81af;
        }

        /* Search bar styling */
        #searchBarContainer {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        #searchBar {
            position: relative;
            width: 50%;
        }

        #search {
            width: 100%;
            padding: 10px 40px 10px 15px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 20px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.2);
        }

        #searchIcon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            cursor: pointer;
        }

        /* PG list styling */
        .pg-item {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 10px 0;
            background-color: #f9f9f9;
        }
        .pg-item img {
    width: 100%; /* Ensures the image takes up the width of the parent div */
    height: auto; /* Maintains aspect ratio */
    border-radius: 5px; /* Optional: for rounded corners */
}
        /* Add these styles */
        .blur-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: 1000;
            display: none;
        }

        .user-dialog {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            z-index: 1001;
            display: none;
            width: 300px;
        }

        .profile-section {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 100;
}

.profile-icon {
    cursor: pointer;
    padding: 10px;
    background: #007BFF; /* Bootstrap Primary Color */
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center; /* Center the icon */
    color: white; /* Icon color */
    transition: background 0.3s, transform 0.3s; /* Smooth transition */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow for depth */
}

.profile-icon:hover {
    background: #0056b3; /* Darker shade on hover */
    transform: scale(1.1); /* Slightly enlarge on hover */
}

.profile-dropdown {
    position: absolute;
    right: 0;
    top: 100%;
    background: white;
    min-width: 200px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    padding: 15px;
    display: none;
    z-index: 200; /* Ensure dropdown is above other elements */
}

.profile-dropdown.show {
    display: block;
}

.profile-dropdown p {
    margin: 0; /* Remove default margin */
    padding: 10px 0; /* Add padding for spacing */
    color: #333; /* Text color */
    cursor: pointer; /* Pointer cursor for clickable items */
    transition: background 0.3s; /* Smooth transition */
}

.profile-dropdown p:hover {
    background: #f0f0f0; /* Light background on hover */
}
nav ul {
    list-style: none;
    display: flex;
    position: relative; /* Needed for absolute positioning of dropdowns */
}

nav ul li {
    position: relative; /* Needed for absolute positioning of dropdowns */
    margin: 0 15px;
}

nav ul li a {
    color: white; /* Change to your desired color */
    text-decoration: none;
    padding: 10px;
    display: block; /* Make the entire area clickable */
}

.dropdown-content {
    display: none; /* Hide dropdown by default */
    position: absolute; /* Position it below the parent */
    background-color:rgb(0, 0, 0); /* Match the header color */
    min-width: 200px; /* Set a minimum width */
    z-index: 1; /* Ensure it appears above other content */
    padding: 10px; /* Add some padding */
    border-radius: 4px; /* Rounded corners */
}

nav ul li:hover .dropdown-content {
    display: block; /* Show dropdown on hover */
}

.dropdown-content p {
    margin: 5px 0; /* Add some margin between items */
    color: white; /* Text color */
}
header {
    background: #4c81af;
    color: #fff;
    padding: 20px;
    text-align: center; /* Center the header text */
}

nav {
    display: flex; /* Use flexbox for the nav */
    justify-content: center; /* Center the nav items */
}

nav ul {
    list-style: none;
    padding: 0;
    display: flex; /* Keep the items in a row */
    margin: 0; /* Remove default margin */
}

nav ul li {
    position: relative; /* Needed for absolute positioning of dropdowns */
    margin: 0 15px; /* Space between items */
}

nav ul li a {
    color: white; /* Text color */
    text-decoration: none;
    padding: 10px;
    display: block; /* Make the entire area clickable */
}

.dropdown-content {
    display: none; /* Hide dropdown by default */
    position: absolute; /* Position it below the parent */
    background-color:rgb(0, 0, 0); /* Match the header color */
    min-width: 200px; /* Set a minimum width */
    z-index: 1; /* Ensure it appears above other content */
    padding: 10px; /* Add some padding */
    border-radius: 4px; /* Rounded corners */
}

nav ul li:hover .dropdown-content {
    display: block; /* Show dropdown on hover */
}

.dropdown-content p {
    margin: 5px 0; /* Add some margin between items */
    color: white; /* Text color */
}
    </style>
</head>
<body>
    <!-- Add this right after your <body> tag -->
    <div class="profile-section">
        <div class="profile-icon" onclick="toggleDropdown()">
            <?php echo '@' . htmlspecialchars($user_data['username']); ?> 👤
        </div>
        <div class="profile-dropdown" id="profileDropdown">
            <?php if ($user_info_exists): ?>
                <h3>Profile Information</h3>
                <p>Name: <?php echo htmlspecialchars($user_data['full_name']); ?></p>
                <p>DOB: <?php echo htmlspecialchars($user_data['dob']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($user_data['phone']); ?></p>
                <hr>
            <?php endif; ?>
            <a href="logout.php" style="color: red; text-decoration: none;">Logout</a>
        </div>
    </div>

    <!-- Add these elements before closing body tag -->
    <div class="blur-overlay" id="blurOverlay"></div>
    <div class="user-dialog" id="userDialog">
        <h2>Complete Your Profile</h2>
        <form id="userInfoForm" method="POST" action="save_user_info.php">
            <div style="margin-bottom: 15px;">
                <label for="fullName">Full Name:</label>
                <input type="text" id="fullName" name="fullName" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" required style="width: 100%; padding: 8px; margin-top: 5px;">
            </div>
            <button type="submit" style="width: 100%; padding: 10px; background: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">Save Information</button>
        </form>
    </div>

    <!-- Your existing page content -->
    <header>
        <h1>Welcome to PG Finder</h1>
        <nav>
            <ul>
                <li><a href="homepage.html">Home</a></li>
                <li>
                <a href="#" class="dropdown-toggle">Contact</a>
                <div class="dropdown-content">
                    <p>Email Address: support@pgfinder.com</p>
                    <p>Phone Number: 9958024014</p>
                    <p>Office Address: 123, Main Street, Delhi, India</p>
                </div>
            </li>
            <li>
                <a href="#" class="dropdown-toggle">About Us</a>
                <div class="dropdown-content">
                    <p>PG Finder is a user-friendly platform designed to help students and working professionals find the perfect paying guest accommodations.</p>
                </div>
            </li>
                
            </ul>
        </nav>
    </header>
<!-- Search Bar -->
    <div id="searchBarContainer">
        <div id="searchBar">
            <input type="text" id="search" placeholder="Search for PGs..." onkeyup="searchPGs()">
            <span id="searchIcon">&#128269;</span> <!-- Magnifying glass icon -->
        </div>
    </div>

    <!-- Sorting Section -->
    <div id="sortSection">
        <label for="sortOptions">Sort By:</label>
        <select id="sortOptions">
            <option value="">Select</option>
            <option value="location">Location</option>
            <option value="rent">Rent</option>
        </select>
        <button onclick="sortPGs()">Sort</button>
    </div>

    <!-- PG Listings Section -->
<div id="pg-list">
    <?php
    $sql = "SELECT * FROM rooms";
    $result = $conn->query($sql);

    if ($result === false) {
        echo "Database query error: " . $conn->error;
    } elseif ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="pg-item" data-location="' . htmlspecialchars($row["location"]) . '" data-rent="' . htmlspecialchars($row["rent"]) . '">';
            echo '<h2>' . htmlspecialchars($row['pg_name']) . '</h2>';
            echo '<p>Location: ' . htmlspecialchars($row["location"]) . '</p>';
            echo '<p>Rent: Rs. ' . htmlspecialchars($row["rent"]) . '</p>';
            
            // Add a check for image URL
            if (!empty($row["image_url"])) {
                echo '<img src="' . htmlspecialchars($row["image_url"]) . '" alt="' . htmlspecialchars($row["room_no"]) . '">';
            } else {
                echo '<p>No image available</p>';
            }
            
            // Inside your while loop where you display PG items
            echo '<button onclick="window.location.href=\'details.php?pg_name=' . urlencode($row["pg_name"]) . '\'">View Details</button>';
            echo '</div>';
        }
    } else {
        echo '<p>No rooms available in the database.</p>';
    }
    ?>
</div>
<!-- Verified PGs Section -->
     <section id="verified-pgs">
        <h2>Verified PG Accommodations</h2>
        <div class="pg-list">
            <!-- This section can also be filled dynamically if you have a different table for verified PGs -->
        </div>
    </section>

    <!-- Reviews and Ratings Section -->
    <section id="reviews">
        <h2>Reviews from Students</h2>
        <blockquote>
            "The PG Finder made it so easy for me to find a safe and comfortable place to stay!"
            <cite>- Student A</cite>
        </blockquote>
        <blockquote>
            "I love the detailed listings and the reviews from other students!"
            <cite>- Student B</cite>
        </blockquote>
    </section>

    <!-- Secure Booking Section -->
    <section id="booking">
        <h2>Secure Booking & Payment</h2>
        <p>Book your preferred PG accommodation with ease and confidence. We offer secure online booking and payment options.</p>
    </section>

    <!-- Customer Support Section -->
    <section id="support">
        <h2>Customer Support</h2>
        <p>Need help? Contact our dedicated customer support team through chat, phone, or email.</p>
    </section>

    <!-- Safety Features Section -->
    <section id="safety">
        <h2>Safety Features</h2>
        <p>All our listed PGs meet high safety standards, including secure entrances, CCTV, and fire safety measures.</p>
    </section>

    <footer>
        <p>&copy; 2024 PG Finder</p>
    </footer>

    <script>
        function sortPGs() {
            const select = document.getElementById('sortOptions'); // Get the select element
            const sortBy = select.value; // Get the selected sorting option
            const pgList = document.getElementById('pg-list'); // Get the container for PG items
            const pgItems = Array.from(pgList.getElementsByClassName('pg-item')); // Get all PG items

            // Sort the PG items based on the selected option
            pgItems.sort((a, b) => {
                if (sortBy === 'location') {
                    return a.dataset.location.localeCompare(b.dataset.location); // Sort by location
                } else if (sortBy === 'rent') {
                    return parseInt(a.dataset.rent) - parseInt(b.dataset.rent); // Sort by rent
                }
                return 0;
            });

            // Clear the existing list and append sorted items
            pgList.innerHTML = '';
            pgItems.forEach(item => pgList.appendChild(item)); // Append sorted items
        }
        function showDialog() {
            document.getElementById('blurOverlay').style.display = 'block';
            document.getElementById('userDialog').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function hideDialog() {
            document.getElementById('blurOverlay').style.display = 'none';
            document.getElementById('userDialog').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function toggleDropdown() {
            document.getElementById('profileDropdown').classList.toggle('show');
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.profile-icon')) {
                var dropdowns = document.getElementsByClassName('profile-dropdown');
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }

        <?php if (!$user_info_exists): ?>
            // Show dialog on page load for first-time users
            window.onload = showDialog;
        <?php endif; ?>
    </script>
</body>
</html>