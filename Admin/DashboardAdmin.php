<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - Atelier </title>
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

    .dashboard-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 3rem;
    }

    .stat-card {
      background-color: var(--card);
      padding: 1.5rem;
      border-radius: var(--radius);
      box-shadow: var(--shadow-sm);
      text-align: center;
      border: 1px solid var(--border);
    }

    .stat-card i {
      font-size: 2rem;
      color: var(--primary);
      margin-bottom: 1rem;
    }

    .stat-card h3 {
      font-size: 1.25rem;
      margin-bottom: 0.5rem;
      color: var(--foreground);
    }

    .stat-card p {
      color: var(--muted-foreground);
      font-size: 0.875rem;
    }

    .admin-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
    }

    .action-card {
      background-color: var(--card);
      padding: 2rem;
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
      font-size: 2.5rem;
      color: var(--primary);
      margin-bottom: 1rem;
    }

    .action-card h3 {
      font-size: 1.25rem;
      margin-bottom: 0.5rem;
      color: var(--foreground);
    }

    .action-card p {
      color: var(--muted-foreground);
      margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
      .welcome-section {
        padding: 2rem 1rem;
      }

      .welcome-section h1 {
        font-size: 2rem;
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
        <a href="index1.php">
          <img src="../imgs/Atelier.png" alt="Atelier Logo">
        </a>
      </div>
      <nav class="admin-nav">
        <ul>
          <li><a href="DashboardAdmin.php"><i class="fas fa-home"></i> Home</a></li>
          <li><a href="Gstock.php"><i class="fas fa-boxes"></i> Gestion Stock</a></li>
          <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

    <!-- Hero Section -->
    <section class="hero-section">
    </section>

  <main class="admin-main">
    <div class="dashboard-stats">
      <div class="stat-card">
        <i class="fas fa-box-open"></i>
        <h3>Products</h3>
        <p>Manage your inventory</p>
      </div>
      <div class="stat-card">
        <i class="fas fa-chart-line"></i>
        <h3>Analytics</h3>
        <p>View store performance</p>
      </div>
      <div class="stat-card">
        <i class="fas fa-users"></i>
        <h3>Customers</h3>
        <p>Manage user accounts</p>
      </div>
    </div>

    <div class="admin-actions">
      <div class="action-card">
        <i class="fas fa-boxes"></i>
        <h3>Stock Management</h3>
        <p>Add, edit, or remove products from your inventory</p>
        <a href="Gstock.php" class="btn btn-primary">Manage Stock</a>
      </div>
      <div class="action-card">
        <i class="fas fa-chart-bar"></i>
        <h3>View Reports</h3>
        <p>Check sales and analytics reports</p>
        <button class="btn btn-secondary">View Reports</button>
      </div>
    </div>
  </main>
</body>
</html>