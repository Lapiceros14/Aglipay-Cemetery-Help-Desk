// ============================================
// SUPABASE CONNECTION
// ============================================

const supabaseUrl =
    'https://urfmjczbaoavwmhrirmt.supabase.co';

const supabaseKey =
    'YOUR_PUBLISHABLE_KEY';

const supabase =
    window.supabase.createClient(
        supabaseUrl,
        supabaseKey
    );


// ============================================
// SELECTED PERSON
// ============================================

let selectedName = null;


// ============================================
// SEARCH A PERSON
// ============================================

async function searchPerson() {

    // Get what the user typed into the search box.
    const input =
        document.getElementById("search").value.trim();


    // Don't search if the input is empty.
    if (input === "") {

        alert("Please enter a name.");

        return;
    }


    try {

        // ========================================
        // SEARCH SUPABASE DATABASE
        // ========================================

        const { data: people, error } =
            await supabase
                .from("cemetery_tb")
                .select("*")
                .ilike("Fullname", `%${input}%`);


        // If Supabase returns an error.
        if (error) {

            console.error("Supabase error:", error);

            alert("Database connection failed.");

            return;
        }


        // ========================================
        // GET SEARCH RESULTS CONTAINER
        // ========================================

        const choices =
            document.getElementById("choices");


        // Clear old search results.
        choices.innerHTML = "";


        // Hide details card.
        document
            .getElementById("result")
            .classList.add("hidden");


        // ========================================
        // DISPLAY RESULTS
        // ========================================

        if (people && people.length > 0) {

            people.forEach(person => {

                // Create button for each person.
                const button =
                    document.createElement("button");


                button.className = "choice-btn";


                // Display person's information.
                button.innerHTML =
                    "<strong>" +
                    person.Fullname +
                    "</strong><br>" +

                    "Birth: " +
                    person.Birthdate +
                    "<br>" +

                    "Death: " +
                    person.Deathdate;


                // When clicked, show details.
                button.onclick = function() {

                    showDetails(person);

                };


                // Add button to results.
                choices.appendChild(button);

            });

        } else {

            // No matching record.
            alert("No record found.");

        }


    } catch (error) {

        console.error("Database error:", error);

        alert("Database connection failed.");

    }

}


// ============================================
// SHOW DETAILS OF SELECTED PERSON
// ============================================

function showDetails(person) {

    // Remember selected person's name.
    selectedName = person.Fullname;


    // Clear search results.
    document
        .getElementById("choices")
        .innerHTML = "";


    // Show details card.
    document
        .getElementById("result")
        .classList
        .remove("hidden");


    // ========================================
    // DISPLAY PERSONAL INFORMATION
    // ========================================

    document.getElementById("name").innerHTML =
        person.Fullname;

    document.getElementById("birth").innerHTML =
        person.Birthdate;

    document.getElementById("death").innerHTML =
        person.Deathdate;


    // ========================================
    // DISPLAY GRAVE LOCATION
    // ========================================

    document.getElementById("section").innerHTML =
        person.Phase;

    document.getElementById("row").innerHTML =
        person.Row;

    document.getElementById("column").innerHTML =
        person.Column;


    // ========================================
    // SAVE 3D COORDINATES
    // ========================================

    window._lastX =
        parseFloat(person.X) || 0;

    window._lastY =
        parseFloat(person.Y) || 0;

    window._lastZ =
        parseFloat(person.Z) || 0;


    // Debugging
    console.log("Selected person:", person);

    console.log(
        "Coordinates:",
        window._lastX,
        window._lastY,
        window._lastZ
    );

}


// ============================================
// VIEW THE 3D MAP
// ============================================

function viewMap() {

    // Only continue if a person was selected.
    if (selectedName != null) {

        // Get saved coordinates.
        const x =
            window._lastX || 0;

        const y =
            window._lastY || 0;

        const z =
            window._lastZ || 0;


        // Open the 3D map.
        window.location.href =
            "map.html?name=" +
            encodeURIComponent(selectedName) +

            "&x=" +
            encodeURIComponent(x) +

            "&y=" +
            encodeURIComponent(y) +

            "&z=" +
            encodeURIComponent(z);

    } else {

        alert("Please select a person first.");

    }

}
