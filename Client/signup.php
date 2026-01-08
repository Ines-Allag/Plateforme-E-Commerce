<?php include('../config.php'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inscription - Atelier</title>
  <link rel="stylesheet" href="../global.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
  <style>
    /* Utilisation du même style que la page login (index.php) */
    .signup-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--primary) 0%, #1a0005 100%);
      padding: 2rem 1rem;
    }

    .signup-card {
      background-color: var(--card);
      padding: 2.5rem;
      border-radius: var(--radius);
      box-shadow: var(--shadow-xl);
      width: 100%;
      max-width: 500px;
      border: 1px solid var(--border);
    }

    .signup-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .signup-header h1 {
      font-family: 'Libre Baskerville', serif;
      color: var(--primary);
      font-size: 2rem;
      margin-bottom: 0.5rem;
    }

    .signup-header p {
      color: var(--muted-foreground);
      font-size: 0.9rem;
    }

    .form-group {
      margin-bottom: 1.25rem;
    }

    .form-group label {
      display: block;
      font-size: 0.875rem;
      font-weight: 500;
      margin-bottom: 0.5rem;
      color: var(--foreground);
    }

    .form-group input {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background-color: var(--input);
      transition: border-color 0.2s, ring 0.2s;
    }

    .form-group input:focus {
	  border-color: var(--ring);
      box-shadow: 0 0 0 2px var(--ring);
    }

    .btn-submit {
      width: 100%;
      padding: 0.75rem;
      background-color: var(--primary);
      color: white;
      border: none;
      border-radius: var(--radius);
      font-weight: 600;
      cursor: pointer;
      transition: opacity 0.2s;
      margin-top: 1rem;
    }

    .btn-submit:hover {
      opacity: 0.9;
    }

    .error-message {
      background-color: #fee2e2;
      color: #dc2626;
      padding: 0.75rem;
      border-radius: var(--radius);
      margin-bottom: 1rem;
      font-size: 0.875rem;
      text-align: center;
    }

    .success-message {
      background-color: #d1fae5;
      color: #059669;
      padding: 0.75rem;
      border-radius: var(--radius);
      margin-bottom: 1rem;
      font-size: 0.875rem;
      text-align: center;
    }

    .login-link {
      text-align: center;
      margin-top: 1.5rem;
      padding-top: 1.5rem;
      border-top: 1px solid var(--border);
    }

    .login-link a {
      color: var(--primary);
      font-weight: 500;
      text-decoration: none;
    }

    .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="signup-container">
    <div class="signup-card">
      <div class="signup-header">
        <h1>Créer un compte</h1>
        <p>Rejoignez l'Atelier pour une expérience unique</p>
      </div>

      <form action="signup-check.php" method="post">
        
        <?php if (isset($_GET['error'])) { ?>
            <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php } ?>

        <?php if (isset($_GET['success'])) { ?>
            <div class="success-message"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php } ?>

        <div class="form-group">
          <label>Nom complet</label>
          <input type="text" name="name" placeholder="Votre nom" 
                 value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>">
        </div>

        <div class="form-group">
          <label>Nom d'utilisateur</label>
          <input type="text" name="uname" placeholder="Identifiant"
                 value="<?php echo isset($_GET['uname']) ? htmlspecialchars($_GET['uname']) : ''; ?>">
        </div>

        <div class="form-group">
          <label>Mot de passe</label>
          <input type="password" name="password" placeholder="••••••••">
        </div>

        <div class="form-group">
          <label>Confirmer le mot de passe</label>
          <input type="password" name="re_password" placeholder="••••••••">
        </div>

        <button type="submit" class="btn-submit">S'inscrire</button>
      </form>

      <div class="login-link">
        <p>Déjà un compte ? <a href="index.php">Se connecter</a></p>
      </div>
    </div>
  </div>
</body>
</html>