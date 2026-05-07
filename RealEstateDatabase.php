<?php
require_once __DIR__ . '/Database.php';

class RealEstateDatabase {
    private PDO $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function addUser(string $userName, string $contactInfo, string $passwordHash, string $userType): bool {
        $sql = "INSERT INTO Users (userName, contactInfo, passwordHash, userType)
                VALUES (:userName, :contactInfo, :passwordHash, :userType)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':userName' => $userName,
            ':contactInfo' => $contactInfo,
            ':passwordHash' => $passwordHash,
            ':userType' => $userType
        ]);
    }

    public function getUserByUsername(string $userName) {
        $sql = "SELECT * FROM Users WHERE userName = :userName LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':userName' => $userName]);
        return $stmt->fetch();
    }

    public function addProperty(string $title, string $propertyType, string $address, string $city, float $price, string $status, int $agentId): bool {
        $sql = "INSERT INTO Properties (title, propertyType, address, city, price, status, agentId)
                VALUES (:title, :propertyType, :address, :city, :price, :status, :agentId)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':title' => $title,
            ':propertyType' => $propertyType,
            ':address' => $address,
            ':city' => $city,
            ':price' => $price,
            ':status' => $status,
            ':agentId' => $agentId
        ]);
    }

    public function getAllProperties(): array {
        $sql = "SELECT p.*, u.userName AS agentName
                FROM Properties p
                JOIN Users u ON p.agentId = u.userId
                ORDER BY p.propertyId DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll();
    }

    public function getPropertyById(int $propertyId) {
        $sql = "SELECT p.*, u.userName AS agentName
                FROM Properties p
                JOIN Users u ON p.agentId = u.userId
                WHERE p.propertyId = :propertyId";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':propertyId' => $propertyId]);
        return $stmt->fetch();
    }

    public function getPropertiesByCity(string $city): array {
        $sql = "SELECT p.*, u.userName AS agentName
                FROM Properties p
                JOIN Users u ON p.agentId = u.userId
                WHERE p.city LIKE :city
                ORDER BY p.propertyId DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':city' => '%' . $city . '%']);
        return $stmt->fetchAll();
    }

    public function addInquiry(int $userId, int $propertyId, string $message): bool {
        $sql = "INSERT INTO Inquiries (userId, propertyId, message, inquiryDate)
                VALUES (:userId, :propertyId, :message, NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':userId' => $userId,
            ':propertyId' => $propertyId,
            ':message' => $message
        ]);
    }

    public function getPropertyListingsView(): array {
    $sql = "SELECT * FROM PropertyListingView";
    $stmt = $this->conn->query($sql);
    return $stmt->fetchAll();
}

    public function getUserDetails(int $userId) {
        // Get basic user info
        $sql = "SELECT * FROM Users WHERE userId = :userId";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        $user = $stmt->fetch();

        if ($user) {
            // Get inquiries for this user
            $inqSql = "SELECT i.*, p.title 
                       FROM Inquiries i 
                       JOIN Properties p ON i.propertyId = p.propertyId 
                       WHERE i.userId = :userId 
                       ORDER BY i.inquiryDate DESC";
            $inqStmt = $this->conn->prepare($inqSql);
            $inqStmt->execute([':userId' => $userId]);
            $user['inquiries'] = $inqStmt->fetchAll();
        }

        return $user;
    }

    public function addFavorite(int $userId, int $propertyId): bool {
    $sql = "INSERT INTO Favorites (userId, propertyId, savedDate)
            VALUES (:userId, :propertyId, NOW())";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([
        ':userId' => $userId,
        ':propertyId' => $propertyId
    ]);
}

public function getFavoritesByUser(int $userId): array {
    $sql = "SELECT p.*, f.savedDate
            FROM Favorites f
            JOIN Properties p ON f.propertyId = p.propertyId
            WHERE f.userId = :userId
            ORDER BY f.savedDate DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([':userId' => $userId]);
    return $stmt->fetchAll();
}
}
?>
