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
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $greska = 'Sva polja su obavezna!';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);
            
            if ($stmt->rowCount() > 0) {
                $greska = 'Korisničko ime ili email već postoje!';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $role = 'user'; 

                $insert = $pdo->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
                
                if ($insert->execute([$username, $email, $hashed_password, $role])) {
                    header("Location: login.php?msg=registered");
                    exit();
                } else {
                    $greska = 'Dogodila se greška pri registraciji. Pokušajte ponovno.';
                }
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
    <title>Registracija - Video Game Tracker</title>
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
        <h2>Napravi račun</h2>
        
        <?php if ($greska): ?>
            <div class="msg error"><?php echo htmlspecialchars($greska); ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label for="username">Korisničko ime</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Lozinka</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Registriraj se</button>
        </form>
        
        <p style="text-align: center; margin-top: 15px; font-size: 14px; color: #aaa;">
            Već imaš račun? <a href="login.php" style="color: #bb86fc;">Prijavi se ovdje</a>.
        </p>
    </div>

</body>
</html>