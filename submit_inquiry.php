<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'classes/RealEstateDatabase.php';

// Only Buyers and Renters should be allowed to send inquiries
requireRole(['buyer', 'renter']);

$db = new RealEstateDatabase();
$message = '';
$propertyId = (int)($_GET['propertyId'] ?? $_POST['propertyId'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int)$_SESSION['user']['userId'];
    $messageText = trim($_POST['message'] ?? '');

    if ($propertyId > 0 && $messageText !== '') {
        try {
            $db->addInquiry($userId, $propertyId, $messageText);
            $message = 'Inquiry submitted successfully! The agent will contact you soon.';
        } catch (Throwable $e) {
            $message = 'Error: ' . $e->getMessage();
        }
    } else {
        $message = 'Please enter a message.';
    }
}
?>
<?php include 'includes/header.php'; ?>

<h2>Send Inquiry</h2>
<?php if ($message): ?>
    <p class="<?= strpos($message, 'Error') !== false ? 'error' : 'success' ?>">
<?php endif; ?>

<div class="card">
    <form method="POST">
        <input type="hidden" name="propertyId" value="<?= $propertyId ?>">
        <label>Your Message</label>
        <textarea name="message" rows="5" required placeholder="I am interested in this property. When is the next viewing?"></textarea>
        <button type="submit">Submit Inquiry</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>