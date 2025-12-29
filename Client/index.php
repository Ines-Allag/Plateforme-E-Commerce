<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Login - Kelthouma Tech Store</title>
  <link rel="stylesheet" href="../global.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
  <style>
    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--primary) 0%, color-mix(in srgb, var(--primary) 80%, black) 100%);
      padding: 1rem;
    }

    .login-card {
      background-color: var(--card);
      padding: 2.5rem;
      border-radius: var(--radius);
      box-shadow: var(--shadow-xl);
      width: 100%;
      max-width: 400px;
      border: 1px solid var(--border);
    }

    .login-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .login-header h1 {
      font-family: var(--font-serif);
      font-size: 2rem;
      color: var(--foreground);
      margin-bottom: 0.5rem;
    }

    .login-header p {
      color: var(--muted-foreground);
      font-size: 0.875rem;
    }

    .login-form {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .form-group label {
      font-weight: 500;
      color: var(--foreground);
      font-size: 0.875rem;
    }

    .form-group input {
      width: 100%;
      padding: 0.75rem 1rem;
      background-color: var(--input);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      color: var(--foreground);
      font-size: 0.875rem;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-group input:focus {
      border-color: var(--ring);
      box-shadow: 0 0 0 2px var(--ring);
    }

    .submit-btn {
      padding: 0.875rem;
      background-color: var(--primary);
      color: var(--primary-foreground);
      border-radius: var(--radius);
      font-weight: 500;
      font-size: 1rem;
      transition: all 0.2s ease;
    }

    .submit-btn:hover {
      background-color: color-mix(in srgb, var(--primary) 90%, black);
      transform: translateY(-1px);
      box-shadow: var(--shadow-sm);
    }

    .error-message {
      background-color: var(--destructive);
      color: var(--destructive-foreground);
      padding: 0.75rem 1rem;
      border-radius: var(--radius);
      margin-bottom: 1rem;
      font-size: 0.875rem;
      text-align: center;
    }

    .success-message {
      background-color: var(--secondary);
      color: var(--secondary-foreground);
      padding: 0.75rem 1rem;
      border-radius: var(--radius);
      margin-bottom: 1rem;
      font-size: 0.875rem;
      text-align: center;
    }

    .register-link {
      text-align: center;
      margin-top: 1.5rem;
      padding-top: 1.5rem;
      border-top: 1px solid var(--border);
    }

    .register-link a {
      color: var(--primary);
      font-weight: 500;
      transition: color 0.2s ease;
    }

    .register-link a:hover {
      color: var(--accent-foreground);
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h1>Client Login</h1>
        <p>Connectez-vous à votre compte</p>
      </div>

      <?php if (isset($_GET['error'])): ?>
        <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
      <?php endif; ?>

      <form action="login.php" method="post" class="login-form">
        <div class="form-group">
          <label for="uname">Nom d'utilisateur</label>
          <input type="text" id="uname" name="uname" placeholder="Entrez votre nom d'utilisateur" required>
        </div>

        <div class="form-group">
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
        </div>

        <button type="submit" class="submit-btn">Se connecter</button>
      </form>

      <div class="register-link">
        <p>Pas de compte ? <a href="signup.php">Créer un compte</a></p>
      </div>
    </div>
  </div>
</body>
</html>