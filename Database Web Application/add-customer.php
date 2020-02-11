<?php
session_start();
include("config.php");
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Add Customer</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
    $(document).ready(function(){
      $("input[name$='type']").click(function() {
        var type = $(this).val();
        if(type=="individual"){$("#individual").show(); $("#business").hide();}
        else{$("#business").show(); $("#individual").hide();}
      });
    });
    </script>
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
      <p style="font-size:20px">Add a customer to the database:</p>
    </div>
    <div>
      <input type="radio" name="type" value="individual">Individual<br>
      <input type="radio" name="type" value="business">Business
    </div><br>
    <form id="individual" style="display:none" action="<?php echo htmlspecialchars('process-customer.php');?>" method="post">
      <input type="hidden" name="type" value="individual">
      First Name:<br>
      <input type="text" name="fName" required><br><br>
      Last Name:<br>
      <input type="text" name="lName" required><br><br>
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
      Email Address:<br>
      <input type="email" name="email"><br><br>
      Driver's License Number:<br>
      <input type="text" name="customerID" required><br><br>
      <input type="submit" name="submit" value="Submit">
    </form>
    <form id="business" style="display:none" action="<?php echo htmlspecialchars('process-customer.php');?>" method="post">
      <input type="hidden" name="type" value="business">
      Tax ID Number:<br>
      <input type="text" name="customerID" required><br><br>
      Business Name:<br>
      <input type="text" name="bName" required><br><br>
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
      Email Address:<br>
      <input type="email" name="email"><br><br>
      Primary Contact Name:<br>
      <input type="text" name="name" required><br><br>
      Primary Contact Title:<br>
      <input type="text" name="title" required><br><br>
      <input type="submit" name="submit" value="Submit">
    </form>
  </body>
</html>
