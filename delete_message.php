<?php
session_start();
require_once 'config/database.php';

// Sigurnosna provjera: Samo administrator može brisati poruke
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message_id'])) {
    $message_id = $_POST['message_id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$message_id]);
        
        header("Location: index.php?msg=message_deleted");
        exit();
    } catch (PDOException $e) {
        die("Greška pri brisanju poruke: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit();
}