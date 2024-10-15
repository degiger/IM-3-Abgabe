<?php

require_once 'config.php'; // Verweist auf die config.php Datei, die die PDO-Datenbankverbindung enthält

header('Content-Type: application/json');

try {
    // PDO-Instanz erstellen und Verbindung herstellen
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Überprüfen, ob der 'ort' Parameter übergeben wurde
    if (isset($_GET['ort'])) {
        $ort = $_GET['ort'];

        // SQL-Abfrage vorbereiten, um die Daten nach Stunde und Wochentag zu gruppieren und den Median zu berechnen
        $stmt = $pdo->prepare("
            SELECT hour, weekday, AVG(counter) AS median_counter
            FROM (
                SELECT 
                    HOUR(timestamp) AS hour, 
                    DAYOFWEEK(timestamp) AS weekday, 
                    counter,
                    @row_number := IF(@hour = HOUR(timestamp) AND @weekday = DAYOFWEEK(timestamp), @row_number + 1, 1) AS row_number,
                    @hour := HOUR(timestamp),
                    @weekday := DAYOFWEEK(timestamp),
                    @total_rows := IF(@hour = HOUR(timestamp) AND @weekday = DAYOFWEEK(timestamp), @total_rows, COUNT(*) OVER (PARTITION BY HOUR(timestamp), DAYOFWEEK(timestamp))) AS total_rows
                FROM SunnigsLuzern 
                WHERE ort = :ort
                ORDER BY weekday, hour, counter
            ) AS subquery
            WHERE row_number IN (FLOOR((total_rows + 1) / 2), CEIL((total_rows + 1) / 2))
            GROUP BY hour, weekday
            ORDER BY weekday, hour
        ");
        
        $stmt->bindParam(':ort', $ort, PDO::PARAM_STR);
        $stmt->execute(); // Führt die Abfrage mit dem angegebenen Ort als Parameter aus
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC); // Speichert die Ergebnisse in $results als assoziatives Array

        // Gibt die Wetterdaten als JSON aus
        echo json_encode($results);
    } else {
        // Falls kein 'ort' Parameter übergeben wurde, eine Fehlermeldung ausgeben
        echo json_encode(['error' => 'Kein Ort angegeben']);
    }
    
} catch (PDOException $e) {
    // Fehlerbehandlung, falls es ein Problem mit der Datenbankverbindung gibt
    echo json_encode(['error' => $e->getMessage()]);
}
?>
