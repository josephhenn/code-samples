<?php
include("config.php");
session_start();
$_SESSION["add_sell"] = "add";
$sql1 = "SELECT type FROM vehicletype";
$result1 = mysqli_query($conn, $sql1);
$sql2 = "SELECT name FROM manufacturer";
$result2 = mysqli_query($conn, $sql2);

$message = "";
$id = "";
if($_SERVER["REQUEST_METHOD"]=="POST"){
  $id = $_POST["customerID"];
  $sql3 = "SELECT customerID FROM customer WHERE customerID='$id'";
  $result3 = mysqli_query($conn,$sql3);
  if($result3->num_rows == 0){
    $message = "Customer not found!";
  }else{
    $message = "Customer found!";
  }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Add Vehicle</title>
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
      <p style="font-size:20px">Add a vehicle to the database:</p>
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
      <form method="post" action="<?php echo htmlspecialchars("process-add.php");?>">
        <input type="hidden" name="id" value=<?php echo $id;?>>
        <fieldset>
          <legend>Vehicle Information:</legend>
          Purchase Date: <br><input type="date" name="date" value=<?php echo date("Y-m-j");?> readonly>
          <br><br>
          VIN: <br><input type="text" name="vin" required>
          <br><br>
          Vehicle Type: <br>
          <select name="type" required>
            <?php while($row = $result1->fetch_assoc()){
              echo '<option value="',$row["type"],'">',$row["type"],'</option>';
            }?>
          </select>
          <br><br>
          Model Year: <br><input type="text" name="year" required>
          <br><br>
          Manufacturer: <br>
          <select name="manufacturer" required>
            <?php while($row = $result2->fetch_assoc()){
              echo '<option value="',$row["name"],'">',$row["name"],'</option>';
            }?>
          </select>
          <br><br>
          Model Name: <br><input type="text" name="model" required>
          <br><br>
          Mileage: <br><input type="number" name="mileage" required>
          <br><br>
          Condition: <br><input type="text" name="condition" required>
          <br><br>
          Blue Book Value: <br>$<input type="number" name="value" min="0" step="0.01" required>
          <br><br>
          Color(s): <br><input type="text" name="color" required>
          <br><br>
          Description: <br><textarea name="description" rows="5" cols="20"></textarea>
          <br><br>
          <input type="submit" name="submit" value="Submit" <?php if($message!="Customer found!"){echo "disabled";}?>>
        </fieldset>
      </form>
    </div>
  </body>
</html>
