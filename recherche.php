<?php
include('config.php');

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
    $whereClauses[] = "nom LIKE '%$searchTerm%'";  // CHANGEMENT : nom au lieu de name
}

if ($prixRange !== '') {
    list($minPrice, $maxPrice) = explode('-', $prixRange);
    $whereClauses[] = "prix BETWEEN $minPrice AND $maxPrice";
}

$whereClause = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";

// Requête finale
$query = "SELECT id, nom, prix, image1, categorie FROM produits $whereClause ORDER BY date_ajout DESC";
$result = mysqli_query($con, $query);

// Affichage
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
                <a href='product_details.php?id=" . $row['id'] . "' class='view-details'>Voir détails</a>
            </div>
        </div>
        ";
    }
} else {
    echo '
    <div class="no-products">
        <i class="fas fa-search" style="font-size:3rem; color:var(--muted-foreground);"></i>
        <h3>Aucun produit trouvé</h3>
        <p>Essayez d\'autres critères ou catégories.</p>
    </div>
    ';
}
?>

<style>
    .product-image img { transition: transform 0.3s ease; }
    .product-image:hover img { transform: scale(1.05); }
</style>