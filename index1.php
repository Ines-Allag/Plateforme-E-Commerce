<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Atelier</title>
  <link rel="stylesheet" href="global.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    /* Store Header */
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
      margin: 15px 50px 15px 50px;
      padding: 0 1rem;
      gap: 0.5rem;
    }

    .store-logo {
      font-family: var(--font-serif);
      font-size: 1.5rem;
      font-weight: bold;
    }

    .store-logo img {
      height: 35px; 
      width: auto;
    }

    .store-logo span {
      color: var(--accent);
    }

    /* Navigation */
    .store-nav {
      display: flex;
      gap: 1.5rem;
      align-items: center;
    }

    .store-nav a {
      color: var(--primary-foreground);
      padding: 0.5rem 1rem;
      border-radius: var(--radius);
      transition: background-color 0.2s ease;
      font-weight: 100;
      white-space: nowrap; /* Prevent text from wrapping */
    }

    .store-nav a:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }

    /* Header Controls */
    .header-controls {
      display: flex;
      align-items: center;
      gap: 1.5rem;
    }

    .search-container {
      position: relative;
      width: 300px;
    }

    .search-input {
      width: 100%;
      padding: 0.75rem 1rem 0.75rem 2.5rem;
      background-color: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: var(--radius);
      color: var(--primary-foreground);
      font-size: 0.875rem;
      transition: all 0.2s ease;
      height: 42px;
      box-sizing: border-box;
    }

    .search-input::placeholder {
      color: rgba(255, 255, 255, 0.7);
    }

    .search-input:focus {
      background-color: rgba(255, 255, 255, 0.05);
      border-color: rgba(255, 255, 255, 0.3);
      box-shadow: 0 0 0 2px var(--accent);
    }

    .search-icon {
      position: absolute;
      left: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(255, 255, 255, 0.7);
    }

    /* Filters */
    .filters-container {
      display: flex;
      gap: 1rem;
      align-items: center;
    }

    .filter-select {
      padding: 0.75rem 1rem; /* Increased padding to match search bar */
      background-color: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: var(--radius);
      color: var(--primary-foreground);
      font-size: 0.875rem;
      min-width: 150px;
      cursor: pointer;
      transition: all 0.2s ease;
      height: 42px; /* Same height as search bar */
      box-sizing: border-box;
    }

    .filter-select:hover {
      background-color: rgba(255, 255, 255, 0.15);
    }

    .filter-select:focus {
      outline: none;
      box-shadow: 0 0 0 2px var(--accent);
    }

    /* User Actions */
    .user-actions {
      display: flex;
      gap: 1rem;
    }

    .action-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 2.5rem;
      height: 2.5rem;
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      color: var(--primary-foreground);
      transition: all 0.2s ease;
      position: relative;
    }

    .action-btn:hover {
      background-color: rgba(255, 255, 255, 0.2);
      transform: translateY(-1px);
    }

    /* Hero Section */
    .hero-section {
      position: relative;
      height: 500px;
      background: url('imgs/banner1.png');
      background-size: cover;
      background-position: center;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      color: var(--primary-foreground);
      margin-bottom: 3rem;
    }

    .hero-content {
      max-width: 800px;
      padding: 2rem;
    }

    .hero-title {
      font-family: var(--font-serif);
      font-size: 3rem;
      margin-bottom: 1rem;
      color: var(--accent);
    }

    .hero-subtitle {
      font-size: 1.25rem;
      opacity: 0.9;
      margin-bottom: 2rem;
    }

    /* Products Grid */
    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 2rem;
      padding: 2rem 1rem;
      max-width: 1200px;
      margin: 0 auto;
    }

    /* Product Card */
    .product-card {
      background-color: var(--card);
      border-radius: var(--radius);
      box-shadow: var(--shadow-sm);
      overflow: hidden;
      border: 1px solid var(--border);
      transition: all 0.3s ease;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-lg);
    }

    .product-image {
      position: relative;
      width: 100%;
      height: 200px;
      overflow: hidden;
    }

    .product-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.3s ease;
    }

    .product-card:hover .product-image img {
      transform: scale(1.05);
    }

    .product-content {
      padding: 1.5rem;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .product-title {
      font-family: var(--font-serif);
      font-size: 1.25rem;
      margin-bottom: 0.5rem;
      color: var(--foreground);
    }

    .product-description {
      color: var(--muted-foreground);
      font-size: 0.875rem;
      margin-bottom: 1rem;
      flex: 1;
    }

    .product-price {
      font-size: 1.5rem;
      font-weight: bold;
      color: var(--primary);
      margin-bottom: 1rem;
    }

    .product-actions {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .quantity-input {
      width: 100%;
      padding: 0.5rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      background-color: var(--input);
      color: var(--foreground);
    }

    .add-to-cart-btn {
      padding: 0.75rem;
      background-color: var(--primary);
      color: var(--primary-foreground);
      border-radius: var(--radius);
      font-weight: 500;
      transition: all 0.2s ease;
      text-align: center;
    }

    .add-to-cart-btn:hover {
      background-color: color-mix(in srgb, var(--primary) 90%, black);
      transform: translateY(-1px);
      box-shadow: var(--shadow-sm);
    }

    .view-details-btn {
      padding: 0.5rem;
      background-color: var(--secondary);
      color: var(--secondary-foreground);
      border-radius: var(--radius);
      font-size: 0.875rem;
      text-align: center;
      transition: all 0.2s ease;
    }

    .view-details-btn:hover {
      background-color: color-mix(in srgb, var(--secondary) 90%, black);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .store-header-content {
        flex-direction: column;
        gap: 1rem;
        padding: 1rem;
      }

      .store-nav {
        order: 3;
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
        gap: 0.5rem;
      }

      .store-nav a {
        white-space: normal; /* Allow wrapping on mobile */
      }

      .header-controls {
        order: 2;
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
      }

      .search-container {
        width: 100%;
      }

      .filters-container {
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
      }

      .filter-select {
        height: auto; /* Auto height on mobile */
        min-height: 42px; /* Minimum height */
      }

      .store-logo img {
        height: 35px; /* Slightly smaller logo on mobile */
      }

      .hero-title {
        font-size: 2rem;
      }

      .hero-subtitle {
        font-size: 1rem;
      }

      .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
        padding: 1rem;
      }
    }

    @media (max-width: 480px) {
      .products-grid {
        grid-template-columns: 1fr;
      }

      .hero-section {
        height: 400px;
      }

      .store-logo img {
        height: 30px; /* Even smaller logo on very small screens */
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="store-header">
    <div class="store-header-content">
      <div class="store-logo">
        <!-- Replace text with logo image -->
        <a href="index1.php">
          <img src="Atelier.png" alt="Atelier Logo">
        </a>
      </div>

      <!-- Navigation -->
      <nav class="store-nav">
        <a href="index1.php">Home</a>
        <a href="#products">Nos Produits</a>
      </nav>

      <!-- Header Controls -->
      <div class="header-controls">
        <div class="search-container">
          <i class="fas fa-search search-icon"></i>
          <input type="text" 
                 id="searchBar" 
                 class="search-input" 
                 placeholder="Rechercher un produit..."
                 oninput="filterProducts()">
        </div>

        <!-- Filters Container -->
        <div class="filters-container">
          <!-- Category Filter -->
          <div class="dropdown">
            <select class="filter-select" id="categorie" onchange="filterProducts()">
              <option value="">Toutes les catégories</option>
              <option value="Pc">PC</option>
              <option value="Souris">Souris</option>
              <option value="Claviers">Claviers</option>
              <option value="Casques">Casques</option>
            </select>
          </div>

          <!-- Price Filter -->
          <div class="dropdown">
            <select class="filter-select" id="prixFilter" onchange="filterProducts()">
              <option value="">Tous les prix</option>
              <option value="0-29999">0 - 29 999 DZD</option>
              <option value="30000-49999">30 000 - 49 999 DZD</option>
              <option value="50000-99999">50 000 - 99 999 DZD</option>
              <option value="100000-199999">100 000 - 199 999 DZD</option>
              <option value="200000-350000">200 000 - 350 000 DZD</option>
            </select>
          </div>
        </div>

        <!-- User Actions -->
        <div class="user-actions">
          <a href="Client/index.php" class="action-btn" title="Mon compte">
            <i class="fas fa-user"></i>
          </a>
          <a href="panier/view_cart.php" class="action-btn" title="Panier">
            <i class="fas fa-shopping-cart"></i>
          </a>
          <a href="Client/logout.php" class="action-btn" title="Déconnexion">
            <i class="fas fa-sign-out-alt"></i>
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero-section">
  </section>

  <!-- Products Section -->
  <section id="products" class="products-section">
    <div class="products-grid" id="section2">
      <?php 
      include('config.php'); 
      $result = mysqli_query($con, "SELECT * FROM produits");
      
      while ($row = mysqli_fetch_array($result)) {  
        echo "
          <div class='product-card'>
            <a href='product_details.php?id=" . $row['id'] . "' class='product-image'>
              <img src='" . $row['img'] . "' alt='" . htmlspecialchars($row['name']) . "'>
            </a>
            <div class='product-content'>
              <h3 class='product-title'>" . htmlspecialchars($row['name']) . "</h3>
              <p class='product-description'>" . htmlspecialchars($row['description']) . "</p>
              <div class='product-price'>" . number_format($row['prix'], 2) . " DZD</div>
              <div class='product-actions'>
                <form action='panier/add_to_cart.php' method='post'>
                  <input type='hidden' name='produit_id' value='" . $row['id'] . "'>
                  <input type='hidden' name='prix' value='" . $row['prix'] . "'>
                  <input type='number' name='quantite' value='1' min='1' class='quantity-input' required>
                  <button type='submit' class='add-to-cart-btn'>
                    <i class='fas fa-cart-plus'></i> Ajouter au panier
                  </button>
                </form>
                <a href='product_details.php?id=" . $row['id'] . "' class='view-details-btn'>
                  <i class='fas fa-eye'></i> Voir détails
                </a>
              </div>
            </div>
          </div>
        ";
      } 
      ?>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-grid">
        <div class="footer-section">
          <h3>À propos de KELTHOUMA</h3>
          <p>Nous sommes un magasin technologique qui propose des produits électroniques de haute qualité à des prix compétitifs.</p>
        </div>

        <div class="footer-section">
          <h3>Liens rapides</h3>
          <div class="footer-links">
            <a href="index1.php">Accueil</a>
            <a href="#products">Nouveautés</a>
            <a href="#contact">Contact</a>
          </div>
        </div>

        <div class="footer-section">
          <h3>Contact</h3>
          <div class="footer-links">
            <a href="tel:+213123456789"><i class="fas fa-phone"></i> +213 123 456 789</a>
            <a href="mailto:contact@kelthouma.com"><i class="fas fa-envelope"></i> contact@kelthouma.com</a>
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
        <p>&copy; 2024 KELTHOUMA || Tech Store. Tous droits réservés.</p>
      </div>
    </div>
  </footer>

  <script>
    function filterProducts() {
      const category = document.getElementById("categorie").value;
      const searchTerm = document.getElementById("searchBar").value;
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
        .catch(error => console.error('Erreur:', error));
    }
  </script>
</body>
</html>