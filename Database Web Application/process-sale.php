<?php
session_start();
include("config.php");
if($_SERVER["REQUEST_METHOD"]=="POST"){
  $VIN = $_POST["VIN"];
  $customer = $_POST["customerID"];
  $salesperson = $_SESSION["login_user"];
  $date = $_POST["salesdate"];
  $price = $_POST["price"];
}
$sql1 = "INSERT INTO salestransaction VALUES ('$VIN','$customer','$salesperson','$date','$price')";
$result = mysqli_query($conn, $sql1);
header("Location: /cs6400/index.php");
exit;
?>
