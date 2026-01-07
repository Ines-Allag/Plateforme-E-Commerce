<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Admin - Atelier Watches</title>
    <link rel="stylesheet" href="../global.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, var(--primary) 0%, color-mix(in srgb, var(--primary) 80%, black) 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem;
            font-family: var(--font-sans);
        }

        .signup-container {
            width: 100%;
            max-width: 500px;
        }

        .signup-card {
            background-color: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow-xl);
            padding: 3rem;
            border: 1px solid var(--border);
        }

        .signup-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .signup-header h1 {
            font-family: var(--font-serif);
            font-size: 2rem;
            color: var(--foreground);
            margin-bottom: 0.5rem;
        }

        .signup-header p {
            color: var(--muted-foreground);
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--foreground);
            font-size: 0.875rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.875rem 1rem;
            background-color: var(--input);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--foreground);
            font-family: var(--font-sans);
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--ring);
            box-shadow: 0 0 0 3px var(--ring);
        }

        .form-group input::placeholder {
            color: var(--muted-foreground);
        }

        .error-message {
            background-color: color-mix(in srgb, var(--destructive) 15%, transparent);
            color: var(--destructive);
            padding: 0.875rem 1rem;
            border-radius: var(--radius);
            border-left: 4px solid var(--destructive);
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .success-message {
            background-color: color-mix(in srgb, #10b981 15%, transparent);
            color: #10b981;
            padding: 0.875rem 1rem;
            border-radius: var(--radius);
            border-left: 4px solid #10b981;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .signup-button {
            width: 100%;
            padding: 0.875rem;
            background-color: var(--primary);
            color: var(--primary-foreground);
            border-radius: var(--radius);
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            margin-top: 0.5rem;
        }

        .signup-button:hover {
            background-color: color-mix(in srgb, var(--primary) 90%, black);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .signup-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
        }

        .signup-footer p {
            color: var(--muted-foreground);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .login-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .login-link:hover {
            color: color-mix(in srgb, var(--primary) 80%, black);
            text-decoration: underline;
        }

        .password-requirements {
            margin-top: 0.5rem;
            padding: 0.75rem;
            background-color: color-mix(in srgb, var(--primary) 10%, transparent);
            border-radius: var(--radius);
            font-size: 0.75rem;
            color: var(--muted-foreground);
        }

        .password-requirements ul {
            margin: 0.5rem 0 0 1.25rem;
            padding: 0;
        }

        .password-requirements li {
            margin-bottom: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-card">
            <div class="signup-header">
                <h1>📝 Créer un compte Admin</h1>
                <p>Rejoignez l'équipe d'administration</p>
            </div>

            <?php if (isset($_GET['error'])) { ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php } ?>

            <?php if (isset($_GET['success'])) { ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php } ?>

            <form action="signup-check.php" method="post">
                <div class="form-group">
                    <label for="uname">Nom d'utilisateur *</label>
                    <input type="text" 
                           id="uname" 
                           name="uname" 
                           placeholder="Choisissez un nom d'utilisateur" 
                           value="<?php echo isset($_GET['uname']) ? htmlspecialchars($_GET['uname']) : ''; ?>"
                           required 
                           autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe *</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="Créez un mot de passe sécurisé" 
                           required>
                    <div class="password-requirements">
                        <strong>Recommandations :</strong>
                        <ul>
                            <li>Au moins 8 caractères</li>
                            <li>Mélange de lettres et chiffres</li>
                            <li>Au moins une majuscule</li>
                        </ul>
                    </div>
                </div>

                <div class="form-group">
                    <label for="re_password">Confirmer le mot de passe *</label>
                    <input type="password" 
                           id="re_password" 
                           name="re_password" 
                           placeholder="Retapez votre mot de passe" 
                           required>
                </div>

                <button type="submit" class="signup-button">Créer mon compte</button>
            </form>

            <div class="signup-footer">
                <p>Vous avez déjà un compte ?</p>
                <a href="index.php" class="login-link">Se connecter</a>
            </div>
        </div>
    </div>
</body>
</html>