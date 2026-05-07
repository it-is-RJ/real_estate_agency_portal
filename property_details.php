<?php
require_once 'config/config.php';
require_once 'classes/RealEstateDatabase.php';

$db = new RealEstateDatabase();

$propertyId = (int)($_GET['id'] ?? 0);
$property = $db->getPropertyById($propertyId);
?>

<?php include 'includes/header.php'; ?>

<h2 class="page-title">Property Details</h2>

<?php if (!$property): ?>
    <p class="error">Property not found.</p>
<?php else: ?>
    <div class="card details-card">
        <h3><?= htmlspecialchars($property['title']) ?></h3>

        <p><strong>Type:</strong> <?= htmlspecialchars($property['propertyType']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($property['address']) ?></p>
        <p><strong>City:</strong> <?= htmlspecialchars($property['city']) ?></p>
        <p><strong>Price:</strong> $<?= number_format($property['price'], 2) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($property['status']) ?></p>
        <p><strong>Agent:</strong> <?= htmlspecialchars($property['agentName']) ?></p>
    </div>

    <?php if (isset($_SESSION['user']) && in_array($_SESSION['user']['userType'], ['buyer','renter'])): ?>
        <div class="card action-card">
            <a class="btn" href="submit_inquiry.php?propertyId=<?= $property['propertyId'] ?>">
                Send Inquiry
            </a>

            <a class="btn secondary" href="save_favorite.php?propertyId=<?= $property['propertyId'] ?>">
                Save to Favorites
            </a>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>