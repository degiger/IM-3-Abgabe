<?php
error_reporting(E_ALL);  // Report all types of errors, including warnings and notices
ini_set('display_errors', 1);  // Display errors on the screen

// Include the config file for DB connection
require_once 'config.php';

// Fetch visitor frequencies function
function fetchVisitorFrequencesLucerne() {
    $url = "https://portal.alfons.io/app/devicecounter/api/sensors?api_key=3ad08d9e67919877e4c9f364974ce07e36cbdc9e";

    // Initialize cURL session
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute cURL session and fetch response
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
    }

    // Close cURL session
    curl_close($ch);

    // Decode JSON response
    $decoded = json_decode($response, true);

    // Check if decoding was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo 'JSON decode error: ' . json_last_error_msg();
    }

    return $decoded;  // Return the decoded JSON as an associative array
}

// Fetch data from the API
$data = fetchVisitorFrequencesLucerne();

// Debug the full API response to verify structure
if ($data === null || !isset($data['data'])) {
    die('Invalid API response or missing "data" key.');
}

var_dump($data['data']); // Debugging the 'data' key to verify its structure

// Establish database connection
try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Prepare the SQL statement
$sql = "INSERT INTO SunnigsLuzern (ort, counter, timestamp) VALUES (:ort, :counter, :timestamp)";
$stmt = $pdo->prepare($sql);

// Insert each entry into the database
foreach ($data['data'] as $entry) {
    if (isset($entry['name']) && isset($entry['counter']) && isset($entry['ISO_time'])) {
        try {
            $stmt->execute([
                ':ort' => $entry['name'],          // 'name' in API becomes 'ort' in DB
                ':counter' => $entry['counter'],   // 'counter' remains the same
                ':timestamp' => $entry['ISO_time'] // 'ISO_time' becomes 'timestamp' in DB
            ]);
        } catch (PDOException $e) {
            echo "Error inserting data: " . $e->getMessage() . "\n";
        }
    } else {
        echo "Skipping entry due to missing data.\n";
    }
}

echo "Data successfully inserted into the database.";

?>
