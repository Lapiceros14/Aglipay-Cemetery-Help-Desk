// ============================================
// SUPABASE CONNECTION
// ============================================

console.log("SCRIPT.JS IS WORKING");

const supabaseUrl =
    "https://urfmjczbaoavwmhrirmt.supabase.co";

const supabaseKey =
    "sb_publishable_JXMzmYxKQhuD2uh-mmPbrQ_Mo4zgLgV";

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

    const input =
        document.getElementById("search").value.trim();

    // Check if search box is empty
    if (input === "") {

        alert("Please enter a name.");

        return;
    }

    console.log("Searching for:", input);


    // ========================================
    // SEARCH SUPABASE
    // ========================================

    const { data: people, error } =
        await supabase
            .from("cemetery_tb")
            .select("*")
            .ilike(
                "Fullname",
                `%${input}%`
            );


    // ========================================
    // CHECK FOR DATABASE ERROR
    // ========================================

    if (error) {

        console.error(
            "Supabase error:",
            error
        );

        alert(
            "Database connection failed:\n" +
            error.message
        );

        return;
    }


    console.log(
        "Search results:",
        people
    );


    // ========================================
    // GET SEARCH RESULTS CONTAINER
    // ========================================

    const choices =
        document.getElementById("choices");


    // Clear previous results
    choices.innerHTML = "";


    // Hide the details card
    document
        .getElementById("result")
        .classList
        .add("hidden");


    // ========================================
    // DISPLAY SEARCH RESULTS
    // ========================================

    if (people && people.length > 0) {

        people.forEach(person => {

            // Create button
            const button =
                document.createElement("button");


            button.className =
                "choice-btn";


            // Display person's information
            button.innerHTML =
                "<strong>" +
                person.Fullname +
                "</strong><br>" +

                "Birth: " +
                person.Birthdate +
                "<br>" +

                "Death: " +
                person.Deathdate;


            // =================================
            // WHEN RESULT IS CLICKED
            // =================================

            button.onclick = function () {

                showDetails(person);

            };


            // Add button to results
            choices.appendChild(button);

        });


    } else {

        // No records found
        alert("No record found.");

    }

}


// ============================================
// SHOW DETAILS OF SELECTED PERSON
// ============================================

function showDetails(person) {

    // Remember selected person's name
    selectedName =
        person.Fullname;


    // ========================================
    // CLEAR SEARCH RESULTS
    // ========================================

    document
        .getElementById("choices")
        .innerHTML = "";


    // ========================================
    // SHOW DETAILS CARD
    // ========================================

    document
        .getElementById("result")
        .classList
        .remove("hidden");


    // ========================================
    // DISPLAY PERSONAL INFORMATION
    // ========================================

    document
        .getElementById("name")
        .innerHTML =
        person.Fullname;


    document
        .getElementById("birth")
        .innerHTML =
        person.Birthdate;


    document
        .getElementById("death")
        .innerHTML =
        person.Deathdate;


    // ========================================
    // DISPLAY GRAVE LOCATION
    // ========================================

    document
        .getElementById("section")
        .innerHTML =
        person.Phase;


    document
        .getElementById("row")
        .innerHTML =
        person.Row;


    document
        .getElementById("column")
        .innerHTML =
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


    // ========================================
    // DEBUG INFORMATION
    // ========================================

    console.log(
        "Selected person:",
        person
    );


    console.log(
        "Coordinates:",
        {
            X: window._lastX,
            Y: window._lastY,
            Z: window._lastZ
        }
    );

}


// ============================================
// VIEW THE 3D MAP
// ============================================

function viewMap() {

    // Check if a person was selected
    if (selectedName === null) {

        alert(
            "Please select a person first."
        );

        return;
    }


    // ========================================
    // GET SAVED COORDINATES
    // ========================================

    const x =
        window._lastX || 0;


    const y =
        window._lastY || 0;


    const z =
        window._lastZ || 0;


    // ========================================
    // CREATE MAP URL
    // ========================================

    const mapURL =
        "map.html" +

        "?name=" +
        encodeURIComponent(
            selectedName
        ) +

        "&x=" +
        encodeURIComponent(x) +

        "&y=" +
        encodeURIComponent(y) +

        "&z=" +
        encodeURIComponent(z);


    console.log(
        "Opening map:",
        mapURL
    );


    // ========================================
    // OPEN 3D MAP
    // ========================================

    window.location.href =
        mapURL;

}
