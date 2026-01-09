<?php
session_start();

// STEP 1: Check if admin is logged in
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=Accès non autorisé");
    exit();
}

include('../config.php');

// STEP 2: Handle DELETE action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    // Get product info first (to delete images from server)
    $query = "SELECT image1, image2, image3 FROM produits WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($product = mysqli_fetch_assoc($result)) {
        // Delete image files from server
        foreach (['image1', 'image2', 'image3'] as $img) {
            if (!empty($product[$img]) && file_exists("../" . $product[$img])) {
                @unlink("../" . $product[$img]);
            }
        }
        
        // Delete product from database
        $delete_query = "DELETE FROM produits WHERE id = ?";
        $stmt = mysqli_prepare($con, $delete_query);
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: stock_management.php?success=Produit supprimé avec succès");
            exit();
        } else {
            header("Location: stock_management.php?error=Erreur lors de la suppression");
            exit();
        }
    }
}

// STEP 3: Get search and filter parameters from URL
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, trim($_GET['search'])) : '';
$category_filter = isset($_GET['category']) ? mysqli_real_escape_string($con, $_GET['category']) : '';

// STEP 4: Build SQL query with filters
$query = "SELECT * FROM produits WHERE 1=1";

// Add search condition
if (!empty($search)) {
    $query .= " AND nom LIKE '%$search%'";
}

// Add category filter
if (!empty($category_filter)) {
    $query .= " AND categorie = '$category_filter'";
}

// Order by: low stock first, then by name
$query .= " ORDER BY quantite_stock ASC, nom ASC";

$result = mysqli_query($con, $query);

// STEP 5: Get all unique categories for the filter dropdown
$categories_query = "SELECT DISTINCT categorie FROM produits WHERE categorie IS NOT NULL ORDER BY categorie";
$categories_result = mysqli_query($con, $categories_query);

// STEP 6: Calculate statistics
$stats_query = "SELECT 
    COUNT(*) as total_products,
    SUM(quantite_stock) as total_stock,
    COUNT(CASE WHEN quantite_stock < 5 THEN 1 END) as low_stock_count
    FROM produits";
