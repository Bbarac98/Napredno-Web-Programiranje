<?php
session_start();

// Procesiranje AJAX zahtjeva za Live Search (Autocomplete) funkcionalnost
if (isset($_GET['q']) && strlen(trim($_GET['q'])) >= 3) {
    $search = urlencode(trim($_GET['q']));
    
    // Učitavanje API ključa
    $env = parse_ini_file(__DIR__ . '/.env');
    $api_key = $env['RAWG_API_KEY'];

    // Sastavljanje API upita (ograničeno na 5 rezultata radi brzine)
    $url = "https://api.rawg.io/api/games?key=" . $api_key . "&search=" . $search . "&page_size=5";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "VideoGameTracker/1.0");
    
    $odgovor = curl_exec($ch);
    curl_close($ch);

    header('Content-Type: application/json');
    echo $odgovor;
} else {
    header('Content-Type: application/json');
    echo json_encode([]);
}