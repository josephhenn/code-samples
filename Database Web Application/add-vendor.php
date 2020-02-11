<?php
session_start();
include("config.php");
if($_SERVER["REQUEST_METHOD"]=="POST"){
  $name = $_POST["name"];
  $street = $_POST["street"];
  $city = $_POST["city"];
  $state = $_POST["state"];
  $zip = $_POST["zip"];
  $phone = str_replace("-","",$_POST["phone"]);
  $sql1 = "INSERT INTO vendor VALUES ('$name','$phone','$street','$city','$state','$zip')";
  $result1 = mysqli_query($conn,$sql1);
  header("Location: /cs6400/add-repair.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Add Vendor</title>
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
      <p style="font-size:20px">Add a vendor to the database:</p>
    </div>
    <form id="vendor" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
      Vendor Name:<br>
      <input type="text" name="name" required><br><br>
      Street Address:<br>
      <input type="text" name="street" required><br><br>
      City:<br>
      <input type="text" name="city" required><br><br>
      State:<br>
      <input type="text" name="state" required><br><br>
      Postal Code:<br>
      <input type="text" name="zip" required><br><br>
      Phone:<br>
      <input type="tel" name="phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required><br><br>
      <input type="submit" name="submit" value="Submit">
    </form>
  </body>
</html>
