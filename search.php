<?php
session_start();
require_once 'config/database.php';

// Učitavanje API ključa
$env = parse_ini_file(__DIR__ . '/.env');
$api_key = $env['RAWG_API_KEY'];

$igre = [];
$search_query = "";

// Dohvaćanje rezultata pretrage s RAWG API-ja
if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $search_query = trim($_GET['query']);
    $url_query = urlencode($search_query);
    
    $url = "https://api.rawg.io/api/games?key=" . $api_key . "&search=" . $url_query;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "VideoGameTracker/1.0");
    
    $odgovor = curl_exec($ch);
    
    if (!curl_errno($ch)) {
        $podaci = json_decode($odgovor, true);
        if (isset($podaci['results'])) {
            $igre = $podaci['results'];
        }
    }
    curl_close($ch);
}

// Dohvaćanje popisa spremljenih igara za prijavljenog korisnika
$spremljene_igre = [];
if (isset($_SESSION['user_id'])) {
    try {
        $stmt_check = $pdo->prepare("SELECT game_id FROM user_games WHERE user_id = ?");
        $stmt_check->execute([$_SESSION['user_id']]);
        $spremljene_igre = $stmt_check->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        // Greška se ignorira jer ne utječe na rad tražilice
    }
}
?>

<!DOCTYPE html>
<html lang="hr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tražilica - Video Game Tracker</title>
    <link rel="stylesheet" href="public/css/style.css?v=2">
</head>
<body>

    <div class="nav-bar">
        <div><strong>Video Game Tracker</strong></div>
        <div>
            <a href="index.php">Početna</a>
            <a href="search.php">Tražilica</a>
            <?php if(isset($_SESSION['user_id'])): ?>
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

    <div class="search-container">
        <h2>Pronađi igru za svoju kolekciju</h2>
        <form action="search.php" method="GET" autocomplete="off">
            <input type="text" id="search-box" name="query" class="search-input" placeholder="Počni tipkati naziv igre..." value="<?php echo htmlspecialchars($search_query); ?>" required>
            <button type="submit" class="search-btn">Traži</button>
            <div id="suggestions-box" class="suggestions-box"></div>
        </form>
    </div>

    <?php if(isset($_GET['status'])): ?>
        <div class="msg <?php echo $_GET['status'] == 'success' ? 'success' : 'error'; ?>" style="display: block; margin-bottom: 20px;">
            <?php 
                if($_GET['status'] == 'success') echo "Igra je uspješno dodana u vašu kolekciju!";
                if($_GET['status'] == 'already_added') echo "Ovu igru već imate u svojoj kolekciji.";
                if($_GET['status'] == 'deleted') echo "Igra je uspješno uklonjena iz kolekcije.";
            ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($search_query)): ?>
        <h3>Rezultati pretrage za: "<?php echo htmlspecialchars($search_query); ?>"</h3>
        
        <div class="games-grid">
            <?php if (empty($igre)): ?>
                <p style="grid-column: 1 / -1; text-align: center; color: #aaa;">Nije pronađena nijedna igra s tim nazivom.</p>
            <?php else: ?>
                <?php foreach ($igre as $igra): ?>
                    <?php $slika = !empty($igra['background_image']) ? $igra['background_image'] : 'https://via.placeholder.com/250x150?text=Nema+slike'; ?>
                    <div class="game-card">
                        
                        <a href="game_details.php?id=<?php echo $igra['id']; ?>&query=<?php echo urlencode($search_query); ?>">
                            <img src="<?php echo $slika; ?>" alt="Naslovnica" class="game-image" style="transition: opacity 0.2s;" onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1">
                        </a>
                        
                        <div class="game-info">
                            <h4 class="game-title"><?php echo htmlspecialchars($igra['name']); ?></h4>
                            <p style="font-size: 12px; color: #aaa; margin: 0 0 15px 0;">
                                Izašla: <?php echo !empty($igra['released']) ? date('d.m.Y.', strtotime($igra['released'])) : 'Nepoznato'; ?>
                            </p>
                            
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php $u_kolekciji = in_array($igra['id'], $spremljene_igre); ?>
                                <?php if ($u_kolekciji): ?>
                                    <form action="delete_game.php" method="POST" style="margin: 0;">
                                        <input type="hidden" name="game_id" value="<?php echo $igra['id']; ?>">
                                        <input type="hidden" name="source" value="search">
                                        <input type="hidden" name="query" value="<?php echo htmlspecialchars($search_query); ?>">
                                        <button type="submit" style="background: #cf6679; color: #121212; padding: 10px; width: 100%; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">❌ Ukloni iz kolekcije</button>
                                    </form>
                                <?php else: ?>
                                    <form action="add_game.php" method="POST" style="margin: 0;">
                                        <input type="hidden" name="rawg_id" value="<?php echo $igra['id']; ?>">
                                        <input type="hidden" name="title" value="<?php echo htmlspecialchars($igra['name']); ?>">
                                        <input type="hidden" name="cover_image" value="<?php echo !empty($igra['background_image']) ? $igra['background_image'] : ''; ?>">
                                        <input type="hidden" name="release_date" value="<?php echo !empty($igra['released']) ? $igra['released'] : null; ?>">
                                        <input type="hidden" name="source" value="search">
                                        <input type="hidden" name="query" value="<?php echo htmlspecialchars($search_query); ?>">
                                        <button type="submit" style="background: #bb86fc; color: #121212; padding: 10px; width: 100%; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">➕ Dodaj u kolekciju</button>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <button type="button" onclick="window.location.href='login.php'" style="background: #555; color: #fff; padding: 10px; width: 100%; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">Prijavi se za dodavanje</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <script src="public/js/script.js"></script>
</body>
</html>