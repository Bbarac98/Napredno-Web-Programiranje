<?php
session_start();
require_once 'config/database.php';

// Provjera autorizacije i metode zahtjeva
if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$game_id = $_POST['game_id'];

// Određivanje izvora zahtjeva radi pravilnog preusmjeravanja
$source = isset($_POST['source']) ? $_POST['source'] : 'kolekcija';
$query = isset($_POST['query']) ? $_POST['query'] : '';

try {
    // Uklanjanje igre iz korisničke kolekcije
    $stmt = $pdo->prepare("DELETE FROM user_games WHERE user_id = ? AND game_id = ?");
    $stmt->execute([$user_id, $game_id]);
    
    // Dinamičko preusmjeravanje
    if ($source === 'search') {
        header("Location: search.php?query=" . urlencode($query) . "&status=deleted");
    } elseif ($source === 'details') {
        header("Location: game_details.php?id=" . $game_id . "&status=deleted&query=" . urlencode($query));
    } else {
        header("Location: kolekcija.php?msg=deleted");
    }
    exit();

} catch (PDOException $e) {
    die("Greška pri uklanjanju igre: " . $e->getMessage());
}