const loginForm = document.getElementById('loginForm');


const validateLoginEmail = () => {
  const email = document.getElementById('login-email');
  const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

  if (!regex.test(email.value.trim())) {
    showError(email, 'Enter a valid email address');
    return false;
  }
  clearError(email);
  return true;
};
const validateLoginPassword = () => {
  const password = document.getElementById('login-pass');

  if (password.value.length < 6) {
    showError(password, 'Password must be at least 6 characters');
    return false;
  }
  clearError(password);
  return true;
};
document.getElementById('login-email').addEventListener('change', validateLoginEmail);
document.getElementById('login-pass').addEventListener('input', validateLoginPassword);

loginForm.addEventListener('submit', async (e) => {
  e.preventDefault();
if(!(validateLoginEmail() && validateLoginPassword())) return;

const loginData = {
    email : loginForm.email.value.trim(),
    password : loginForm.password.value
}
fetch('/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(loginData)
    })
   .then(res => res.json())
  .then(data => {
       if (data.status === "error") {
           showMessage(data.message, "error");
            loginForm.reset();
           return;
        }
        showMessage(data.message, "success");
                if(data.data.role === 'provider'){
            window.location.href = "/admin/dashboard";
            return;
        }else{
        window.location.href = "/";
        }
   
        }) .catch(err => {
        console.error("Fetch error:", err);
        showMessage("An error occurred while login user", "error");
    });

   
      });
function logout(){
fetch('/logout',{
        method: 'POST',
        credentials: 'include'
})
   .then(res => res.json())
  .then(data => {
       if (data.status === "error") {
           showMessage(data.message, "error");
           return;
        }
        showMessage(data.message, "success");       
        window.location.href = "/";

   
        }) .catch(err => {
        console.error("logout error:", err);
        showMessage("An error occurred while logout", "error");
    });
}
  


