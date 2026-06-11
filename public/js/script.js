/* public/js/script.js */

// Funkcija za otvaranje i zatvaranje dugih admin poruka na početnoj stranici
function toggleMessage(btn) {
    let container = btn.parentElement;
    let preview = container.querySelector('.preview-text');
    let full = container.querySelector('.full-text');

    if (full.style.display === 'none') {
        full.style.display = 'block';
        preview.style.display = 'none';
        btn.innerHTML = 'Prikaži manje ⬆';
    } else {
        full.style.display = 'none';
        preview.style.display = 'block';
        btn.innerHTML = 'Prikaži više ⬇';
    }
}

// Logika za Live Search (Autocomplete) na stranici pretrage
document.addEventListener("DOMContentLoaded", function() {
    const searchBox = document.getElementById('search-box');
    const suggestionsBox = document.getElementById('suggestions-box');

    // Provjera postoje li elementi kako bi izbjegli greške na stranicama bez tražilice
    if (searchBox && suggestionsBox) {
        searchBox.addEventListener('input', function() {
            let query = this.value.trim();

            if (query.length >= 3) {
                fetch('live_search.php?q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        suggestionsBox.innerHTML = ''; 

                        if (data.results && data.results.length > 0) {
                            data.results.forEach(game => {
                                let div = document.createElement('div');
                                div.className = 'suggestion-item';
                                
                                let imgUrl = game.background_image ? game.background_image : 'https://via.placeholder.com/45x45?text=No+Img';
                                
                                div.innerHTML = `
                                    <img src="${imgUrl}" alt="">
                                    <span>${game.name}</span>
                                `;
                                
                                div.addEventListener('click', function() {
                                    window.location.href = 'game_details.php?id=' + game.id + '&query=' + encodeURIComponent(query);
                                });
                                
                                suggestionsBox.appendChild(div);
                            });
                            suggestionsBox.style.display = 'block'; 
                        } else {
                            suggestionsBox.style.display = 'none'; 
                        }
                    })
                    .catch(err => console.error('Greška pri live search-u:', err));
            } else {
                suggestionsBox.style.display = 'none'; 
            }
        });

        // Zatvaranje izbornika s prijedlozima klikom izvan njega
        document.addEventListener('click', function(e) {
            if (e.target !== searchBox && e.target !== suggestionsBox) {
                suggestionsBox.style.display = 'none';
            }
        });
    }
});