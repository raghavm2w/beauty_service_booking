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
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!regex.test(email.value.trim())) {
    showError(email, 'Enter a valid email address');
    return false;
  }
  clearError(email);
  return true;
};
const validatePhone = () => {
  const phone = document.getElementById('phone');
  const regex = /^[6-9]\d{9}$/;

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
  const password = document.getElementById('password');

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
    role: form.role.value,
    password: form.password.value
  };

  try {
    const response = await fetch('/api/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    });

    const result = await response.json();

    if (!response.ok) {
      alert(result.message || 'Registration failed');
      return;
    }

    alert('Registration successful');
    form.reset();

  } catch (error) {
    alert('Something went wrong. Try again.');
  }
});
