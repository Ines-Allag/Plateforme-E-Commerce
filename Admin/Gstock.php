<?php
session_start();

// Vérifier si l'admin est connecté
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=Accès non autorisé");
    exit();
}

include('../config.php');

// Variables pour le mode édition
$edit_mode = false;
$product = null;

// Si on est en mode édition
if (isset($_GET['edit_id'])) {
    $edit_mode = true;
    $edit_id = intval($_GET['edit_id']);
    
    $query = "SELECT * FROM produits WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $edit_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 1) {
        $product = mysqli_fetch_assoc($result);
    } else {
        header("Location: stock_management.php?error=Produit introuvable");
        exit();
    }
}

// Récupérer les catégories existantes
$categories_query = "SELECT DISTINCT categorie FROM produits WHERE categorie IS NOT NULL ORDER BY categorie";
$categories_result = mysqli_query($con, $categories_query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $edit_mode ? 'Modifier' : 'Ajouter'; ?> un Produit - Atelier</title>
  <link rel="stylesheet" href="../global.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    body {
      background-color: var(--background);
      font-family: var(--font-sans);
      margin: 0;
      padding: 0;
    }

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
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 2rem;
    }

    .admin-logo img {
      height: 35px;
      width: auto;
    }

    .admin-nav ul {
      display: flex;
      gap: 1.5rem;
      align-items: center;
      list-style: none;
      margin: 0;
      padding: 0;
    }

    .admin-nav a {
      color: var(--primary-foreground);
      padding: 0.5rem 1rem;
      border-radius: var(--radius);
      transition: background-color 0.2s ease;
      font-weight: 500;
      text-decoration: none;
    }

    .admin-nav a:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }

    .page-header {
      background-color: var(--card);
      padding: 2rem;
      margin-bottom: 2rem;
      border-bottom: 1px solid var(--border);
    }

    .page-header h1 {
      font-family: var(--font-serif);
      font-size: 2rem;
      color: var(--foreground);
      margin: 0 0 0.5rem 0;
    }

    .page-header p {
      color: var(--muted-foreground);
      margin: 0;
    }

    .main-content {
      max-width: 900px;
      margin: 0 auto;
      padding: 0 2rem 3rem;
      width: 100%;
    }

    .alert {
      padding: 1rem;
      border-radius: var(--radius);
      margin-bottom: 1.5rem;
      font-size: 0.875rem;
    }

    .alert-error {
      background-color: color-mix(in srgb, var(--destructive) 15%, transparent);
      color: var(--destructive);
      border-left: 4px solid var(--destructive);
    }

    .form-card {
      background-color: var(--card);
      border-radius: var(--radius);
      padding: 2.5rem;
      border: 1px solid var(--border);
      box-shadow: var(--shadow-sm);
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

    .form-group label .required {
      color: var(--destructive);
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
      font-family: var(--font-sans);
      transition: border-color 0.2s ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: var(--ring);
      box-shadow: 0 0 0 3px var(--ring);
    }

    .form-group textarea {
      min-height: 120px;
      resize: vertical;
    }

    .form-group input[type="file"] {
      padding: 0.5rem;
      background-color: transparent;
      border: 2px dashed var(--border);
      cursor: pointer;
    }

    .form-group input[type="file"]:hover {
      border-color: var(--primary);
    }

    .form-help {
      font-size: 0.75rem;
      color: var(--muted-foreground);
      margin-top: 0.25rem;
    }

    .images-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
    }

    .image-preview {
      position: relative;
      aspect-ratio: 1;
      border: 2px dashed var(--border);
      border-radius: var(--radius);
      overflow: hidden;
      background-color: var(--muted);
    }

    .image-preview img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .image-preview-placeholder {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
      color: var(--muted-foreground);
      font-size: 2rem;
    }

    .form-actions {
      display: flex;
      gap: 1rem;
      margin-top: 2rem;
      padding-top: 2rem;
      border-top: 1px solid var(--border);
    }

    .btn {
      padding: 0.875rem 2rem;
      border-radius: var(--radius);
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
      border: none;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      font-size: 0.875rem;
    }

    .btn-primary {
      flex: 1;
      background-color: var(--primary);
      color: var(--primary-foreground);
    }

    .btn-primary:hover {
      background-color: color-mix(in srgb, var(--primary) 90%, black);
      transform: translateY(-2px);
      box-shadow: var(--shadow);
    }

    .btn-secondary {
      background-color: var(--secondary);
      color: var(--secondary-foreground);
    }

    .btn-secondary:hover {
      background-color: color-mix(in srgb, var(--secondary) 90%, black);
    }

    @media (max-width: 768px) {
      .admin-nav ul {
        flex-direction: column;
        gap: 0.5rem;
      }

      .images-grid {
        grid-template-columns: 1fr;
      }

      .form-actions {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <div class="admin-container">
    <header class="admin-header">
      <div class="admin-header-content">
        <div class="admin-logo">
          <a href="DashboardAdmin.php">
            <img src="../imgs/Atelier.png" alt="Atelier Logo">
          </a>
        </div>
        <nav class="admin-nav">
          <ul>
            <li><a href="DashboardAdmin.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="stock_management.php"><i class="fas fa-boxes"></i> Gestion Stock</a></li>
            <li><a href="orders_management.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
          </ul>
        </nav>
      </div>
    </header>

    <div class="page-header">
      <h1>
        <i class="fas fa-<?php echo $edit_mode ? 'edit' : 'plus'; ?>"></i>
        <?php echo $edit_mode ? 'Modifier le Produit' : 'Ajouter un Nouveau Produit'; ?>
      </h1>
      <p><?php echo $edit_mode ? 'Modifiez les informations du produit' : 'Remplissez tous les champs pour ajouter un nouveau produit'; ?></p>
    </div>

    <div class="main-content">
      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
      <?php endif; ?>

      <div class="form-card">
        <form action="GstockBack.php" method="post" enctype="multipart/form-data">
          <?php if ($edit_mode): ?>
            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
          <?php endif; ?>

          <div class="form-grid">
            <!-- Nom du produit -->
            <div class="form-group">
              <label for="nom">
                Nom du produit <span class="required">*</span>
              </label>
              <input type="text" 
                     id="nom" 
                     name="nom" 
                     value="<?php echo $edit_mode ? htmlspecialchars($product['nom']) : ''; ?>"
                     placeholder="Ex: Rolex Submariner" 
                     required>
            </div>

            <!-- Description -->
            <div class="form-group">
              <label for="description">
                Description <span class="required">*</span>
              </label>
              <textarea id="description" 
                        name="description" 
                        placeholder="Décrivez le produit en détail..."
                        required><?php echo $edit_mode ? htmlspecialchars($product['description']) : ''; ?></textarea>
            </div>

            <!-- Catégorie -->
            <div class="form-group">
              <label for="categorie">
                Catégorie <span class="required">*</span>
              </label>
              <input type="text" 
                     id="categorie" 
                     name="categorie" 
                     value="<?php echo $edit_mode ? htmlspecialchars($product['categorie']) : ''; ?>"
                     placeholder="Ex: Montres de luxe, Montres sport..." 
                     list="categories-list"
                     required>
              <datalist id="categories-list">
                <?php 
                mysqli_data_seek($categories_result, 0);
                while ($cat = mysqli_fetch_assoc($categories_result)): 
                ?>
                  <option value="<?php echo htmlspecialchars($cat['categorie']); ?>">
                <?php endwhile; ?>
              </datalist>
              <span class="form-help">Tapez pour créer une nouvelle catégorie ou sélectionnez une existante</span>
            </div>

            <!-- Prix et Stock -->
            <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
              <div class="form-group">
                <label for="prix">
                  Prix (DZD) <span class="required">*</span>
                </label>
                <input type="number" 
                       id="prix" 
                       name="prix" 
                       value="<?php echo $edit_mode ? $product['prix'] : ''; ?>"
                       placeholder="0.00" 
                       step="0.01" 
                       min="0"
                       required>
              </div>

              <div class="form-group">
                <label for="quantite_stock">
                  Quantité en stock <span class="required">*</span>
                </label>
                <input type="number" 
                       id="quantite_stock" 
                       name="quantite_stock" 
                       value="<?php echo $edit_mode ? $product['quantite_stock'] : ''; ?>"
                       placeholder="0" 
                       min="0"
                       required>
              </div>
            </div>

            <!-- Images -->
            <div class="form-group">
              <label>Images du produit (3 maximum)</label>
              
              <?php if ($edit_mode): ?>
                <div class="images-grid" style="margin-bottom: 1rem;">
                  <?php for ($i = 1; $i <= 3; $i++): ?>
                    <div class="image-preview">
                      <?php if (!empty($product["image$i"])): ?>
                        <img src="<?php echo htmlspecialchars($product["image$i"]); ?>" alt="Image <?php echo $i; ?>">
                      <?php else: ?>
                        <div class="image-preview-placeholder">
                          <i class="fas fa-image"></i>
                        </div>
                      <?php endif; ?>
                    </div>
                  <?php endfor; ?>
                </div>
                <span class="form-help" style="display: block; margin-bottom: 1rem;">Images actuelles (laissez vide pour conserver)</span>
              <?php endif; ?>

              <div class="form-grid" style="grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                <div class="form-group">
                  <label for="image1">Image 1 <?php echo !$edit_mode ? '<span class="required">*</span>' : ''; ?></label>
                  <input type="file" 
                         id="image1" 
                         name="image1" 
                         accept="image/*"
                         <?php echo !$edit_mode ? 'required' : ''; ?>>
                </div>
                <div class="form-group">
                  <label for="image2">Image 2</label>
                  <input type="file" 
                         id="image2" 
                         name="image2" 
                         accept="image/*">
                </div>
                <div class="form-group">
                  <label for="image3">Image 3</label>
                  <input type="file" 
                         id="image3" 
                         name="image3" 
                         accept="image/*">
                </div>
              </div>
              <span class="form-help">Formats acceptés: JPG, PNG, GIF (Max 5MB par image)</span>
            </div>
          </div>

          <!-- Actions -->
          <div class="form-actions">
            <a href="stock_management.php" class="btn btn-secondary">
              <i class="fas fa-times"></i> Annuler
            </a>
            <button type="submit" name="<?php echo $edit_mode ? 'modifier' : 'ajouter'; ?>" class="btn btn-primary">
              <i class="fas fa-<?php echo $edit_mode ? 'save' : 'plus'; ?>"></i>
              <?php echo $edit_mode ? 'Enregistrer les modifications' : 'Ajouter le produit'; ?>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>