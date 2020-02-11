<?php
session_start();
include("config.php");
if($_SERVER["REQUEST_METHOD"] == "POST"){
  $VIN = $_POST["vin"];
  $type = $_POST["type"];
  $year = $_POST["year"];
  $manufacturer = $_POST["manufacturer"];
  $model = $_POST["model"];
  $mileage = $_POST["mileage"];
  $condition = $_POST["condition"];
  $value = $_POST["value"];
  $color = $_POST["color"];
  $description = $_POST["description"];
  $date = $_POST["date"];
  $user = $_SESSION["login_user"];
  $id = $_POST["id"];

  $sql1 = "INSERT INTO vehicle VALUES('$VIN', '$mileage', '$model', '$year', '$condition', '$description', '$type', '$manufacturer')";
  $result1 = mysqli_query($conn, $sql1);
  $sql2 = "INSERT INTO purchasetransaction VALUES('$VIN', '$id', '$user', '$date', '$value')";
  $result2 = mysqli_query($conn, $sql2);
  $sql3 = "INSERT INTO vehiclecolor VALUES('$VIN','$color')";
  $result3 = mysqli_query($conn, $sql3);
  header("Location: vehicle-info.php/?VIN=$VIN");
  exit;
}else{
  echo "Error, unauthorized access!";
}
?>