$stats_result = mysqli_query($con, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion du Stock - Atelier</title>
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
    }

    .page-header p {
      color: var(--muted-foreground);
      font-size: 1.125rem;
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

    .alert-success {
      background-color: #d1fae5;
      color: #065f46;
      border: 1px solid #34d399;
    }

    .alert-error {
      background-color: #fee2e2;
      color: #991b1b;
      border: 1px solid #ef4444;
    }

    /* Toolbar */
    .toolbar {
      background-color: var(--card);
      padding: 1.5rem;
      border-radius: var(--radius);
      margin-bottom: 2rem;
      border: 0.5px solid var(--border);
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      align-items: center;
      box-shadow: var(--shadow-sm);
    }

    .search-box {
      flex: 1;
      min-width: 250px;
      position: relative;
    }

    .search-box input {
      width: 100%;
      padding: 0.875rem 1rem 0.875rem 3rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      font-size: 0.875rem;
      transition: all 0.3s ease;
    }
    .search-box i {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      font-size: 1rem;
    }

    .filter-select {
      max-width: 100%;
      padding: 0.875rem 1rem 0.875rem 1rem;
      border: 1px solid var(--border);
      align-items: start;
      background-color: transparent; 
      font-size: 0.875rem;

    }

    .btn {
      padding: 0.875rem 1.5rem;
      border-radius: var(--radius);
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      border: none;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
    }

    .btn-primary {
      background-color: var(--primary);
      color: var(--primary-foreground);
    }
    
    .btn-primary:hover {
    opacity: 0.9;
    }

    /* Products Table */
    .products-table {
      background-color: var(--card);
      border-radius: var(--radius);
      overflow: hidden;
      border: 0.5px solid var(--border);
      box-shadow: var(--shadow-sm);
    }

    .table-header {
      display: grid;
      grid-template-columns: 100px 2fr 1fr 1fr 1fr 220px;
      gap: 1.5rem;
      padding: 1.5rem 2rem;
      background-color: var(--muted);
      border-bottom: 1px solid var(--border);
      font-weight: 700;
      font-size: 0.875rem;
      color: var(--foreground);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .product-row {
      display: grid;
      grid-template-columns: 100px 2fr 1fr 1fr 1fr 220px;
      gap: 1.5rem;
      padding: 1.5rem 2rem;
      border-bottom: 1px solid var(--border);
      align-items: center;
      transition: all 0.3s ease;
    }

    .product-row:hover {
      background-color: var(--muted);
    }

    .product-row:last-child {
      border-bottom: none;
    }

    /* Product image thumbnail */
    .product-image {
      width: 80px;
      height: 80px;
      border-radius: var(--radius);
      overflow: hidden;
      border: 1px solid var(--border);
      box-shadow: var(--shadow-sm);
    }

    .product-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .product-name {
      font-weight: 600;
      color: var(--foreground);
      font-size: 1rem;
    }

    .product-category {
      display: inline-block;
      padding: 0.375rem 1rem;
      background-color: var(--muted);
      border-radius: 50px;
      font-size: 0.813rem;
      color: var(--muted-foreground);
      font-weight: 600;
      border: 1px solid var(--border);
    }

    .product-price {
      font-weight: 700;
      color: var(--primary);
      font-size: 1.1rem;
      font-family: var(--font-serif);
    }

    .product-stock {
      font-weight: 700;
      font-size: 1.125rem;
      color: var(--foreground);
    }

    /* Low stock warning */
    .low-stock {
      color: var(--destructive);
    }

    /* Product actions */
    .product-actions {
      display: flex;
      gap: 0.5rem;
    }

    .btn-sm {
      padding: 0.625rem 1rem;
      font-size: 0.813rem;
    }

    .btn-secondary {
      padding: 0.5rem 1rem;
      border: 1px solid #b08d6d;
      background: transparent;
      color: #b08d6d;
      font-weight: 600;
      border-radius: var(--radius);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      color: white;
      background-color: solid #b08d6d;
      box-shadow: var(--shadow-sm);
    }

    .btn-danger {
      padding: 0.5rem 1rem;
      border: 1px solid var(--destructive);
      background: transparent;
      color: var(--destructive);
      font-weight: 600;
      border-radius: var(--radius);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-danger:hover {
      background-color: color-mix(in srgb, var(--destructive) 90%, black);
      color: white;
      transform: translateY(-2px);
      box-shadow: var(--shadow-sm);
    }

    /* Empty state */
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
      color: var(--muted-foreground);
    }

    .empty-state i {
      font-size: 4rem;
      margin-bottom: 1.5rem;
      color: var(--border);
    }

    .empty-state h3 {
      font-size: 1.25rem;
      margin-bottom: 0.75rem;
      color: var(--foreground);
      font-family: var(--font-serif);
    }

    /* Responsive */
    @media (max-width: 1200px) {
      .table-header,
      .product-row {
        grid-template-columns: 80px 1.5fr 1fr 1fr 1fr 200px;
      }
    }

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

      .toolbar {
        flex-direction: column;
      }

      .search-box {
        width: 100%;
      }

      .filter-select {
        width: 100%;
      }

      .table-header {
        display: none;
      }

      .product-row {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 1.5rem;
      }

      .product-image {
        justify-self: center;
      }

      .product-actions {
        justify-content: center;
        width: 100%;
      }

      .btn-sm {
        flex: 1;
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

      .stats-grid {
        grid-template-columns: 1fr;
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
          <a href="stock_management.php" class="nav-link active">
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
          <a href="Gstock.php" class="nav-link">
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
        <h1><i class="fas fa-boxes"></i> Gestion du Stock</h1>
        <p>Gérez tous vos produits en un seul endroit</p>
      </div>

      <!-- Alerts -->
      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i>
          <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i>
          <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
      <?php endif; ?>



      <!-- Toolbar -->
      <div class="toolbar">
        <form method="GET" action="stock_management.php" class="search-box">
          <i class="fas fa-search"></i>
          <input type="text" 
                 name="search" 
                 placeholder="Rechercher un produit..." 
                 value="<?php echo htmlspecialchars($search); ?>">
          <?php if (!empty($category_filter)): ?>
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
          <?php endif; ?>
        </form>

        <form method="GET" action="stock_management.php">
          <select name="category" class="filter-select" onchange="this.form.submit()">
            <option value="">Toutes les catégories</option>
            <?php 
            mysqli_data_seek($categories_result, 0);
            while ($cat = mysqli_fetch_assoc($categories_result)): 
            ?>
              <option value="<?php echo htmlspecialchars($cat['categorie']); ?>"
                      <?php echo $category_filter === $cat['categorie'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($cat['categorie']); ?>
              </option>
            <?php endwhile; ?>
          </select>
          <?php if (!empty($search)): ?>
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
          <?php endif; ?>
        </form>

        <a href="Gstock.php" class="btn btn-primary">
          <i class="fas fa-plus"></i> Ajouter un produit
        </a>
      </div>

      <!-- Products Table -->
      <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="products-table">
          <div class="table-header">
            <div>Image</div>
            <div>Produit</div>
            <div>Catégorie</div>
            <div>Prix</div>
            <div>Stock</div>
            <div>Actions</div>
          </div>

          <?php while ($product = mysqli_fetch_assoc($result)): ?>
            <div class="product-row">
              <!-- Product Image -->
              <div class="product-image">
                <?php if (!empty($product['image1'])): ?>
                  <img src="../<?php echo htmlspecialchars($product['image1']); ?>" 
                       alt="<?php echo htmlspecialchars($product['nom']); ?>">
                <?php else: ?>
                  <img src="../imgs/placeholder.jpg" alt="No image">
                <?php endif; ?>
              </div>

              <!-- Product Name -->
              <div class="product-name">
                <?php echo htmlspecialchars($product['nom']); ?>
              </div>

              <!-- Category -->
              <div>
                <span class="product-category">
                  <?php echo htmlspecialchars($product['categorie'] ?? 'Non catégorisé'); ?>
                </span>
              </div>

              <!-- Price -->
              <div class="product-price">
                <?php echo number_format($product['prix'], 2); ?> DZD
              </div>

              <!-- Stock -->
              <div class="product-stock <?php echo $product['quantite_stock'] < 5 ? 'low-stock' : ''; ?>">
                <?php echo $product['quantite_stock']; ?>
                <?php if ($product['quantite_stock'] < 5): ?>
                  <i class="fas fa-exclamation-triangle" title="Stock faible"></i>
                <?php endif; ?>
              </div>

              <!-- Actions -->
              <div class="product-actions">
                <a href="Gstock.php?edit_id=<?php echo $product['id']; ?>" 
                   class="btn btn-secondary btn-sm">
                  <i class="fas fa-edit"></i> Modifier
                </a>
                <a href="stock_management.php?delete_id=<?php echo $product['id']; ?>" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ? Cette action est irréversible.');">
                  <i class="fas fa-trash"></i> Supprimer
                </a>
              </div>
            </div>
          <?php endwhile; ?>
        </div>

      <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
          <i class="fas fa-box-open"></i>
          <h3>Aucun produit trouvé</h3>
          <p>
            <?php if (!empty($search) || !empty($category_filter)): ?>
              Aucun produit ne correspond à vos critères de recherche.
            <?php else: ?>
              Commencez par ajouter votre premier produit.
            <?php endif; ?>
          </p>
          <a href="Gstock.php" class="btn btn-primary" style="margin-top: 1.5rem;">
            <i class="fas fa-plus"></i> Ajouter un produit
          </a>
        </div>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>