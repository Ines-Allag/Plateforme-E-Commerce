<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Watch Store - Boutique de Montres</title>
  <link rel="stylesheet" href="global.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    /* Header boutique */
    .store-header {
      background-color: var(--primary);
      color: var(--primary-foreground);
      padding: 0.75rem 0;
      box-shadow: var(--shadow);
    }

    .store-header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1200px;
      margin: 15px 50px;
      padding: 0 1rem;
      gap: 0.5rem;
    }

    .store-logo {
      font-family: var(--font-serif);
      font-size: 1.8rem;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .store-logo span {
      color: var(--accent);
    }

    .store-nav a {
      color: var(--primary-foreground);
      padding: 0.5rem 1rem;
      border-radius: var(--radius);
      transition: background-color 0.2s ease;
    }

    .store-nav a:hover {
      background-color: rgba(255,255,255,0.1);
    }

    /* Filtres */
    .filters {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 1rem;
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      align-items: center;
      justify-content: center;
      background-color: var(--card);
      border-radius: var(--radius);
      box-shadow: var(--shadow-sm);
    }

    .filters select, .filters input, .filters button {
      padding: 0.75rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      font-size: 1rem;
    }

    .filters button {
      background-color: var(--primary);
      color: var(--primary-foreground);
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .filters button:hover {
      background-color: var(--accent-foreground);
    }

    /* Grid produits */
    .products-grid {
      max-width: 1200px;
      margin: 2rem auto;
      padding: 0 1rem;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 2rem;
    }

    .product-card {
      background-color: var(--card);
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .product-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow);
    }

    .product-image img {
      width: 100%;
      height: 300px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .product-card:hover .product-image img {
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <?php include('config.php'); ?>

  <header class="store-header">
    <div class="store-header-content">
      <div class="store-logo">
        <span>Watch Store</span>
      </div>

      <nav class="store-nav">
        <a href="index1.php">Accueil</a>
        <a href="#">Montres</a>
        <a href="panier/view_cart.php">Panier <i class="fas fa-shopping-cart"></i></a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <a href="Admin/DashboardAdmin.php">Administration</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['nom_utilisateur'])): ?>
          <a href="logout.php">Déconnexion (<?= htmlspecialchars($_SESSION['nom_utilisateur']) ?>)</a>
        <?php else: ?>
          <a href="Client/index.php">Connexion</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main>
    <!-- Filtres -->
    <div class="filters">
      <select id="categorie">
        <option value="">Toutes les catégories</option>
        <option value="Luxury">Luxe</option>
        <option value="Sport">Sport</option>
        <option value="Dress">Habillées</option>
        <option value="Casual">Casual</option>
        <option value="Smart">Connectées</option>
      </select>

      <input type="text" id="searchBar" placeholder="Rechercher une montre...">

      <select id="prixFilter">
        <option value="">Tous les prix</option>
        <option value="0-500">0 - 500 DZD</option>
        <option value="500-1000">500 - 1 000 DZD</option>
        <option value="1000-5000">1 000 - 5 000 DZD</option>
        <option value="5000-10000">5 000 - 10 000 DZD</option>
        <option value="10000-999999">+ 10 000 DZD</option>
      </select>

      <button onclick="filterProducts()">Filtrer</button>
    </div>

    <!-- Liste des produits -->
    <section id="section2" class="products-grid">
      <?php
      $query = "SELECT id, nom, prix, image1, categorie FROM produits ORDER BY date_ajout DESC";
      $result = mysqli_query($con, $query);

      if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
              echo "
              <div class='product-card'>
                  <a href='product_details.php?id=" . $row['id'] . "' class='product-image'>
                      <img src='" . htmlspecialchars($row['image1']) . "' alt='" . htmlspecialchars($row['nom']) . "'>
                  </a>
                  <div class='product-content'>
                      <h3 class='product-title'>" . htmlspecialchars($row['nom']) . "</h3>
                      <p class='product-price'>" . number_format($row['prix'], 2) . " DZD</p>
                      <a href='product_details.php?id=" . $row['id'] . "' class='view-details'>Voir les détails</a>
                  </div>
              </div>
              ";
          }
      } else {
          echo '<p style="grid-column: 1 / -1; text-align: center; font-size: 1.2rem;">Aucune montre disponible pour le moment.</p>';
      }
      ?>
    </section>
  </main>

  <!-- Footer (tu gardes ton footer existant ici) -->

  <script>
    function filterProducts() {
      const category = document.getElementById("categorie").value;
      const searchTerm = document.getElementById("searchBar").value.trim();
      const prixRange = document.getElementById("prixFilter").value;

      let url = "recherche.php?";
      if (category) url += "categorie=" + encodeURIComponent(category) + "&";
      if (searchTerm) url += "query=" + encodeURIComponent(searchTerm) + "&";
      if (prixRange) url += "prix=" + encodeURIComponent(prixRange);

      fetch(url)
        .then(response => response.text())
        .then(data => {
          document.getElementById("section2").innerHTML = data;
        })
        .catch(error => console.error('Erreur de filtrage:', error));
    }
  </script>
</body>
</html>