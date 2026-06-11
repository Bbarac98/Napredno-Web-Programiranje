<?php
session_start();
require_once 'config/database.php';

// Preusmjeravanje već prijavljenih korisnika
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$greska = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_key = trim($_POST['login_key']);
    $password = $_POST['password'];

    if (empty($login_key) || empty($password)) {
        $greska = 'Molimo unesite korisničko ime (ili email) i lozinku.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$login_key, $login_key]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: index.php");
                exit();
            } else {
                $greska = 'Pogrešni podaci za prijavu!';
            }
        } catch (PDOException $e) {
            $greska = "Greška baze podataka: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prijava - Video Game Tracker</title>
    <link rel="stylesheet" href="public/css/style.css?v=2">
</head>
<body>

    <div class="nav-bar">
        <div><strong>Video Game Tracker</strong></div>
        <div>
            <a href="index.php">Početna</a>
            <a href="search.php">Tražilica</a>
            <a href="contact.php">Kontakt</a>
            <a href="login.php">Prijava</a>
            <a href="register.php">Registracija</a>
        </div>
    </div>

    <div class="form-container">
        <h2>Prijava</h2>
        
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'registered'): ?>
            <div class="msg success">Uspješna registracija! Sada se možete prijaviti.</div>
        <?php endif; ?>

        <?php if ($greska): ?>
            <div class="msg error"><?php echo htmlspecialchars($greska); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="login_key">Korisničko ime ili Email</label>
            <input type="text" id="login_key" name="login_key" required>

            <label for="password">Lozinka</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Prijavi se</button>
        </form>
        
        <p style="text-align: center; margin-top: 15px; font-size: 14px; color: #aaa;">
            Nemaš račun? <a href="register.php" style="color: #bb86fc;">Registriraj se ovdje</a>.
        </p>
    </div>

</body>
</html>