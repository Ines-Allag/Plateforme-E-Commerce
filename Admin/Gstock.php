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
      background: linear-gradient(135deg, var(--primary) 0%, color-mix(in srgb, var(--primary) 70%, black) 100%);
      min-height: 100vh;
      margin: 0;
      padding: 0;
    }

    .dashboard-container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar Navigation - Same as Dashboard */
    .sidebar {
      width: 280px;
      background-color: #3B000B;
      padding: 2rem 0;
      position: fixed;
      height: 100vh;
      overflow-y: auto;
    }

    .sidebar-header {
      padding: 0 2rem 1rem;
      border-bottom: 0.5px solid var(--border);
      margin-bottom: 2rem;
    }

    .sidebar-logo {
      display: flex;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .sidebar-logo img {
      height: 45px;
    }

    .admin-info {
      padding: 1rem;
      border-radius: var(--radius);
      color: var(--primary-foreground);
    }

    .admin-info p {
      font-size: 0.75rem;
      opacity: 0.9;
      margin-bottom: 0.25rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .admin-info .admin-name {
      font-size: 1.125rem;
      font-weight: 700;
      font-family: var(--font-serif);
    }

    .sidebar-nav {
      padding: 0 1rem;
    }

    .nav-item {
      margin-bottom: 0.5rem;
    }

    .nav-link {
      display: flex;
      align-items: center;
      gap: 1rem;
      padding: 1rem 1.5rem;
      color: var(--background);
      text-decoration: none;
      border-radius: var(--radius);
      transition: all 0.3s ease;
      font-weight: 500;
    }

    .nav-link:hover {
      background-color: rgba(255,255,255,0.1);
      color: var(--background);
      transform: translateX(8px);
      box-shadow: var(--shadow-sm);
    }

    .nav-link.active {
      background: rgba(255,255,255,0.1);
      color: var(--accent);
      box-shadow: var(--shadow-md);
    }

    .nav-link i {
      font-size: 1.25rem;
      width: 24px;
      text-align: center;
    }

    /* Main Content */
    .main-content {
      flex: 1;
      margin-left: 280px;
      padding: 2.5rem;
      background-color: var(--background);
    }

    .page-header {
      margin-bottom: 3rem;
      padding-bottom: 2rem;
      border-bottom: 1px solid var(--border);
    }

    .page-header h1 {
      font-family: var(--font-serif);
      font-size: 2.75rem;
      color: var(--primary);
      margin-bottom: 0.5rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .page-header h1 i {
      font-size: 2.25rem;
    }

    .page-header p {
      color: var(--muted-foreground);
      font-size: 1.125rem;
    }

    .form-container {
      max-width: 900px;
      margin: 0 auto;
    }

    /* Alerts */
    .alert {
      padding: 1rem 1.5rem;
      border-radius: var(--radius);
      margin-bottom: 1.5rem;
      font-size: 0.875rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .alert-error {
      background-color: #fee2e2;
      color: #991b1b;
      border: 1px solid #ef4444;
    }

    /* Form Card */
    .form-card {
      background-color: var(--card);
      border-radius: var(--radius);
      padding: 3rem;
      border: 0.5px solid var(--border);
      box-shadow: var(--shadow-sm);
    }

    .form-grid {
      display: grid;
      gap: 2rem;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .form-group label {
      font-weight: 600;
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
      padding: 0.875rem 1rem;
      background-color: var(--input);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      color: var(--foreground);
      font-size: 0.875rem;
      font-family: var(--font-sans);
      transition: all 0.3s ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(59, 0, 11, 0.1);
    }

    .form-group textarea {
      min-height: 120px;
      resize: vertical;
    }

    .form-group input[type="file"] {
      padding: 0.75rem;
      background-color: var(--muted);
      border: 2px dashed var(--border);
      cursor: pointer;
    }

    .form-group input[type="file"]:hover {
      border-color: var(--primary);
      background-color: var(--card);
    }

    .form-help {
      font-size: 0.75rem;
      color: var(--muted-foreground);
      margin-top: 0.25rem;
    }

    .two-columns {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;
    }

    /* Current Images Preview */
    .current-images {
      margin-bottom: 1.5rem;
    }

    .current-images-label {
      font-size: 0.875rem;
      font-weight: 600;
      color: var(--foreground);
      margin-bottom: 1rem;
      display: block;
    }

    .images-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 1rem;
    }

    .image-preview {
      position: relative;
      aspect-ratio: 1;
      border: 1px solid var(--border);
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

    /* Form Actions */
    .form-actions {
      display: flex;
      gap: 1rem;
      margin-top: 3rem;
      padding-top: 2rem;
      border-top: 1px solid var(--border);
    }

    .btn {
      padding: 0.875rem 2rem;
      border-radius: var(--radius);
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
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
    opacity: 0.9;
    }

    .btn-secondary {
      padding: 1rem 3rem;
      border: 2px solid #b08d6d;
      background: transparent;
      color: #b08d6d;
      font-weight: 600;
      border-radius: var(--radius);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      background: #b08d6d;
      color: white;
    }

    /* Responsive */
    @media (max-width: 968px) {
      .sidebar {
        width: 80px;
      }

      .sidebar-header {
        padding: 0 1rem 2rem;
      }

      .admin-info p,
      .admin-info .admin-name,
      .nav-link span {
        display: none;
      }

      .nav-link {
        justify-content: center;
        padding: 1rem;
      }

      .main-content {
        margin-left: 80px;
        padding: 1.5rem;
      }

      .two-columns {
        grid-template-columns: 1fr;
      }

      .form-card {
        padding: 2rem;
      }
    }

    @media (max-width: 640px) {
      .sidebar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        height: auto;
        border-right: none;
        border-top: 2px solid var(--border);
        padding: 1rem 0;
        z-index: 1000;
      }

      .sidebar-header {
        display: none;
      }

      .sidebar-nav {
        display: flex;
        justify-content: space-around;
        padding: 0;
      }

      .nav-item {
        margin: 0;
      }

      .nav-link {
        padding: 0.75rem 1rem;
      }

      .main-content {
        margin-left: 0;
        margin-bottom: 100px;
        padding: 1rem;
      }

      .page-header h1 {
        font-size: 2rem;
      }

      .form-card {
        padding: 1.5rem;
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
  <div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-logo">
          <img src="../imgs/Atelier.png" alt="Atelier">
        </div>
        <div class="admin-info">
          <p>Bienvenue,</p>
          <div class="admin-name"><?php echo htmlspecialchars($_SESSION['nom_utilisateur']); ?></div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <div class="nav-item">
          <a href="DashboardAdmin.php" class="nav-link">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="stock_management.php" class="nav-link">
            <i class="fas fa-boxes"></i>
            <span>Gestion Stock</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="orders_management.php" class="nav-link">
            <i class="fas fa-shopping-cart"></i>
            <span>Commandes</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="Gstock.php" class="nav-link active">
            <i class="fas fa-plus-circle"></i>
            <span>Ajouter Produit</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="logout.php" class="nav-link">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
          </a>
        </div>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <div class="page-header">
        <h1>
          <i class="fas fa-<?php echo $edit_mode ? 'edit' : 'plus-circle'; ?>"></i>
          <?php echo $edit_mode ? 'Modifier le Produit' : 'Ajouter un Produit'; ?>
        </h1>
        <p><?php echo $edit_mode ? 'Modifiez les informations du produit' : 'Remplissez les informations pour ajouter un nouveau produit'; ?></p>
      </div>

      <div class="form-container">
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
              <div class="two-columns">
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
                <label>Images du produit</label>
                
                <?php if ($edit_mode): ?>
                  <div class="current-images">
                    <span class="current-images-label">Images actuelles</span>
                    <div class="images-grid">
                      <?php for ($i = 1; $i <= 3; $i++): ?>
                        <div class="image-preview">
                          <?php if (!empty($product["image$i"])): ?>
                            <img src="../<?php echo htmlspecialchars($product["image$i"]); ?>" alt="Image <?php echo $i; ?>">
                          <?php else: ?>
                            <div class="image-preview-placeholder">
                              <i class="fas fa-image"></i>
                            </div>
                          <?php endif; ?>
                        </div>
                      <?php endfor; ?>
                    </div>
                  </div>
                <?php endif; ?>

                <div class="two-columns" style="grid-template-columns: repeat(3, 1fr);">
                  <div class="form-group">
                    <label for="image1" style="font-size: 0.813rem;">
                      Image 1 <?php echo !$edit_mode ? '<span class="required">*</span>' : ''; ?>
                    </label>
                    <input type="file" 
                           id="image1" 
                           name="image1" 
                           accept="image/*"
                           <?php echo !$edit_mode ? 'required' : ''; ?>>
                  </div>
                  <div class="form-group">
                    <label for="image2" style="font-size: 0.813rem;">Image 2</label>
                    <input type="file" 
                           id="image2" 
                           name="image2" 
                           accept="image/*">
                  </div>
                  <div class="form-group">
                    <label for="image3" style="font-size: 0.813rem;">Image 3</label>
                    <input type="file" 
                           id="image3" 
                           name="image3" 
                           accept="image/*">
                  </div>
                </div>
                <span class="form-help">
                  <?php echo $edit_mode ? 'Laissez vide pour conserver les images actuelles. ' : ''; ?>
                  Formats acceptés: JPG, PNG, GIF (Max 5MB)
                </span>
              </div>
            </div>

            <!-- Actions -->
            <div class="form-actions">
              <a href="stock_management.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Annuler
              </a>
              <button type="submit" name="<?php echo $edit_mode ? 'modifier' : 'ajouter'; ?>" class="btn btn-primary">
                <i class="fas fa-<?php echo $edit_mode ? 'save' : 'plus'; ?>"></i>
                <?php echo $edit_mode ? 'Enregistrer' : 'Ajouter le produit'; ?>
              </button>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</body>
</html>