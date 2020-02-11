<?php
session_start();
include("config.php");
if($_SERVER["REQUEST_METHOD"]=="POST"){
  $vendor = $_POST["vendor"];
  $VIN = $_POST["VIN"];
  $start_date = $_POST["start_date"];
  $end_date = $_POST["end_date"];
  $cost = $_POST["total_cost"];
  $description = $_POST["description"];
  $recall = $_POST["recall"];
  $r_description = $_POST["recall_description"];
  $manufacturer = $_POST["manufacturer"];
  $sql1 = "SELECT start_date, end_date FROM repair WHERE VIN=$VIN";
  $result1 = mysqli_query($conn, $sql1);
  if($result1->num_rows > 0){
    while($row1=$result1->fetch_assoc()){
      $t1 = strtotime($start_date); $t2=strtotime($end_date);
      $t3 = strtotime($row1["start_date"]); $t4=strtotime($row1["end_date"]);
      if($t1 < $t4 and $t2 > $t3){
        $err = "Overlapping Repairs!";
        header("Location: /cs6400/add-repair.php?err=$err");
        exit;
      }
    }
  }
  elseif($start_date > $end_date){
    $err = "Start Date must be before End Date!";
    header("Location: /cs6400/add-repair.php?err=$err");
    exit;
  }
  else{
    $sql2 = "INSERT INTO repair VALUES ('$VIN','$vendor','$recall','$start_date','$end_date','$cost','pending','$description')";
    $result2 = mysqli_query($conn,$sql2);
    if($r_description != "" or $manufacturer != ""){
      $sql3 = "INSERT INTO recall VALUES ('$recall','$r_description','$manufacturer')";
      $result3 = mysqli_query($conn,$sql3);
    }
    header("Location: /cs6400/vehicle-info.php?VIN=$VIN");
    exit;
  }
}
?>
