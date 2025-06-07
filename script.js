// script.js

// Function to get PG details from the URL and display them
function displayPGDetails() {
    // Get URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const pgName = urlParams.get('name');
    const location = urlParams.get('location');
    const rent = urlParams.get('rent');
    const amenities = urlParams.get('amenities');

    // Check if the parameters exist
    if (pgName && location && rent && amenities) {
        // Update the details section with the PG info
        document.querySelector('#pg-name').innerText = pgName;
        document.querySelector('#pg-location').innerText = 'Location: ' + location;
        document.querySelector('#pg-rent').innerText = 'Rent: ' + rent;
        document.querySelector('#pg-amenities').innerText = 'Amenities: ' + amenities;
    } else {
        // Handle case where parameters are missing
        document.querySelector('#pg-name').innerText = 'PG details not found';
        document.querySelector('#pg-location').innerText = '';
        document.querySelector('#pg-rent').innerText = '';
        document.querySelector('#pg-amenities').innerText = '';
    }
}

// Call the function when the page loads
document.addEventListener('DOMContentLoaded', displayPGDetails);

function searchPGs() {
    const input = document.getElementById('search').value.toLowerCase();
    const pgItems = document.getElementsByClassName('pg-item');

    Array.from(pgItems).forEach(item => {
        const pgName = item.getElementsByTagName('h2')[0].innerText.toLowerCase();
        const location = item.getElementsByTagName('p')[0].innerText.toLowerCase();

        if (pgName.includes(input) || location.includes(input)) {
            item.style.display = ''; // Show the item
        } else {
            item.style.display = 'none'; // Hide the item
        }
    });
}
function submitReview(pgId) {
    const username = document.getElementById(`username-${pgId}`).value;
    const rating = document.getElementById(`rating-${pgId}`).value;
    const comment = document.getElementById(`comment-${pgId}`).value;

    // AJAX call to submit the review to the server
    fetch('submit_review.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ pg_id: pgId, username, rating, comment })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadReviews(pgId); // Refresh the reviews section
        } else {
            alert('Error submitting review');
        }
    });
}
// Function to load and display reviews
function loadReviews(pgId) {
    fetch(`get_reviews.php?pg_id=${pgId}`) // Ensure you have a PHP script to get reviews based on PG ID
        .then(response => response.json())
        .then(data => {
            const reviewsList = document.getElementById('reviews-list');
            reviewsList.innerHTML = ''; // Clear existing reviews
            data.forEach(review => {
                const reviewElement = document.createElement('div');
                reviewElement.classList.add('review');
                reviewElement.innerHTML = `
                    <p><strong>Rating: ${review.rating} Stars</strong></p>
                    <p>${review.review_text}</p>
                `;
                reviewsList.appendChild(reviewElement);
            });
        })
        .catch(error => console.error('Error loading reviews:', error));
}

// Call this function when the page loads
document.addEventListener('DOMContentLoaded', () => {
    const pgId = 1; // Replace with the actual PG ID dynamically
    loadReviews(pgId);
});
document.getElementById('review-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from submitting normally

    const reviewText = document.getElementById('review_text').value;
    const rating = document.getElementById('rating').value;
    const pgId = 1; // Replace with the actual PG ID dynamically

    fetch('submit_review.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ pg_id: pgId, review_text: reviewText, rating: rating })
    })
    .then(response => response.json())
    .then(data => {
        // Reload reviews after submitting
        loadReviews(pgId);
        document.getElementById('review-form').reset(); // Clear the form
    })
    .catch(error => console.error('Error submitting review:', error));
});
function sortPGs() {
    const sortOption = document.getElementById('sortOptions').value;
    const pgList = document.getElementById('pg-list');
    const pgItems = Array.from(pgList.getElementsByClassName('pg-item'));

    // If no option is selected, return without doing anything
    if (!sortOption) {
        alert("Please select a sorting option!");
        return;
    }

    // Perform sorting based on the selected option
    pgItems.sort((a, b) => {
        if (sortOption === 'location') {
            return a.dataset.location.localeCompare(b.dataset.location);
        } else if (sortOption === 'rent') {
            return parseInt(a.dataset.rent) - parseInt(b.dataset.rent);
        }
    });

    // Re-render the sorted PG items in the DOM
    pgList.innerHTML = ''; // Clear existing items
    pgItems.forEach(item => pgList.appendChild(item)); // Append sorted items
}

