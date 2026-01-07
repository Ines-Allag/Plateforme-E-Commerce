<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - Atelier</title>
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

    .admin-logo img {
      height: 35px; 
      width: auto;
      display: flex;
      align-items: center;
      gap: 0.5rem;
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
      max-width: 1200px;
      margin: 0 auto;
      width: 100%;
    }

    /* Hero Section */
    .hero-section {
      position: relative;
      height: 500px;
      background: url('../imgs/banner2.png');
      background-size: cover;
      background-position: center;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: var(--primary-foreground);
      margin-bottom: 3rem;
    }

    .admin-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
      gap: 2rem;
    }

    .action-card {
      background-color: var(--card);
      padding: 2.5rem;
      border-radius: var(--radius);
      box-shadow: var(--shadow-sm);
      text-align: center;
      border: 1px solid var(--border);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .action-card:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow);
    }

    .action-card i {
      font-size: 3rem;
      color: var(--primary);
      margin-bottom: 1.5rem;
    }

    .action-card h3 {
      font-size: 1.5rem;
      margin-bottom: 1rem;
      color: var(--foreground);
    }

    .action-card p {
      color: var(--muted-foreground);
      margin-bottom: 2rem;
      line-height: 1.6;
    }

    @media (max-width: 768px) {
      .admin-nav ul {
        flex-direction: column;
        gap: 0.5rem;
      }

      .admin-actions {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body class="admin-container">
  <!-- ===================================== -->
  <!-- NAVIGATION BAR - UPDATED VERSION ✅ -->
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
          <!-- Home link - goes to dashboard -->
          <li><a href="DashboardAdmin.php"><i class="fas fa-home"></i> Home</a></li>
          
          <!-- ⚠️ CHANGED: Now links to stock_management.php (product list) -->
          <!-- Before: <a href="Gstock.php"> -->
          <!-- After:  <a href="stock_management.php"> -->
          <li><a href="stock_management.php"><i class="fas fa-boxes"></i> Gestion Stock</a></li>
          
          <!-- Orders link -->
          <li><a href="orders_management.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
          
          <!-- Logout link -->
          <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero-section">
  </section>

  <main class="admin-main">
    <div class="admin-actions">

      <div class="action-card">
        <i class="fas fa-boxes"></i>
        <h3>Gestion du Stock</h3>
        <p>Voir, ajouter, modifier ou supprimer des produits de votre inventaire</p>
        <a href="stock_management.php" class="btn btn-primary">Gérer le Stock</a>
      </div>

      <!-- Orders Management Card - No changes -->
      <div class="action-card">
        <i class="fas fa-shopping-cart"></i>
        <h3>Gestion des Commandes</h3>
        <p>Accepter, refuser ou gérer les commandes clients</p>
        <a href="orders_management.php" class="btn btn-primary">Gérer Commandes</a>
      </div>
    </div>
  </main>
</body>
</html>