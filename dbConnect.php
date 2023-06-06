<?php

$dsn = 'mysql:host=localhost;dbname=pleasanttime';
$usernameDB = 'root';
$passwordDB = '';

try {
    $db = new PDO($dsn, $usernameDB, $passwordDB);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array('message' => 'Erreur de connexion à la base de données : ' . $e->getMessage()));
    exit;
}

?>