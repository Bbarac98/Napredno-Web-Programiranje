<?php
session_start();
require_once 'config/database.php';

// Sigurnosna provjera: Samo prijavljeni korisnici imaju pristup
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Logika za sortiranje kolekcije
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$order_by = "ORDER BY ug.added_at DESC"; 

if ($sort === 'abc') {
    $order_by = "ORDER BY g.title ASC";
} elseif ($sort === 'abc_desc') {
    $order_by = "ORDER BY g.title DESC";
} elseif ($sort === 'rating_desc') {
    // Gura NULL vrijednosti (bez ocjene) na dno liste
    $order_by = "ORDER BY CASE WHEN ug.rating IS NULL OR ug.rating = '' THEN 1 ELSE 0 END, ug.rating DESC, g.title ASC";
} elseif ($sort === 'rating_asc') {
    $order_by = "ORDER BY CASE WHEN ug.rating IS NULL OR ug.rating = '' THEN 1 ELSE 0 END, ug.rating ASC, g.title ASC";
}

try {
    $sql = "SELECT g.id as game_id, g.title, g.cover_image, g.release_date, ug.status, ug.rating 
            FROM user_games ug 
            JOIN games g ON ug.game_id = g.id 
            WHERE ug.user_id = ? 
            $order_by";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $kolekcija = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Greška pri dohvaćanju kolekcije: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moja Kolekcija - Video Game Tracker</title>
    <link rel="stylesheet" href="public/css/style.css?v=2">
</head>
<body>

    <div class="nav-bar">
        <div><strong>Video Game Tracker</strong></div>
        <div>
            <a href="index.php">Početna</a>
            <a href="search.php">Tražilica</a>
            <a href="kolekcija.php">Moja Kolekcija</a>
            <a href="contact.php">Kontakt</a>
            <a href="logout.php" style="color: #ff5252;">Odjavi se</a>
        </div>
    </div>

    <div class="header-section">
        <h2>🎮 Moja Kolekcija</h2>
        <p>Ovdje se nalaze sve igre koje pratiš.</p>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="msg <?php echo $_GET['msg'] == 'updated' ? 'success' : 'error'; ?>" style="display: block;">
            <?php 
                if($_GET['msg'] == 'updated') echo "Podaci o igri su uspješno ažurirani!";
                if($_GET['msg'] == 'deleted') echo "Igra je uklonjena iz kolekcije.";
            ?>
        </div>
    <?php endif; ?>

    <div style="margin-bottom: 30px; text-align: right; background-color: #1e1e1e; padding: 15px; border-radius: 8px;">
        <form action="kolekcija.php" method="GET" style="margin: 0; display: inline-block;">
            <label for="sort" style="font-size: 14px; color: #aaa; margin-right: 10px;">Sortiraj zbirku po:</label>
            <select name="sort" id="sort" onchange="this.form.submit();" style="width: auto; display: inline-block; margin: 0; padding: 8px 20px;">
                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Najnovije dodano</option>
                <option value="abc" <?php echo $sort == 'abc' ? 'selected' : ''; ?>>Abecedi (A-Z)</option>
                <option value="abc_desc" <?php echo $sort == 'abc_desc' ? 'selected' : ''; ?>>Abecedi (Z-A)</option>
                <option value="rating_desc" <?php echo $sort == 'rating_desc' ? 'selected' : ''; ?>>Najvišoj ocjeni (5 ⭐ -> 1 ⭐)</option>
                <option value="rating_asc" <?php echo $sort == 'rating_asc' ? 'selected' : ''; ?>>Najnižoj ocjeni (1 ⭐ -> 5 ⭐)</option>
            </select>
        </form>
        
        <div style="margin-top: 10px; font-size: 13px; color: #888;">
            <?php 
                $ukupno = count($kolekcija);
                if ($ukupno == 1) {
                    echo "<strong>1</strong> igra u kolekciji";
                } elseif ($ukupno >= 2 && $ukupno <= 4) {
                    echo "<strong>" . $ukupno . "</strong> igre u kolekciji";
                } else {
                    echo "<strong>" . $ukupno . "</strong> igara u kolekciji";
                }
            ?>
        </div>
    </div>

    <div class="games-grid">
        <?php if (empty($kolekcija)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px; background: #1e1e1e; border-radius: 8px;">
                <h3 style="color: #888;">Tvoja kolekcija je trenutno prazna.</h3>
                <br>
                <a href="search.php" style="padding: 10px 20px; background: #bb86fc; color: #121212; text-decoration: none; border-radius: 5px; font-weight: bold;">Pronađi prvu igru</a>
            </div>
        <?php else: ?>
            
            <?php foreach ($kolekcija as $igra): ?>
                <div class="game-card">
                    <a href="game_details.php?id=<?php echo $igra['game_id']; ?>&from=kolekcija" style="display: block; line-height: 0;">
                        <img src="<?php echo htmlspecialchars($igra['cover_image']); ?>" alt="Naslovnica" class="game-image" style="transition: opacity 0.2s;" onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1">
                    </a>
                    
                    <div class="game-info">
                        <h4 class="game-title">
                            <a href="game_details.php?id=<?php echo $igra['game_id']; ?>&from=kolekcija" style="color: #e0e0e0; text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='#bb86fc'" onmouseout="this.style.color='#e0e0e0'">
                                <?php echo htmlspecialchars($igra['title']); ?>
                            </a>
                        </h4>
                        
                        <div class="game-controls">
                            <form action="update_game.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="game_id" value="<?php echo $igra['game_id']; ?>">
                                
                                <label style="font-size: 12px; color: #aaa;">Status igranja:</label>
                                <select name="status">
                                    <option value="plan_to_play" <?php echo $igra['status'] == 'plan_to_play' ? 'selected' : ''; ?>>Želim igrati</option>
                                    <option value="playing" <?php echo $igra['status'] == 'playing' ? 'selected' : ''; ?>>Trenutno igram</option>
                                    <option value="completed" <?php echo $igra['status'] == 'completed' ? 'selected' : ''; ?>>Završeno</option>
                                    <option value="dropped" <?php echo $igra['status'] == 'dropped' ? 'selected' : ''; ?>>Odustao</option>
                                </select>

                                <label style="font-size: 12px; color: #aaa; margin-top: 5px; display: block;">Moja ocjena:</label>
                                <select name="rating">
                                    <option value="">Bez ocjene</option>
                                    <option value="5" <?php echo $igra['rating'] == '5' ? 'selected' : ''; ?>>⭐⭐⭐⭐⭐</option>
                                    <option value="4" <?php echo $igra['rating'] == '4' ? 'selected' : ''; ?>>⭐⭐⭐⭐</option>
                                    <option value="3" <?php echo $igra['rating'] == '3' ? 'selected' : ''; ?>>⭐⭐⭐</option>
                                    <option value="2" <?php echo $igra['rating'] == '2' ? 'selected' : ''; ?>>⭐⭐</option>
                                    <option value="1" <?php echo $igra['rating'] == '1' ? 'selected' : ''; ?>>⭐</option>
                                </select>
                                
                                <div class="action-buttons">
                                    <button type="submit" class="btn-spremi">Spremi</button>
                                    <button type="submit" formaction="delete_game.php" class="btn-ukloni" onclick="return confirm('Jeste li sigurni da želite ukloniti ovu igru?');">Ukloni</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>

</body>
</html>