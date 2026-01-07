<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - Atelier Watches</title>
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

        .login-container {
            width: 100%;
            max-width: 450px;
        }

        .login-card {
            background-color: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow-xl);
            padding: 3rem;
            border: 1px solid var(--border);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
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

        .login-button {
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

        .login-button:hover {
            background-color: color-mix(in srgb, var(--primary) 90%, black);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
        }

        .login-footer p {
            color: var(--muted-foreground);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .signup-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .signup-link:hover {
            color: color-mix(in srgb, var(--primary) 80%, black);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Connexion Admin</h1>
                <p>Accédez au tableau de bord administrateur</p>
            </div>

            <?php if (isset($_GET['error'])) { ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php } ?>

            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="uname">Nom d'utilisateur</label>
                    <input type="text" id="uname" name="uname" placeholder="Entrez votre nom d'utilisateur" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>
                </div>

                <button type="submit" class="login-button">Se connecter</button>
            </form>

            <div class="login-footer">
                <p>Pas encore de compte administrateur ?</p>
                <a href="signup.php" class="signup-link">Créer un compte admin</a>
            </div>
        </div>
    </div>
</body>
</html>