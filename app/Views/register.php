<?php include "partials/header.php"; ?>
  <div id="messageBox" class="message"></div>
<main class="auth-page">
  <h1>Create an account</h1>

  <div class="auth-card">
    <form id="registerForm">

      <div class="form-group">
        <input id="reg-name" type="text" name="name" placeholder="Your name" required>
            <small class="error"></small>

      </div>

      <div class="form-group">
        <input id="reg-email" type="email" name="email" placeholder="email-id" required>
            <small class="error"></small>

      </div>

      <div class="form-group">
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
            <option>male</option>
            <option>female</option>
            <option>others</option>

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
        <input type="password" name="password" id="reg-pass" placeholder="password" required>
            <small class="error"></small>

      </div>

      <div class="form-group">
        <input type="password" name="confirm_password" id="confirm_pass" placeholder="confirm password" required>
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
