<?php
include("config.php");
session_start();
header_remove();
$VIN = $_GET["VIN"];
$sql1 = "SELECT mileage, model_name, model_year, vehicle.description, type, manufacturer, purchase_price
        FROM vehicle INNER JOIN purchasetransaction ON vehicle.VIN=purchasetransaction.VIN WHERE vehicle.VIN='$VIN'";
$result1 = mysqli_query($conn, $sql1);
$row1 = mysqli_fetch_row($result1);
$mileage = number_format($row1[0]);
$model_name = $row1[1];
$model_year = $row1[2];
$description = $row1[3];
$type = $row1[4];
$manufacturer = $row1[5];
$purchaseprice = $row1[6];
$sql2 = "SELECT SUM(total_cost) FROM repair WHERE VIN='$VIN'";
$result2 = mysqli_query($conn, $sql2);
if($result2->num_rows > 0){
  $total_cost = $result2->fetch_assoc()["SUM(total_cost)"];
} else {$total_cost = 0;}
$salesprice = number_format($purchaseprice * 1.25 + $total_cost * 1.1,2);
$sql3 = "SELECT color FROM vehiclecolor WHERE VIN='$VIN'";
$result3 = mysqli_query($conn, $sql3);
$color = $result3->fetch_assoc()["color"];
$sql5 = "SELECT VIN FROM salestransaction WHERE VIN='$VIN'";
$result5 = mysqli_query($conn,$sql5);
if($result5->num_rows == 1){
  $sold = TRUE;
}else{
  $sold = FALSE;
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Vehicle Information</title>
  </head>
  <style>
    body{
      padding: 20px;
    }
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
    }
    th, td {
      padding: 5px;
    }
    th {
      text-align: left;
      background-color: #72a1ed;
    }
    .topnav {
      background-color: #333;
      overflow: hidden;
      text-align: left;
      height: 50px;
      line-height: 50px;
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
      <p style="font-size: 20px">Your selected vehicle:</p>
    </div>
    <table id="results" style="width:75%">
      <thead>
        <tr>
          <th>Attribute</th>
          <th>Value</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>VIN</td>
          <td><?php echo $VIN;?></td>
        </tr>
        <tr>
          <td>Vehicle Type</td>
          <td><?php echo $type;?></td>
        </tr>
        <tr>
          <td>Model Year</td>
          <td><?php echo $model_year;?></td>
        </tr>
        <tr>
          <td>Manufacturer</td>
          <td><?php echo $manufacturer;?></td>
        </tr>
        <tr>
          <td>Model Name</td>
          <td><?php echo $model_name;?></td>
        </tr>
        <tr>
          <td>Color(s)</td>
          <td><?php echo $color;?></td>
        </tr>
        <tr>
          <td>Mileage</td>
          <td><?php echo $mileage;?></td>
        </tr>
        <tr>
          <td>Sales Price</td>
          <td><?php echo "$",$salesprice;?></td>
        </tr>
        <tr>
          <td>Description</td>
          <td><?php echo $description;?></td>
        </tr>
        <?php if(isset($_SESSION['role']) and $_SESSION["role"]=="Clerk"){
          echo
          "<tr>
            <td>Original Purchase Price</td>
            <td>$",number_format($purchaseprice,2),"</td>
          </tr>
          <tr>
            <td>Repair Costs</td>
            <td>$",number_format($total_cost,2),"</td>
          </tr>";
        }?>
      </tbody>
    </table>
    <?php
    if(isset($_SESSION['role']) and in_array($_SESSION['role'], ['Sales Person','All Roles'])){
      echo "<div style='padding:10px 0px'>
            <form action='/cs6400/sales-order-form.php' method='get'>
            <input type='hidden' name='VIN' value='$VIN'>
            <input type='submit' value='Sell Vehicle'>
            </form>
            </div>";
    }
    if(isset($_SESSION['role']) and in_array($_SESSION['role'], ['Clerk','Manager','All Roles'])){
      $sql4 = "SELECT vendor_name, recall_number, start_date, end_date, total_cost, status, description FROM repair WHERE VIN='$VIN'";
      $result4 = mysqli_query($conn,$sql4);
      echo
      "<div>
        <p style='font-size: 20px'>Repairs:</p>
      </div>
      <table id='repairs' style='width:75%'>
        <thead>
          <tr>
            <th>Status</th>
            <th>Vendor Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Total Cost</th>
            <th>Recall Number</th>
            <th>Description</th>
          </tr>
        </thead>
        <tbody>";
      while($row=$result4->fetch_assoc()){
        echo '<tr>
        <td>', $row["status"], '</td>
        <td>', $row["vendor_name"], '</td>
        <td>', $row["start_date"], '</td>
        <td>', $row["end_date"], '</td>
        <td> $', number_format($row["total_cost"],2), '</td>
        <td>', $row["recall_number"], '</td>
        <td>View</td>
        </tr>';
      }
    echo "</tbody>
    </table>
    <div style='padding: 10px 0px'>";
    if(isset($_SESSION['login_user']) && in_array($_SESSION['role'], ['Clerk','All Roles'])){
    echo "<a href='/cs6400/add-repair.php?VIN=$VIN'><button>Add Repair</button></a>";}
    }
    if(isset($_SESSION['login_user']) && in_array($_SESSION['role'], ['Manager','All Roles'])){
    echo "<div>
            <p style='font-size: 20px'>Seller Info:</p>
          </div>
          <table id='seller' style='width:75%'>
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Street</th>
                <th>City</th>
                <th>State</th>
                <th>Zip</th>
                <th>Phone</th>
                <th>Clerk</th>
              </tr>
            </thead>
            </table>";
            if($sold==TRUE){
             echo "<div>
                      <p style='font-size: 20px'>Buyer Info:</p>
                    </div>
                    <table id='buyer' style='width:75%'>
                      <thead>
                        <tr>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Street</th>
                          <th>City</th>
                          <th>State</th>
                          <th>Zip</th>
                          <th>Phone</th>
                          <th>Salesperson</th>
                        </tr>
                      </thead>
                      </table>";
    }
  }
  ?>
  </body>
</html>
