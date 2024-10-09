// Fetch die Zähldaten aus unload.php
fetch('https://etl.mmp.li/sunnigsluzern/etl/unload.php')
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        return response.json(); // Konvertiert die Antwort in JSON
    })
    .then(data => {
        console.log(data); // Zeigt die Zähldaten in der Konsole an
        /*const dataDiv = document.getElementById('city-data'); // Holt den Div, wo die Daten eingefügt werden

        for (const city in data) {
            // Erstelle ein neues Element für jede Stadt und zeige die Zähldaten an
            const cityData = document.createElement('div');
            cityData.classList.add('city-data'); // Füge eine Klasse hinzu für Styling
            cityData.innerHTML = `<h2>${city}</h2>`;

            // Iteriere durch die Zähldaten für jede Stadt
            data[city].forEach(entry => {
                cityData.innerHTML += `<p>Ort: ${entry.location} | Zähler: ${entry.counter} | Zeit: ${entry.timestamp}</p>`;
            });

            dataDiv.appendChild(cityData); // Füge die Stadt-Daten zum Haupt-Div hinzu
        }
            */
    })
    .catch(error => {
        console.error('Fetch error:', error); // Zeigt Fehler an, falls der Fetch fehlschlägt
    });
