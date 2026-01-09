<?php
session_start();

// Vérifier si l'admin est connecté
if (!isset($_SESSION['nom_utilisateur']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php?error=Accès non autorisé");
    exit();
}

include('../config.php');

// CHANGER LE STATUT D'UNE COMMANDE
if (isset($_POST['update_status'])) {
    $commande_id = intval($_POST['commande_id']);
    $nouveau_statut = mysqli_real_escape_string($con, $_POST['statut']);
    
    $update_query = "UPDATE commandes SET statut = ? WHERE id = ?";
    $stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($stmt, "si", $nouveau_statut, $commande_id);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: orders_management.php?success=Statut mis à jour avec succès");
        exit();
    } else {
        header("Location: orders_management.php?error=Erreur lors de la mise à jour");
        exit();
    }
}

// FILTRER PAR STATUT
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($con, $_GET['status']) : '';

$query = "SELECT c.*, u.nom_utilisateur, u.email, u.telephone,
          COUNT(dc.id) as nombre_articles
          FROM commandes c
          JOIN utilisateurs u ON c.utilisateur_id = u.id
          LEFT JOIN details_commande dc ON c.id = dc.commande_id";

if (!empty($filter_status)) {
    $query .= " WHERE c.statut = '$filter_status'";
}

$query .= " GROUP BY c.id ORDER BY c.date_creation DESC";

$result = mysqli_query($con, $query);

// STATISTIQUES
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN statut = 'en_attente' THEN 1 ELSE 0 END) as en_attente,
    SUM(CASE WHEN statut = 'confirmee' THEN 1 ELSE 0 END) as confirmee,
    SUM(CASE WHEN statut = 'expediee' THEN 1 ELSE 0 END) as expediee,
    SUM(CASE WHEN statut = 'livree' THEN 1 ELSE 0 END) as livree,
    SUM(CASE WHEN statut = 'annulee' THEN 1 ELSE 0 END) as annulee
    FROM commandes";
