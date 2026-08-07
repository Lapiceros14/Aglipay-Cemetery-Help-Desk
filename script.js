// ============================================
// SUPABASE CONNECTION
// ============================================

const supabaseUrl = "https://urfmjczbaoavwmhrirmt.supabase.co";

const supabaseKey = "sb_publishable_JXMzmYxKQhuD2uh-mmPbrQ_Mo4zgLgV";

const supabase = window.supabase.createClient(
    supabaseUrl,
    supabaseKey
);

let selectedName = null;

// ============================================
// SELECTED PERSON
// ============================================

let selectedName = null;


// ============================================
// SEARCH A PERSON
// ============================================

async function searchPerson() {

    const input = document
        .getElementById("search")
        .value
        .trim();

    if (input === "") {
        alert("Please enter a name.");
        return;
    }

    const { data: people, error } = await supabase
        .from("cemetery_tb")
        .select("*")
        .ilike("Fullname", `%${input}%`);

    if (error) {
        console.error(error);
        alert("Database connection failed.");
        return;
    }

    const choices =
        document.getElementById("choices");

    choices.innerHTML = "";

    document
        .getElementById("result")
        .classList
        .add("hidden");

    if (people.length > 0) {

        people.forEach(person => {

            const button =
                document.createElement("button");

            button.className = "choice-btn";

            button.innerHTML =
                "<strong>" + person.Fullname + "</strong><br>" +
                "Birth: " + person.Birthdate + "<br>" +
                "Death: " + person.Deathdate;

            button.onclick = function() {
                showDetails(person);
            };

            choices.appendChild(button);

        });

    } else {

        alert("No record found.");

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
