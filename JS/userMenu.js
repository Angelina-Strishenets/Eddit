document.addEventListener("DOMContentLoaded", function() {
    var userMenu = document.getElementById("userMenu");
    var userMenuBtn = document.getElementById("userMenuBtn");
    var userMenuClose = userMenu.getElementsByClassName("close")[0];

    userMenuBtn.onclick = function() {
        userMenu.style.display = "block";
    }

    userMenuClose.onclick = function() {
        userMenu.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == userMenu) {
            userMenu.style.display = "none";
        }
    }
});
