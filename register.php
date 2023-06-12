<?php
require_once "dbConnect.php";

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
    $email = htmlspecialchars(trim($data->email));
    $password = htmlspecialchars(trim($data->password));
    $passwordRe = htmlspecialchars(trim($data->passwordRe));
    $error = 0;

    if(empty($username)){
        http_response_code(400);
        echo json_encode(array('message' => 'L identifiant est vide.'));
        $error += 1;

    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($username))){
        http_response_code(400);
        echo json_encode(array('message' => 'Lidentifiant ne peut contenir que des lettres, chiffres et underscores.'));
        $error += 1;

    } elseif( $password != $passwordRe){
        http_response_code(400);
        echo json_encode(array('message' => 'Les deux mots de passe ne correspondent pas.'));
        $error += 1;
    } elseif( strlen($password) < 12){
        http_response_code(400);
        echo json_encode(array('message' => 'Le mot de passe est trop court.'));
        $error += 1;
    } else {
        // si email déjà en base
        $queryMail = "SELECT * FROM users WHERE email = :email";
        if($stmtMail = $db->prepare($queryMail)){
            
            $stmtMail->bindParam(":email", $email, PDO::PARAM_STR);
            
            if($stmtMail->execute()){
                if($stmtMail->rowCount() > 0){
                    http_response_code(400);
                    echo json_encode(array('message' => "Cet email est déjà enregistré."));
                    $error += 1;
                }
            } else{
                http_response_code(400);
                echo json_encode(array('message' => "Erreur, veuillez essayer plus tard."));
                $error += 1;
            }
        }
        // si user déjà en base
        $queryID = "SELECT * FROM users WHERE username = :username";
        if($stmtID = $db->prepare($queryID)){
            $stmtID->bindParam(":username", $email, PDO::PARAM_STR);
            if($stmtID->execute()){
                if($stmtID->rowCount() > 0){
                    http_response_code(400);
                    echo json_encode(array('message' => "Cet utilisateur est déjà enregistré."));
                    $error += 1;
                }
            } else{
                http_response_code(400);
                echo json_encode(array('message' => "Erreur, veuillez essayer plus tard."));
                $error += 1;
            }
        }
        //si on n'a pas rencontré d'erreur
        if($error === 0){
            $queryInsert = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
            if($stmtInsert = $db->prepare($queryInsert)){
                $stmtInsert->bindParam(":username",$username, PDO::PARAM_STR);
                $stmtInsert->bindParam(":email",$email, PDO::PARAM_STR);
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmtInsert->bindParam(":password", $hashedPassword, PDO::PARAM_STR);

                if($stmtInsert->execute()){
                    http_response_code(200);
                    echo json_encode(array('message' => htmlspecialchars("Inscription réussie.")));
                    echo json_encode(array('registerStatus' => true));
                } else{
                    http_response_code(400);
                    echo json_encode(array('message' => "Erreur, veuillez réessayer plus tard."));
                    echo json_encode(array('registerStatus' => false));
                }
            }
        }
    }
}
?>