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

    /* Filtres */
    .filters {
      max-width: 1200px;
      margin: 1rem auto;
      padding: 1rem;
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      align-items: center;
      justify-content: left;
      background-color: var(--card);
    }

    .filters select, .filters input, .filters button {
      min-width: 250px;
      padding: 0.75rem;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      font-size: 1rem;
    }

    .filters button {
      min-width: 150px;
      background-color: var(--primary);
      color: var(--primary-foreground);
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .filters button:hover {
      background-color: var(--accent-foreground);
    }

    /* Hero Section */
    .hero-section {
      position: relative;
      height: 700px; 
      background: linear-gradient(to right, rgba(0, 0, 0, 0.09) 0%, transparent 0%), url('imgs/banner.png');
      background-size: cover;
      background-position: center;
      background-attachment: fixed; 
      display: flex;
      align-items: center; 
      justify-content: flex-start;
      color: var(--primary-foreground);
      padding: 0 10%;
      z-index: 1;
    }

    .hero-content {
      max-width: 600px;
      text-align: left;
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }

    .hero-logo {
      width: 350px; /* Adjust based on your logo size */
      height: auto;
    }

    .hero-subtitle {
      font-family: 'Poppins', sans-serif;
      font-size: 1.8rem;
      font-weight: 10;
      letter-spacing: 2px;
      text-transform: lowercase;
    }

    .hero-btn {
      width: fit-content;
      padding: 1rem 2.5rem;
      background-color: #b08d6d; /* Matches the bronze/tan color in your pic */
      color: white;
      border: none;
      border-radius: var(--radius);
      font-size: 1.1rem;
      cursor: pointer;
      transition: transform 0.2s, background-color 0.2s;
    }

    .hero-btn:hover {
      background-color: #8e7056;
      transform: translateY(-2px);
    }

    /* About Us Section */
    .about-section {
      max-width: 1200px;
      margin: 4rem auto;
      padding: 0 1rem;
      display: flex;
      align-items: center;
      gap: 4rem;
    }

    .about-content {
      flex: 1;
      max-width: 40%;
    }

    .about-content p {
      font-size: 1.1rem;
      line-height: 1.8;
      color: var(--muted-foreground);
      margin-bottom: 1.5rem;
    }

    .about-image {
      flex: 1.5;
      border-radius: var(--radius);
      overflow: hidden;
      max-width: 55%;
    }

    .about-image img {
      width: 100%;
      height: 500px;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    /* Quote Section */
    .quote-section {
      background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('imgs/quote.png');
      background-size: cover;
      padding: 6rem 1rem;
      text-align: center;
      color: var(--primary-foreground);
    }

    .quote-container {
      max-width: 800px;
      margin: 0 auto;
    }

    .quote-text {
      font-family: 'Libre Baskerville', serif;
      font-size: 2rem;
      font-style: italic;
      line-height: 1.6;
      margin-bottom: 2rem;
      position: relative;
    }

    .quote-text::before,
    .quote-text::after {
      content: '"';
      font-size: 3rem;
      color: var(--secondary);
      position: absolute;
    }

    .quote-text::before {
      top: -1rem;
      left: -2rem;
    }

    .quote-text::after {
      bottom: -2rem;
      right: -2rem;
    }

    .quote-author {
      font-size: 1.2rem;
      font-weight: 600;
      color: var(--secondary);
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

/* Assure que toutes les cartes ont la même hauteur */
    .product-card {
        display: flex;
        flex-direction: column;
        height: 100%; /* Important pour l'alignement */
        background-color: var(--card);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    /* Conteneur de texte sous l'image */
    .product-content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1; /* Remplit l'espace vide pour pousser le bouton en bas */
        text-align: center; /* Centre le texte comme sur votre image 2 */
    }

    .product-title {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        min-height: 3rem; /* Réserve l'espace pour 2 lignes de titre */
    }

    .product-price {
        font-weight: bold;
        font-size: 1.4rem;
        color: #4a0404; /* Couleur sombre comme sur l'image */
        margin-bottom: 1.5rem;
    }

    /* Transformation du lien en bouton sombre centré */
    .view-details {
        display: inline-block;
        padding: 0.8rem 1.5rem;
        background-color: #2d0a0a; /* Marron très foncé/noir */
        color: white !important;
        text-decoration: none;
        border-radius: 4px;
        margin: 0 auto; /* Centre le bouton */
        width: fit-content;
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
    /* Style pour le titre de la collection */
    .section-title {
      text-align: center;
      font-family: 'Libre Baskerville', serif;
      font-size: 2.5rem;
      margin: 4rem 0 2rem;
      color: var(--primary);
    }

    /* Conteneur pour le bouton voir plus */
    .view-more-container {
      display: flex;
      justify-content: center;
      margin: 3rem 0;
    }

    .btn-secondary {
      padding: 1rem 3rem;
      border: 2px solid #b08d6d;
      background: transparent;
      color: #b08d6d;
      font-weight: 600;
      border-radius: var(--radius);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      background: #b08d6d;
      color: white;
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
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'client'): ?>
        <a href="panier/view_cart.php"><i class="fas fa-shopping-cart"></i>Panier </a>
        <a href="panier/mes_commandes.php">Mes commandes </a>
        <?php endif; ?>
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
    <section class="hero-section">
      <div class="hero-content">
        <img src="imgs/Atelier-white.png" alt="Atelier Logo" class="hero-logo">
        
        <p class="hero-subtitle">Every woman different on her way</p>
        
        <button class="hero-btn" onclick="window.location.href='ProductList.php'">
          Discover our products
        </button>
      </div>
    </section>
    <!-- About Us Section -->
    <section class="about-section">
      <div class="about-content">
        <p>Depuis 2010, Atelier s'est imposé comme une référence dans l'art de l'horlogerie. Notre passion pour les mécanismes précis et l'esthétique intemporelle nous pousse à créer des pièces uniques qui racontent votre histoire.</p>
        <p>Chaque montre est conçue avec soin par nos artisans experts, combinant savoir-faire traditionnel et innovation moderne. Nous sélectionnons uniquement les matériaux les plus nobles pour garantir qualité et durabilité.</p>
        <p>Notre engagement va au-delà de la création d'accessoires : nous créons des héritages qui se transmettent de génération en génération.</p>
      </div>
      <div class="about-image">
        <img src="imgs/about-us.png" alt="Atelier Artisanat Montres">        
      </div>
    </section>
    
    <!-- Liste des produits -->
    <h2 class="section-title">Nouvelle Collection</h2>

    <section id="section2" class="products-grid">
      <?php
      // Ajout de LIMIT 3 pour n'afficher que les 3 derniers produits
      $query = "SELECT id, nom, prix, image1, categorie FROM produits ORDER BY date_ajout DESC LIMIT 3";
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

    <div class="view-more-container">
        <button class="btn-secondary" onclick="window.location.href='ProductList.php'">
            Voir plus
        </button>
    </div>
    
    <!-- Quote Section -->
    <section class="quote-section">
      <div class="quote-container">
        <div class="quote-text">
          Le temps est notre bien le plus précieux. Une montre Atelier ne le mesure pas, elle le célèbre.
        </div>
        <div class="quote-author">— Pierre Dubois, Maître Horloger</div>
      </div>
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

    document.getElementById("searchBar").addEventListener("keyup", function(event) {
        if (event.key === "Enter") {
            filterProducts();
        }
    });
</script>
</body>
</html>