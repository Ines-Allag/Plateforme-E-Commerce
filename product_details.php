<?php
include('config.php');

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $query = "SELECT * FROM produits WHERE id = $product_id";
    $result = mysqli_query($con, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . htmlspecialchars($row['name']) . ' - Kelthouma Tech Store</title>
            <link rel="stylesheet" href="global.css">
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <style>
                .product-detail-container {
                    max-width: 1200px;
                    margin: 2rem auto;
                    padding: 0 1rem;
                }

                .product-breadcrumb {
                    margin-bottom: 2rem;
                }

                .breadcrumb-link {
                    color: var(--primary);
                    text-decoration: none;
                    transition: color 0.2s ease;
                }

                .breadcrumb-link:hover {
                    color: var(--accent-foreground);
                }

                .product-detail {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 3rem;
                    background-color: var(--card);
                    border-radius: var(--radius);
                    box-shadow: var(--shadow-sm);
                    border: 1px solid var(--border);
                    padding: 2rem;
                }

                .product-gallery {
                    position: relative;
                }

                .product-main-image {
                    width: 100%;
                    height: 400px;
                    border-radius: var(--radius);
                    overflow: hidden;
                    background-color: var(--muted);
                }

                .product-main-image img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
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
                }

                .product-title {
                    font-family: var(--font-serif);
                    font-size: 2rem;
                    color: var(--foreground);
                    line-height: 1.2;
                }

                .product-price {
                    font-size: 2rem;
                    font-weight: bold;
                    color: var(--primary);
                }

                .product-description {
                    color: var(--muted-foreground);
                    line-height: 1.6;
                }

                .product-actions {
                    display: flex;
                    flex-direction: column;
                    gap: 1rem;
                }

                .quantity-selector {
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                }

                .quantity-selector label {
                    font-weight: 500;
                    color: var(--foreground);
                }

                .quantity-input {
                    width: 80px;
                    padding: 0.5rem;
                    border: 1px solid var(--border);
                    border-radius: var(--radius);
                    background-color: var(--input);
                    color: var(--foreground);
                    text-align: center;
                }

                .add-to-cart-btn {
                    padding: 1rem;
                    background-color: var(--primary);
                    color: var(--primary-foreground);
                    border-radius: var(--radius);
                    font-weight: 500;
                    font-size: 1rem;
                    transition: all 0.2s ease;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 0.5rem;
                }

                .add-to-cart-btn:hover {
                    background-color: color-mix(in srgb, var(--primary) 90%, black);
                    transform: translateY(-1px);
                    box-shadow: var(--shadow-sm);
                }

                .back-btn {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.75rem 1.5rem;
                    background-color: var(--secondary);
                    color: var(--secondary-foreground);
                    border-radius: var(--radius);
                    text-decoration: none;
                    transition: all 0.2s ease;
                }

                .back-btn:hover {
                    background-color: color-mix(in srgb, var(--secondary) 90%, black);
                }

                @media (max-width: 768px) {
                    .product-detail {
                        grid-template-columns: 1fr;
                        gap: 2rem;
                        padding: 1.5rem;
                    }

                    .product-main-image {
                        height: 300px;
                    }

                    .product-title {
                        font-size: 1.5rem;
                    }

                    .product-price {
                        font-size: 1.5rem;
                    }
                }
            </style>
        </head>
        <body>
            <div class="product-detail-container">
                <nav class="product-breadcrumb">
                    <a href="index1.php" class="breadcrumb-link">
                        <i class="fas fa-arrow-left"></i> Retour aux produits
                    </a>
                </nav>

                <div class="product-detail">
                    <div class="product-gallery">
                        <div class="product-main-image">
                            <img src="' . htmlspecialchars($row['img']) . '" alt="' . htmlspecialchars($row['name']) . '">
                        </div>
                    </div>

                    <div class="product-info">
                        <span class="product-category">' . htmlspecialchars($row['categorie']) . '</span>
                        <h1 class="product-title">' . htmlspecialchars($row['name']) . '</h1>
                        <div class="product-price">' . number_format($row['prix'], 2) . ' DZD</div>
                        
                        <div class="product-description">
                            ' . nl2br(htmlspecialchars($row['description'])) . '
                        </div>

                        <form action="panier/add_to_cart.php" method="post" class="product-actions">
                            <input type="hidden" name="produit_id" value="' . $row['id'] . '">
                            <input type="hidden" name="prix" value="' . $row['prix'] . '">
                            
                            <div class="quantity-selector">
                                <label for="quantite">Quantité:</label>
                                <input type="number" id="quantite" name="quantite" value="1" min="1" class="quantity-input" required>
                            </div>

                            <button type="submit" class="add-to-cart-btn">
                                <i class="fas fa-cart-plus"></i> Ajouter au panier
                            </button>
                        </form>

                        <a href="index1.php" class="back-btn">
                            <i class="fas fa-arrow-left"></i> Retour à la boutique
                        </a>
                    </div>
                </div>
            </div>
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