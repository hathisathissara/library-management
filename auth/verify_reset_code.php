<?php
session_start();
require 'db.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: reset_password.php");
    exit();
}

$message = '';
$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);

    // Get user id
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $message = "User not found.";
    } else {
        $userId = $user['id'];

        // Get reset token from email_resets
        $stmt = $pdo->prepare("SELECT token_hash, expires_at FROM email_resets WHERE user_id = ?");
        $stmt->execute([$userId]);
        $tokenData = $stmt->fetch();

        if (!$tokenData) {
            $message = "No reset request found. Please request again.";
        } elseif (new DateTime() > new DateTime($tokenData['expires_at'])) {
            $message = "Reset code expired. Please request again.";
        } elseif (!password_verify($code, $tokenData['token_hash'])) {
            $message = "Invalid reset code.";
        } else {
            // Code valid - allow password reset
            $_SESSION['verified_reset_email'] = $email;
            unset($_SESSION['reset_email']); // no longer needed
            header("Location: new_password.php");
            exit();
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Verify Reset Code</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Bootstrap CSS -->
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

    /* Smaller inputs on very small screens */
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
      <h3 class="mb-4 text-center">Enter Reset Code</h3>
      <form method="POST" action="" novalidate onsubmit="return submitCode()">
        <div class="mb-3">
          <label class="form-label d-block mb-3">Reset Code</label>
          <div class="code-inputs" id="codeInputs">
            <input type="text" maxlength="1" pattern="\d" inputmode="numeric" required>
            <input type="text" maxlength="1" pattern="\d" inputmode="numeric" required>
            <input type="text" maxlength="1" pattern="\d" inputmode="numeric" required>
            <input type="text" maxlength="1" pattern="\d" inputmode="numeric" required>
            <input type="text" maxlength="1" pattern="\d" inputmode="numeric" required>
            <input type="text" maxlength="1" pattern="\d" inputmode="numeric" required>
          </div>
          <input type="hidden" name="code" id="hiddenCode">
          <?php if ($message): ?>
            <div class="invalid-feedback d-block mt-2"><?php echo htmlspecialchars($message); ?></div>
          <?php endif; ?>
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
          alert('Please enter all digits of the reset code.');
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
