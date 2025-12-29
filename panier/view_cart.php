<?php
session_start();
include('../config.php');

if (!isset($_SESSION['id'])) {
    header("Location: ../Client/index.php");
    exit();
}

$user_id = $_SESSION['id'];

$stmt = $con->prepare("
    SELECT c.quantite, c.prix, c.produit_id, c.img, p.name, 
           (c.quantite * c.prix) as total 
    FROM panier c 
    JOIN produits p ON c.produit_id = p.id 
    WHERE c.client_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier - Kelthouma Tech Store</title>
    <link rel="stylesheet" href="../global.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .cart-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .cart-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .cart-header h1 {
            font-family: var(--font-serif);
            font-size: 2.5rem;
            color: var(--foreground);
            margin-bottom: 0.5rem;
        }

        .cart-header p {
            color: var(--muted-foreground);
            font-size: 1.125rem;
        }

        .cart-items {
            background-color: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            overflow: hidden;
        }

        .cart-item-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
            gap: 1rem;
            padding: 1.5rem;
            background-color: var(--muted);
            border-bottom: 1px solid var(--border);
            font-weight: 600;
            color: var(--foreground);
        }

        .cart-item {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr auto;
            gap: 1rem;
            padding: 1.5rem;
            align-items: center;
            border-bottom: 1px solid var(--border);
            transition: background-color 0.2s ease;
        }

        .cart-item:hover {
            background-color: var(--muted);
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .product-image {
            width: 80px;
            height: 80px;
            border-radius: var(--radius);
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-details h3 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
            color: var(--foreground);
        }

        .product-price {
            color: var(--primary);
            font-weight: 600;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
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

        .update-btn {
            padding: 0.5rem 1rem;
            background-color: var(--secondary);
            color: var(--secondary-foreground);
            border-radius: var(--radius);
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .update-btn:hover {
            background-color: color-mix(in srgb, var(--secondary) 90%, black);
        }

        .item-total {
            font-weight: 600;
            color: var(--foreground);
        }

        .remove-btn {
            padding: 0.5rem 1rem;
            background-color: var(--destructive);
            color: var(--destructive-foreground);
            border-radius: var(--radius);
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .remove-btn:hover {
            background-color: color-mix(in srgb, var(--destructive) 90%, black);
        }

        .cart-summary {
            margin-top: 2rem;
            background-color: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            padding: 2rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .summary-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .summary-label {
            color: var(--muted-foreground);
        }

        .summary-value {
            font-weight: 600;
            color: var(--foreground);
        }

        .grand-total {
            font-size: 1.25rem;
            color: var(--primary);
        }

        .cart-actions {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 2rem;
        }

        .continue-shopping {
            padding: 0.875rem 2rem;
            background-color: var(--secondary);
            color: var(--secondary-foreground);
            border-radius: var(--radius);
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .continue-shopping:hover {
            background-color: color-mix(in srgb, var(--secondary) 90%, black);
        }

        .checkout-btn {
            padding: 0.875rem 2rem;
            background-color: var(--primary);
            color: var(--primary-foreground);
            border-radius: var(--radius);
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .checkout-btn:hover {
            background-color: color-mix(in srgb, var(--primary) 90%, black);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .empty-cart {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--muted-foreground);
        }

        .empty-cart i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--border);
        }

        .empty-cart h2 {
            font-family: var(--font-serif);
            margin-bottom: 0.5rem;
            color: var(--foreground);
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal {
            background-color: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow-xl);
            width: 90%;
            max-width: 500px;
            padding: 2rem;
            border: 1px solid var(--border);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-header h2 {
            font-family: var(--font-serif);
            color: var(--foreground);
        }

        .close-btn {
            background: none;
            border: none;
            color: var(--muted-foreground);
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .close-btn:hover {
            color: var(--destructive);
        }

        .modal-body {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: 500;
            color: var(--foreground);
            font-size: 0.875rem;
        }

        .form-group input {
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background-color: var(--input);
            color: var(--foreground);
            transition: border-color 0.2s ease;
        }

        .form-group input:focus {
            border-color: var(--ring);
            outline: none;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1rem;
        }

        @media (max-width: 768px) {
            .cart-item-header,
            .cart-item {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .product-info {
                justify-content: center;
                text-align: center;
            }

            .cart-actions {
                flex-direction: column;
            }

            .cart-actions a,
            .cart-actions button {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <div class="cart-header">
            <h1>Mon Panier</h1>
            <p>Gérez vos articles</p>
        </div>

        <?php 
        $grand_total = 0;
        $items_count = 0;

        if ($result->num_rows > 0): 
        ?>
            <div class="cart-items">
                <div class="cart-item-header">
                    <div>Produit</div>
                    <div>Prix unitaire</div>
                    <div>Quantité</div>
                    <div>Total</div>
                    <div>Actions</div>
                </div>

                <?php while ($row = $result->fetch_assoc()): 
                    $grand_total += $row['total'];
                    $items_count++;
                ?>
                    <div class="cart-item">
                        <div class="product-info">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($row['img']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                            </div>
                            <div class="product-details">
                                <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                                <div class="product-price"><?php echo number_format($row['prix'], 2); ?> DZD</div>
                            </div>
                        </div>
                        <div class="product-price"><?php echo number_format($row['prix'], 2); ?> DZD</div>
                        <div class="quantity-control">
                            <form action="update_cart.php" method="POST" class="update-form">
                                <input type="hidden" name="produit_id" value="<?php echo $row['produit_id']; ?>">
                                <input type="number" name="quantite" value="<?php echo $row['quantite']; ?>" min="1" class="quantity-input" onchange="this.form.submit()">
                            </form>
                        </div>
                        <div class="item-total"><?php echo number_format($row['total'], 2); ?> DZD</div>
                        <div>
                            <form action="remove_from_cart.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
                                <input type="hidden" name="produit_id" value="<?php echo $row['produit_id']; ?>">
                                <button type="submit" class="remove-btn">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="cart-summary">
                <div class="summary-row">
                    <span class="summary-label">Sous-total</span>
                    <span class="summary-value"><?php echo number_format($grand_total, 2); ?> DZD</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Livraison</span>
                    <span class="summary-value">Gratuite</span>
                </div>
                <div class="summary-row grand-total">
                    <span class="summary-label">Total</span>
                    <span class="summary-value"><?php echo number_format($grand_total, 2); ?> DZD</span>
                </div>

                <div class="cart-actions">
                    <a href="../index1.php" class="continue-shopping">
                        <i class="fas fa-arrow-left"></i> Continuer les achats
                    </a>
                    <button class="checkout-btn" onclick="openModal()">
                        <i class="fas fa-check"></i> Valider la commande
                    </button>
                </div>
            </div>

        <?php else: ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h2>Votre panier est vide</h2>
                <p>Ajoutez des articles à votre panier pour continuer.</p>
                <a href="../index1.php" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-shopping-bag"></i> Parcourir les produits
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Checkout Modal -->
    <div class="modal-overlay" id="checkoutModal">
        <div class="modal">
            <div class="modal-header">
                <h2>Informations de livraison</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="orderForm" action="submit_form.php" method="POST">
                    <div class="form-group">
                        <label for="name">Nom complet</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Adresse de livraison</label>
                        <input type="text" id="address" name="address" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Numéro de téléphone</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="continue-shopping" onclick="closeModal()">Annuler</button>
                        <button type="submit" class="checkout-btn">Confirmer la commande</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('checkoutModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('checkoutModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('checkoutModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>