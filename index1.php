<?php 
session_start(); 
include('config.php'); 
?>
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
    .store-header {
      background-color: #2d0a0a; 
      color: white;
      padding: 1.5rem 0;
      box-shadow: var(--shadow);
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000; 
    }

    main {
      padding-top: 80px; 
    }

    .store-header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 4rem;
    }

    .store-logo img {
      height: 45px;
      width: auto;
    }

    .store-nav {
      display: flex;
      align-items: center;
      gap: 1.5rem;
    }

    .store-nav a {
      color: white;
      text-decoration: none;
      font-family: 'Poppins', sans-serif;
      font-size: 0.95rem;
      font-weight: 400;
      transition: opacity 0.2s;
    }

    .store-nav a:hover {
      opacity: 0.8;
    }

    .nav-cart {
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 600;
      margin-left: 1.5rem;
    }

    .nav-cart i {
      font-size: 1.1rem;
    }

    .hero-section {
      position: relative;
      height: 700px; 
      background: linear-gradient(to right, rgba(0, 0, 0, 0.09) 0%, transparent 0%), url('imgs/banner1.png');
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
      width: 350px;
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
      background-color: var(--secondary); 
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
    
    @media (max-width: 992px) {
      .about-section {
        flex-direction: column;
        gap: 2rem;
        text-align: center;
      }

      .about-content {
        max-width: 100%;
        order: 1; 
      }

      .about-image {
        max-width: 100%; 
        order: 2; 
      }

      .about-image img {
        height: auto;
        max-height: 400px;
      }
    }

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
        transition: transform 0.3s ease;
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
        background-color: var(--primary); 
        color: white !important;
        text-decoration: none;
        border-radius: 1px;
        margin: 0 auto;
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

    .section-title {
      text-align: center;
      font-family: 'Libre Baskerville', serif;
      font-size: 2.5rem;
      margin: 4rem 0 2rem;
      color: var(--primary);
    }

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
      border-radius: 1px var(--radius);
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .btn-secondary:hover {
      background: #b08d6d;
      color: white;
    }

    #cookiePopup {
      position: fixed;
      bottom: -100%;
      left: 0;
      width: 100%;
      background: #ffffff;
      color: #333;
      padding: 25px;
      box-shadow: 0 -10px 30px rgba(0,0,0,0.3);
      z-index: 10000;
      transition: bottom 0.5s ease-in-out;
      border-top: 4px solid #b08d6d;
    }

    #cookiePopup.active { 
      bottom: 0; 
    }
    
    .cookie-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 20px;
    }

    .cookie-text {
      flex: 1;
    }

    .cookie-buttons {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }
    
    .btn-accept-cookie { 
      background: #2d0a0a; 
      color: white; 
      border: none; 
      padding: 12px 25px; 
      border-radius: 4px; 
      cursor: pointer; 
      font-weight: bold;
      transition: background-color 0.2s;
    }

    .btn-accept-cookie:hover {
      background: #1a0505;
    }

    .btn-essential-cookie {
      background: #6b7280;
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
      transition: background-color 0.2s;
    }

    .btn-essential-cookie:hover {
      background: #4b5563;
    }
    
    #cookieOverlay {
      display: none;
      position: fixed;
      top: 0; 
      left: 0; 
      width: 100%; 
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 9999;
      backdrop-filter: blur(3px);
    }

    #cookieOverlay.active { 
      display: block; 
    }

    @media (max-width: 768px) {
      .cookie-container {
        flex-direction: column;
        text-align: center;
      }

      .cookie-buttons {
        width: 100%;
        justify-content: center;
      }

      .btn-accept-cookie,
      .btn-essential-cookie {
        flex: 1;
        min-width: 140px;
      }
    }
  </style>
