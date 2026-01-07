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
      max-width: 1600px;
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

    /* Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background-color: var(--card);
      padding: 1.25rem;
      border-radius: var(--radius);
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border);
      text-align: center;
      cursor: pointer;
      transition: transform 0.2s ease;
    }

    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow);
    }

    .stat-card.active {
      border: 2px solid var(--primary);
    }

    .stat-number {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 0.25rem;
    }

    .stat-label {
      font-size: 0.875rem;
      color: var(--muted-foreground);
      font-weight: 500;
    }

    .stat-all .stat-number { color: #3b82f6; }
    .stat-pending .stat-number { color: #f59e0b; }
    .stat-confirmed .stat-number { color: #8b5cf6; }
    .stat-shipped .stat-number { color: #06b6d4; }
    .stat-delivered .stat-number { color: #10b981; }
    .stat-cancelled .stat-number { color: #ef4444; }

    /* Orders List */
    .orders-list {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .order-card {
      background-color: var(--card);
      border-radius: var(--radius);
      padding: 1.5rem;
      border: 1px solid var(--border);
      box-shadow: var(--shadow-sm);
      transition: box-shadow 0.2s ease;
    }

    .order-card:hover {
      box-shadow: var(--shadow);
    }

    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--border);
    }

    .order-id {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--foreground);
    }

    .order-date {
      color: var(--muted-foreground);
      font-size: 0.875rem;
    }

    .order-body {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 2rem;
      margin-bottom: 1rem;
    }

    .customer-info h4 {
      font-size: 0.875rem;
      color: var(--muted-foreground);
      margin-bottom: 0.5rem;
      text-transform: uppercase;
      font-weight: 600;
    }

    .customer-info p {
      color: var(--foreground);
      margin: 0.25rem 0;
      font-size: 0.9rem;
    }

    .customer-info p i {
      width: 20px;
      color: var(--primary);
    }

    .order-details {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem;
      background-color: var(--muted);
      border-radius: var(--radius);
    }

    .detail-label {
      color: var(--muted-foreground);
      font-size: 0.875rem;
    }

    .detail-value {
      font-weight: 600;
      color: var(--foreground);
    }

    .total-amount {
      font-size: 1.5rem !important;
      color: var(--primary) !important;
    }

    .order-actions {
      display: flex;
      gap: 1rem;
      padding-top: 1rem;
      border-top: 1px solid var(--border);
    }

    .status-select {
      flex: 1;
      padding: 0.75rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background-color: var(--input);
      color: var(--foreground);
      font-size: 0.875rem;
      font-weight: 500;
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
    }

    /* Status Badge */
    .status-badge {
      display: inline-block;
      padding: 0.375rem 1rem;
      border-radius: 9999px;
      font-size: 0.813rem;
      font-weight: 600;
    }

    .status-en_attente { 
      background-color: #fef3c7; 
      color: #92400e; 
    }
    .status-confirmee { 
      background-color: #e0e7ff; 
      color: #4338ca; 
    }
    .status-expediee { 
      background-color: #cffafe; 
      color: #0e7490; 
    }
    .status-livree { 
      background-color: #d1fae5; 
      color: #065f46; 
    }
    .status-annulee { 
      background-color: #fee2e2; 
      color: #991b1b; 
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
      margin-bottom: 1rem;
      color: var(--border);
    }

    @media (max-width: 968px) {
      .order-body {
        grid-template-columns: 1fr;
      }

      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .admin-nav ul {
        flex-direction: column;
        gap: 0.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="admin-container">
    <!-- ===================================== -->
    <!-- NAVIGATION BAR - FIXED VERSION ✅ -->
    <!-- ===================================== -->
    <header class="admin-header">
      <div class="admin-header-content">
        <div class="admin-logo">
          <a href="DashboardAdmin.php">
            <img src="../imgs/Atelier.png" alt="Atelier Logo">
          </a>
        </div>
        <nav class="admin-nav">
          <ul>
            <!-- Home link -->
            <li><a href="DashboardAdmin.php"><i class="fas fa-home"></i> Home</a></li>
            
            <!-- ⚠️ CHANGED: Was Gstock.php, now stock_management.php -->
            <li><a href="stock_management.php"><i class="fas fa-boxes"></i> Gestion Stock</a></li>
            
            <!-- Orders link (current page) -->
            <li><a href="orders_management.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
            
            <!-- Logout link -->
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
          </ul>
        </nav>
      </div>
    </header>

    <div class="page-header">
      <h1><i class="fas fa-shopping-cart"></i> Gestion des Commandes</h1>
      <p>Accepter, refuser et gérer toutes les commandes clients</p>
    </div>

    <div class="main-content">
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
        <a href="orders_management.php" style="text-decoration: none;">
          <div class="stat-card stat-all <?php echo empty($filter_status) ? 'active' : ''; ?>">
            <div class="stat-number"><?php echo $stats['total']; ?></div>
            <div class="stat-label">Toutes</div>
          </div>
        </a>
        <a href="orders_management.php?status=en_attente" style="text-decoration: none;">
          <div class="stat-card stat-pending <?php echo $filter_status === 'en_attente' ? 'active' : ''; ?>">
            <div class="stat-number"><?php echo $stats['en_attente']; ?></div>
            <div class="stat-label">En attente</div>
          </div>
        </a>
        <a href="orders_management.php?status=confirmee" style="text-decoration: none;">
          <div class="stat-card stat-confirmed <?php echo $filter_status === 'confirmee' ? 'active' : ''; ?>">
            <div class="stat-number"><?php echo $stats['confirmee']; ?></div>
            <div class="stat-label">Confirmées</div>
          </div>
        </a>
        <a href="orders_management.php?status=expediee" style="text-decoration: none;">
          <div class="stat-card stat-shipped <?php echo $filter_status === 'expediee' ? 'active' : ''; ?>">
            <div class="stat-number"><?php echo $stats['expediee']; ?></div>
            <div class="stat-label">Expédiées</div>
          </div>
        </a>
        <a href="orders_management.php?status=livree" style="text-decoration: none;">
          <div class="stat-card stat-delivered <?php echo $filter_status === 'livree' ? 'active' : ''; ?>">
            <div class="stat-number"><?php echo $stats['livree']; ?></div>
            <div class="stat-label">Livrées</div>
          </div>
        </a>
        <a href="orders_management.php?status=annulee" style="text-decoration: none;">
          <div class="stat-card stat-cancelled <?php echo $filter_status === 'annulee' ? 'active' : ''; ?>">
            <div class="stat-number"><?php echo $stats['annulee']; ?></div>
            <div class="stat-label">Annulées</div>
          </div>
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
                <form method="POST" action="orders_management.php" style="flex: 1; display: flex; gap: 1rem;">
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
          <h2>Aucune commande trouvée</h2>
          <p>Il n'y a pas de commandes correspondant à ce filtre</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>