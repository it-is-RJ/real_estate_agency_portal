<?php
require_once 'config/config.php';
require_once 'includes/auth.php';
require_once 'classes/RealEstateDatabase.php';

requireRole(['buyer', 'renter']);

$db = new RealEstateDatabase();

$userId = $_SESSION['user']['userId'];
$propertyId = (int)($_GET['propertyId'] ?? 0);

if ($propertyId > 0) {
    try {
        
        $favorites = $db->getFavoritesByUser($userId);
        foreach ($favorites as $fav) {
            if ($fav['propertyId'] == $propertyId) {
                header("Location: favorites.php");
                exit;
            }
        }

        $db->addFavorite($userId, $propertyId);

    } catch (Throwable $e) {
    
    }
}

header("Location: favorites.php");
exit;
?>