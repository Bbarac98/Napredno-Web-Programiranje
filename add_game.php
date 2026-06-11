<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    
    $rawg_id = $_POST['rawg_id'];
    $title = $_POST['title'];
    $cover_image = $_POST['cover_image'];
    $release_date = !empty($_POST['release_date']) ? $_POST['release_date'] : null;
    
    $source = isset($_POST['source']) ? $_POST['source'] : 'search';
    $query = isset($_POST['query']) ? $_POST['query'] : $title;

    try {
        // 1. Provjera i unos u globalnu tablicu igara (ako već ne postoji)
        $stmt = $pdo->prepare("SELECT id FROM games WHERE id = ?");
        $stmt->execute([$rawg_id]);
        $igra_postoji = $stmt->fetch();

        if (!$igra_postoji) {
            $insert_game = $pdo->prepare("INSERT INTO games (id, title, cover_image, release_date) VALUES (?, ?, ?, ?)");
            $insert_game->execute([$rawg_id, $title, $cover_image, $release_date]);
        }

        // 2. Unos u korisničku kolekciju
        $insert_user_game = $pdo->prepare("INSERT IGNORE INTO user_games (user_id, game_id, status) VALUES (?, ?, 'plan_to_play')");
        $insert_user_game->execute([$user_id, $rawg_id]);
        
        $status_poruke = ($insert_user_game->rowCount() > 0) ? "success" : "already_added";
        
        // 3. Dinamičko preusmjeravanje natrag na izvor
        if ($source === 'details') {
            header("Location: game_details.php?id=" . $rawg_id . "&status=" . $status_poruke . "&query=" . urlencode($query));
        } else {
            header("Location: search.php?query=" . urlencode($query) . "&status=" . $status_poruke);
        }
        exit();

    } catch (PDOException $e) {
        die("Došlo je do greške prilikom spremanja: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit();
}