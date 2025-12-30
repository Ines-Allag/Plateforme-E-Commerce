<?php
include('config.php');

if (isset($_GET['id'])) {
    $product_id = mysqli_real_escape_string($con, $_GET['id']);
    $query = "SELECT * FROM produits WHERE id = '$product_id'";
    $result = mysqli_query($con, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        echo '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . htmlspecialchars($row['nom']) . ' - Watch Store</title>
            <link rel="stylesheet" href="global.css">
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <style>
                .product-gallery { display: flex; flex-direction: column; gap: 1rem; }
                .main-image { width: 100%; height: 450px; object-fit: cover; border-radius: var(--radius); }
                .thumbnails { display: flex; gap: 1rem; flex-wrap: wrap; }
                .thumbnail { width: 100px; height: 100px; object-fit: cover; border: 2px solid transparent; border-radius: var(--radius); cursor: pointer; transition: border 0.2s; }
                .thumbnail:hover, .thumbnail.active { border-color: var(--primary); }
                .product-stock { margin: 1rem 0; font-weight: 500; color: var(--foreground); }
            </style>
        </head>
        <body>
            <div class="product-detail-container">
                <nav class="product-breadcrumb">
                    <a href="index1.php" class="breadcrumb-link">Accueil</a> > 
                    <a href="index1.php" class="breadcrumb-link">' . htmlspecialchars($row['categorie']) . '</a> > 
                    ' . htmlspecialchars($row['nom']) . '
                </nav>

                <div class="product-detail">
                    <div class="product-gallery">
                        <img src="' . htmlspecialchars($row['image1']) . '" alt="' . htmlspecialchars($row['nom']) . '" class="main-image" id="mainImage">

                        <div class="thumbnails">
                            <img src="' . htmlspecialchars($row['image1']) . '" class="thumbnail active" onclick="changeImage(this.src, this)">
                            ';
                            if (!empty($row['image2'])) {
                                echo '<img src="' . htmlspecialchars($row['image2']) . '" class="thumbnail" onclick="changeImage(this.src, this)">';
                            }
                            if (!empty($row['image3'])) {
                                echo '<img src="' . htmlspecialchars($row['image3']) . '" class="thumbnail" onclick="changeImage(this.src, this)">';
                            }
                        echo '
                        </div>
                    </div>

                    <div class="product-info">
                        <span class="product-category">' . htmlspecialchars($row['categorie']) . '</span>
                        <h1 class="product-title">' . htmlspecialchars($row['nom']) . '</h1>
                        <div class="product-price">' . number_format($row['prix'], 2) . ' DZD</div>

                        <div class="product-stock">
                            Stock : ' . $row['quantite_stock'] . ' unité' . ($row['quantite_stock'] > 1 ? 's' : '') . '
                        </div>

                        <div class="product-description">
                            ' . nl2br(htmlspecialchars($row['description'])) . '
                        </div>

                        <form action="panier/add_to_cart.php" method="post" class="product-actions">
                            <input type="hidden" name="produit_id" value="' . $row['id'] . '">
                            <input type="hidden" name="prix" value="' . $row['prix'] . '">

                            <div class="quantity-selector">
                                <label for="quantite">Quantité :</label>
                                <input type="number" name="quantite" value="1" min="1" max="' . $row['quantite_stock'] . '" required>
                            </div>

                            <button type="submit" ' . ($row['quantite_stock'] <= 0 ? 'disabled' : '') . '>
                                <i class="fas fa-cart-plus"></i> 
                                ' . ($row['quantite_stock'] <= 0 ? 'Rupture de stock' : 'Ajouter au panier') . '
                            </button>
                        </form>

                        <a href="index1.php" class="back-btn">
                            <i class="fas fa-arrow-left"></i> Retour à la boutique
                        </a>
                    </div>
                </div>
            </div>

            <script>
                function changeImage(src, thumb) {
                    document.getElementById("mainImage").src = src;
                    document.querySelectorAll(".thumbnail").forEach(t => t.classList.remove("active"));
                    thumb.classList.add("active");
                }
            </script>
        </body>
        </html>
        ';
    } else {
        echo '<p class="error-message">Produit non trouvé.</p>';
    }
} else {
    echo '<p class="error-message">Aucun produit sélectionné.</p>';
}
?>