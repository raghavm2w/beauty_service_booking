
function logout(){
    console.log("Logout initiated");
fetch('/logout',{
        method: 'POST',
        credentials: 'include'
})
   .then(res => res.json())
  .then(data => {
       if (data.status === "error") {
        showAlert("Logout error","error");
        //    showMessage(data.message, "error");
        console.log("Logout error");
           return;
        }
        // showMessage(data.message, "success");       
        window.location.href = "/login";

   
        }) .catch(err => {
        console.error("logout error:", err);
        showAlert("An error occurred while logout", "error");
    });
}
 function showAlert(message, type) {
    const alertBox = document.getElementById("custom-alert");
    alertBox.innerHTML = message;
    alertBox.className = "alert-box alert-" + type;
    alertBox.style.display = "block";

    setTimeout(() => {
        alertBox.style.display = "none";
    }, 3500);
}   
 function toggleSettings() {
        const settingsDiv = document.querySelector('.settings');
        if (settingsDiv.style.display === 'block') {
            settingsDiv.style.display = 'none';
        } else {
            settingsDiv.style.display = 'block';
        }
    }
    function openTime(){
        document.getElementById("timeOverlay").style.display = "block";
        const  timeModal = document.getElementById("timeModal");        
            timeModal.style.display = 'block';
        
    }
    function closeTimeForm(){
                document.getElementById("timeOverlay").style.display = "none";
          const  timeModal = document.getElementById("timeModal");
                timeModal.style.display = 'none';

    }
    document.getElementById("timezoneForm").addEventListener("submit", async e => {
  e.preventDefault();

  const timezone = document.getElementById("timezone").value;

  const res = await fetch("/admin/set-timezone", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ timezone })
  });

  const data = await res.json();

  if (data.status === "success") {
    showAlert("Timezone updated successfully");
    closeTimeForm();
    // location.reload(); 
  } else {
    alert(data.message || "Failed to update timezone");
  }
});

    