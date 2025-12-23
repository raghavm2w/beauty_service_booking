<?php include "partials/header.php"; ?>
  <div id="messageBox" class="message"></div>

<main class="auth-page">
  <h1 >Sign in</h1>

  <div class="auth-card">
    <form id="loginForm">

      <div class="form-group">
        <input id="login-email" type="email" name="email" placeholder="email-id" required>
            <small class="error"></small>

      </div>
      <div class="form-group">
        <input type="password" name="password" id="login-pass" placeholder="password" required>
            <small class="error"></small>

      </div>


      <button class="btn-primary" type="submit">Sign in</button>
     <p class="auth-link">
        not registered? <a href="/register">Create new account</a>
      </p>
    </form>
    
  </div>

</main>






<?php include "partials/footer.php"; ?>
