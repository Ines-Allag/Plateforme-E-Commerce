<?php
include('config.php');

$searchTerm = isset($_GET['query']) ? mysqli_real_escape_string($con, $_GET['query']) : '';
$userId = isset($_SESSION['id']) ? $_SESSION['id'] : 'guest'; // On récupère l'ID

//  HISTORIQUE PAR CLIENT
if (!empty($searchTerm)) {
    $cookieName = 'search_history_' . $userId; 
    $history = isset($_COOKIE[$cookieName]) ? explode('|', $_COOKIE[$cookieName]) : [];
    
    if (($key = array_search($searchTerm, $history)) !== false) {
        unset($history[$key]);
    }
    
    array_unshift($history, $searchTerm);
    $history = array_slice($history, 0, 5);
    
    setcookie($cookieName, implode('|', $history), time() + (86400 * 30), "/");
}
// Récupérer les filtres AJAX
$selectedCategory = isset($_GET['categorie']) ? mysqli_real_escape_string($con, $_GET['categorie']) : '';
$searchTerm = isset($_GET['query']) ? mysqli_real_escape_string($con, $_GET['query']) : '';
$prixRange = isset($_GET['prix']) ? $_GET['prix'] : '';

// Construire WHERE
$whereClauses = [];


if ($selectedCategory !== '') {
    $whereClauses[] = "categorie = '$selectedCategory'";
}

if ($searchTerm !== '') {
    $whereClauses[] = "nom LIKE '%$searchTerm%'";
}

if ($prixRange !== '') {
    list($minPrice, $maxPrice) = explode('-', $prixRange);
    $whereClauses[] = "prix BETWEEN $minPrice AND $maxPrice";
}

$whereClause = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Requête finale
$query = "SELECT id, nom, prix, image1 FROM produits $whereClause ORDER BY date_ajout DESC";
$result = mysqli_query($con, $query);

// Affichage des produits
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo '
        <div class="product-card">
            <a href="product_details.php?id=' . $row['id'] . '" class="product-image">
                <img src="' . htmlspecialchars($row['image1']) . '" alt="' . htmlspecialchars($row['nom']) . '">
            </a>
            <div class="product-content">
                <h3 class="product-title">' . htmlspecialchars($row['nom']) . '</h3>
                <p class="product-price">' . number_format($row['prix'], 2) . ' DZD</p>
                <a href="product_details.php?id=' . $row['id'] . '" class="view-details">Voir les détails</a>
            </div>
        </div>
        ';
    }
} else {
    echo '
    <div class="no-products">
        <i class="fas fa-search" style="font-size: 3rem; color: var(--muted-foreground); margin-bottom: 1rem;"></i>
        <h3>Aucun produit trouvé</h3>
        <p>Essayez d\'autres critères ou catégories.</p>
    </div>
    ';
}
?>

<style>
    /* On copie le style essentiel de index1.php pour que ça reste identique après filtrage */
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
    .product-content {
        padding: 1rem;
        text-align: center;
    }
    .product-title {
        font-size: 1.2rem;
        margin: 0.5rem 0;
        font-weight: 600;
    }
    .product-price {
        font-size: 1.4rem;
        font-weight: bold;
        color: var(--primary);
        margin: 0.5rem 0;
    }
    .view-details {
        display: inline-block;
        margin-top: 0.5rem;
        padding: 0.5rem 1rem;
        background-color: var(--primary);
        color: var(--primary-foreground);
        border-radius: var(--radius);
        text-decoration: none;
        font-size: 0.9rem;
        transition: background-color 0.2s ease;
    }
    .view-details:hover {
        background-color: var(--accent);
    }
    .no-products {
        grid-column: 1 / -1;
        text-align: center;
        padding: 3rem;
        color: var(--muted-foreground);
    }
</style>