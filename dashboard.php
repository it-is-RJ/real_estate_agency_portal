<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'classes/RealEstateDatabase.php';

requireLogin();
$db = new RealEstateDatabase();
$user = $db->getUserDetails($_SESSION['user']['userId']);
?>
<?php include 'includes/header.php'; ?>

<h2>Welcome, <?= htmlspecialchars($user['userName']) ?>!</h2>

<div class="card">
    <p><strong>Account Type:</strong> <?= ucfirst(htmlspecialchars($user['userType'])) ?></p>
    <p><strong>Contact:</strong> <?= htmlspecialchars($user['contactInfo']) ?></p>
</div>

<?php if ($user['userType'] === 'agent'): ?>

    <div class="card">
        <h3>Agent Tools</h3>
        <a href="add_property.php">Add New Property Listing</a>
    </div>

    <div class="card">
        <h3>Your Listings</h3>
        <?php
        $properties = $db->getAllProperties();
        $found = false;

        foreach ($properties as $p):
            if ($p['agentId'] == $user['userId']):
                $found = true;
        ?>
            <p>
                <strong><?= htmlspecialchars($p['title']) ?></strong>  
                (<?= htmlspecialchars($p['status']) ?>)  
                - $<?= number_format($p['price'], 2) ?>
            </p>
        <?php
            endif;
        endforeach;
        ?>

        <?php if (!$found): ?>
            <p>No listings yet.</p>
        <?php endif; ?>
    </div>

<?php else: ?>

    <div class="card">
        <h3>Your Inquiries</h3>
        <?php if (empty($user['inquiries'])): ?>
            <p>You haven't sent any inquiries yet.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($user['inquiries'] as $inquiry): ?>
                    <li>
                        <strong><?= htmlspecialchars($inquiry['title']) ?>:</strong> 
                        "<?= htmlspecialchars($inquiry['message']) ?>" 
                        <br>
                        <small><?= $inquiry['inquiryDate'] ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>