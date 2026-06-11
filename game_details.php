<?php
session_start();
require_once 'config/database.php';

// Učitavanje API ključa
$env = parse_ini_file(__DIR__ . '/.env');
$api_key = $env['RAWG_API_KEY'];

$igra = null;
$greska = "";

// Dohvaćanje detalja igre putem RAWG API-ja
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $game_id = $_GET['id'];
    $url = "https://api.rawg.io/api/games/" . $game_id . "?key=" . $api_key;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "VideoGameTracker/1.0");

    $odgovor = curl_exec($ch);

    if (!curl_errno($ch)) {
        $igra = json_decode($odgovor, true);
        if (isset($igra['detail']) && $igra['detail'] === 'Not found.') {
            $greska = "Tražena igra nije pronađena na serveru.";
            $igra = null;
        }
    } else {
        $greska = "Dogodila se greška pri spajanju na RAWG API.";
    }
    curl_close($ch);
} else {
    $greska = "Nevažeći ID igre.";
}

// Provjera statusa igre u korisničkoj kolekciji
$u_kolekciji = false;
if (isset($_SESSION['user_id']) && $igra) {
    $stmt_check = $pdo->prepare("SELECT 1 FROM user_games WHERE user_id = ? AND game_id = ?");
    $stmt_check->execute([$_SESSION['user_id'], $igra['id']]);
    if ($stmt_check->fetch()) {
        $u_kolekciji = true;
    }
}
?>

<!DOCTYPE html>
<html lang="hr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $igra ? htmlspecialchars($igra['name']) : 'Detalji igre'; ?> - Video Game Tracker</title>
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

    <div style="max-width: 1000px; margin: 0 auto 20px auto;">
        <?php
        // Dinamički povratni link ovisno o izvoru dolaska
        if (isset($_GET['from']) && $_GET['from'] === 'kolekcija') {
            $back_url = "kolekcija.php";
            $tekst_nazad = "⬅ Nazad u moju kolekciju";
        } elseif (isset($_GET['query'])) {
            $back_url = "search.php?query=" . urlencode($_GET['query']);
            $tekst_nazad = "⬅ Nazad na rezultate pretrage";
        } else {
            $back_url = "search.php";
            $tekst_nazad = "⬅ Nazad na tražilicu";
        }
        ?>
        <a href="<?php echo $back_url; ?>" style="color: #03dac6; text-decoration: none; font-weight: bold;"><?php echo $tekst_nazad; ?></a>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <div class="msg <?php echo $_GET['status'] == 'success' ? 'success' : 'error'; ?>" style="display: block; margin-bottom: 20px;">
            <?php
            if ($_GET['status'] == 'success') echo "Igra je uspješno dodana u vašu kolekciju!";
            if ($_GET['status'] == 'deleted') echo "Igra je uklonjena iz vaše kolekcije.";
            if ($_GET['status'] == 'already_added') echo "Ovu igru već imate u svojoj kolekciji.";
            ?>
        </div>
    <?php endif; ?>

    <?php if ($greska): ?>
        <div class="msg error"><?php echo $greska; ?></div>
    <?php elseif ($igra): ?>

        <div class="details-container">
            <img src="<?php echo !empty($igra['background_image']) ? $igra['background_image'] : 'https://via.placeholder.com/1000x400?text=Nema+slike'; ?>" alt="Naslovnica" class="hero-banner">

            <div class="content-wrapper">
                <div class="main-info">
                    <h1 style="margin-top: 0; color: #bb86fc; font-size: 36px;"><?php echo htmlspecialchars($igra['name']); ?></h1>

                    <h3 style="color: #e0e0e0;">O igri:</h3>
                    <div style="line-height: 1.6; color: #ccc;">
                        <?php echo $igra['description'] ? $igra['description'] : 'Opis nije dostupan.'; ?>
                    </div>
                </div>

                <div class="side-info">
                    <?php if (isset($igra['metacritic'])): ?>
                        <?php
                        $meta = $igra['metacritic'];
                        $meta_class = $meta >= 75 ? 'meta-high' : ($meta >= 50 ? 'meta-mid' : 'meta-low');
                        ?>
                        <div class="meta-score <?php echo $meta_class; ?>">Metacritic: <?php echo $meta; ?></div>
                    <?php endif; ?>

                    <p><strong>Datum izlaska:</strong> <br>
                        <?php echo !empty($igra['released']) ? date('d.m.Y.', strtotime($igra['released'])) : 'Nepoznato'; ?>
                    </p>

                    <p><strong>Platforme:</strong> <br>
                        <?php
                        if (!empty($igra['platforms'])) {
                            foreach ($igra['platforms'] as $p) {
                                echo '<span class="tag">' . htmlspecialchars($p['platform']['name']) . '</span>';
                            }
                        } else {
                            echo 'Nepoznato';
                        }
                        ?>
                    </p>

                    <p><strong>Žanrovi:</strong> <br>
                        <?php
                        if (!empty($igra['genres'])) {
                            foreach ($igra['genres'] as $g) {
                                echo '<span class="tag" style="background: #03dac6;">' . htmlspecialchars($g['name']) . '</span>';
                            }
                        } else {
                            echo 'Nepoznato';
                        }
                        ?>
                    </p>

                    <hr style="border-top: 1px solid #444; margin: 20px 0;">

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($u_kolekciji): ?>
                            <form action="delete_game.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="game_id" value="<?php echo $igra['id']; ?>">
                                <input type="hidden" name="source" value="details">
                                <input type="hidden" name="query" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>">
                                <button type="submit" class="btn-danger" style="font-size: 16px; padding: 15px; width: 100%; background-color: #cf6679; color: #121212; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">❌ Ukloni iz kolekcije</button>
                            </form>
                        <?php else: ?>
                            <form action="add_game.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="rawg_id" value="<?php echo $igra['id']; ?>">
                                <input type="hidden" name="title" value="<?php echo htmlspecialchars($igra['name']); ?>">
                                <input type="hidden" name="cover_image" value="<?php echo !empty($igra['background_image']) ? $igra['background_image'] : ''; ?>">
                                <input type="hidden" name="release_date" value="<?php echo !empty($igra['released']) ? $igra['released'] : null; ?>">
                                <input type="hidden" name="source" value="details">
                                <button type="submit" class="btn-primary" style="font-size: 16px; padding: 15px; width: 100%;">➕ Dodaj u kolekciju</button>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <button type="button" class="btn-primary" onclick="window.location.href='login.php'" style="background-color: #555; width: 100%; padding: 15px;">Prijavi se za dodavanje</button>
                    <?php endif; ?>

                </div>
            </div>
        </div>

    <?php endif; ?>

</body>

</html>