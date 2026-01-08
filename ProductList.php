<?php include('config.php'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Atelier</title>
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

    .store-logo img {
      height: 35px; 
      width: auto;
      display: flex;
      align-items: center;
      gap: 0.5rem;
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

    .filters {
      max-width: 1200px;
      margin: 1rem auto; 
      padding: 0 1rem;
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      align-items: center;
      justify-content: flex-start; 
      background-color: transparent; 
    }

    .filters select, 
    .filters input {
      flex: 1; 
      min-width: 200px; 
      max-width: 300px;  
      padding: 0.75rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      font-size: 0.9rem;
    }

    .filters button {
      padding: 0.75rem 2rem;
      background-color: var(--primary);
      color: var(--primary-foreground);
      border: none;
      border-radius: var(--radius);
      cursor: pointer;
      transition: opacity 0.2s;
    }

    .filters button:hover {
      background-color: var(--accent-foreground);
    }

    /* Conteneur pour la barre de recherche et l'historique */
    .search-container {
      position: relative;
      flex: 1;
      min-width: 200px;
      max-width: 300px;
    }

    .search-container input {
      width: 100%;
    }

    /* Liste de l'historique */
    .search-history {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background-color: white;
      border: 1px solid var(--border);
      border-top: none;
      border-radius: 0 0 var(--radius) var(--radius);
      box-shadow: var(--shadow);
      z-index: 1000;
      max-height: 250px;
      overflow-y: auto;
    }

    .search-history.show {
      display: block;
    }

    .search-history-item {
      padding: 0.75rem 1rem;
      cursor: pointer;
      transition: background-color 0.2s ease;
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .search-history-item:last-child {
      border-bottom: none;
    }

    .search-history-item:hover {
      background-color: #f5f5f5;
    }

    .search-history-item i {
      color: var(--muted-foreground);
      font-size: 0.9rem;
    }

    .search-history-empty {
      padding: 1rem;
      text-align: center;
      color: var(--muted-foreground);
      font-size: 0.9rem;
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
      display: flex;
      flex-direction: column;
      height: 100%; 
      background-color: var(--card);
      border-radius: var(--radius);
      border: 1px solid var(--border);
      overflow: hidden;
      box-shadow: var(--shadow-sm);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .product-content {
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      flex-grow: 1;
      text-align: center;
    }

    .product-title {
      font-size: 1.2rem;
      margin-bottom: 0.5rem;
      min-height: 3rem; 
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .product-price {
      font-weight: bold;
      font-size: 1.4rem;
      color: #4a0404;
      margin-bottom: 1.5rem;
    }

    .view-details {
      display: inline-block;
      padding: 0.8rem 1.5rem;
      background-color: #2d0a0a;
      color: white !important;
      text-decoration: none;
      border-radius: 4px;
      margin: auto auto 0 auto;
      width: fit-content;
      font-weight: 500;
      transition: opacity 0.2s;
    }

    .view-details:hover {
      opacity: 0.9;
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
  <header class="store-header">
    <div class="store-header-content">
      <div class="store-logo">
        <a href="index1.php">
          <img src="imgs/Atelier.png" alt="Atelier Logo">
        </a>
      </div>

      <nav class="store-nav">
        <a href="index1.php">Accueil</a>
        <a href="ProductList.php">Produits</a>
        <a href="panier/view_cart.php">Panier</a>
        <a href="panier/mes_commandes.php">Mes commandes</a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <a href="Admin/DashboardAdmin.php">Administration</a>
        <?php endif; ?>
        <?php if (isset($_SESSION['nom_utilisateur'])): ?>
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="Admin/logout.php">Déconnexion (<?= htmlspecialchars($_SESSION['nom_utilisateur']) ?>)</a>
          <?php else: ?>
            <a href="Client/logout.php">Déconnexion (<?= htmlspecialchars($_SESSION['nom_utilisateur']) ?>)</a>
          <?php endif; ?>
        <?php else: ?>
          <a href="Client/index.php">Connexion</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <main>    
    <!-- Filtres -->
    <div id="filters" class="filters">
      <!-- Conteneur pour la recherche avec historique -->
      <div class="search-container">
        <input type="text" id="searchBar" placeholder="Rechercher une montre..." autocomplete="off">
        <div id="searchHistory" class="search-history">
          <?php
          // Récupérer l'historique depuis les cookies
          $userId = isset($_SESSION['id']) ? $_SESSION['id'] : 'guest';
          $cookieName = 'search_history_' . $userId;
          $history = isset($_COOKIE[$cookieName]) ? explode('|', $_COOKIE[$cookieName]) : [];
          
          if (!empty($history)) {
              foreach ($history as $term) {
                  echo '<div class="search-history-item" onclick="selectHistoryItem(\'' . htmlspecialchars($term, ENT_QUOTES) . '\')">';
                  echo '<i class="fas fa-history"></i>';
                  echo '<span>' . htmlspecialchars($term) . '</span>';
                  echo '</div>';
              }
          } else {
              echo '<div class="search-history-empty">Aucun historique de recherche</div>';
          }
          ?>
        </div>
      </div>

      <select id="categorie">
        <option value="">Toutes les catégories</option>
        <option value="Luxury">Luxe</option>
        <option value="Sport">Sport</option>
        <option value="Dress">Habillées</option>
        <option value="Casual">Casual</option>
        <option value="Smart">Connectées</option>
      </select>

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

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-grid">
        <div class="footer-section">
          <h3>À propos de Atelier</h3>
          <p>Notre atelier s'engage à vous offrir des produits artisanaux de qualité, fabriqués avec des matériaux nobles et une attention particulière aux détails.</p>
        </div>

        <div class="footer-section">
          <h3>Liens rapides</h3>
          <div class="footer-links">
            <a href="index1.php">Accueil</a>
            <a href="#filters">Nouveautés</a>
            <a href="#contact">Contact</a>
          </div>
        </div>

        <div class="footer-section">
          <h3>Contact</h3>
          <div class="footer-links">
            <a href="tel:+213123456789"><i class="fas fa-phone"></i> +213 123 456 789</a>
            <a href="mailto:contact@kelthouma.com"><i class="fas fa-envelope"></i> contact@kAtelier.com</a>
            <a href="#"><i class="fas fa-map-marker-alt"></i> Alger, Algérie</a>
          </div>
        </div>

        <div class="footer-section">
          <h3>Suivez-nous</h3>
          <div class="footer-social">
            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <p>&copy; 2025 Atelier || Watch Store. Tous droits réservés.</p>
      </div>
    </div>
  </footer>

  <script>
    const searchBar = document.getElementById("searchBar");
    const searchHistory = document.getElementById("searchHistory");

    // Afficher l'historique quand on focus sur la barre de recherche
    searchBar.addEventListener("focus", function() {
      searchHistory.classList.add("show");
    });

    // Cacher l'historique quand on clique ailleurs
    document.addEventListener("click", function(event) {
      if (!event.target.closest('.search-container')) {
        searchHistory.classList.remove("show");
      }
    });

    // Sélectionner un élément de l'historique
    function selectHistoryItem(term) {
      searchBar.value = term;
      searchHistory.classList.remove("show");
      filterProducts();
    }

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
          // Recharger la page pour mettre à jour l'historique dans les cookies
          if (searchTerm) {
            setTimeout(() => location.reload(), 100);
          }
        })
        .catch(error => console.error('Erreur de filtrage:', error));
    }

    searchBar.addEventListener("keyup", function(event) {
      if (event.key === "Enter") {
        filterProducts();
      }
    });
  </script>
</body>
</html>