</head>
<body>
  <!-- Bannière des cookies: pour les clients connectés uniquement -->
  <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'client'): ?>
    <div id="cookieOverlay"></div>
    <div id="cookiePopup">
      <div class="cookie-container">
        <div class="cookie-text">
          <strong style="font-size: 1.2rem; display: block; margin-bottom: 8px;">
            <i class="fas fa-cookie-bite"></i> Utilisation des Cookies
          </strong>
          <p style="margin-bottom: 10px;">
            Nous utilisons des <strong>cookies essentiels</strong> pour gérer votre session et votre panier d'achat. 
            Ces cookies sont nécessaires au bon fonctionnement du site.
          </p>
          <p style="font-size: 0.9rem; opacity: 0.9;">
            Vous pouvez continuer à naviguer avec uniquement les cookies essentiels si vous le souhaitez.
          </p>
        </div>
        <div class="cookie-buttons">
          <button onclick="handleCookieConsent('all')" class="btn-accept-cookie">
            J'accepte tous les cookies
          </button>
          <button onclick="handleCookieConsent('essential')" class="btn-essential-cookie">
            Cookies essentiels uniquement
          </button>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- En-tête du site -->
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
          <a href="panier/mes_commandes.php">Mes commandes</a>
          <a href="panier/view_cart.php" class="nav-cart">
            <i class="fas fa-shopping-cart"></i>Panier
          </a>
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
    <!-- Section hero -->
    <section class="hero-section">
      <div class="hero-content">
        <img src="imgs/Atelier-white.png" alt="Atelier Logo" class="hero-logo">
        <p class="hero-subtitle">Every woman different on her way</p>
        <button class="hero-btn" onclick="window.location.href='ProductList.php'">
          Découvrir nos produits
        </button>
      </div>
    </section>

    <!-- Section about us -->
    <section class="about-section">
      <div class="about-content">
        <p>Depuis 2006, Atelier s'est imposé comme une référence dans l'art de l'horlogerie. Notre passion pour les mécanismes précis et l'esthétique intemporelle nous pousse à créer des pièces uniques qui racontent votre histoire.</p>
        <p>Chaque montre est conçue avec soin par nos artisans experts, combinant savoir-faire traditionnel et innovation moderne. Nous sélectionnons uniquement les matériaux les plus nobles pour garantir qualité et durabilité.</p>
        <p>Notre engagement va au-delà de la création d'accessoires : nous créons des héritages qui se transmettent de génération en génération.</p>
      </div>
      <div class="about-image">
        <img src="imgs/about-us.png" alt="Atelier Artisanat Montres">        
      </div>
    </section>
    
    <h2 id="produitList" class="section-title">Nouvelle Collection</h2>

    <!-- produits:affiche les 3 derniers produits ajoutés -->
    <section id="section2" class="products-grid">
      <?php
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

    <!-- Bouton pour voir plus de produits qui mène vers la liste des produits -->
    <div class="view-more-container">
        <button class="btn-secondary" onclick="window.location.href='ProductList.php'">
            Voir plus
        </button>
    </div>
    
    <!-- Section citation -->
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
            <a href="#produitList">Nouveautés</a>
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
  // Récupération de l'ID user pour personnaliser le cookies de consentement
  const userId = "<?php echo isset($_SESSION['id']) ? $_SESSION['id'] : 'guest'; ?>";
  const cookieName = "atelier_consent_user_" + userId;

  function setCookie(name, value, days) {
      let date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      document.cookie = name + "=" + value + "; expires=" + date.toUTCString() + "; path=/; SameSite=Lax";
  }

  // Fonction pour lire un cookie existant
  function getCookie(name) {
      let nameEQ = name + "=";
      let ca = document.cookie.split(';');
      for (let i = 0; i < ca.length; i++) {
          let c = ca[i];
          while (c.charAt(0) == ' ') c = c.substring(1, c.length);
          if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
      }
      return null;
  }

  // Gestion du consentement de l'utilisateur
  function handleCookieConsent(type) {
      if (type === 'all') {
          setCookie(cookieName, "all", 365);
          console.log("Tous les cookies acceptés");
      } else if (type === 'essential') {
          setCookie(cookieName, "essential", 365);
          console.log("Cookies essentiels uniquement");
      }
      
      document.getElementById("cookiePopup").classList.remove("active");
      document.getElementById("cookieOverlay").classList.remove("active");
  }

  window.addEventListener("load", function() {
      const popup = document.getElementById("cookiePopup");
      const overlay = document.getElementById("cookieOverlay");

      if (popup && !getCookie(cookieName)) {
          setTimeout(() => {
              popup.classList.add("active");
              overlay.classList.add("active");
          }, 500);
      }
  });

  function filterProducts() {
      const category = document.getElementById("categorie") ? document.getElementById("categorie").value : "";
      const searchTerm = document.getElementById("searchBar") ? document.getElementById("searchBar").value.trim() : "";
      const prixRange = document.getElementById("prixFilter") ? document.getElementById("prixFilter").value : "";

      let url = "recherche.php?";
      if (category) url += "categorie=" + encodeURIComponent(category) + "&";
      if (searchTerm) url += "query=" + encodeURIComponent(searchTerm) + "&";
      if (prixRange) url += "prix=" + encodeURIComponent(prixRange);

      fetch(url)
          .then(response => response.text())
          .then(data => {
              const section = document.getElementById("section2");
              if (section) {
                  section.innerHTML = data;
              }
          })
          .catch(error => console.error('Erreur lors du filtrage:', error));
  }

  const searchBar = document.getElementById("searchBar");
  if (searchBar) {
      searchBar.addEventListener("keyup", function(event) {
          if (event.key === "Enter") {
              filterProducts();
          }
      });
  }
</script>
</body>
</html>