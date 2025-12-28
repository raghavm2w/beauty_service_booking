// function showMessage(message, type) {
//     const messageBox = document.getElementById("messageBox");
//     messageBox.textContent = message;
//     messageBox.classList.add(type);
//     messageBox.style.display = "block";

//     setTimeout(() => {
//         messageBox.style.display = "none";
//     }, 3500);
// }
function showAlert(message, type) {
    const alertBox = document.getElementById("custom-alert");
    alertBox.innerHTML = message;
    alertBox.className = "alert-box alert-" + type;
    alertBox.style.display = "block";

    setTimeout(() => {
        alertBox.style.display = "none";
    }, 3500);
}   