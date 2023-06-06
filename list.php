<?php
/**
 * Retourne la liste des cars
 */
require 'dbConnect.php';
    
$cars = [];
$sql = "SELECT id, model, price FROM cars";

if($result = mysqli_query($con,$sql))
{
  $cr = 0;
  while($row = mysqli_fetch_assoc($result))
  {
    $cars[$cr]['id']    = $row['id'];
    $cars[$cr]['model'] = $row['model'];
    $cars[$cr]['price'] = $row['price'];
    $cr++;
  }
  
  $data = array("data" => $cars);
  echo json_encode($data);
}
else
{
  http_response_code(404);
}