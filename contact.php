<?php
session_start();
require_once 'config/database.php';

$poruka = "";
$tip_poruke = "";

// Predispunjavanje imena ako je korisnik prijavljen
$default_name = isset($_SESSION['username']) ? $_SESSION['username'] : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Validacija unosa
    if (empty($name) || empty($email) || empty($message)) {
        $poruka = "Molimo ispunite sva obavezna polja (označena zvjezdicom).";
        $tip_poruke = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $poruka = "Unijeli ste neispravan format e-mail adrese!";
        $tip_poruke = "error";
    } else {
        try {
            $sql = "INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $email, $subject, $message]);

            $poruka = "Vaša poruka je uspješno poslana! Odgovorit ćemo vam u najkraćem mogućem roku.";
            $tip_poruke = "success";
        } catch (PDOException $e) {
            $poruka = "Došlo je do sistemske greške pri slanju: " . $e->getMessage();
            $tip_poruke = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt - Video Game Tracker</title>
    <link rel="stylesheet" href="public/css/style.css?v=2">
</head>
<body>

    <div class="nav-bar">
        <div><strong>Video Game Tracker</strong></div>
        <div>
            <a href="index.php">Početna</a>
            <a href="search.php">Tražilica</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="kolekcija.php">Moja Kolekcija</a>
                <a href="contact.php">Kontakt</a>
                <a href="logout.php" style="color: #ff5252;">Odjavi se</a>
            <?php else: ?>
                <a href="contact.php">Kontakt</a>
                <a href="login.php">Prijava</a>
                <a href="register.php">Registracija</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-container" style="max-width: 600px;">
        <h2>Kontaktirajte nas</h2>
        <p style="text-align: center; color: #aaa; margin-bottom: 20px;">
            Imate pitanje, prijedlog ili želite prijaviti bug? Ispunite formu ispod!
        </p>

        <?php if (!empty($poruka)): ?>
            <div class="msg <?php echo $tip_poruke; ?>">
                <?php echo $poruka; ?>
            </div>
        <?php endif; ?>

        <form action="contact.php" method="POST">
            <label for="name">Ime *</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($default_name); ?>" required>

            <label for="email">E-mail *</label>
            <input type="email" id="email" name="email" required>

            <label for="subject">Naslov poruke</label>
            <input type="text" id="subject" name="subject">

            <label for="message">Vaša poruka *</label>
            <textarea id="message" name="message" required placeholder="Ovdje upišite svoju poruku..."></textarea>

            <button type="submit">Pošalji poruku</button>
        </form>
    </div>

</body>
</html>