<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion de Stock - Watch Store</title>
  <link rel="stylesheet" href="../global.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    .admin-container {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .admin-header {
      background-color: var(--primary);
      color: var(--primary-foreground);
      padding: 1rem 0;
      box-shadow: var(--shadow);
    }

    .admin-header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 1rem;
    }

    .admin-logo {
      font-family: var(--font-serif);
      font-size: 1.5rem;
      font-weight: bold;
    }

    .admin-logo span {
      color: var(--accent);
    }

    .admin-nav ul {
      display: flex;
      gap: 1.5rem;
      align-items: center;
    }

    .admin-nav a {
      color: var(--primary-foreground);
      padding: 0.5rem 1rem;
      border-radius: var(--radius);
      transition: background-color 0.2s ease;
      font-weight: 500;
    }

    .admin-nav a:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }

    .admin-main {
      flex: 1;
      padding: 2rem 1rem;
      max-width: 800px;
      margin: 0 auto;
      width: 100%;
    }

    .form-container {
      background-color: var(--card);
      padding: 2.5rem;
      border-radius: var(--radius);
      box-shadow: var(--shadow-lg);
      border: 1px solid var(--border);
    }

    .form-container h1 {
      font-family: var(--font-serif);
      font-size: 2rem;
      margin-bottom: 2rem;
      color: var(--foreground);
      text-align: center;
    }

    .form-grid {
      display: grid;
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

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 0.75rem 1rem;
      background-color: var(--input);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      color: var(--foreground);
      font-size: 0.875rem;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: var(--ring);
      box-shadow: 0 0 0 2px var(--ring);
    }

    .form-group textarea {
      min-height: 100px;
      resize: vertical;
    }

    .form-group input[type="file"] {
      padding: 0.5rem;
      background-color: transparent;
      border: 1px dashed var(--border);
    }

    .form-group input[type="file"]:hover {
      border-color: var(--ring);
    }

    .form-group small {
      color: var(--muted-foreground);
      font-size: 0.75rem;
    }

    .submit-btn {
      margin-top: 1rem;
      padding: 0.875rem 2rem;
      background-color: var(--primary);
      color: var(--primary-foreground);
      border-radius: var(--radius);
      font-weight: 500;
      font-size: 1rem;
      transition: all 0.2s ease;
      width: 100%;
    }

    .submit-btn:hover {
      background-color: color-mix(in srgb, var(--primary) 90%, black);
      transform: translateY(-1px);
      box-shadow: var(--shadow-sm);
    }

    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      margin-top: 1.5rem;
      color: var(--primary);
      font-weight: 500;
      transition: color 0.2s ease;
    }

    .back-link:hover {
      color: var(--accent-foreground);
    }

    @media (max-width: 640px) {
      .form-container {
        padding: 1.5rem;
      }

      .form-container h1 {
        font-size: 1.75rem;
      }

      .admin-nav ul {
        flex-direction: column;
        gap: 0.5rem;
      }
    }
  </style>
</head>
<body class="admin-container">
  <header class="admin-header">
    <div class="admin-header-content">
      <div class="admin-logo">
        <h2>Watch Store <span>|| Admin</span></h2>
      </div>
      <nav class="admin-nav">
        <ul>
          <li><a href="DashboardAdmin.php"><i class="fas fa-home"></i> Home</a></li>
          <li><a href="Gstock.php" class="active"><i class="fas fa-boxes"></i> Gestion Stock</a></li>
          <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main class="admin-main">
    <div class="form-container">
      <h1><i class="fas fa-boxes"></i> Gestion de Stock</h1>
      
      <form action="GstockBack.php" method="post" enctype="multipart/form-data" class="form-grid">
        <div class="form-group">
          <label for="nom">Nom de la montre</label>
          <input type="text" id="nom" name="nom" placeholder="Ex: Rolex Submariner" required>
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <textarea id="description" name="description" placeholder="Décrivez la montre (mouvement, matériaux, etc.)" required></textarea>
        </div>

        <div class="form-group">
          <label for="categorie">Catégorie</label>
          <select id="categorie" name="categorie" required>
            <option value="">Sélectionnez une catégorie</option>
            <option value="Luxury">Montres de Luxe</option>
            <option value="Sport">Montres Sport</option>
            <option value="Dress">Montres Habillées</option>
            <option value="Casual">Montres Casual</option>
            <option value="Smart">Montres Connectées</option>
          </select>
        </div>

        <div class="form-group">
          <label for="prix">Prix (DZD)</label>
          <input type="number" id="prix" name="prix" placeholder="0.00" step="0.01" required>
        </div>

        <div class="form-group">
          <label for="img1">Image 1 (Principale) *</label>
          <input type="file" id="img1" name="img1" accept="image/*" required>
          <small>Formats acceptés: JPG, PNG, GIF - Obligatoire</small>
        </div>

        <div class="form-group">
          <label for="img2">Image 2 (Optionnelle)</label>
          <input type="file" id="img2" name="img2" accept="image/*">
          <small>Formats acceptés: JPG, PNG, GIF</small>
        </div>

        <div class="form-group">
          <label for="img3">Image 3 (Optionnelle)</label>
          <input type="file" id="img3" name="img3" accept="image/*">
          <small>Formats acceptés: JPG, PNG, GIF</small>
        </div>

        <button type="submit" name="ajouter" class="submit-btn">
          <i class="fas fa-plus"></i> Ajouter la montre
        </button>
      </form>

      <a href="DashboardAdmin.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Retour au Dashboard
      </a>
    </div>
  </main>
</body>
</html>