<?php
session_start();
include('../config.php');

if (!isset($_SESSION['id'])) {
    header("Location: ../Client/index.php");
    exit();
}

$user_id = $_SESSION['id'];


$stmt = $con->prepare("
    SELECT c.id, c.total, c.statut, c.nom_livraison, c.adresse_livraison, 
           c.telephone_livraison, c.email_livraison, c.date_creation,
           COUNT(DISTINCT d.id) as nombre_articles,
           GROUP_CONCAT(DISTINCT CONCAT(d.nom_produit, ' (x', d.quantite, ')') SEPARATOR ', ') as articles_list
    FROM commandes c
    LEFT JOIN details_commande d ON c.id = d.commande_id
    WHERE c.utilisateur_id = ?
    GROUP BY c.id
    ORDER BY c.date_creation DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Commandes - Atelier</title>
    <link rel="stylesheet" href="../global.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .orders-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .orders-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .orders-header h1 {
            font-family: var(--font-serif);
            font-size: 2.5rem;
            color: var(--foreground);
            margin-bottom: 0.5rem;
        }

        .orders-header p {
            color: var(--muted-foreground);
            font-size: 1.125rem;
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
            margin-bottom: 2rem;
            transition: all 0.2s ease;
        }

        .back-btn:hover {
            background-color: color-mix(in srgb, var(--secondary) 90%, black);
        }

        .order-card {
            background-color: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.2s ease;
        }

        .order-card:hover {
            box-shadow: var(--shadow-md);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .order-number {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--foreground);
        }

        .order-date {
            color: var(--muted-foreground);
            font-size: 0.875rem;
        }

        .order-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-en_attente {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-confirmee {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-expediee {
            background-color: #e0e7ff;
            color: #4338ca;
        }

        .status-livree {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-annulee {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .order-body {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .order-info {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .order-info-label {
            font-size: 0.75rem;
            color: var(--muted-foreground);
            text-transform: uppercase;
            font-weight: 500;
        }

        .order-info-value {
            color: var(--foreground);
            font-weight: 500;
        }

        .articles-section {
            margin-top: 1rem;
            padding: 1rem;
            background-color: var(--muted);
            border-radius: var(--radius);
        }

        .articles-section h4 {
            font-size: 0.875rem;
            color: var(--muted-foreground);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .articles-list {
            color: var(--foreground);
            line-height: 1.6;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }

        .order-total {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary);
        }

        .empty-orders {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--muted-foreground);
        }

        .empty-orders i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--border);
        }

        .empty-orders h2 {
            font-family: var(--font-serif);
            margin-bottom: 0.5rem;
            color: var(--foreground);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 2rem;
            background-color: var(--primary);
            color: var(--primary-foreground);
            border-radius: var(--radius);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background-color: color-mix(in srgb, var(--primary) 90%, black);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .timeline {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            position: relative;
            padding: 1rem 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background-color: var(--border);
            z-index: 0;
        }

        .timeline-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            z-index: 1;
            background-color: var(--card);
            padding: 0 0.5rem;
        }

        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--muted);
            color: var(--muted-foreground);
            font-size: 1rem;
        }

        .timeline-step.active .timeline-icon {
            background-color: var(--primary);
            color: var(--primary-foreground);
        }

        .timeline-step.completed .timeline-icon {
            background-color: #10b981;
            color: white;
        }

        .timeline-label {
            font-size: 0.75rem;
            color: var(--muted-foreground);
            text-align: center;
        }

        .timeline-step.active .timeline-label,
        .timeline-step.completed .timeline-label {
            color: var(--foreground);
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .order-body {
                grid-template-columns: 1fr;
            }

            .timeline {
                flex-direction: column;
                gap: 1rem;
            }

            .timeline::before {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="orders-container">
        <a href="../index1.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Retour au magasin
        </a>

        <div class="orders-header">
            <h1>Mes Commandes</h1>
            <p>Suivez l'état de vos commandes</p>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($order = $result->fetch_assoc()): 
                $steps = ['en_attente', 'confirmee', 'expediee', 'livree'];
                $currentStepIndex = array_search($order['statut'], $steps);
                if ($order['statut'] == 'annulee') {
                    $currentStepIndex = -1;
                }
            ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-number">Commande #<?php echo $order['id']; ?></div>
                            <div class="order-date">
                                <i class="far fa-calendar"></i>
                                <?php echo date('d/m/Y à H:i', strtotime($order['date_creation'])); ?>
                            </div>
                        </div>
                        <span class="order-status status-<?php echo $order['statut']; ?>">
                            <?php 
                            $statuts = [
                                'en_attente' => 'En attente',
                                'confirmee' => 'Confirmée',
                                'expediee' => 'Expédiée',
                                'livree' => 'Livrée',
                                'annulee' => 'Annulée'
                            ];
                            echo $statuts[$order['statut']];
                            ?>
                        </span>
                    </div>

                    <div class="order-body">
                        <div class="order-info">
                            <span class="order-info-label">Téléphone</span>
                            <span class="order-info-value"><?php echo htmlspecialchars($order['telephone_livraison']); ?></span>
                        </div>
                        <div class="order-info">
                            <span class="order-info-label">Adresse</span>
                            <span class="order-info-value"><?php echo htmlspecialchars($order['adresse_livraison']); ?></span>
                        </div>
                        <div class="order-info">
                            <span class="order-info-label">Articles</span>
                            <span class="order-info-value"><?php echo $order['nombre_articles']; ?> article(s)</span>
                        </div>
                    </div>

                    <?php if ($order['articles_list']): ?>
                    <div class="articles-section">
                        <h4><i class="fas fa-box"></i> Articles commandés</h4>
                        <div class="articles-list">
                            <?php echo htmlspecialchars($order['articles_list']); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($order['statut'] != 'annulee'): ?>
                    <div class="timeline">
                        <div class="timeline-step <?php echo $currentStepIndex >= 0 ? 'completed' : ''; ?>">
                            <div class="timeline-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <span class="timeline-label">En attente</span>
                        </div>
                        <div class="timeline-step <?php echo $currentStepIndex >= 1 ? 'completed' : ''; ?>">
                            <div class="timeline-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <span class="timeline-label">Confirmée</span>
                        </div>
                        <div class="timeline-step <?php echo $currentStepIndex >= 2 ? 'completed' : ''; ?>">
                            <div class="timeline-icon">
                                <i class="fas fa-shipping-fast"></i>
                            </div>
                            <span class="timeline-label">Expédiée</span>
                        </div>
                        <div class="timeline-step <?php echo $currentStepIndex >= 3 ? 'completed' : ''; ?>">
                            <div class="timeline-icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <span class="timeline-label">Livrée</span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="order-footer">
                        <div class="order-total">Total: <?php echo number_format($order['total'], 2); ?> DZD</div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-orders">
                <i class="fas fa-box-open"></i>
                <h2>Aucune commande</h2>
                <p>Vous n'avez pas encore passé de commande.</p>
                <a href="../index1.php" class="btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-shopping-bag"></i> Découvrir nos produits
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>