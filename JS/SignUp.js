var signupModal = document.getElementById("signupModal");
var signupBtn = document.getElementById("signupBtn");
var signupClose = signupModal.getElementsByClassName("close")[0];

signupBtn.onclick = function() {
    signupModal.style.display = "block";
}

signupClose.onclick = function() {
    signupModal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == signupModal) {
        signupModal.style.display = "none";
    }
}
