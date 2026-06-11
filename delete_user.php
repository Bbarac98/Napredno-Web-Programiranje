<?php
session_start();
require_once 'config/database.php';

// Sigurnosna provjera: Samo administrator može brisati korisnike putem POST metode
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.php");
    exit();
}

$user_id_za_brisanje = $_POST['user_id'];

try {
    // 1. Brisanje svih povezanih zapisa (igara) iz korisničke kolekcije
    $stmt1 = $pdo->prepare("DELETE FROM user_games WHERE user_id = ?");
    $stmt1->execute([$user_id_za_brisanje]);

    // 2. Brisanje samog korisnika iz sustava
    $stmt2 = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt2->execute([$user_id_za_brisanje]);

    header("Location: index.php?msg=user_deleted");
    exit();
} catch (PDOException $e) {
    die("Greška pri brisanju korisnika: " . $e->getMessage());
}
