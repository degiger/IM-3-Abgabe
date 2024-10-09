<?php

require_once 'config.php'; // Verweist auf die config.php Datei, die die PDO-Datenbankverbindung enthält

header('Content-Type: application/json');

try {
    // PDO-Instanz erstellen und Verbindung herstellen
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Array der Städte, für die die Wetterdaten abgerufen werden sollen
    $results = [];

            // Bereitet die SQL-Abfrage vor
            $stmt = $pdo->prepare("SELECT * FROM SunnigsLuzern");
            $stmt->execute(); // Führt die Abfrage mit der aktuellen Stadt als Parameter aus
            $results = $stmt->fetchAll(); // Speichert die Ergebnisse in $results

    // Gibt die Wetterdaten als JSON aus
    echo json_encode($results);
    
} catch (PDOException $e) {
    // Fehlerbehandlung, falls es ein Problem mit der Datenbankverbindung gibt
    echo json_encode(['error' => $e->getMessage()]);
}
?>
