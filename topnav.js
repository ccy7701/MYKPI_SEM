// Javascript for the responsive Topnav

document.addEventListener("DOMContentLoaded", function () {
    const menuToggle = document.getElementById("topnav-collapse-btn");

    // Add click event listener for mouse interaction
    menuToggle.addEventListener("click", function (event) {
        event.preventDefault(); // Prevent default anchor behavior
        toggleMenu();
    });

    // Function to toggle the responsive class on the topnav
    function toggleMenu() {
        const topnav = document.getElementById("myTopnav");
        if (topnav.className === "topnav") {
            topnav.className += " responsive";
        } else {
            topnav.className = "topnav";
        }
    }
});
