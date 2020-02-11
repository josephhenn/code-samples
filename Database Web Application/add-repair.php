<?php
session_start();
include("config.php");
$message = "";
$vendor= "";
if(isset($_GET["VIN"])){
  $VIN = $_GET["VIN"];
  $_SESSION["repair_VIN"] = $VIN;
}
elseif($_SERVER["REQUEST_METHOD"]=="POST" AND isset($_POST["vendor"])){
  $VIN = $_SESSION["repair_VIN"];
  $vendor = $_POST["vendor"];
  $sql1 = "SELECT vendor_name FROM vendor WHERE vendor_name='$vendor'";
  $result1 = mysqli_query($conn,$sql1);
  if($result1->num_rows == 0){
    $message = "Vendor not found!";
  }else{
    $message = "Vendor found!";
  }
}
elseif(isset($_GET["err"])){
  $err = $_GET["err"];
  $VIN = $_SESSION["repair_VIN"];
}
else{
  $VIN = $_SESSION["repair_VIN"];
}
$sql2 = "SELECT name FROM manufacturer";
$result2 = mysqli_query($conn, $sql2);

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Add Repair</title>
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
    <?php if(isset($err)){
      echo "<div>
            <p style='font-size:20px'>Error: $err</p>
            </div>";
    }?>
    <div>
      <p style="font-size:20px">Add a Repair:</p>
    </div>
    <div style="width:60%">
      <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        Vendor Name:<br>
        <input type="text" name="vendor" required>
        <input type="submit" value="Look-Up" <?php if($message != ""){echo "disabled";}?>><br>
        <?php echo $message;?><br>
      </form>
      <?php if($message=="Vendor not found!"){
        echo "<a href='/cs6400/add-vendor.php'><button>Add Vendor Information</button></a><br><br>";
      }?>
      <form method="post" action="<?php echo htmlspecialchars('process-repair.php');?>">
        <input type="hidden" name="vendor" value="<?php echo $vendor;?>">
        VIN:<br>
        <input type="text" name="VIN" value="<?php echo $VIN;?>" readonly><br><br>
        Start Date:<br>
        <input type="date" name="start_date" required><br><br>
        End Date:<br>
        <input type="date" name="end_date" required><br><br>
        Total Cost:<br>
        $<input type="number" name="total_cost" step=".01" min="0" required><br><br>
        Description:<br>
        <textarea name="description" rows="5" cols="20"></textarea><br><br>
        Recall Number:<br>
        <input type="text" name="recall"><br><br>
        Recall Description:<br>
        <textarea name="recall_description" rows="5" cols="20"></textarea><br><br>
        Recall Associated Manufacturer:<br>
        <select name="manufacturer">
          <option value=""></option>
          <?php while($row = $result2->fetch_assoc()){
            echo '<option value="',$row["name"],'">',$row["name"],'</option>';
          }?>
        </select><br><br>
        <input type="submit" value="Submit" <?php if($message!="Vendor found!"){echo "disabled";}?>>
      </form>
    </div>
  </body>
</html>
