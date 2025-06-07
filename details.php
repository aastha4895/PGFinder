<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PG Details</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #4c81af;
            color: white;
            padding: 15px;
            text-align: center;
        }

        #pg-details {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        #pg-images img {
            max-width: 100%;
            height: auto;
            margin: 10px 0;
            border-radius: 5px;
        }

        button {
            background-color: #4c81af;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            display: block;
            width: 100%;
        }

        button:hover {
            background-color: #4c81af;
        }

        .review-section, #reviews-section {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            border: 2px solid #4c81af;
        }

        .review-item {
            background-color: #ffffff;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .review-item strong {
            color: #333;
        }

        #review-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        #review-form input, #review-form textarea {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        #review-form button {
            width: auto;
            align-self: flex-start;
        }
        .contact-owner-btn {
    background-color: #4CAF50; /* Green background */
    color: white; /* White text */
    padding: 10px 20px; /* Some padding around the text */
    border: none; /* Remove border */
    border-radius: 5px; /* Rounded corners */
    font-size: 16px; /* Font size */
    cursor: pointer; /* Pointer cursor on hover */
    transition: background-color 0.3s ease; /* Smooth transition for background color */
}

.contact-owner-btn:hover {
    background-color: #45a049; /* Slightly darker green when hovered */
}
.contact-owner-btn {
    text-decoration: none;  /* Remove underline from button */
}

a {
    text-decoration: none;  /* Remove underline from the link itself */
}

    </style>
</head>
<body>
    <header>
        <h1>PG Details</h1>
    </header>

    <section id="pg-details">
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);   
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'pg_finder');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if (isset($_GET['pg_name'])) {
            $pg_name = htmlspecialchars($_GET['pg_name']);
            echo '<h2 style="text-align: center;">' . $pg_name . '</h2>';
        
            // Modified query to join with owners table
            $stmt = $conn->prepare("
                SELECT d.*, o.owner_id 
                FROM details d 
                JOIN owners o ON d.pg_name = o.pg_name 
                WHERE d.pg_name = ?
            ");
            $stmt->bind_param("s", $pg_name);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $owner_id = $row['owner_id']; // Now we have the owner_id
                
                echo '<div id="pg-images">';
                echo '<img src="' . $row['image1'] . '" alt="PG Image">';
                echo '<img src="' . $row['image2'] . '" alt="PG Image">';
                echo '<img src="' . $row['image3'] . '" alt="PG Image">';
                echo '</div>';
        
                echo '<p><span style="font-weight: bold;">Description:</span> ' . $row['description'] . '</p>';
                
                // Now the Contact Owner button will work correctly
                echo '<a href="owner_contact.php?owner_id=' . htmlspecialchars($owner_id) . '">';
                echo '<button class="contact-owner-btn">Contact Owner</button>';
                echo '</a>';
            } else {
                echo '<p>No details found for this PG.</p>';
            }
        } else {
            echo '<p>No PG selected.</p>';
        }
        ?>


        
        <div id="reviews-section">
        <h3>Reviews</h3>
            <?php
            // Fetch reviews from the database
            $stmt = $conn->prepare("SELECT * FROM reviews WHERE pg_name = ? ORDER BY created_at DESC");
            $stmt->bind_param("s", $pg_name);
            $stmt->execute();
            $reviews_result = $stmt->get_result();

            if ($reviews_result->num_rows > 0) {
                while ($review = $reviews_result->fetch_assoc()) {
                    echo '<div class="review-item">';
                    echo '<strong>Rating: ' . htmlspecialchars($review['rating']) . '/5</strong>';
                    echo '<p>' . htmlspecialchars($review['review_text']) . '</p>';
                    echo '<small>Submitted on: ' . htmlspecialchars($review['created_at']) . '</small>';
                    echo '</div>';
                }
            } else {
                echo '<p>No reviews yet.</p>';
            }
            ?>
        </div>

        <div class="review-section">
    <h2>Submit Your Review</h2>
    
    <form id="review-form" method="POST" action="./submit_review.php">
        <?php if(isset($pg_name)): ?>
            <input type="hidden" name="pg_name" value="<?php echo htmlspecialchars($pg_name); ?>">
        <?php endif; ?>
        <textarea name="review" placeholder="Write your review here..." required></textarea>
        <input type="number" name="rating" min="1" max="5" placeholder="Rate (1-5)" required>
        <button type="submit">Submit Review</button>
    </form>

    <script>
        // Standalone form validation
        document.getElementById('review-form').addEventListener('submit', function(e) {
            // Prevent the default form submission
            e.preventDefault();
            
            // Log form data
            console.log('Form submitted');
            console.log('PG Name:', this.querySelector('input[name="pg_name"]')?.value);
            console.log('Review:', this.querySelector('textarea[name="review"]')?.value);
            console.log('Rating:', this.querySelector('input[name="rating"]')?.value);
            
            // If everything is valid, submit the form
            this.submit();
        });
    </script>
</div>
    </section>

    <footer>
        <p>&copy; 2024 PG Finder</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>

