<?php
include('config.php');

// Récupérer les filtres envoyés par la requête AJAX
$selectedCategory = isset($_GET['categorie']) ? mysqli_real_escape_string($con, $_GET['categorie']) : '';
$searchTerm = isset($_GET['query']) ? mysqli_real_escape_string($con, $_GET['query']) : '';
$prixRange = isset($_GET['prix']) ? $_GET['prix'] : '';

// Construire la clause WHERE en fonction des filtres
$whereClauses = [];

if ($selectedCategory) {
    $whereClauses[] = "categorie = '$selectedCategory'";
}

if ($searchTerm) {
    $whereClauses[] = "name LIKE '%$searchTerm%'";
}

if ($prixRange) {
    list($minPrice, $maxPrice) = explode('-', $prixRange);
    $whereClauses[] = "prix BETWEEN $minPrice AND $maxPrice";
}

// Joindre toutes les clauses WHERE
$whereClause = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Construire la requête SQL
$query = "SELECT * FROM produits $whereClause";
$result = mysqli_query($con, $query);

// Afficher les produits filtrés
if (mysqli_num_rows($result) > 0) {
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
} else {
    echo "
    <div class='no-products'>
        <i class='fas fa-search'></i>
        <h3>Aucun produit trouvé</h3>
        <p>Essayez de modifier vos critères de recherche</p>
    </div>
    ";
}
?>
<style>
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
        width: 100%;
    }

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
        display: block;
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
        line-height: 1.3;
    }

    .product-description {
        color: var(--muted-foreground);
        font-size: 0.875rem;
        margin-bottom: 1rem;
        flex: 1;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
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
        font-size: 0.875rem;
        text-align: center;
    }

    .quantity-input:focus {
        border-color: var(--ring);
        outline: none;
        box-shadow: 0 0 0 2px var(--ring);
    }

    .add-to-cart-btn {
        padding: 0.75rem;
        background-color: var(--primary);
        color: var(--primary-foreground);
        border-radius: var(--radius);
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
    }

    .add-to-cart-btn:hover {
        background-color: color-mix(in srgb, var(--primary) 90%, black);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .add-to-cart-btn:active {
        transform: translateY(0);
    }

    .view-details-btn {
        padding: 0.5rem;
        background-color: var(--secondary);
        color: var(--secondary-foreground);
        border-radius: var(--radius);
        font-size: 0.875rem;
        text-align: center;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
    }

    .view-details-btn:hover {
        background-color: color-mix(in srgb, var(--secondary) 90%, black);
    }

    /* No products found */
    .no-products {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--muted-foreground);
        grid-column: 1 / -1;
    }

    .no-products i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: var(--border);
    }

    .no-products h3 {
        font-family: var(--font-serif);
        margin-bottom: 0.5rem;
        color: var(--foreground);
    }

    .no-products p {
        font-size: 0.875rem;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
    }

    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1rem;
        }

        .product-content {
            padding: 1rem;
        }

        .product-title {
            font-size: 1.125rem;
        }

        .product-price {
            font-size: 1.25rem;
        }
    }

    @media (max-width: 480px) {
        .products-grid {
            grid-template-columns: 1fr;
        }

        .product-image {
            height: 180px;
        }
    }

    /* Loading animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .product-card {
        animation: fadeIn 0.3s ease-out;
    }
</style>