<?php
session_start();

// STEP 1: Check if admin is logged in
// This prevents non-admin users from accessing this page
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=Accès non autorisé");
    exit();
}

include('../config.php');

// STEP 2: Handle DELETE action
// When admin clicks "Delete" button, this code runs
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']); // Convert to integer for security
    
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
                @unlink("../" . $product[$img]); // @ suppresses errors if file doesn't exist
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
$query = "SELECT * FROM produits WHERE 1=1"; // 1=1 makes it easier to add conditions

// Add search condition (searches in product name)
if (!empty($search)) {
    $query .= " AND nom LIKE '%$search%'";
}

// Add category filter
if (!empty($category_filter)) {
    $query .= " AND categorie = '$category_filter'";
}

// Order by: low stock first (so admin sees urgent items), then by name
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

    /* HEADER STYLES */
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
      max-width: 1600px;
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

    /* PAGE HEADER */
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

    /* MAIN CONTENT */
    .main-content {
      max-width: 1600px;
      margin: 0 auto;
      padding: 0 2rem 3rem;
      width: 100%;
    }

    /* ALERTS (success/error messages) */
    .alert {
      padding: 1rem;
      border-radius: var(--radius);
      margin-bottom: 1.5rem;
      font-size: 0.875rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .alert-success {
      background-color: color-mix(in srgb, #10b981 15%, transparent);
      color: #10b981;
      border-left: 4px solid #10b981;
    }

    .alert-error {
      background-color: color-mix(in srgb, var(--destructive) 15%, transparent);
      color: var(--destructive);
      border-left: 4px solid var(--destructive);
    }

    /* STATISTICS CARDS */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background-color: var(--card);
      padding: 1.5rem;
      border-radius: var(--radius);
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border);
    }

    .stat-card h3 {
      font-size: 0.875rem;
      color: var(--muted-foreground);
      margin: 0 0 0.5rem 0;
      font-weight: 500;
    }

    .stat-card .stat-value {
      font-size: 2rem;
      font-weight: 700;
      color: var(--foreground);
    }

    .stat-card .stat-icon {
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
    }

    .stat-products .stat-icon { color: #3b82f6; }
    .stat-stock .stat-icon { color: #10b981; }
    .stat-low .stat-icon { color: #ef4444; }

    /* TOOLBAR (search, filter, add button) */
    .toolbar {
      background-color: var(--card);
      padding: 1.5rem;
      border-radius: var(--radius);
      margin-bottom: 2rem;
      border: 1px solid var(--border);
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
      align-items: center;
    }

    .search-box {
      flex: 1;
      min-width: 250px;
      position: relative;
    }

    .search-box input {
      width: 100%;
      padding: 0.75rem 1rem 0.75rem 2.5rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background-color: var(--input);
      color: var(--foreground);
      font-size: 0.875rem;
    }

    .search-box i {
      position: absolute;
      left: 0.875rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--muted-foreground);
    }

    .filter-select {
      padding: 0.75rem 1rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background-color: var(--input);
      color: var(--foreground);
      font-size: 0.875rem;
      min-width: 180px;
    }

    .btn {
      padding: 0.75rem 1.5rem;
      border-radius: var(--radius);
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
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
      background-color: color-mix(in srgb, var(--primary) 90%, black);
      transform: translateY(-2px);
      box-shadow: var(--shadow);
    }

    /* PRODUCTS TABLE */
    .products-table {
      background-color: var(--card);
      border-radius: var(--radius);
      overflow: hidden;
      border: 1px solid var(--border);
      box-shadow: var(--shadow-sm);
    }

    .table-header {
      display: grid;
      grid-template-columns: 100px 2fr 1fr 1fr 1fr 200px;
      gap: 1rem;
      padding: 1rem 1.5rem;
      background-color: var(--muted);
      border-bottom: 1px solid var(--border);
      font-weight: 600;
      font-size: 0.875rem;
      color: var(--foreground);
    }

    .product-row {
      display: grid;
      grid-template-columns: 100px 2fr 1fr 1fr 1fr 200px;
      gap: 1rem;
      padding: 1.5rem;
      border-bottom: 1px solid var(--border);
      align-items: center;
      transition: background-color 0.2s ease;
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
    }

    .product-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .product-name {
      font-weight: 600;
      color: var(--foreground);
      font-size: 0.95rem;
    }

    .product-category {
      display: inline-block;
      padding: 0.25rem 0.75rem;
      background-color: var(--muted);
      border-radius: 9999px;
      font-size: 0.75rem;
      color: var(--muted-foreground);
      font-weight: 500;
    }

    .product-price {
      font-weight: 600;
      color: var(--primary);
      font-size: 1rem;
    }

    .product-stock {
      font-weight: 600;
      font-size: 1rem;
    }

    /* Low stock warning (red text) */
    .low-stock {
      color: var(--destructive);
    }

    /* Product actions (edit/delete buttons) */
    .product-actions {
      display: flex;
      gap: 0.5rem;
    }

    .btn-sm {
      padding: 0.5rem 1rem;
      font-size: 0.813rem;
    }

    .btn-secondary {
      background-color: var(--secondary);
      color: var(--secondary-foreground);
    }

    .btn-secondary:hover {
      background-color: color-mix(in srgb, var(--secondary) 90%, black);
    }

    .btn-danger {
      background-color: var(--destructive);
      color: var(--destructive-foreground);
    }

    .btn-danger:hover {
      background-color: color-mix(in srgb, var(--destructive) 90%, black);
    }

    /* Empty state (when no products found) */
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
      color: var(--muted-foreground);
    }

    .empty-state i {
      font-size: 4rem;
      margin-bottom: 1rem;
      color: var(--border);
    }

    .empty-state h3 {
      font-size: 1.25rem;
      margin-bottom: 0.5rem;
      color: var(--foreground);
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 1200px) {
      .table-header,
      .product-row {
        grid-template-columns: 80px 1.5fr 1fr 1fr 1fr 180px;
      }
    }

    @media (max-width: 968px) {
      .admin-nav ul {
        flex-direction: column;
        gap: 0.5rem;
      }

      .toolbar {
        flex-direction: column;
      }

      .search-box {
        width: 100%;
      }

      .table-header {
        display: none; /* Hide header on mobile */
      }

      .product-row {
        grid-template-columns: 1fr;
        gap: 1rem;
      }

      .product-image {
        justify-self: center;
      }

      .product-actions {
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <div class="admin-container">
    <!-- HEADER -->
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

    <!-- PAGE HEADER -->
    <div class="page-header">
      <h1><i class="fas fa-boxes"></i> Gestion du Stock</h1>
      <p>Gérez tous vos produits en un seul endroit</p>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
      <!-- SUCCESS/ERROR MESSAGES -->
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

      <!-- STATISTICS CARDS -->
      <div class="stats-grid">
        <div class="stat-card stat-products">
          <div class="stat-icon"><i class="fas fa-box"></i></div>
          <h3>Total Produits</h3>
          <div class="stat-value"><?php echo $stats['total_products']; ?></div>
        </div>
        <div class="stat-card stat-stock">
          <div class="stat-icon"><i class="fas fa-cubes"></i></div>
          <h3>Articles en Stock</h3>
          <div class="stat-value"><?php echo number_format($stats['total_stock']); ?></div>
        </div>
        <div class="stat-card stat-low">
          <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
          <h3>Stock Faible</h3>
          <div class="stat-value"><?php echo $stats['low_stock_count']; ?></div>
        </div>
      </div>

      <!-- TOOLBAR (Search, Filter, Add Button) -->
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

      <!-- PRODUCTS TABLE -->
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

              <!-- Stock (with low stock warning) -->
              <div class="product-stock <?php echo $product['quantite_stock'] < 5 ? 'low-stock' : ''; ?>">
                <?php echo $product['quantite_stock']; ?>
                <?php if ($product['quantite_stock'] < 5): ?>
                  <i class="fas fa-exclamation-triangle" title="Stock faible"></i>
                <?php endif; ?>
              </div>

              <!-- Actions (Edit/Delete) -->
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
        <!-- EMPTY STATE (no products found) -->
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
          <a href="Gstock.php" class="btn btn-primary" style="margin-top: 1rem;">
            <i class="fas fa-plus"></i> Ajouter un produit
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>