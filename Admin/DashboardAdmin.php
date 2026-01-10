<?php
session_start();

// Checking if admin is logged in
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=Accès non autorisé");
    exit();
}

include('../config.php');

// statistics
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM produits) as total_products,
    (SELECT SUM(quantite_stock) FROM produits) as total_stock,
    (SELECT COUNT(*) FROM produits WHERE quantite_stock < 5) as low_stock,
    (SELECT COUNT(*) FROM commandes) as total_orders,
    (SELECT COUNT(*) FROM commandes WHERE statut = 'en_attente') as pending_orders,
    (SELECT COUNT(*) FROM commandes WHERE statut = 'confirmee') as confirmed_orders,
    (SELECT COUNT(*) FROM commandes WHERE statut = 'expediee') as shipped_orders,
    (SELECT COUNT(*) FROM commandes WHERE statut = 'livree') as delivered_orders,
    (SELECT SUM(total) FROM commandes WHERE statut != 'annulee') as total_revenue,
    (SELECT SUM(total) FROM commandes WHERE DATE(date_creation) = CURDATE() AND statut != 'annulee') as today_revenue";
$stats_result = mysqli_query($con, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// recent orders
$recent_orders_query = "SELECT c.*, u.nom_utilisateur 
                        FROM commandes c 
                        JOIN utilisateurs u ON c.utilisateur_id = u.id 
                        ORDER BY c.date_creation DESC 
                        LIMIT 5";
$recent_orders = mysqli_query($con, $recent_orders_query);

// low stock products
$low_stock_query = "SELECT * FROM produits WHERE quantite_stock < 5 ORDER BY quantite_stock ASC LIMIT 5";
$low_stock_products = mysqli_query($con, $low_stock_query);

// top selling products (based on order details)
$top_products_query = "SELECT p.*, COALESCE(SUM(dc.quantite), 0) as total_sold 
                       FROM produits p 
                       LEFT JOIN details_commande dc ON p.id = dc.produit_id 
                       GROUP BY p.id 
                       ORDER BY total_sold DESC 
                       LIMIT 5";
$top_products = mysqli_query($con, $top_products_query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - Atelier</title>
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

    /* Sidebar */
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
      border-bottom: 0.3px solid var(--border);
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
      background:  rgba(255,255,255,0.1);
      color: var(--accent);
      box-shadow: var(--shadow-md);
    }

    .nav-link i {
      font-size: 1.25rem;
      width: 24px;
      text-align: center;
    }


    .main-content {
      flex: 1;
      margin-left: 280px;
      padding: 2.5rem;
      background-color: var(--background);
    }

    .dashboard-header {
      margin-bottom: 3rem;
      padding-bottom: 2rem;
      border-bottom: 1px solid var(--border);
    }

    .dashboard-header h1 {
      font-family: var(--font-serif);
      font-size: 2.75rem;
      color: var(--primary);
      margin-bottom: 0.5rem;
      font-weight: 700;
    }

    .dashboard-header p {
      color: var(--muted-foreground);
      font-size: 1.125rem;
    }

    /* Stats */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 1.5rem;
      margin-bottom: 3rem;
    }

    .stat-card {
      background-color: var(--card);
      border-radius: var(--radius);
      padding: 2rem;
      position: relative;
      overflow: hidden;
      transition: all 0.3s ease;
      box-shadow: var(--shadow-sm);
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, var(--primary), var(--secondary));
      opacity: 0;
      transition: opacity 0.3s ease;
    }


    .stat-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 1.5rem;
    }

    .stat-icon {
      width: 70px;
      height: 70px;
      border-radius: var(--radius);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      box-shadow: var(--shadow-md);
    }

    .stat-icon.primary {
      background: var(--sidebar-border);
      color:var(--accent);
    }

    .stat-icon.success {
      
      background: var(--sidebar-border);
      color:var(--accent);
    }

    .stat-icon.warning {
      background: var(--sidebar-border);
      color:var(--accent);
    }

    .stat-icon.info {
      background: var(--sidebar-border);
      color:var(--accent);
    }

    .stat-value {
      font-size: 2.75rem;
      font-weight: 700;
      color: var(--primary);
      font-family: var(--font-serif);
      margin-bottom: 0.5rem;
      line-height: 1;
    }

    .stat-label {
      color: var(--muted-foreground);
      font-size: 0.875rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* Dashboard Sections */
    .dashboard-section {
      background-color: var(--card);
      border-radius: var(--radius);
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: var(--shadow-sm);
    }

    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      padding-bottom: 1.5rem;
    }

    .section-title {
      font-size: 1.75rem;
      font-weight: 700;
      color: var(--primary);
      font-family: var(--font-serif);
    }


    .section-link {
      padding: 0.5rem 1rem;
      border: 1px solid #b08d6d;
      background: transparent;
      color: #b08d6d;
      font-weight: 600;
      border-radius: var(--radius);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .section-link:hover {
      color: var(--secondary-foreground);
      gap: 0.75rem;
      box-shadow: var(--shadow-sm);
    }

    /* Orders List */
    .orders-list {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .order-item {
      background-color: var(--bg);
      border: 0.5px solid var(--border);
      border-radius: var(--radius);
      padding: 1.5rem;
      display: grid;
      grid-template-columns: auto 1fr auto auto;
      gap: 1.5rem;
      align-items: center;
      transition: all 0.3s ease;
    }


    .order-id {
      font-weight: 700;
      color: var(--primary);
      font-size: 1.25rem;
      font-family: var(--font-serif);
    }

    .order-info h4 {
      font-size: 1rem;
      color: var(--foreground);
      margin-bottom: 0.25rem;
      font-weight: 600;
    }

    .order-info p {
      font-size: 0.813rem;
      color: var(--muted-foreground);
    }

    .order-amount {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--secondary);
      font-family: var(--font-serif);
    }

    .status-badge {
      padding: 0.5rem 1.25rem;
      border-radius: 50px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .status-en_attente {
      background-color: #fef3c7;
      color: #92400e;
      border: 1px solid #fbbf24;
    }

    .status-confirmee {
      background-color: #e0e7ff;
      color: #4338ca;
      border: 1px solid #818cf8;
    }

    .status-expediee {
      background-color: #dbeafe;
      color: #1e40af;
      border: 1px solid #60a5fa;
    }

    .status-livree {
      background-color: #d1fae5;
      color: #065f46;
      border: 1px solid #34d399;
    }

    /* Products List */
    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 1.5rem;
    }

    .product-card {
      background-color: var(--muted);
      border: 0.5px solid var(--border);
      border-radius: var(--radius);
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .product-card:hover {
      transform: translateY(-8px);
      border: 0.5px solid var(--border);
      box-shadow: var(--shadow-xl);
    }

    .product-image {
      width: 100%;
      height: 220px;
      background: var(--background);
      position: relative;
      overflow: hidden;
    }

    .product-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .product-card:hover .product-image img {
      transform: scale(1.1);
    }

    .stock-badge {
      position: absolute;
      top: 1rem;
      right: 1rem;
      padding: 0.5rem 1rem;
      border-radius: 50px;
      font-size: 0.75rem;
      font-weight: 700;
      background-color: var(--destructive);
      color: var(--destructive-foreground);
      box-shadow: var(--shadow-md);
    }

    .product-info {
      padding: 1.5rem;
      background-color: var(--card);
    }

    .product-name {
      font-size: 1.063rem;
      font-weight: 600;
      color: var(--foreground);
      margin-bottom: 0.75rem;
      font-family: var(--font-serif);
    }

    .product-price {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 0.75rem;
      font-family: var(--font-serif);
    }

    .product-stock {
      font-size: 0.875rem;
      color: var(--muted-foreground);
      font-weight: 500;
    }

    .product-stock.low {
      color: var(--destructive);
      font-weight: 700;
    }

    /* Empty State */
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
      color: var(--foreground);
      margin-bottom: 0.5rem;
    }

    
    .two-column {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
    }

    /* Responsive */
    @media (max-width: 1200px) {
      .two-column {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 968px) {
      .sidebar {
        width: 80px;
      }

      .sidebar-header {
        padding: 0 1rem 2rem;
      }

      .sidebar-logo-text,
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

      .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      }

      .order-item {
        grid-template-columns: 1fr;
        gap: 1rem;
      }

      .products-grid {
        grid-template-columns: 1fr;
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

      .dashboard-header h1 {
        font-size: 2rem;
      }

      .stat-card {
        padding: 1.5rem;
      }

      .stat-value {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>
  <div class="dashboard-container">

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
          <a href="DashboardAdmin.php" class="nav-link active">
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


    <main class="main-content">
      <div class="dashboard-header">
        <h1>Tableau de Bord</h1>
        <p>Vue d'ensemble de votre boutique Atelier</p>
      </div>


      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-header">
            <div>
              <div class="stat-value"><?php echo number_format($stats['total_products']); ?></div>
              <div class="stat-label">Produits</div>
            </div>
            <div class="stat-icon primary">
              <i class="fas fa-box"></i>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div>
              <div class="stat-value"><?php echo number_format($stats['total_stock']); ?></div>
              <div class="stat-label">Articles en Stock</div>
            </div>
            <div class="stat-icon success">
              <i class="fas fa-cubes"></i>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div>
              <div class="stat-value"><?php echo number_format($stats['low_stock']); ?></div>
              <div class="stat-label">Stock Faible</div>
            </div>
            <div class="stat-icon warning">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div>
              <div class="stat-value"><?php echo number_format($stats['total_orders']); ?></div>
              <div class="stat-label">Commandes Total</div>
            </div>
            <div class="stat-icon info">
              <i class="fas fa-shopping-cart"></i>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div>
              <div class="stat-value"><?php echo number_format($stats['pending_orders']); ?></div>
              <div class="stat-label">En Attente</div>
            </div>
            <div class="stat-icon warning">
              <i class="fas fa-clock"></i>
            </div>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-header">
            <div>
              <div class="stat-value"><?php echo number_format($stats['total_revenue'], 0); ?></div>
              <div class="stat-label">Revenu (DZD)</div>
            </div>
            <div class="stat-icon success">
              <i class="fas fa-chart-line"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="two-column">

        <div class="dashboard-section">
          <div class="section-header">
            <h2 class="section-title">Commandes Récentes</h2>
            <a href="orders_management.php" class="section-link">
              Voir tout <i class="fas fa-arrow-right"></i>
            </a>
          </div>

          <div class="orders-list">
            <?php if (mysqli_num_rows($recent_orders) > 0): ?>
              <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                <div class="order-item">
                  <div class="order-id">#<?php echo $order['id']; ?></div>
                  <div class="order-info">
                    <h4><?php echo htmlspecialchars($order['nom_livraison']); ?></h4>
                    <p><?php echo date('d/m/Y H:i', strtotime($order['date_creation'])); ?></p>
                  </div>
                  <div class="order-amount"><?php echo number_format($order['total'], 0); ?> DZD</div>
                  <span class="status-badge status-<?php echo $order['statut']; ?>">
                    <?php echo str_replace('_', ' ', $order['statut']); ?>
                  </span>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Aucune commande récente</h3>
                <p>Les nouvelles commandes apparaîtront ici</p>
              </div>
            <?php endif; ?>
          </div>
        </div>


        <div class="dashboard-section">
          <div class="section-header">
            <h2 class="section-title">Stock Faible</h2>
            <a href="stock_management.php" class="section-link">
              Voir tout <i class="fas fa-arrow-right"></i>
            </a>
          </div>

          <div class="orders-list">
            <?php if (mysqli_num_rows($low_stock_products) > 0): ?>
              <?php while ($product = mysqli_fetch_assoc($low_stock_products)): ?>
                <div class="order-item">
                  <div class="order-info">
                    <h4><?php echo htmlspecialchars($product['nom']); ?></h4>
                    <p><?php echo htmlspecialchars($product['categorie'] ?? 'Sans catégorie'); ?></p>
                  </div>
                  <div class="product-stock low">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo $product['quantite_stock']; ?> restant(s)
                  </div>
                  <a href="Gstock.php?edit_id=<?php echo $product['id']; ?>" class="section-link">
                    Réapprovisionner <i class="fas fa-arrow-right"></i>
                  </a>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>Tout est bien approvisionné</h3>
                <p>Aucun produit n'a un stock faible</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>


      <div class="dashboard-section">
        <div class="section-header">
          <h2 class="section-title">Produits Populaires</h2>
          <a href="stock_management.php" class="section-link">
            Voir tout <i class="fas fa-arrow-right"></i>
          </a>
        </div>

        <div class="products-grid">
          <?php if (mysqli_num_rows($top_products) > 0): ?>
            <?php while ($product = mysqli_fetch_assoc($top_products)): ?>
              <div class="product-card">
                <div class="product-image">
                  <?php if (!empty($product['image1'])): ?>
                    <img src="../<?php echo htmlspecialchars($product['image1']); ?>" alt="<?php echo htmlspecialchars($product['nom']); ?>">
                  <?php endif; ?>
                  <?php if ($product['quantite_stock'] < 5): ?>
                    <span class="stock-badge">Stock Faible</span>
                  <?php endif; ?>
                </div>
                <div class="product-info">
                  <div class="product-name"><?php echo htmlspecialchars($product['nom']); ?></div>
                  <div class="product-price"><?php echo number_format($product['prix'], 0); ?> DZD</div>
                  <div class="product-stock <?php echo $product['quantite_stock'] < 5 ? 'low' : ''; ?>">
                    Stock: <?php echo $product['quantite_stock']; ?> | Vendus: <?php echo $product['total_sold']; ?>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="empty-state">
              <i class="fas fa-box-open"></i>
              <h3>Aucun produit disponible</h3>
              <p>Commencez par ajouter des produits</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>
</body>
</html>