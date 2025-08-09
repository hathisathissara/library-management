<?php $userId = htmlspecialchars($_GET['user_id'] ?? ''); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Verify Email</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .code-inputs {
      display: flex;
      justify-content: center;
      gap: 0.5rem;
      max-width: 100%;
      margin: 0 auto;
    }
    .code-inputs input {
      flex: 1 1 0;
      max-width: 3.5rem;
      min-width: 2.5rem;
      height: 3.5rem;
      font-size: 2rem;
      text-align: center;
      border: 1px solid #ced4da;
      border-radius: 0.375rem;
      outline-offset: 2px;
      transition: border-color 0.3s, box-shadow 0.3s;
    }
    .code-inputs input:focus {
      border-color: #0d6efd;
      box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    @media (max-width: 400px) {
      .code-inputs input {
        max-width: 2.8rem;
        min-width: 2rem;
        height: 2.8rem;
        font-size: 1.6rem;
      }
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center vh-100 flex-column">
    <div class="card p-4 shadow" style="max-width: 400px; width: 100%;">
      <h2 class="mb-4 text-center">Enter Verification Code</h2>
      <form action="verify_handler.php" method="post" novalidate onsubmit="return submitCode()">
        <input type="hidden" name="user_id" value="<?php echo $userId; ?>">
        <div class="mb-3">
          <label class="form-label d-block mb-3">Code</label>
          <div class="code-inputs" id="codeInputs">
            <input type="text" maxlength="1" pattern="\d" inputmode="numeric" required>
            <input type="text" maxlength="1" pattern="\d" inputmode="numeric" required>
            <input type="text" maxlength="1" pattern="\d" inputmode="numeric" required>
            <input type="text" maxlength="1" pattern="\d" inputmode="numeric" required>
            <input type="text" maxlength="1" pattern="\d" inputmode="numeric" required>
            <input type="text" maxlength="1" pattern="\d" inputmode="numeric" required>
          </div>
          <input type="hidden" name="code" id="hiddenCode">
          <div class="form-text mt-2">Enter the 6-digit code sent to your email.</div>
        </div>
        <button type="submit" class="btn btn-primary w-100">Verify</button>
      </form>
    </div>
  </div>

<script>
  const inputs = document.querySelectorAll('.code-inputs input');

  inputs.forEach((input, index) => {
    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace') {
        input.value = '';
        if (index > 0) inputs[index - 1].focus();
        e.preventDefault();
      } else if (e.key.match(/^[0-9]$/)) {
        input.value = '';
      } else if (e.key !== 'Tab') {
        e.preventDefault();
      }
    });

    input.addEventListener('input', () => {
      if (input.value.length === 1 && index < inputs.length -1) {
        inputs[index + 1].focus();
      }
    });
  });

  function submitCode() {
    let code = '';
    for (const input of inputs) {
      if (!input.value.match(/^\d$/)) {
        alert('Please enter all 6 digits of the verification code.');
        input.focus();
        return false;
      }
      code += input.value;
    }
    document.getElementById('hiddenCode').value = code;
    return true;
  }

  inputs[0].focus();
</script>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
