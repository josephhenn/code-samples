<?php
include("config.php");
session_start();
$sql1 = "SELECT COUNT(*) FROM vehicle WHERE VIN NOT IN (SELECT VIN FROM salestransaction)
        AND VIN NOT IN (SELECT VIN FROM repair WHERE status IN ('pending', 'in progress'))";
$vcount = mysqli_query($conn,$sql1);
$vcount = mysqli_fetch_assoc($vcount)["COUNT(*)"];
$error = '';

if($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = mysqli_real_escape_string($conn,$_POST['username']);
  $password = mysqli_real_escape_string($conn,$_POST['psw']);

  $sql2 = "SELECT role FROM user WHERE username = '$username' and password = '$password'";
  $result = mysqli_query($conn,$sql2);
  $count = mysqli_num_rows($result);

  if($count == 1) {
    $_SESSION['login_user'] = $username;
    $_SESSION['role'] = mysqli_fetch_assoc($result)["role"];
  }
  else {
    $error = "Invalid Username or Password";
  }
}

if(isset($_SESSION['login_user']) && in_array($_SESSION['role'],['Manager','Clerk', 'All Roles'])){
  $sql3 = "SELECT COUNT(DISTINCT VIN) as inrepair FROM repair WHERE status IN
          ('pending', 'in progress') AND VIN NOT IN (SELECT VIN FROM salestransaction)";
  $result3 = mysqli_query($conn,$sql3);
  $repcount = $result3->fetch_assoc()["inrepair"];
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Burdell's Ramblin' Wrecks</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
    $(document).ready(function(){
      <?php if(empty($error)){
       echo '$(function() {$("#login").hide();});';
      }?>
      $("#showLogin").click(function() {
        $("#login").show();
      });
    });
    </script>
  </head>
  <style>
    body{
      padding: 20px;
    }
    .search {
      font-size: 17px;
    }
    .search input[type=text]{
      padding: 6px;
      margin-top: 8px;
      font-size: 17px;
      border: none;
    }
    .search button{
      padding: 6px;
      margin-top: 8px;
      margin-right: 16px;
      background: #ddd;
      font-size: 17px;
      border: none;
      cursor: pointer;
    }
    .search button:hover{
      background: #ccc;
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
      <a href='index.php'>Home</a>
      <?php if(isset($_SESSION['login_user'])){
        echo "<a href='logout.php'>Logout</a>";
      }?>
    </div>
    <div>
      <?php
      if(isset($_SESSION['login_user']) && in_array($_SESSION['role'], ['Clerk','Manager','All Roles'])){
        echo "<p>$repcount vehicles under repair</p>";
      }
      echo "<p>Search $vcount vehicles for sale:</p>"; ?>
    </div>
    <div class="search">
      <form id="search" action="search-results.php" method="get">
        <input type="text" placeholder="Search"  name="search" required>
        <button type="submit">Submit</button>
        <p style="padding-left:6px"><br>Search By:<br></p>
        <input type="radio" name="search-by" value="keyword" checked> Keyword<br>
        <input type="radio" name="search-by" value="vehicle-type"> Vehicle Type<br>
        <input type="radio" name="search-by" value="manufacturer"> Manufacturer<br>
        <input type="radio" name="search-by" value="model-year"> Model Year<br>
        <input type="radio" name="search-by" value="color"> Color<br>
        <?php if(isset($_SESSION['login_user'])){
          echo "<input type='radio' name='search-by' value='vin'> VIN";
        } ?>
      </form>
    </div>
    <?php if(!isset($_SESSION['login_user'])){
      echo "<div style='padding:50px 0px 10px 0px'>
              <button id='showLogin'>Login</button>
            </div>";
    }?>
    <div id="login">
      <form method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
        <label for="username"><b>Username:</b></label>
        <input type="text" name="username" required>
        <label for="psw"><b>Password:</b></label>
        <input type="password" name="psw" required>
        <button type="submit">Login</button>
        <?php echo (!empty($error))?$error:'';?>
      </form>

    </div>
    <?php
    if(isset($_SESSION['login_user']) && ($_SESSION['role'] == "Manager" or $_SESSION['role'] == "All Roles")){
      echo "<div style='padding:50px 0px 10px 0px'>
              <a href='add-vehicle.php'><button>Add Vehicle</button></a>
            </div>";
    }
    if(isset($_SESSION['login_user']) && ($_SESSION['role'] == "Manager" or $_SESSION['role'] == "All Roles")){
      echo "<div style='padding:50px 0px 10px 0px'>
              <form method='post' action='reports.php'>
              <select name='report'>
              <option value='seller'>Seller History</option>
              <option value='inv'>Inventory Age</option>
              <option value='time'>Average Time in Inventory</option>
              <option value='condition'>Price Per Condition</option>
              <option value='repair'>Repair Statistics</option>
              <option value='sales'>Monthly Sales</option>
              </select>
              <input type='submit' value='Run Report'>
              </form
            </div>";
    }
    ?>
  </body>
</html>
