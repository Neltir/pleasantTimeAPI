<?php
use Firebase\JWT\JWT;
require_once "dbConnect.php";
// require_once "./vendor/firebase/php-jwt/src/JWT.php";
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//     http_response_code(200);
//     exit();
// }

$postdata = file_get_contents("php://input");
if(isset($postdata) && !empty($postdata))
{
    $data = json_decode($postdata);
    // eviter les injections sql
    $username = htmlspecialchars(trim($data->username));
    $password = htmlspecialchars(trim($data->password));



    if(empty(trim($username))){
        http_response_code(400);
        echo json_encode(array('message' => 'L identifiant est vide.'));
        exit;

    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($username))){
        http_response_code(400);
        echo json_encode(array('message' => 'Lidentifiant ne peut contenir que des lettres, chiffres et underscores.'));
        exit;

    } else{
        $query = 'SELECT * FROM users WHERE username = :username';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);

        try{
            $stmt->execute();
            if($stmt->rowCount() == 1){
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if($user){
                    // $tokenID = base64_encode(random_bytes(32));
                    // echo json_encode(array('mdp' => $user));
                    if(password_verify($password, $user['password'])){
                    // if($password == $user['password']){
                        // mot de passe correct
                        $timestamp = time();
                        $expire = $timestamp + 3600;
                        $key = "PleasantTime";
                        $payload = array(
                            // issued at
                            'iat' => $timestamp,
                            // expire
                            'exp' => $expire,
                            'data' => array(
                                'userID' => $user['userID'],
                                'username' => $user['username'],
                                'isAdmin' => $user['isAdmin']
                            )
                        );
                        // on ecncode le token , hs256 est algo d'encode
                        $jwt = array('payload' => $payload, $key);
                        // on envoit le token
                        echo json_encode(array('token' => $jwt));
                    } else{
                        http_response_code(401);
                        echo json_encode(array('message' => 'Mot de passe incorrect.'));
                    }
                } else {
                    http_response_code(401);
                    echo json_encode(array('message' => 'Identifiants invalides.'));
                }
            } else {
                http_response_code(401);
                echo json_encode(array('message' => 'Utilisateur non enregistré.'));
            }        
        } 
        catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(array('message' => 'Erreur, veuillez réessayer plus tard.'));
            exit;
        }
    }
}