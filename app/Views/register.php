<?php include "partials/header.php"; ?>

<main class="auth-page">
  <h1>Create an account</h1>

  <div class="auth-card">
    <form id="registerForm">

      <div class="form-group">
        <label>Full Name</label>
        <input id="reg-name" type="text" name="name" required>
            <small class="error"></small>

      </div>

      <div class="form-group">
        <label>Email address</label>
        <input id="reg-email" type="email" name="email" required>
            <small class="error"></small>

      </div>

      <div class="form-group">
        <label>Phone Number</label>
        <div class="phone-input">
          <span class="country-code">+91</span>
          <input id="phone" type="number" name="phone" placeholder="mobile number" required>
              <small class="error"></small>

        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Gender</label>
          <select id="gender"name="gender">
            <option>Select gender</option>
            <option>Male</option>
            <option>Female</option>
          </select>
              <small class="error"></small>

        </div>

        <div class="form-group">
          <label>Register As</label>
          <select id="role" name="role">
            <option value="customer">Customer</option>
            <option value="provider">Professional</option>
          </select>
              <small class="error"></small>

        </div>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" id="reg-pass" required>
            <small class="error"></small>

      </div>

      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_pass" required>
            <small class="error"></small>

      </div>

      <button class="btn-primary" type="submit">Register</button>
     <p class="auth-link">
        Or <a href="/login">login to your existing account</a>
      </p>
    </form>
    
  </div>
</main>

<?php include "partials/footer.php"; ?>
