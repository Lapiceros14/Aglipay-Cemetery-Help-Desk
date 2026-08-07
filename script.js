// ============================================
// SUPABASE CONNECTION
// ============================================

console.log("SCRIPT.JS IS WORKING");

const supabaseUrl =
    "https://urfmjczbaoavwmhrirmt.supabase.co";

const supabaseKey =
    "sb_publishable_JXMzmYxKQhuD2uh-mmPbrQ_Mo4zgLgV";

const db =
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

    if (input === "") {
        alert("Please enter a name.");
        return;
    }

    console.log("Searching for:", input);

    const { data: people, error } =
        await db
            .from("cemetery_tb")
            .select("*")
            .ilike(
                "Fullname",
                `%${input}%`
            );

    if (error) {

        console.error("Supabase error:", error);

        alert(
            "Database connection failed:\n" +
            error.message
        );

        return;
    }

    console.log("Search results:", people);

    const choices =
        document.getElementById("choices");

    choices.innerHTML = "";

    document
        .getElementById("result")
        .classList
        .add("hidden");


    if (people && people.length > 0) {

        people.forEach(person => {

            const button =
                document.createElement("button");

            button.className =
                "choice-btn";

            button.innerHTML =
                "<strong>" +
                person.Fullname +
                "</strong><br>" +

                "Birth: " +
                person.Birthdate +
                "<br>" +

                "Death: " +
                person.Deathdate;


            button.onclick = function () {

                showDetails(person);

            };


            choices.appendChild(button);

        });

    } else {

        alert("No record found.");

    }

}


// ============================================
// SHOW DETAILS
// ============================================

function showDetails(person) {

    selectedName =
        person.Fullname;


    document
        .getElementById("choices")
        .innerHTML = "";


    document
        .getElementById("result")
        .classList
        .remove("hidden");


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


    window._lastX =
        parseFloat(person.X) || 0;


    window._lastY =
        parseFloat(person.Y) || 0;


    window._lastZ =
        parseFloat(person.Z) || 0;


    console.log(
        "Selected person:",
        person
    );


    console.log(
        "Coordinates:",
        window._lastX,
        window._lastY,
        window._lastZ
    );

}


// ============================================
// VIEW 3D MAP
// ============================================

function viewMap() {

    if (selectedName === null) {

        alert(
            "Please select a person first."
        );

        return;
    }


    const x =
        window._lastX || 0;

    const y =
        window._lastY || 0;

    const z =
        window._lastZ || 0;


    const mapURL =
        "map.html" +

        "?name=" +
        encodeURIComponent(selectedName) +

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


    window.location.href =
        mapURL;

}