$stats_result = mysqli_query($con, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des Commandes - Atelier</title>
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
      background: var(--sidebar-border);
      color:var(--accent);
    }

    .alert-error {
      background-color: #fee2e2;
      color: #991b1b;
      border: 1px solid #ef4444;
    }

    /* Stats Grid */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 1.5rem;
      margin-bottom: 3rem;
    }

    .stat-card {
      background-color: var(--card);
      color: var(--primary);
      border: 0.5px solid var(--border);
      border-radius: var(--radius);
      padding: 1.5rem;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: var(--shadow-sm);
      text-decoration: none;
      display: block;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-xl);
      border-color: var(--primary);
    }

    .stat-card.active {
      background-color: var(--card);
      color:var(--accent);
    }

    .stat-number {
      font-size: 2.5rem;
      font-weight: 700;
      font-family: var(--font-serif);
      margin-bottom: 0.5rem;
      line-height: 1;
    }

    .stat-label {
      font-size: 0.875rem;
      color: var(--muted-foreground);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }


    /* Orders List */
    .orders-list {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .order-card {
      background-color: var(--card);
      border-radius: var(--radius);
      padding: 2rem;
      border: 0.5px solid var(--border);
      box-shadow: var(--shadow-sm);
      transition: all 0.3s ease;
    }

    .order-card:hover {
      box-shadow: var(--shadow-xl);
      transform: translateY(-2px);
    }

    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.5rem;
      padding-bottom: 1.5rem;
      border-bottom: 1px solid var(--border);
    }

    .order-id {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--primary);
      font-family: var(--font-serif);
    }

    .order-date {
      color: var(--muted-foreground);
      font-size: 0.875rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .order-body {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 2rem;
      margin-bottom: 1.5rem;
    }

    .customer-info h4 {
      font-size: 0.875rem;
      color: var(--muted-foreground);
      margin-bottom: 1rem;
      text-transform: uppercase;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .customer-info p {
      color: var(--foreground);
      margin: 0.75rem 0;
      font-size: 0.938rem;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .customer-info p i {
      width: 20px;
      color: var(--secondary);
    }

    .customer-info p strong {
      font-weight: 600;
    }

    .order-details {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem;
      background-color: var(--muted);
      border-radius: var(--radius);
    }

    .detail-label {
      color: var(--muted-foreground);
      font-size: 0.875rem;
      font-weight: 500;
    }

    .detail-value {
      font-weight: 700;
      color: var(--foreground);
      font-size: 1rem;
    }

    .total-amount {
      font-size: 1.75rem !important;
      color: var(--primary) !important;
      font-family: var(--font-serif);
    }

    .order-actions {
      display: flex;
      gap: 1rem;
      padding-top: 1.5rem;
      border-top: 1px solid var(--border);
      align-items: center;
    }

    .status-select {
      flex: 1;
      max-width: 100%;
      padding: 0.875rem 1rem 0.875rem 1rem;
      border: 1px solid var(--border);
      align-items: start;
      background-color: var(--muted);
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

    /* Status Badge */
    .status-badge {
      display: inline-block;
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

    .status-annulee {
      background-color: #fee2e2;
      color: #991b1b;
      border: 1px solid #ef4444;
    }

    .no-orders {
      text-align: center;
      padding: 4rem 2rem;
      color: var(--muted-foreground);
      background-color: var(--card);
      border-radius: var(--radius);
      border: 1px solid var(--border);
    }

    .no-orders i {
      font-size: 4rem;
      margin-bottom: 1.5rem;
      color: var(--border);
    }

    .no-orders h3 {
      font-size: 1.25rem;
      margin-bottom: 0.5rem;
      color: var(--foreground);
    }

    /* Responsive */
    @media (max-width: 1200px) {
      .order-body {
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
        grid-template-columns: repeat(3, 1fr);
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
        grid-template-columns: repeat(2, 1fr);
      }

      .order-actions {
        flex-direction: column;
      }

      .status-select {
        width: 100%;
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
          <a href="orders_management.php" class="nav-link active">
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
        <h1><i class="fas fa-shopping-cart"></i> Gestion des Commandes</h1>
        <p>Accepter, refuser et gérer toutes les commandes clients</p>
      </div>

      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
      <?php endif; ?>

      <!-- Stats -->
      <div class="stats-grid">
        <a href="orders_management.php" class="stat-card stat-all <?php echo empty($filter_status) ? 'active' : ''; ?>">
          <div class="stat-number"><?php echo $stats['total']; ?></div>
          <div class="stat-label">Toutes</div>
        </a>
        <a href="orders_management.php?status=en_attente" class="stat-card stat-pending <?php echo $filter_status === 'en_attente' ? 'active' : ''; ?>">
          <div class="stat-number"><?php echo $stats['en_attente']; ?></div>
          <div class="stat-label">En attente</div>
        </a>
        <a href="orders_management.php?status=confirmee" class="stat-card stat-confirmed <?php echo $filter_status === 'confirmee' ? 'active' : ''; ?>">
          <div class="stat-number"><?php echo $stats['confirmee']; ?></div>
          <div class="stat-label">Confirmées</div>
        </a>
        <a href="orders_management.php?status=expediee" class="stat-card stat-shipped <?php echo $filter_status === 'expediee' ? 'active' : ''; ?>">
          <div class="stat-number"><?php echo $stats['expediee']; ?></div>
          <div class="stat-label">Expédiées</div>
        </a>
        <a href="orders_management.php?status=livree" class="stat-card stat-delivered <?php echo $filter_status === 'livree' ? 'active' : ''; ?>">
          <div class="stat-number"><?php echo $stats['livree']; ?></div>
          <div class="stat-label">Livrées</div>
        </a>
        <a href="orders_management.php?status=annulee" class="stat-card stat-cancelled <?php echo $filter_status === 'annulee' ? 'active' : ''; ?>">
          <div class="stat-number"><?php echo $stats['annulee']; ?></div>
          <div class="stat-label">Annulées</div>
        </a>
      </div>

      <!-- Orders List -->
      <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="orders-list">
          <?php while ($order = mysqli_fetch_assoc($result)): ?>
            <div class="order-card">
              <div class="order-header">
                <div>
                  <span class="order-id">Commande #<?php echo $order['id']; ?></span>
                  <span class="status-badge status-<?php echo $order['statut']; ?>">
                    <?php echo ucfirst(str_replace('_', ' ', $order['statut'])); ?>
                  </span>
                </div>
                <div class="order-date">
                  <i class="fas fa-clock"></i>
                  <?php echo date('d/m/Y à H:i', strtotime($order['date_creation'])); ?>
                </div>
              </div>

              <div class="order-body">
                <!-- Customer Info -->
                <div class="customer-info">
                  <h4><i class="fas fa-user"></i> Informations Client</h4>
                  <p><i class="fas fa-user"></i> <strong><?php echo htmlspecialchars($order['nom_livraison']); ?></strong></p>
                  <p><i class="fas fa-at"></i> <?php echo htmlspecialchars($order['email'] ?? 'Non renseigné'); ?></p>
                  <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['telephone_livraison']); ?></p>
                  <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($order['adresse_livraison']); ?></p>
                </div>

                <!-- Order Details -->
                <div class="order-details">
                  <div class="detail-row">
                    <span class="detail-label">Articles</span>
                    <span class="detail-value"><?php echo $order['nombre_articles']; ?> produit(s)</span>
                  </div>
                  <div class="detail-row">
                    <span class="detail-label">Montant Total</span>
                    <span class="detail-value total-amount"><?php echo number_format($order['total'], 2); ?> DZD</span>
                  </div>
                </div>
              </div>

              <!-- Actions -->
              <div class="order-actions">
                <form method="POST" action="orders_management.php" style="flex: 1; display: flex; gap: 1rem; align-items: center;">
                  <input type="hidden" name="commande_id" value="<?php echo $order['id']; ?>">
                  <select name="statut" class="status-select" required>
                    <option value="">Changer le statut</option>
                    <option value="en_attente" <?php echo $order['statut'] === 'en_attente' ? 'selected' : ''; ?>>En attente</option>
                    <option value="confirmee" <?php echo $order['statut'] === 'confirmee' ? 'selected' : ''; ?>>✅ Confirmer</option>
                    <option value="expediee" <?php echo $order['statut'] === 'expediee' ? 'selected' : ''; ?>>📦 Expédier</option>
                    <option value="livree" <?php echo $order['statut'] === 'livree' ? 'selected' : ''; ?>>✔️ Livrer</option>
                    <option value="annulee" <?php echo $order['statut'] === 'annulee' ? 'selected' : ''; ?>>❌ Annuler</option>
                  </select>
                  <button type="submit" name="update_status" class="btn btn-primary">
                    <i class="fas fa-save"></i> Mettre à jour
                  </button>
                </form>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      <?php else: ?>
        <div class="no-orders">
          <i class="fas fa-inbox"></i>
          <h3>Aucune commande trouvée</h3>
          <p>Il n'y a pas de commandes correspondant à ce filtre</p>
        </div>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>