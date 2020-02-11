<?php
session_start();
include("config.php");
$sql1 = "SELECT type, vehicle_condition, AVG(purchase_price) as Price from vehicle v INNER JOIN purchasetransaction p ON v.VIN=p.VIN GROUP BY type,vehicle_condition";
$result1 = mysqli_query($conn, $sql1);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Average Time in Inventory</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.1/js/jquery.tablesorter.min.js"></script>
    <script>
    $(function setRowPrice(tableId, rowId, colNum, newValue)
      {
          $('#'+table).find('tr#'+rowId).find('td:eq(colNum)').html(newValue);
      )};
    </script>
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
    tbody tr:hover{
      background-color: #ccc;
      cursor: pointer;
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
      <p style="font-size:20px">Average Time in Inventory Report</p>
    </div>
    <table id="results" style="width:60%" class="tablesorter">
      <thead>
        <tr>
        <th>Vehicle Type</th>
        <th>Fair</th>
        <th>Good</th>
        <th>Very Good</th>
        <th>Excellent</th>
        </tr>
      </thead>
      <tbody>
        <tr id="Convertible">
          <td>Convertible</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
        </tr>
        <tr id="Coupe">
          <td>Coupe</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
        </tr>
        <tr id="Minivan">
          <td>Minivan</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
        </tr>
        <tr id="Sedan">
          <td>Sedan</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
        </tr>
        <tr id="SUV">
          <td>SUV</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
        </tr>
        <tr id="Truck">
          <td>Truck</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
        </tr>
        <tr id="Van">
          <td>Van</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
          <td>$0</td>
        </tr>
      </tbody>
    </table>
      <?php
      while($row = $result1->fetch_assoc()){
        $type = $row["type"];
        $condition = $row["vehicle_condition"];
        switch ($condition) {
          case 'Fair':
            $condition = 1;
            break;
          case 'Good':
            $condition = 2;
            break;
          case 'Very Good':
            $condition = 3;
            break;
          case 'Excellent':
            $condition = 4;
            break;
        }
        $price = number_format($row["Price"],2);
        echo jquery(setRowPrice('results','$type','$condition','$$price'));
      }
      ?>
  </body>
</html>
