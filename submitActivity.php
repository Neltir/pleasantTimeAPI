<?php
require_once "dbConnect.php";

$postdata = file_get_contents("php://input");

if(isset($postdata) && !empty($postdata)){

    $request = json_decode($postdata);

    $title = htmlspecialchars(trim($request->name));
    $description = htmlspecialchars(trim($request->description));
}