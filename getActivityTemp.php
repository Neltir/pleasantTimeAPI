<?php
require_once "dbConnect.php";

header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$query = "SELECT * FROM activitiestemp";
$allActivityQuery = $db->query($query);
if($allActivityQuery->execute()){
    $allActivity = $allActivityQuery->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    echo json_encode(
        array(
            'payload' => $allActivity,
            'message' => htmlspecialchars("Récupération réussie."),
            'registerStatus' => true
        )
    );
} else{
    http_response_code(400);
    echo json_encode(
        array(
            'message' => "Erreur, veuillez réessayer plus tard.",
            'registerStatus' => false
        )
    );
}