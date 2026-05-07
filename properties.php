<?php
require_once 'config/config.php';
require_once 'classes/RealEstateDatabase.php';

$db = new RealEstateDatabase();

$searchCity = trim($_GET['search_city'] ?? '');

if ($searchCity !== '') {
    $properties = $db->getPropertiesByCity($searchCity);
} else {
    $properties = $db->getPropertyListingsView();
}
?>

<?php include 'includes/header.php'; ?>

<h2 class="page-title">Available Properties</h2>

<div class="card">
    <form method="GET" class="search-bar">
        <input type="text" name="search_city"
               value="<?= htmlspecialchars($searchCity) ?>"
               placeholder="Search by city...">

        <button type="submit">Search</button>

        <?php if ($searchCity): ?>
            <a href="properties.php" class="clear-btn">Clear</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($properties)): ?>
    <p>No properties found.</p>
<?php else: ?>
    <div class="grid">
        <?php foreach ($properties as $property): ?>
            <div class="card property-card">
                <h3><?= htmlspecialchars($property['title']) ?></h3>

                <p><strong>📍 City:</strong> <?= htmlspecialchars($property['city']) ?></p>
                <p><strong> Price:</strong> $<?= number_format($property['price'], 2) ?></p>
                <p><strong>Status:</strong> <?= htmlspecialchars($property['status']) ?></p>

                <p class="agent">
                    Agent: <?= htmlspecialchars($property['agentName']) ?>
                </p>

                <a class="btn" href="property_details.php?id=<?= (int)$property['propertyId'] ?>">
                    View Details
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>

