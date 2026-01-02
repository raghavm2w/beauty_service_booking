document.addEventListener('DOMContentLoaded', () => {
    const menuBtn = document.getElementById('menuBtn');
    const dropdownMenu = document.getElementById('dropdownMenu');

    if (menuBtn && dropdownMenu) {
        menuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!dropdownMenu.contains(e.target) && !menuBtn.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    }
});
function logout(){
fetch('/logout',{
        method: 'POST',
        credentials: 'include'
})
   .then(res => res.json())
  .then(data => {
       if (data.status === "error") {
           sh(data.message, "error");
           return;
        }
        window.location.href = "/";

   
        }) .catch(err => {
        console.error("logout error:", err);
        showAlert("An error occurred while logout", "error");
    });
}