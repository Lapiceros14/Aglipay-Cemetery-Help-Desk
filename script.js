// ============================================
// SCRIPT FILE (Main Search Page)
// This file controls what happens when the user
// searches for a person on the main page.
// ============================================

// This stores the name of the person the user clicked on.
// It starts as nothing (null).
let selectedName = null;

// ============================================
// SEARCH A PERSON
// This runs when the user clicks the "Search" button.
// It asks the server to find matching people.
// ============================================
function searchPerson() {
    // Get what the user typed into the search box.
    const input = document.getElementById("search").value;

    // Ask the server to find people with that name.
    // We use encodeURIComponent to make the name safe for the web address.
    fetch("search.php?name=" + encodeURIComponent(input))
        .then(response => response.json())   // Turn the answer into a list
        .then(people => {

            // Get the box that will hold the search results.
            const choices = document.getElementById("choices");

            // Clear the box first (remove old results).
            choices.innerHTML = "";

            // Hide the details card (until the user clicks a result).
            document.getElementById("result").classList.add("hidden");

            // If we found at least one person...
            if (people.length > 0) {
                // Loop through every person found.
                people.forEach(person => {
                    // Make a button for each person.
                    const button = document.createElement("button");
                    button.className = "choice-btn";
                    button.innerHTML =
                        "<strong>" + person.Fullname + "</strong><br>" +
                        "Birth: " + person.Birthdate + "<br>" +
                        "Death: " + person.Deathdate;

                    // When the user clicks this button, show the details.
                    button.onclick = function() {
                        showDetails(person);
                    };

                    // Add the button to the results box.
                    choices.appendChild(button);
                });
            } else {
                // No people found.
                alert("No record found.");
            }
        })
        .catch(error => {
            // Something went wrong (like the database is down).
            console.error(error);
            alert("Database connection failed.");
        });
}

// ============================================
// SHOW THE DETAILS OF A PERSON
// This fills the "Grave Details" card with
// the information of the person that was clicked.
// ============================================
function showDetails(person) {
    // Remember which person was selected.
    selectedName = person.Fullname;

    // Clear the search results box.
    document.getElementById("choices").innerHTML = "";

    // Show the details card (remove the "hidden" class).
    document.getElementById("result").classList.remove("hidden");

    // Fill each part of the card with the person's info.
    document.getElementById("name").innerHTML = person.Fullname;
    document.getElementById("birth").innerHTML = person.Birthdate;
    document.getElementById("death").innerHTML = person.Deathdate;
    document.getElementById("section").innerHTML = person.Phase;
    document.getElementById("row").innerHTML = person.Row;
    document.getElementById("column").innerHTML = person.Column;

    // Remember the 3D coordinates of this person.
    // These are used later when we open the 3D map.
    window._lastX = parseFloat(person.X) || 0;
    window._lastY = parseFloat(person.Y) || 0;
    window._lastZ = parseFloat(person.Z) || 0;
}

// ============================================
// VIEW THE 3D MAP
// This opens the 3D map page, showing the location
// of the selected person.
// ============================================
function viewMap() {
    // Only do something if a person was selected.
    if (selectedName != null) {
        // Get the saved coordinates (use 0 if missing).
        let x = window._lastX || 0;
        let y = window._lastY || 0;
        let z = window._lastZ || 0;

        // Go to the map page, carrying the name and coordinates.
        window.location.href =
            "map.html?name=" + encodeURIComponent(selectedName) +
            "&x=" + x + "&y=" + y + "&z=" + z;
    }
}
