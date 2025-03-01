var loginModal = document.getElementById("loginModal");
var loginBtn = document.getElementById("loginBtn");
var loginClose = loginModal.getElementsByClassName("close")[0];

loginBtn.onclick = function() {
    loginModal.style.display = "block";
}

loginClose.onclick = function() {
    loginModal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == loginModal) {
        loginModal.style.display = "none";
    }
}
