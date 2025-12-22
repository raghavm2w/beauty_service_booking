function showMessage(message, type) {
    const messageBox = document.getElementById("messageBox");
    messageBox.textContent = message;
    messageBox.classList.add(type);
    messageBox.style.display = "block";

    setTimeout(() => {
        messageBox.style.display = "none";
    }, 3500);
}