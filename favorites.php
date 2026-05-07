<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'classes/RealEstateDatabase.php';

requireRole(['buyer', 'renter']);

$db = new RealEstateDatabase();
$favorites = $db->getFavoritesByUser($_SESSION['user']['userId']);
?>

<?php include 'includes/header.php'; ?>

<h2 class="page-title">Your Favorites</h2>

<?php if (empty($favorites)): ?>
    <p>No saved properties yet.</p>
<?php else: ?>
    <div class="grid">
        <?php foreach ($favorites as $property): ?>
            <div class="card property-card">
                <h3><?= htmlspecialchars($property['title']) ?></h3>
                <p><?= htmlspecialchars($property['city']) ?></p>
                <p>$<?= number_format($property['price'], 2) ?></p>

                <a class="btn" href="property_details.php?id=<?= $property['propertyId'] ?>">
                    View
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>