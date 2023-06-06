<?php
require_once "dbConnect.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
 
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$passwordRe = password_hash($_POST['passwordRe'], PASSWORD_DEFAULT);
$username_err = $email_err = $password_err = $passwordRe_err = "";
 
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // vérification pour l'username
    if(empty(trim($username))){
        $username_err = "L'identifiant est vide.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($username))){
        $username_err = "L'identifiant ne peut contenir que des lettres, chiffres et underscores.";
    } else{

        $sql = "SELECT id FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "Cet identifiant est déjà enregistré";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Erreur, veuillez réessayer plus tard.";
            }

            unset($stmt);
        }
    }

    // vérification pour l'email
    if(empty(trim($_POST["email"]))){
        $email_err = "L'email est vide.";
    } else{

        $sql = "SELECT id FROM users WHERE email = :email";
        
        if($stmt = $pdo->prepare($sql)){
            
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            
            $param_email = trim($_POST["email"]);
            
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $email_err = "Cet email est déjà enregistré";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Erreur, veuillez réessayer plus tard.";
            }

            unset($stmt);
        }
    }
    
    // vérification du mot de passe
    if(empty(trim($_POST["password"]))){
        $password_err = "Le mot de passe est vide";     
    } elseif(strlen(trim($_POST["password"])) < 12){
        $password_err = "Le mot de passe doit contenir 12 caractères au minimum";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // vérification du répéter mot de passe
    if(empty(trim($_POST["passwordRe"]))){
        $passwordRe_err = "Répétez le mot de passe";     
    } else{
        $passwordRe = trim($_POST["passwordRe"]);
        if(empty($password_err) && ($password != $passwordRe)){
            $passwordRe_err = "Les mots de passe ne correpondent pas";
        }
    }
    
    //si on a pas rencontré d'erreur
    if(empty($username_err) && empty($email_err) && empty($password_err) && empty($asswordRe_err)){
        
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
         
        if($stmt = $pdo->prepare($sql)){
            
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":password", password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
            
            if($stmt->execute()){
                // header("location: login.php");
                echo "Bien joué";
            } else{
                echo "Erreur, veuillez réessayer plus tard.";
            }
            unset($stmt);
        }
    }
    unset($pdo);
}
?>