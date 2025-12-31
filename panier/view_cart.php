<?php
session_start();
include('../config.php');

if (!isset($_SESSION['id'])) {
    header("Location: ../Client/index.php");
    exit();
}

$user_id = $_SESSION['id'];

// Récupérer les informations de l'utilisateur
$stmt_user = $con->prepare("SELECT nom_utilisateur, email, telephone, adresse FROM utilisateurs WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_info = $stmt_user->get_result()->fetch_assoc();

// Récupérer le panier avec détails produits
$stmt = $con->prepare("
    SELECT c.quantite, c.prix, c.produit_id, c.image, p.nom, p.quantite_stock,
           (c.quantite * c.prix) as ligne_total 
    FROM panier c 
    JOIN produits p ON c.produit_id = p.id 
    WHERE c.utilisateur_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculer le total global
$total = 0;
$items = []; // Pour stocker les items et réafficher
while ($item = $result->fetch_assoc()) {
    $total += $item['ligne_total'];
    $items[] = $item;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier - Watch Store</title>
    <link rel="stylesheet" href="../global.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Tes styles existants... */
        .quantity-input { max-width: 80px; }
    </style>
</head>
<body>
    <div class="cart-container">
        <header class="cart-header">
            <h1>Mon Panier</h1>
            <a href="../index1.php" class="continue-shopping">
                <i class="fas fa-arrow-left"></i> Continuer les achats
            </a>
        </header>

        <?php if (!empty($items)): ?>
            <div class="cart-items">
                <?php foreach ($items as $item): ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['nom']); ?>" class="item-image">

                        <div class="item-details">
                            <h2 class="item-title"><?php echo htmlspecialchars($item['nom']); ?></h2>
                            <p class="item-price"><?php echo number_format($item['prix'], 2); ?> DZD</p>
                            <p class="item-stock">Stock disponible : <?php echo $item['quantite_stock']; ?></p>
                        </div>

                        <div class="item-quantity">
                            <form action="update_cart.php" method="post">
                                <input type="hidden" name="produit_id" value="<?php echo $item['produit_id']; ?>">
                                <input type="number" name="quantite" value="<?php echo $item['quantite']; ?>" min="1" max="<?php echo $item['quantite_stock']; ?>" class="quantity-input" required>
                                <button type="submit" class="btn-update">Mettre à jour</button>
                            </form>
                        </div>

                        <div class="item-total"><?php echo number_format($item['ligne_total'], 2); ?> DZD</div>

                        <form action="remove_from_cart.php" method="post" class="item-remove">
                            <input type="hidden" name="produit_id" value="<?php echo $item['produit_id']; ?>">
                            <button type="submit" class="btn-remove">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h2>Récapitulatif</h2>
                <div class="summary-line">
                    <span>Sous-total</span>
                    <span id="subtotal"><?php echo number_format($total, 2); ?> DZD</span>
                </div>
                <div class="summary-line total">
                    <span>Total</span>
                    <span id="grandtotal"><?php echo number_format($total, 2); ?> DZD</span>
                </div>
                <button onclick="openModal()" class="checkout-btn" <?php if ($total == 0) echo 'disabled'; ?>>
                    <i class="fas fa-credit-card"></i> Procéder au paiement
                </button>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h2>Votre panier est vide</h2>
                <p>Commencez vos achats dès maintenant !</p>
                <a href="../index1.php" class="btn-primary">
                    <i class="fas fa-arrow-left"></i> Retour à la boutique
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Checkout (ton modal existant) -->
    <div id="checkoutModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2>Finaliser votre commande</h2>

            <form action="submit_form.php" method="post">
                <div class="form-group">
                    <label for="name">Nom</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_info['nom_utilisateur'] ?? ''); ?>" required readonly>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_info['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="address">Adresse de livraison</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user_info['adresse'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Téléphone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user_info['telephone'] ?? ''); ?>" required>
                </div>

                <button type="submit" class="btn-primary">Confirmer la commande</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('checkoutModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('checkoutModal').style.display = 'none';
        }

        // Fermer modal si clic dehors
        document.getElementById('checkoutModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>