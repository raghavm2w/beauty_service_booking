<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Summary</title>
  <link rel="stylesheet" href="/assets/styles/pages/payment.css">
</head>
<body>

  <div class="payment-container">
    <div class="payment-card">
      <h1>Payment Summary</h1>
      <div id="loadingMessage">Loading booking details...</div>
      
      <div id="paymentDetails" class="hidden">
        <div class="summary-item">
          <span class="label">Service:</span>
          <span class="value" id="serviceName"></span>
        </div>
        <div class="summary-item">
          <span class="label">Provider:</span>
          <span class="value" id="providerName"></span>
        </div>
        <div class="summary-item">
          <span class="label">Date</span>
          <span class="value" id="date"></span>
        </div>
        <div class="summary-item">
          <span class="label">Time</span>
          <span class="value" id="time"></span>
        </div>
        <div class="summary-item">
          <span class="label">Duration:</span>
          <span class="value" id="duration"></span>
        </div>
        <div class="summary-item">
          <span class="label">Time Remaining:</span>
          <span class="value" id="paymentTimer" style="color: red; font-weight: bold;">--:--</span>
        </div>
        <div class="summary-item total">
          <span class="label">Total Amount:</span>
          <span class="value" id="price"></span>
        </div>

        <button id="payBtn" class="pay-btn">Confirm and Pay</button>
        <button id="cancelPayBtn" class="pay-btn">Cancel Payment</button>

      </div>
    <div id="custom-alert" class="alert-box"></div>
    </div>
  </div>

  <script src="/assets/scripts/payments.js"></script>
</body>
</html>