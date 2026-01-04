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
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: var(--font-sans);
                    background-color: var(--background);
                    color: var(--foreground);
                    line-height: 1.6;
                }

                .product-detail-container {
                    max-width: 1200px;
                    margin: 2rem auto;
                    padding: 0 1rem;
                }

                .product-breadcrumb {
                    margin-bottom: 2rem;
                    font-size: 0.9rem;
                    color: var(--muted-foreground);
                    display: flex;
                    align-items: center;
                    flex-wrap: wrap;
                    gap: 0.5rem;
                    padding: 1rem;
                    background-color: var(--card);
                    border-radius: var(--radius);
                    box-shadow: var(--shadow-sm);
                }

                .breadcrumb-link {
                    color: var(--primary);
                    text-decoration: none;
                    transition: color 0.2s ease;
                    display: flex;
                    align-items: center;
                }

                .breadcrumb-link:hover {
                    color: var(--accent-foreground);
                    text-decoration: underline;
                }

                .product-detail {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 3rem;
                    background-color: var(--card);
                    border-radius: var(--radius);
                    box-shadow: var(--shadow-md);
                    border: 1px solid var(--border);
                    padding: 2rem;
                    margin-top: 1rem;
                }

                .product-gallery {
                    display: flex;
                    flex-direction: column;
                    gap: 1.5rem;
                }

                .main-image-container {
                    width: 100%;
                    height: 450px;
                    border-radius: var(--radius);
                    overflow: hidden;
                    background-color: var(--muted);
                    position: relative;
                    box-shadow: var(--shadow-lg);
                }

                .main-image {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    transition: transform 0.3s ease;
                }

                .main-image-container:hover .main-image {
                    transform: scale(1.02);
                }

                .product-badge {
                    position: absolute;
                    top: 15px;
                    left: 15px;
                    background-color: var(--primary);
                    color: white;
                    padding: 0.5rem 1rem;
                    border-radius: var(--radius);
                    font-weight: 500;
                    font-size: 0.875rem;
                    z-index: 10;
                }

                .thumbnails {
                    display: flex;
                    gap: 1rem;
                    flex-wrap: wrap;
                    justify-content: center;
                }

                .thumbnail {
                    width: 80px;
                    height: 80px;
                    object-fit: cover;
                    border: 2px solid transparent;
                    border-radius: var(--radius);
                    cursor: pointer;
                    transition: all 0.2s ease;
                    background-color: var(--muted);
                    box-shadow: var(--shadow-sm);
                }

                .thumbnail:hover {
                    border-color: var(--primary);
                    transform: translateY(-2px);
                    box-shadow: var(--shadow-md);
                }

                .thumbnail.active {
                    border-color: var(--primary);
                    box-shadow: var(--shadow-sm);
                }

                .product-info {
                    display: flex;
                    flex-direction: column;
                    gap: 1.5rem;
                }

                .product-category {
                    display: inline-block;
                    padding: 0.25rem 0.75rem;
                    background-color: var(--secondary);
                    color: var(--secondary-foreground);
                    border-radius: var(--radius);
                    font-size: 0.875rem;
                    font-weight: 500;
                    align-self: flex-start;
                }

                .product-title {
                    font-family: var(--font-serif);
                    font-size: 2.25rem;
                    color: var(--foreground);
                    line-height: 1.2;
                    margin-top: 0.5rem;
                }

                .product-price {
                    font-size: 2rem;
                    font-weight: bold;
                    color: var(--primary);
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }

                .product-stock {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.5rem 1rem;
                    background-color: var(--muted);
                    border-radius: var(--radius);
                    font-weight: 500;
                    color: var(--foreground);
                    align-self: flex-start;
                }

                .stock-in {
                    color: #22c55e;
                }

                .stock-low {
                    color: #f59e0b;
                }

                .stock-out {
                    color: var(--destructive);
                }

                .product-description {
                    color: var(--muted-foreground);
                    line-height: 1.8;
                    font-size: 1rem;
                    padding: 1rem 0;
                    border-top: 1px solid var(--border);
                    border-bottom: 1px solid var(--border);
                }

                .product-actions {
                    display: flex;
                    flex-direction: column;
                    gap: 1.5rem;
                    margin-top: 1rem;
                }

                .quantity-selector {
                    display: flex;
                    flex-direction: column;
                    gap: 0.75rem;
                }

                .quantity-selector label {
                    font-weight: 500;
                    color: var(--foreground);
                    font-size: 0.875rem;
                }

                .quantity-controls {
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                }

                .quantity-btn {
                    width: 40px;
                    height: 40px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background-color: var(--muted);
                    border-radius: var(--radius);
                    color: var(--foreground);
                    cursor: pointer;
                    transition: all 0.2s ease;
                    border: 1px solid var(--border);
                }

                .quantity-btn:hover {
                    background-color: var(--primary);
                    color: var(--primary-foreground);
                    border-color: var(--primary);
                }

                .quantity-input {
                    width: 80px;
                    padding: 0.5rem;
                    border: 1px solid var(--border);
                    border-radius: var(--radius);
                    background-color: var(--input);
                    color: var(--foreground);
                    text-align: center;
                    font-size: 1rem;
                    font-weight: 500;
                }

                .quantity-input:focus {
                    border-color: var(--ring);
                    outline: none;
                    box-shadow: 0 0 0 2px var(--ring);
                }

                .add-to-cart-btn {
                    padding: 1rem 2rem;
                    background-color: var(--primary);
                    color: var(--primary-foreground);
                    border-radius: var(--radius);
                    font-weight: 500;
                    font-size: 1rem;
                    transition: all 0.2s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 0.75rem;
                    border: none;
                    cursor: pointer;
                    box-shadow: var(--shadow-sm);
                }

                .add-to-cart-btn:hover:not(:disabled) {
                    background-color: color-mix(in srgb, var(--primary) 90%, black);
                    transform: translateY(-2px);
                    box-shadow: var(--shadow-md);
                }

                .add-to-cart-btn:disabled {
                    background-color: var(--muted);
                    color: var(--muted-foreground);
                    cursor: not-allowed;
                    opacity: 0.7;
                }

                .back-btn {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.75rem;
                    padding: 0.875rem 1.5rem;
                    background-color: var(--secondary);
                    color: var(--secondary-foreground);
                    border-radius: var(--radius);
                    text-decoration: none;
                    transition: all 0.2s ease;
                    font-weight: 500;
                    align-self: flex-start;
                }

                .back-btn:hover {
                    background-color: color-mix(in srgb, var(--secondary) 90%, black);
                    transform: translateY(-1px);
                    box-shadow: var(--shadow-sm);
                }

                .error-message {
                    text-align: center;
                    padding: 4rem 2rem;
                    color: var(--destructive);
                    font-size: 1.25rem;
                    max-width: 600px;
                    margin: 0 auto;
                }

                /* Responsive Design */
                @media (max-width: 1024px) {
                    .product-detail {
                        gap: 2rem;
                        padding: 1.5rem;
                    }
                    
                    .product-title {
                        font-size: 2rem;
                    }
                    
                    .product-price {
                        font-size: 1.75rem;
                    }
                }

                @media (max-width: 768px) {
                    .product-detail {
                        grid-template-columns: 1fr;
                        gap: 2rem;
                    }
                    
                    .main-image-container {
                        height: 350px;
                    }
                    
                    .product-title {
                        font-size: 1.75rem;
                    }
                    
                    .product-price {
                        font-size: 1.5rem;
                    }
                }

                @media (max-width: 480px) {
                    .product-detail-container {
                        padding: 1rem;
                    }
                    
                    .product-detail {
                        padding: 1rem;
                    }
                    
                    .main-image-container {
                        height: 250px;
                    }
                    
                    .thumbnail {
                        width: 60px;
                        height: 60px;
                    }
                    
                    .add-to-cart-btn,
                    .back-btn {
                        width: 100%;
                        justify-content: center;
                    }
                    
                    .quantity-controls {
                        justify-content: center;
                    }
                }

                /* Animation */
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .product-detail {
                    animation: fadeIn 0.5s ease-out;
                }
            </style>
        </head>
        <body>
            <div class="product-detail-container">
                <nav class="product-breadcrumb">
                    <a href="index1.php" class="breadcrumb-link">
                        <i class="fas fa-home"></i> Accueil
                    </a>
                    <span style="color: var(--muted-foreground); margin: 0 0.5rem;">/</span>
                    <a href="index1.php" class="breadcrumb-link">
                        ' . htmlspecialchars($row['categorie']) . '
                    </a>
                    <span style="color: var(--muted-foreground); margin: 0 0.5rem;">/</span>
                    <span>' . htmlspecialchars($row['nom']) . '</span>
                </nav>

                <div class="product-detail">
                    <div class="product-gallery">
                        <div class="main-image-container">
                            <div class="product-badge">
                                ' . ($row['quantite_stock'] > 10 ? 'EN STOCK' : ($row['quantite_stock'] > 0 ? 'STOCK LIMITÉ' : 'RUPTURE')) . '
                            </div>
                            <img src="' . htmlspecialchars($row['image1']) . '" 
                                 alt="' . htmlspecialchars($row['nom']) . '" 
                                 class="main-image" 
                                 id="mainImage">
                        </div>

                        <div class="thumbnails">
                            <img src="' . htmlspecialchars($row['image1']) . '" 
                                 class="thumbnail active" 
                                 onclick="changeImage(this.src, this)"
                                 alt="Vue principale">
                            ';
                            if (!empty($row['image2'])) {
                                echo '<img src="' . htmlspecialchars($row['image2']) . '" 
                                      class="thumbnail" 
                                      onclick="changeImage(this.src, this)"
                                      alt="Vue alternative 1">';
                            }
                            if (!empty($row['image3'])) {
                                echo '<img src="' . htmlspecialchars($row['image3']) . '" 
                                      class="thumbnail" 
                                      onclick="changeImage(this.src, this)"
                                      alt="Vue alternative 2">';
                            }
                        echo '
                        </div>
                    </div>

                    <div class="product-info">
                        <span class="product-category">
                            <i class="fas fa-tag"></i> ' . htmlspecialchars($row['categorie']) . '
                        </span>
                        
                        <h1 class="product-title">' . htmlspecialchars($row['nom']) . '</h1>
                        
                        <div class="product-price">
                            <i class="fas fa-tag"></i> ' . number_format($row['prix'], 2) . ' DZD
                        </div>

                        <div class="product-stock ' . ($row['quantite_stock'] > 10 ? 'stock-in' : ($row['quantite_stock'] > 0 ? 'stock-low' : 'stock-out')) . '">
                            <i class="fas fa-box"></i>
                            Stock : ' . $row['quantite_stock'] . ' unité' . ($row['quantite_stock'] > 1 ? 's' : '') . '
                        </div>

                        <div class="product-description">
                            <h3><i class="fas fa-info-circle"></i> Description</h3>
                            <p>' . nl2br(htmlspecialchars($row['description'])) . '</p>
                        </div>

                        <form action="panier/add_to_cart.php" method="post" class="product-actions">
                            <input type="hidden" name="produit_id" value="' . $row['id'] . '">
                            <input type="hidden" name="prix" value="' . $row['prix'] . '">

                            <div class="quantity-selector">
                                <label for="quantite">
                                    <i class="fas fa-sort-amount-up"></i> Quantité :
                                </label>
                                <div class="quantity-controls">
                                    <button type="button" class="quantity-btn" onclick="decreaseQuantity()">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" 
                                           id="quantite" 
                                           name="quantite" 
                                           value="1" 
                                           min="1" 
                                           max="' . $row['quantite_stock'] . '" 
                                           class="quantity-input"
                                           onchange="validateQuantity(this)"
                                           required>
                                    <button type="button" class="quantity-btn" onclick="increaseQuantity()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="action-buttons">
                                <button type="submit" 
                                        class="add-to-cart-btn" 
                                        ' . ($row['quantite_stock'] <= 0 ? 'disabled' : '') . '>
                                    <i class="fas fa-cart-plus"></i> 
                                    ' . ($row['quantite_stock'] <= 0 ? 'Rupture de stock' : 'Ajouter au panier') . '
                                </button>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
                function changeImage(src, thumb) {
                    // Update main image
                    document.getElementById("mainImage").src = src;
                    
                    // Update active thumbnail
                    document.querySelectorAll(".thumbnail").forEach(t => t.classList.remove("active"));
                    thumb.classList.add("active");
                    
                    // Add loading effect
                    const mainImage = document.getElementById("mainImage");
                    mainImage.style.opacity = "0.5";
                    setTimeout(() => {
                        mainImage.style.opacity = "1";
                    }, 200);
                }

                function decreaseQuantity() {
                    const input = document.getElementById("quantite");
                    if (parseInt(input.value) > parseInt(input.min)) {
                        input.value = parseInt(input.value) - 1;
                    }
                }

                function increaseQuantity() {
                    const input = document.getElementById("quantite");
                    if (parseInt(input.value) < parseInt(input.max)) {
                        input.value = parseInt(input.value) + 1;
                    }
                }

                function validateQuantity(input) {
                    const max = parseInt(input.max);
                    const min = parseInt(input.min);
                    let value = parseInt(input.value);
                    
                    if (value < min) {
                        input.value = min;
                    } else if (value > max) {
                        input.value = max;
                        alert("Quantité maximale disponible : " + max);
                    }
                }
            </script>
        </body>
        </html>
        ';
    } else {
        echo '
        <div style="max-width: 600px; margin: 4rem auto; padding: 3rem 2rem; text-align: center; background-color: var(--card); border-radius: var(--radius); box-shadow: var(--shadow-md); border: 1px solid var(--border);">
            <div style="font-size: 4rem; color: var(--destructive); margin-bottom: 1.5rem;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h2 style="font-size: 1.5rem; color: var(--foreground); margin-bottom: 1rem;">Produit non trouvé</h2>
            <p style="color: var(--muted-foreground); margin-bottom: 2rem;">
                Le produit que vous recherchez n\'existe pas ou a été déplacé.
            </p>
            <a href="index1.php" style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; background-color: var(--primary); color: var(--primary-foreground); border-radius: var(--radius); text-decoration: none; transition: all 0.2s ease; font-weight: 500;">
                <i class="fas fa-arrow-left"></i> Retour à la boutique
            </a>
        </div>
        ';
    }
} else {
    echo '
    <div style="max-width: 600px; margin: 4rem auto; padding: 3rem 2rem; text-align: center; background-color: var(--card); border-radius: var(--radius); box-shadow: var(--shadow-md); border: 1px solid var(--border);">
        <div style="font-size: 4rem; color: var(--muted-foreground); margin-bottom: 1.5rem;">
            <i class="fas fa-search"></i>
        </div>
        <h2 style="font-size: 1.5rem; color: var(--foreground); margin-bottom: 1rem;">Aucun produit sélectionné</h2>
        <p style="color: var(--muted-foreground); margin-bottom: 2rem;">
            Veuillez sélectionner un produit depuis notre catalogue.
        </p>
        <a href="index1.php" style="display: inline-flex; align-items: center; gap: 0.75rem; padding: 0.875rem 1.5rem; background-color: var(--primary); color: var(--primary-foreground); border-radius: var(--radius); text-decoration: none; transition: all 0.2s ease; font-weight: 500;">
            <i class="fas fa-store"></i> Parcourir la boutique
        </a>
    </div>
    ';
}
?>