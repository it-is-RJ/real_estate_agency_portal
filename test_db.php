<?php
require_once 'classes/Database.php';

$db = new Database();
$conn = $db->connect();

echo "Connected successfully!";
?>