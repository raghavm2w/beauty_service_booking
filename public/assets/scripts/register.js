const form = document.getElementById('registerForm');

const showError = (input, message) => {
  const error = input.parentElement.querySelector('.error');
  error.textContent = message;
  input.classList.add('invalid');
};

const clearError = (input) => {
  const error = input.parentElement.querySelector('.error');
  error.textContent = '';
  input.classList.remove('invalid');
};
const validateName = () => {
  const name = document.getElementById('reg-name');
  if (name.value.trim().length < 3) {
    showError(name, 'Name must be at least 3 characters');
    return false;
  }
  clearError(name);
  return true;
};
const validateEmail = () => {
  const email = document.getElementById('reg-email');
  const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

  if (!regex.test(email.value.trim())) {
    showError(email, 'Enter a valid email address');
    return false;
  }
  clearError(email);
  return true;
};
const validatePhone = () => {
  const phone = document.getElementById('phone');
const regex = /^[1-9]\d{9}$/;

  if (!regex.test(phone.value.trim())) {
    showError(phone, 'Enter a valid 10-digit phone number');
    return false;
  }
  clearError(phone);
  return true;
};
const validateGender = () => {
  const gender = document.getElementById('gender');
  if (!gender.value) {
    showError(gender, 'Please select gender');
    return false;
  }
  clearError(gender);
  return true;
};
const validatePassword = () => {
  const password = document.getElementById('reg-pass');

  if (password.value.length < 6) {
    showError(password, 'Password must be at least 6 characters');
    return false;
  }
  clearError(password);
  return true;
};
const validateConfirmPassword = () => {
  const password = document.getElementById('reg-pass');
  const confirm = document.getElementById('confirm_pass');

  if (password.value !== confirm.value) {
    showError(confirm, 'Passwords do not match');
    return false;
  }
  clearError(confirm);
  return true;
};
document.getElementById('reg-name').addEventListener('input', validateName);
document.getElementById('reg-email').addEventListener('input', validateEmail);
document.getElementById('phone').addEventListener('input', validatePhone);
document.getElementById('gender').addEventListener('change', validateGender);
document.getElementById('reg-pass').addEventListener('input', validatePassword);
document.getElementById('confirm_pass').addEventListener('input', validateConfirmPassword);
const role = document.getElementById('role');
form.addEventListener('submit', async (e) => {
  e.preventDefault();

  const isValid =
    validateName() &&
    validateEmail() &&
    validatePhone() &&
    validateGender() &&
    validatePassword() &&
    validateConfirmPassword();

  if (!isValid) return;

  const payload = {
    name: form.name.value.trim(),
    email: form.email.value.trim(),
    phone: form.phone.value.trim(),
    gender: form.gender.value,
    role: role.value,
    password: form.password.value,
    confirm_password: form.confirm_password.value
  };
  console.log(payload);

fetch('/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    })
   .then(res => res.json())
  .then(data => {
       if (data.status === "error") {
           showMessage(data.message, "error");
            form.reset();
           return;
        }
        console.log(data.data.user_id);
        showMessage(data.message, "success");
                form.reset();
        if(data.data.role === 'provider'){
            window.location.href = "/admin/dash";
        }else{
        window.location.href = "/";
        }

   
        }) .catch(err => {
        console.error("Fetch error:", err);
        showMessage("An error occurred while registering user", "error");
    });

   
      });
  