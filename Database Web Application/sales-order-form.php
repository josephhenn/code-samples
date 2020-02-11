<?php
session_start();
include("config.php");
$_SESSION["add_sell"] = "sell";
$message = "";
$id = "";
if($_SERVER["REQUEST_METHOD"]=="GET"){
  $VIN = $_GET["VIN"];
  $_SESSION["VIN_SELL"] = $VIN;
}
elseif($_SERVER["REQUEST_METHOD"]=="POST" AND isset($_POST["customerID"])){
  $VIN = $_SESSION["VIN_SELL"];
  $id = $_POST["customerID"];
  $sql3 = "SELECT customerID FROM customer WHERE customerID='$id'";
  $result3 = mysqli_query($conn,$sql3);
  if($result3->num_rows == 0){
    $message = "Customer not found!";
  }else{
    $message = "Customer found!";
  }
}
$sql1 = "SELECT model_year, model_name, manufacturer, purchase_price FROM vehicle INNER JOIN purchasetransaction
        ON vehicle.VIN=purchasetransaction.VIN WHERE vehicle.VIN='$VIN'";
$result1 = mysqli_query($conn, $sql1);
$row1 = $result1->fetch_assoc();
$year = $row1["model_year"];
$model = $row1["model_name"];
$manufacturer = $row1["manufacturer"];
$price = $row1["purchase_price"];
$sql2 = "SELECT SUM(total_cost) FROM repair WHERE VIN='$VIN'";
$result2 = mysqli_query($conn, $sql2);
if($result2->num_rows > 0){
  $total_cost = $result2->fetch_assoc()["SUM(total_cost)"];
} else {$total_cost = 0;}
$salesprice = round($price * 1.25 + $total_cost * 1.1,2);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Sales Order Form</title>
  </head>
  <style>
    body{
      padding: 20px;
    }
    .topnav {
      background-color: #333;
      overflow: hidden;
      text-align: left;
      height: 50px;
      line-height: 50px;
      width: 60%;
    }
    .topnav span {
      padding: 10px;
      color: #f2f2f2;
      font-size: 20px;
    }
    .topnav a {
      float: right;
      color: #f2f2f2;
      text-align: center;
      padding: 0px 15px;
      text-decoration: none;
      font-size: 17px;
    }
    .topnav a:hover{
      background-color: #ddd;
      color: black;
    }
  </style>
  <body>
    <div class="topnav">
      <span> Welcome to Burdell's Ramblin' Wrecks! </span>
      <a href='/cs6400/index.php'>Home</a>
      <?php if(isset($_SESSION['login_user'])){
        echo "<a href='/cs6400/logout.php'>Logout</a>";
      }?>
    </div>
    <div>
      <p style="font-size:20px">Complete the Sales Order Form:</p>
    </div>
    <div style="width:60%">
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <fieldset>
          <legend>Seller Information:</legend>
          Look Up Existing Customer ID:<br>
          <input type="text" name="customerID" required <?php if($message != ""){echo "value=$id readonly";}?>>
          <input type="submit" name="look-up" value="Look-Up" <?php if($message != ""){echo "disabled";}?>>
          <br><?php echo $message;?>
        </fieldset><br>
      </form>
      <?php if($message=="Customer not found!"){
        echo "<a href='/cs6400/add-customer.php'><button>Add Seller Information</button></a><br><br>";
      }?>
      <form action="/cs6400/process-sale.php" method="post">
        <fieldset>
          <legend>Your Selected Vehicle:</legend>
          VIN:<br>
          <input type="text" name="VIN" value=<?php echo "$VIN";?> readonly><br><br>
          Model Year:<br>
          <input type="text" name="year" value=<?php echo "$year";?> readonly><br><br>
          Manufacturer:<br>
          <input type="text" name="manufacturer" value=<?php echo "$manufacturer";?> readonly><br><br>
          Model Name:<br>
          <input type="text" name="model" value=<?php echo "$model";?> readonly><br><br>
          Sales Price:<br>
          $<input type="number" name="price" value="<?php echo $salesprice;?>" step=".01" readonly>
        </fieldset>
        <fieldset>
          <legend>Confirm Sale:</legend>
          Enter Sales Date:<br>
          <input type="date" name="salesdate" required>
        </fieldset><br>
        <input type="hidden" name="customerID" value="<?php echo $id;?>">
        <input type="submit" value="Submit" <?php if($message!="Customer found!"){echo "disabled";}?>>
      </form>
    </div>
  </body>
</html>
