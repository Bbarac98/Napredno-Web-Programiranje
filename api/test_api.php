<?php
// api/test_api.php

// 1. ZAMIJENI OVO SA SVOJIM PRAVIM API KLJUČEM!
$api_key = "a9a795c110934941ab37fb3d7a3bdc70"; 

// 2. Definiramo koju igru tražimo i slažemo URL za RAWG
$search_query = urlencode("world of warcraft"); // urlencode pretvara razmake u %20 kako bi URL bio ispravan
$url = "https://api.rawg.io/api/games?key=" . $api_key . "&search=" . $search_query;

// 3. Inicijaliziramo cURL (alat za slanje HTTP zahtjeva iz PHP-a)
$ch = curl_init();

// 4. Postavljamo cURL opcije
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Želimo da nam vrati podatke, a ne da ih samo direktno ispiše
curl_setopt($ch, CURLOPT_USERAGENT, "VideoGameTracker/1.0"); // RAWG traži da se aplikacija 'predstavi'

// 5. Izvršavamo zahtjev i spremamo odgovor
$response = curl_exec($ch);

// 6. Provjera ima li grešaka na mreži
if(curl_errno($ch)){
    echo '<h3 style="color: red;">cURL greška: ' . curl_error($ch) . '</h3>';
} else {
    // RAWG vraća podatke u JSON formatu. Mi ga pretvaramo u PHP asocijativni niz kako bi nam bio čitljiv
    $data = json_decode($response, true);

    echo "<h2>Uspješno spajanje na RAWG API!</h2>";
    echo "<p>Evo što nam je API vratio za prvi rezultat:</p>";
    
    // Ispisujemo sirove podatke prve pronađene igre (<pre> tag služi da tekst bude uredno formatiran)
    echo "<pre style='background: #1e1e1e; color: #00ff00; padding: 20px; border-radius: 5px; overflow-x: auto;'>";
    if(isset($data['results'][0])) {
        print_r($data['results'][0]);
    } else {
        echo "Nema rezultata ili je ključ neispravan.";
    }
    echo "</pre>";
}

// 7. Zatvaramo cURL vezu kako ne bismo trošili memoriju
curl_close($ch);
?>