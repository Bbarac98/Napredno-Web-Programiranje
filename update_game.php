<?php
session_start();
require_once 'config/database.php';

// Sigurnosna provjera autorizacije
if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$game_id = $_POST['game_id'];
$novi_status = $_POST['status'];

try {
    // Obrada ocjene: ako je odabrano "Bez ocjene", u bazu se upisuje NULL
    $nova_ocjena = !empty($_POST['rating']) ? $_POST['rating'] : null;

    $stmt = $pdo->prepare("UPDATE user_games SET status = ?, rating = ? WHERE user_id = ? AND game_id = ?");
    $stmt->execute([$novi_status, $nova_ocjena, $user_id, $game_id]);

    header("Location: kolekcija.php?msg=updated");
    exit();
} catch (PDOException $e) {
    die("Greška pri ažuriranju: " . $e->getMessage());
}