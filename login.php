<?php
require_once 'config/config.php';
require_once 'classes/RealEstateDatabase.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new RealEstateDatabase();
    $userName = trim($_POST['userName'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = $db->getUserByUsername($userName);

    // Verify the password hash against the plain text input
    if ($user && password_verify($password, $user['passwordHash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = $user;
        header('Location: dashboard.php');
        exit;
    } else {
        $message = 'Invalid username or password.';
    }
}
?>
<?php include 'includes/header.php'; ?>

<section>
    <h2>Login</h2>
    <?php if ($message): ?>
        <p class="error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <label>Username</label>
            <input type="text" name="userName" required autofocus>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>