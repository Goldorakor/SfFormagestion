/* https://www.w3schools.com/howto/howto_js_responsive_navbar_dropdown.asp */

/* Toggle between adding and removing the "responsive" class to topnav when the user clicks on the icon */
function myFunction() {
    var x = document.getElementById("mySidenav");
    if (x.className === "sidenav") {
      x.className += " responsive";
    } else {
      x.className = "sidenav";
    }
  }
