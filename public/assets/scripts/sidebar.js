function toggleActive(e,btn){
    e.preventDefault(); 
    const buttons = document.querySelectorAll('.menu a');
    buttons.forEach(button => {
        button.classList.remove('active');
    });
    btn.classList.add('active');
     const page = btn.dataset.page; 
    loadPage(page);
}
function loadPage(page){
    fetch(page, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'include'
    })
    .then(async res => {
        const type = res.headers.get('content-type');

        if (type && type.includes('application/json')) {
            const data = await res.json();

            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }

            showAlert(data.message || 'Access denied', 'error');
            return;
        }

        return res.text();
    })
    .then(html => {
        if (!html) return;

        document.getElementById('content').innerHTML = html;
        if(page === '/admin/services'){
            initServices();
        }
        // history.pushState({}, '', page);
    })
    .catch(err => {
        console.error(err);
        showAlert('Failed to load page', 'error');
    });
}
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